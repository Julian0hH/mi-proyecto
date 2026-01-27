<?php

declare(strict_types=1);

namespace App\Controllers;

use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\HTTP\ResponseInterface;

class Home extends BaseController
{
    private ?string $supabaseUrl = null;
    private ?string $supabaseKey = null;
    private bool $configError = false;

    public function __construct()
    {
        helper(['form', 'url', 'date', 'text']);
        $this->supabaseUrl = getenv('SUPABASE_URL') ?: null;
        $this->supabaseKey = getenv('SUPABASE_SERVICE_ROLE_KEY') ?: null;

        if (empty($this->supabaseUrl) || empty($this->supabaseKey)) {
            $this->configError = true;
        }
    }

    public function index(): string
    {
        $data['breadcrumbs'] = [['name' => 'Inicio', 'url' => base_url(), 'active' => true]];
        return view('home_welcome', $data);
    }

    public function registro(): string
    {
        if ($this->configError) {
            return view('errors/html/production');
        }

        $client = \Config\Services::curlrequest();
        $data['usuarios'] = [];

        try {
            $response = $client->request('GET', $this->supabaseUrl . '/rest/v1/usuarios?select=*', [
                'headers' => [
                    'apikey'        => $this->supabaseKey,
                    'Authorization' => 'Bearer ' . $this->supabaseKey,
                ],
                'http_errors' => false,
                'timeout'     => 5
            ]);

            if ($response->getStatusCode() === 200) {
                $body = json_decode($response->getBody(), true);
                $data['usuarios'] = json_last_error() === JSON_ERROR_NONE ? $body : [];
            }
        } catch (\Throwable $e) {
            log_message('error', 'Supabase Connection: ' . $e->getMessage());
        }

        $data['sitekey'] = getenv('RECAPTCHA_SITEKEY');
        $data['breadcrumbs'] = [
            ['name' => 'Inicio', 'url' => base_url(), 'active' => false],
            ['name' => 'Registro', 'url' => base_url('registro'), 'active' => true]
        ];
        $data['validation'] = \Config\Services::validation();

        return view('register_view', $data);
    }

