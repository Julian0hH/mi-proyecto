<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\RecuperacionModel;
use CodeIgniter\HTTP\ResponseInterface;

class PasswordController extends BaseController
{
    private RecuperacionModel $model;

    public function __construct()
    {
        helper(['form', 'url']);
        $this->model = new RecuperacionModel();
    }

    public function solicitar(): string
    {
        return view('auth/password_recovery_view', [
            'breadcrumbs' => [
                ['name' => 'Login',               'url' => base_url('login'), 'active' => false],
                ['name' => 'Recuperar Contraseña', 'url' => '#',               'active' => true],
            ],
            'paso' => 'email',
        ]);
    }

    public function enviarCodigo(): ResponseInterface
    {
        try {
            $throttler = \Config\Services::throttler();
            if ($throttler->check('recovery_' . md5($this->request->getIPAddress()), 3, 300) === false) {
                return $this->response->setJSON(['success' => false, 'mensaje' => 'Demasiados intentos. Espere 5 minutos.']);
            }

            $rules = ['email' => 'required|valid_email|max_length[150]'];
            if (!$this->validate($rules)) {
                return $this->response->setJSON(['success' => false, 'errors' => $this->validator->getErrors()]);
            }

            $email   = $this->request->getPost('email');
            $usuario = $this->model->buscarUsuarioPorEmail($email);

            // Respuesta siempre exitosa para evitar enumeración de emails
            if (!empty($usuario) && ($usuario['activo'] ?? false)) {
                $token = $this->model->crearToken($email);
                $this->enviarEmailRecuperacion($email, $usuario['nombre'], $token);
                log_message('info', "Código de recuperación enviado a: $email");
            }

            return $this->response->setJSON([
                'success' => true,
                'mensaje' => 'Si el email está registrado, recibirás un código de 6 dígitos. Revisa tu bandeja de entrada.',
            ]);
        } catch (\Throwable $e) {
            log_message('error', 'PasswordController::enviarCodigo ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'mensaje' => 'Error del servidor.']);
        }
    }

    public function verificarCodigo(): ResponseInterface
    {
        try {
            $rules = [
                'email' => 'required|valid_email',
                'token' => 'required|exact_length[6]|regex_match[/^[0-9]{6}$/]',
            ];
            if (!$this->validate($rules)) {
                return $this->response->setJSON(['success' => false, 'errors' => $this->validator->getErrors()]);
            }

            $valido = $this->model->verificarToken(
                $this->request->getPost('email'),
                $this->request->getPost('token')
            );

            return $this->response->setJSON([
                'success' => $valido,
                'mensaje' => $valido
                    ? 'Código verificado. Ingresa tu nueva contraseña.'
                    : 'Código inválido o expirado. Solicita uno nuevo.',
            ]);
        } catch (\Throwable $e) {
            return $this->response->setJSON(['success' => false, 'mensaje' => 'Error del servidor.']);
        }
    }

    public function cambiarPassword(): ResponseInterface
    {
        try {
            $rules = [
                'email'            => 'required|valid_email',
                'token'            => 'required|exact_length[6]|regex_match[/^[0-9]{6}$/]',
                'password'         => [
                    'rules'  => 'required|min_length[12]|regex_match[/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{12,}$/]',
                    'errors' => [
                        'min_length'   => 'La contraseña debe tener al menos 12 caracteres.',
                        'regex_match'  => 'Debe incluir mayúscula, minúscula, número y carácter especial (@$!%*?&).',
                    ],
                ],
                'password_confirm' => 'required|matches[password]',
            ];
            if (!$this->validate($rules)) {
                return $this->response->setJSON(['success' => false, 'errors' => $this->validator->getErrors()]);
            }

            $email = $this->request->getPost('email');
            $token = $this->request->getPost('token');

            if (!$this->model->verificarToken($email, $token)) {
                return $this->response->setJSON(['success' => false, 'mensaje' => 'Token inválido o expirado.']);
            }

            $newHash = password_hash($this->request->getPost('password'), PASSWORD_BCRYPT, ['cost' => 12]);
            $ok      = $this->model->actualizarPassword($email, $newHash);

            if ($ok) {
                $this->model->invalidarToken($email, $token);
            }

            return $this->response->setJSON([
                'success'  => $ok,
                'mensaje'  => $ok ? 'Contraseña actualizada. Ahora puedes iniciar sesión.' : 'Error al actualizar contraseña.',
                'redirect' => $ok ? base_url('login') : null,
            ]);
        } catch (\Throwable $e) {
            log_message('error', 'PasswordController::cambiarPassword ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'mensaje' => 'Error del servidor.']);
        }
    }

    private function enviarEmailRecuperacion(string $email, string $nombre, string $token): void
    {
        try {
            $mailer = \Config\Services::email();
            $mailer->setTo($email);
            $mailer->setFrom(getenv('MAIL_FROM') ?: 'noreply@portfolio.com', 'Portfolio Pro');
            $mailer->setSubject('Código de recuperación de contraseña');
            $mailer->setMessage("
                <div style='font-family:Arial,sans-serif;max-width:500px;margin:auto;padding:30px;background:#f8f9fa;border-radius:12px'>
                    <h2 style='color:#4f46e5'>Recuperación de Contraseña</h2>
                    <p>Hola, <strong>$nombre</strong></p>
                    <p>Tu código de verificación es:</p>
                    <div style='background:#4f46e5;color:#fff;font-size:36px;font-weight:bold;letter-spacing:12px;padding:20px;border-radius:8px;text-align:center'>$token</div>
                    <p style='margin-top:20px;color:#6c757d'>Este código expira en <strong>15 minutos</strong>.</p>
                    <p style='color:#6c757d'>Si no solicitaste este cambio, ignora este correo.</p>
                </div>
            ");
            $mailer->send();
        } catch (\Throwable $e) {
            log_message('error', 'Email recovery failed: ' . $e->getMessage());
            log_message('debug', "[DEV] Token para $email: $token");
        }
    }
}
