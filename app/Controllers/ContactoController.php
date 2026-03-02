<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\ContactoModel;
use App\Models\NotificacionModel;
use CodeIgniter\HTTP\ResponseInterface;

class ContactoController extends BaseController
{
    private ContactoModel $model;
    private NotificacionModel $notiModel;

    public function __construct()
    {
        helper(['form', 'url']);
        $this->model     = new ContactoModel();
        $this->notiModel = new NotificacionModel();
    }

    public function formulario(): string
    {
        return view('public/contacto_view', [
            'breadcrumbs' => [
                ['name' => 'Inicio',   'url' => base_url(), 'active' => false],
                ['name' => 'Contacto', 'url' => '#',         'active' => true],
            ],
            'sitekey' => getenv('RECAPTCHA_SITEKEY') ?: '',
        ]);
    }

    public function enviar(): ResponseInterface
    {
        try {
            $throttler = \Config\Services::throttler();
            if ($throttler->check('contact_' . md5($this->request->getIPAddress()), 3, 60) === false) {
                return $this->response->setJSON(['success' => false, 'mensaje' => 'Demasiados intentos. Espere un momento.']);
            }

            $rules = [
                'nombre'    => 'required|min_length[2]|max_length[100]|regex_match[/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/]',
                'email'     => 'required|valid_email|max_length[150]',
                'telefono'  => 'permit_empty|regex_match[/^[0-9+\-\s]{7,20}$/]',
                'asunto'    => 'permit_empty|max_length[200]',
                'mensaje'   => 'required|min_length[10]|max_length[2000]',
                'categoria' => 'permit_empty|in_list[consulta,presupuesto,soporte,colaboracion]',
            ];

            if (!$this->validate($rules)) {
                return $this->response->setJSON(['success' => false, 'errors' => $this->validator->getErrors()]);
            }

            $captchaToken = $this->request->getPost('g-recaptcha-response', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            if (!$this->verificarCaptcha($captchaToken)) {
                return $this->response->setJSON(['success' => false, 'mensaje' => 'Verificación de seguridad fallida. Intente nuevamente.']);
            }

            $data = [
                'nombre'    => $this->request->getPost('nombre'),
                'email'     => $this->request->getPost('email'),
                'telefono'  => $this->request->getPost('telefono') ?: null,
                'asunto'    => $this->request->getPost('asunto') ?: 'Sin asunto',
                'mensaje'   => $this->request->getPost('mensaje'),
                'categoria' => $this->request->getPost('categoria') ?: 'consulta',
                'estado'    => 'pendiente',
                'leido'     => false,
                'ip_origen' => $this->request->getIPAddress(),
            ];

            $ok = $this->model->crear($data);

            if ($ok) {
                $this->notiModel->crear([
                    'tipo'       => 'info',
                    'titulo'     => 'Nuevo mensaje de contacto',
                    'mensaje'    => "De: {$data['nombre']} ({$data['email']}) - {$data['asunto']}",
                    'url_accion' => base_url('admin/contactos'),
                    'leido'      => false,
                ]);
            }

            return $this->response->setJSON([
                'success' => $ok,
                'mensaje' => $ok
                    ? 'Mensaje enviado correctamente. Te responderé a la brevedad.'
                    : 'Error al enviar el mensaje. Intenta de nuevo.',
            ]);
        } catch (\Throwable $e) {
            log_message('error', 'ContactoController::enviar ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'mensaje' => 'Error interno del servidor.']);
        }
    }

    public function admin(): string
    {
        try {
            return view('admin/contactos_view', [
                'breadcrumbs' => [
                    ['name' => 'Admin',     'url' => base_url('admin/dashboard'), 'active' => false],
                    ['name' => 'Contactos', 'url' => '#',                          'active' => true],
                ],
            ]);
        } catch (\Throwable $e) {
            log_message('critical', 'ContactoController::admin ' . $e->getMessage());
            return view('errors/html/error_500');
        }
    }

    public function listar(): ResponseInterface
    {
        try {
            $filters = [
                'busqueda'     => $this->request->getGet('busqueda') ?: '',
                'estado'       => $this->request->getGet('estado') ?: '',
                'categoria'    => $this->request->getGet('categoria') ?: '',
                'fecha_desde'  => $this->request->getGet('fecha_desde') ?: '',
                'fecha_hasta'  => $this->request->getGet('fecha_hasta') ?: '',
            ];
            $page    = max(1, (int)($this->request->getGet('page') ?: 1));
            $result  = $this->model->obtenerFiltrado($filters, $page, 5);
            return $this->response->setJSON(['success' => true, ...$result]);
        } catch (\Throwable $e) {
            log_message('error', 'ContactoController::listar ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'data' => [], 'total' => 0, 'total_pages' => 1, 'page' => 1]);
        }
    }

    public function ver(int $id): ResponseInterface
    {
        $contacto = $this->model->obtenerPorId($id);
        if (!empty($contacto) && !($contacto['leido'] ?? true)) {
            $this->model->marcarLeido($id);
        }
        return $this->response->setJSON(['success' => !empty($contacto), 'data' => $contacto]);
    }

    public function actualizar(int $id): ResponseInterface
    {
        try {
            $data = [];
            if ($this->request->getPost('estado') !== null) {
                $data['estado'] = $this->request->getPost('estado');
            }
            if ($this->request->getPost('leido') !== null) {
                $data['leido'] = $this->request->getPost('leido') === '1';
            }
            $ok = !empty($data) && $this->model->actualizar($id, $data);
            return $this->response->setJSON(['success' => $ok, 'mensaje' => $ok ? 'Actualizado' : 'Sin cambios']);
        } catch (\Throwable $e) {
            return $this->response->setJSON(['success' => false]);
        }
    }

    public function eliminar(int $id): ResponseInterface
    {
        $ok = $this->model->eliminar($id);
        return $this->response->setJSON(['success' => $ok, 'mensaje' => $ok ? 'Contacto eliminado' : 'Error al eliminar']);
    }

    private function verificarCaptcha(?string $response): bool
    {
        $secret = getenv('RECAPTCHA_SECRETKEY');
        if (empty($secret)) return true;
        if (empty($response) || $response === 'dev-bypass') return empty($secret);

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
            log_message('error', 'Contacto reCAPTCHA: ' . $e->getMessage());
            return false;
        }
    }
}