    public function guardar(): ResponseInterface
    {
        $throttler = \Config\Services::throttler();
        if ($throttler->check(md5($this->request->getIPAddress()), 5, 60) === false) {
            return redirect()->back()->withInput()->with('error', 'Demasiados intentos. Por favor espere un minuto.');
        }

        if ($this->configError) {
            return redirect()->back()->withInput()->with('error', 'Error de configuración del servidor.');
        }

        $rules = [
            'nombre' => 'required|min_length[3]|max_length[50]|regex_match[/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/]',
            'email'  => [
                'rules'  => 'required|max_length[100]|regex_match[/^[a-zA-Z0-9._+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/]',
                'errors' => ['regex_match' => 'Format invalid or dangerous symbols detected.']
            ],
            'g-recaptcha-response' => 'required'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $recaptchaResponse = $this->request->getPost('g-recaptcha-response', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        if (!$this->verificarCaptcha($recaptchaResponse)) {
            return redirect()->back()->withInput()->with('error', 'Security check failed.');
        }

        try {
            $client = \Config\Services::curlrequest();
            $client->request('POST', $this->supabaseUrl . '/rest/v1/usuarios', [
                'headers' => [
                    'apikey'        => $this->supabaseKey,
                    'Authorization' => 'Bearer ' . $this->supabaseKey,
                    'Content-Type'  => 'application/json',
                    'Prefer'        => 'return=minimal',
                ],
                'json' => [
                    'nombre' => $this->request->getPost('nombre'),
                    'email'  => $this->request->getPost('email'),
                ],
                'timeout' => 10
            ]);

            return redirect()->to('/registro')->with('success', 'Usuario registrado correctamente.');
        } catch (\Throwable $e) {
            log_message('critical', 'Error guardando usuario: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Servicio no disponible temporalmente.');
        }
    }

    public function eliminar(string $id): ResponseInterface
    {
        if ($this->configError) {
            return redirect()->to('/registro')->with('error', 'Error de configuración.');
        }

        if (!ctype_alnum($id) && !str_contains($id, '-')) {
            return redirect()->to('/registro')->with('error', 'ID inválido.');
        }

        try {
            $client = \Config\Services::curlrequest();
            $client->request('DELETE', $this->supabaseUrl . '/rest/v1/usuarios?id=eq.' . $id, [
                'headers' => [
                    'apikey'        => $this->supabaseKey,
                    'Authorization' => 'Bearer ' . $this->supabaseKey,
                ],
                'timeout' => 10
            ]);
            return redirect()->to('/registro')->with('success', 'Usuario eliminado.');
        } catch (\Throwable $e) {
            log_message('error', 'Error eliminando: ' . $e->getMessage());
            return redirect()->to('/registro')->with('error', 'No se pudo eliminar el registro.');
        }
    }

    public function validacion(): string
    {
        $data['breadcrumbs'] = [
            ['name' => 'Inicio', 'url' => base_url(), 'active' => false],
            ['name' => 'Validación UI', 'url' => '#', 'active' => true]
        ];
        return view('validacion_view', $data);
    }

    public function procesar_validacion(): ResponseInterface
    {
        $throttler = \Config\Services::throttler();
        if ($throttler->check(md5($this->request->getIPAddress()), 10, 60) === false) {
            return redirect()->back()->withInput()->with('error', 'Demasiados intentos. Espere un momento.');
        }

        $rules = [
            'nombre'    => 'required|min_length[2]|max_length[50]|regex_match[/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/]',
            'apellido'  => 'required|min_length[2]|max_length[50]|regex_match[/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/]',
            'sexo'      => 'required|in_list[masculino,femenino,otro]',
            'email'     => 'required|max_length[100]|regex_match[/^[a-zA-Z0-9._+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/]',
            'pais'      => 'required|alpha',
            'telefono'  => 'required|regex_match[/^[0-9]{7,15}$/]',
            'edad'      => 'required|integer|greater_than_equal_to[18]|less_than[120]',
            'fecha_nac' => 'required|valid_date[Y-m-d]',
            'sitio_web' => 'required|valid_url_strict[https]',
            'archivo'   => 'uploaded[archivo]|max_size[archivo,2048]|is_image[archivo]|mime_in[archivo,image/jpg,image/jpeg,image/png]|ext_in[archivo,jpg,jpeg,png]',
            'password'  => 'required|min_length[12]|regex_match[/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{12,}$/]',
            'pass_conf' => 'required|matches[password]',
            'terminos'  => 'required|in_list[1,on]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $fechaNac = $this->request->getPost('fecha_nac');
        $edadInput = (int)$this->request->getPost('edad');

        try {
            $nacimiento = new \DateTime($fechaNac);
            $hoy = new \DateTime();
            $edadReal = $hoy->diff($nacimiento)->y;

            if ($edadReal !== $edadInput) {
                return redirect()->back()->withInput()->with('errors', ['Integrity error: age mismatch.']);
            }
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('errors', ['Date invalid.']);
        }

        return redirect()->to('/validacion')->with('success', 'Validated');
    }

    public function servicios(): string
    {
        $data['breadcrumbs'] = [
            ['name' => 'Inicio', 'url' => base_url(), 'active' => false],
            ['name' => 'Catálogo', 'url' => '#', 'active' => false],
            ['name' => 'Servicios', 'url' => '#', 'active' => true]
        ];
        return view('servicios_view', $data);
    }

    public function detalles(): string
    {
        $data['breadcrumbs'] = [
            ['name' => 'Inicio', 'url' => base_url(), 'active' => false],
            ['name' => 'Catálogo', 'url' => '#', 'active' => false],
            ['name' => 'Servicios', 'url' => base_url('servicios'), 'active' => false],
            ['name' => 'Detalles', 'url' => '#', 'active' => true]
        ];
        return view('detalles_view', $data);
    }

    public function contratar(): string
    {
        $data['breadcrumbs'] = [
            ['name' => 'Inicio', 'url' => base_url(), 'active' => false],
            ['name' => 'Catálogo', 'url' => '#', 'active' => false],
            ['name' => 'Servicios', 'url' => base_url('servicios'), 'active' => false],
            ['name' => 'Detalles', 'url' => base_url('detalles'), 'active' => false],
            ['name' => 'Contratación', 'url' => '#', 'active' => true]
        ];
        return view('contratar_view', $data);
    }

    public function prueba_error(): void
    {
        throw new \RuntimeException("Error de prueba.");
    }

    private function verificarCaptcha($response): bool
    {
        $secret = getenv('RECAPTCHA_SECRETKEY');
        if (empty($secret)) {
            log_message('warning', 'Recaptcha secret missing.');
            return true; 
        }

        $client = \Config\Services::curlrequest();
        try {
            $verify = $client->request('POST', 'https://www.google.com/recaptcha/api/siteverify', [
                'form_params' => [
                    'secret'   => $secret,
                    'response' => $response,
                    'remoteip' => $this->request->getIPAddress()
                ],
                'timeout' => 5
            ]);
            $body = json_decode($verify->getBody());
            return isset($body->success) && $body->success;
        } catch (\Exception $e) {
            log_message('error', 'Recaptcha Verify Error: ' . $e->getMessage());
            return false;
        }
    }
}