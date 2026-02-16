<?php

declare(strict_types=1);

namespace App\Controllers;

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
            log_message('error', 'Supabase credentials not configured');
        }
    }

    public function index(): string
    {
        try {
            $data['breadcrumbs'] = [
                ['name' => 'Inicio', 'url' => base_url(), 'active' => true]
            ];
            return view('home_view', $data);
        } catch (\Throwable $e) {
            log_message('critical', 'Error in index: ' . $e->getMessage());
            return view('errors/html/error_500');
        }
    }

    public function registro(): string
    {
        try {
            $data['usuarios'] = [];
            $data['sitekey'] = getenv('RECAPTCHA_SITEKEY') ?: '';
            $data['breadcrumbs'] = [
                ['name' => 'Inicio', 'url' => base_url(), 'active' => false],
                ['name' => 'Registro', 'url' => base_url('registro'), 'active' => true]
            ];
            $data['validation'] = \Config\Services::validation();

            if ($this->configError) {
                log_message('warning', 'Supabase not configured, showing empty list');
                return view('register_view', $data);
            }

            try {
                $client = \Config\Services::curlrequest();
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
                    if (json_last_error() === JSON_ERROR_NONE && is_array($body)) {
                        $data['usuarios'] = $body;
                    }
                }
            } catch (\Throwable $e) {
                log_message('error', 'Supabase request failed: ' . $e->getMessage());
            }

            return view('register_view', $data);

        } catch (\Throwable $e) {
            log_message('critical', 'Error in registro: ' . $e->getMessage() . ' | File: ' . $e->getFile() . ' | Line: ' . $e->getLine());
            return view('errors/html/error_500');
        }
    }

    public function guardar(): ResponseInterface
    {
        try {
            $throttler = \Config\Services::throttler();
            if ($throttler->check(md5($this->request->getIPAddress()), 5, 60) === false) {
                return redirect()->back()->withInput()->with('error', 'Demasiados intentos. Por favor espere un minuto.');
            }

            if ($this->configError) {
                return redirect()->back()->withInput()->with('error', 'Error de configuración del servidor. Contacte al administrador.');
            }

            $rules = [
                'nombre' => 'required|min_length[3]|max_length[50]|regex_match[/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/]',
                'email'  => [
                    'rules'  => 'required|max_length[100]|regex_match[/^[a-zA-Z0-9._+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/]',
                    'errors' => ['regex_match' => 'Formato de email inválido.']
                ],
                'g-recaptcha-response' => 'required'
            ];

            if (!$this->validate($rules)) {
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }

            $recaptchaResponse = $this->request->getPost('g-recaptcha-response', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            if (!$this->verificarCaptcha($recaptchaResponse)) {
                return redirect()->back()->withInput()->with('error', 'Verificación de seguridad fallida. Intente nuevamente.');
            }

            $client = \Config\Services::curlrequest();
            $response = $client->request('POST', $this->supabaseUrl . '/rest/v1/usuarios', [
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
                'timeout' => 10,
                'http_errors' => false
            ]);

            if ($response->getStatusCode() === 201 || $response->getStatusCode() === 200) {
                return redirect()->to('/registro')->with('success', 'Usuario registrado correctamente.');
            } else {
                log_message('error', 'Supabase insert failed: ' . $response->getBody());
                return redirect()->back()->withInput()->with('error', 'Error al guardar en la base de datos.');
            }

        } catch (\Throwable $e) {
            log_message('critical', 'Error in guardar: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Error del servidor. Por favor intente más tarde.');
        }
    }

    public function eliminar(string $id): ResponseInterface
    {
        try {
            if ($this->configError) {
                return redirect()->to('/registro')->with('error', 'Error de configuración.');
            }

            if (!preg_match('/^[a-zA-Z0-9-]+$/', $id)) {
                return redirect()->to('/registro')->with('error', 'ID inválido.');
            }

            $client = \Config\Services::curlrequest();
            $response = $client->request('DELETE', $this->supabaseUrl . '/rest/v1/usuarios?id=eq.' . $id, [
                'headers' => [
                    'apikey'        => $this->supabaseKey,
                    'Authorization' => 'Bearer ' . $this->supabaseKey,
                ],
                'timeout' => 10,
                'http_errors' => false
            ]);

            if ($response->getStatusCode() === 204 || $response->getStatusCode() === 200) {
                return redirect()->to('/registro')->with('success', 'Usuario eliminado correctamente.');
            } else {
                return redirect()->to('/registro')->with('error', 'Error al eliminar el registro.');
            }

        } catch (\Throwable $e) {
            log_message('error', 'Error eliminando: ' . $e->getMessage());
            return redirect()->to('/registro')->with('error', 'Error del servidor.');
        }
    }

    public function validacion(): string
    {
        try {
            $data['breadcrumbs'] = [
                ['name' => 'Inicio', 'url' => base_url(), 'active' => false],
                ['name' => 'Validación', 'url' => '#', 'active' => true]
            ];
            return view('validacion_view', $data);
        } catch (\Throwable $e) {
            log_message('critical', 'Error in validacion: ' . $e->getMessage());
            return view('errors/html/error_500');
        }
    }

    public function procesar_validacion(): ResponseInterface
    {
        try {
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

            $nacimiento = new \DateTime($fechaNac);
            $hoy = new \DateTime();
            $edadReal = $hoy->diff($nacimiento)->y;

            if ($edadReal !== $edadInput) {
                return redirect()->back()->withInput()->with('errors', ['La edad no coincide con la fecha de nacimiento.']);
            }

            return redirect()->to('/validacion')->with('success', 'Datos validados correctamente. Formulario procesado con éxito.');

        } catch (\Throwable $e) {
            log_message('critical', 'Error in procesar_validacion: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Error del servidor.');
        }
    }

    public function servicios(): string
    {
        try {
            $data['breadcrumbs'] = [
                ['name' => 'Inicio', 'url' => base_url(), 'active' => false],
                ['name' => 'Catálogo', 'url' => '#', 'active' => false],
                ['name' => 'Servicios', 'url' => '#', 'active' => true]
            ];
            return view('servicios_view', $data);
        } catch (\Throwable $e) {
            log_message('critical', 'Error in servicios: ' . $e->getMessage());
            return view('errors/html/error_500');
        }
    }

    public function detalles(): string
    {
        try {
            $data['breadcrumbs'] = [
                ['name' => 'Inicio', 'url' => base_url(), 'active' => false],
                ['name' => 'Catálogo', 'url' => '#', 'active' => false],
                ['name' => 'Servicios', 'url' => base_url('servicios'), 'active' => false],
                ['name' => 'Detalles', 'url' => '#', 'active' => true]
            ];
            return view('detalles_view', $data);
        } catch (\Throwable $e) {
            log_message('critical', 'Error in detalles: ' . $e->getMessage());
            return view('errors/html/error_500');
        }
    }

    public function contratar(): string
    {
        try {
            $data['breadcrumbs'] = [
                ['name' => 'Inicio', 'url' => base_url(), 'active' => false],
                ['name' => 'Catálogo', 'url' => '#', 'active' => false],
                ['name' => 'Servicios', 'url' => base_url('servicios'), 'active' => false],
                ['name' => 'Detalles', 'url' => base_url('detalles'), 'active' => false],
                ['name' => 'Contratación', 'url' => '#', 'active' => true]
            ];
            return view('contratar_view', $data);
        } catch (\Throwable $e) {
            log_message('critical', 'Error in contratar: ' . $e->getMessage());
            return view('errors/html/error_500');
        }
    }

    private function verificarCaptcha($response): bool
    {
        $secret = getenv('RECAPTCHA_SECRETKEY');
        if (empty($secret)) {
            log_message('warning', 'Recaptcha secret key not configured');
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
                    'remoteip' => $this->request->getIPAddress()
                ],
                'timeout' => 5,
                'http_errors' => false
            ]);

            $body = json_decode($verify->getBody());
            return isset($body->success) && $body->success === true;

        } catch (\Throwable $e) {
            log_message('error', 'Recaptcha verification error: ' . $e->getMessage());
            return false;
        }
    }
}