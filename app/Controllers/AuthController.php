<?php

namespace App\Controllers;

use App\Models\UsuarioModel;
use App\Models\PermisoModel;
use App\Traits\InputSanitizer;

class AuthController extends BaseController
{
    use InputSanitizer;

    public function login()
    {
        if (session('logueado')) {
            return redirect()->to(base_url('admin/dashboard'));
        }

        return view('login_view', [
            'breadcrumbs' => [
                ['name' => 'Inicio', 'url' => base_url(), 'active' => false],
                ['name' => 'Login',  'url' => '#',          'active' => true],
            ],
        ]);
    }

    public function procesarLogin()
    {
        $rules = [
            'email' => [
                'rules'  => 'required|valid_email',
                'errors' => [
                    'required'    => 'El correo es obligatorio.',
                    'valid_email' => 'Ingresa un correo válido.',
                ],
            ],
            'password' => [
                'rules'  => 'required|min_length[6]',
                'errors' => [
                    'required'   => 'La contraseña es obligatoria.',
                    'min_length' => 'Mínimo 6 caracteres.',
                ],
            ],
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()
                ->with('errors', \Config\Services::validation()->getErrors());
        }

        if (!$this->verificarCaptcha($this->request->getPost('g-recaptcha-response'))) {
            return redirect()->back()->withInput()
                ->with('error', 'Verificación de seguridad fallida. Intenta nuevamente.');
        }

        $email    = $this->sanitize($this->request->getPost('email'));
        $password = $this->request->getPost('password');

        if ($this->hasDangerous($email)) {
            return redirect()->back()->withInput()
                ->with('error', 'Caracteres no permitidos.');
        }

        $usuarioModel = new UsuarioModel();
        $user = $usuarioModel->buscarPorEmail($email);

        if (empty($user)) {
            return redirect()->back()->withInput()
                ->with('error', 'Credenciales incorrectas.');
        }

        if (!password_verify($password, $user['password_hash'] ?? '')) {
            return redirect()->back()->withInput()
                ->with('error', 'Credenciales incorrectas.');
        }

        if (empty($user['activo'])) {
            return redirect()->back()->withInput()
                ->with('error', 'Tu cuenta está inactiva. Contacta al administrador.');
        }

        $permisoModel = new PermisoModel();
        $filas        = $permisoModel->obtenerPorPerfil((int)$user['perfil_id']);
        $sesion       = $permisoModel->construirSesion($filas);

        session()->set([
            'logueado'        => true,
            'user_id'         => $user['id'],
            'user_nombre'     => $user['nombre'],
            'user_email'      => $user['email'],
            'perfil_id'       => $user['perfil_id'],
            'perfil_nombre'   => $user['perfiles']['nombre'] ?? '',
            'permisos'        => $sesion['permisos'],
            'sidebar_modulos' => $sesion['sidebar_modulos'],
        ]);

        $usuarioModel->actualizarUltimoLogin((int)$user['id']);

        return redirect()->to(base_url('admin/dashboard'))
            ->with('success', '¡Bienvenido, ' . $user['nombre'] . '!');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to(base_url('login'))
            ->with('success', 'Sesión cerrada correctamente.');
    }

    private function verificarCaptcha(?string $response): bool
    {
        $secret = getenv('RECAPTCHA_SECRETKEY');
        if (empty($secret)) {
            return true;
        }
        if (empty($response)) {
            return false;
        }
        try {
            $client = \Config\Services::curlrequest();
            $verify = $client->request('POST', 'https://www.google.com/recaptcha/api/siteverify', [
                'form_params' => [
                    'secret'   => $secret,
                    'response' => $response,
                    'remoteip' => $this->request->getIPAddress(),
                ],
                'timeout'     => 5,
                'http_errors' => false,
            ]);
            $body = json_decode($verify->getBody());
            return isset($body->success) && $body->success === true;
        } catch (\Throwable $e) {
            log_message('error', 'reCAPTCHA error: ' . $e->getMessage());
            return false;
        }
    }
}
