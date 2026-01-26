<?php

namespace App\Controllers;

class Home extends BaseController
{
    private ?string $supabaseUrl = null;
    private ?string $supabaseKey = null;

    public function __construct()
    {
        helper(['form', 'url', 'date']);
        $this->supabaseUrl = getenv('SUPABASE_URL');
        $this->supabaseKey = getenv('SUPABASE_SERVICE_ROLE_KEY');

        if (empty($this->supabaseUrl) || empty($this->supabaseKey)) {
            die('Error: Credenciales de Supabase no configuradas.');
        }
    }

    public function index()
    {
        $data['breadcrumbs'] = [['name' => 'Inicio', 'url' => base_url(), 'active' => true]];
        return view('home_welcome', $data);
    }

    public function registro()
    {
        $client = \Config\Services::curlrequest();
        $data['usuarios'] = [];

        try {
            if ($this->supabaseUrl && $this->supabaseKey) {
                $response = $client->request('GET', $this->supabaseUrl . '/rest/v1/usuarios?select=*', [
                    'headers' => [
                        'apikey'        => $this->supabaseKey,
                        'Authorization' => 'Bearer ' . $this->supabaseKey,
                        'Content-Type'  => 'application/json',
                    ],
                    'http_errors' => false
                ]);
                if ($response->getStatusCode() === 200) {
                    $data['usuarios'] = json_decode($response->getBody(), true);
                }
            }
        } catch (\Exception $e) { }

        $data['sitekey'] = getenv('RECAPTCHA_SITEKEY');
        
        $data['breadcrumbs'] = [
            ['name' => 'Inicio', 'url' => base_url(), 'active' => false],
            ['name' => 'Registro', 'url' => base_url('registro'), 'active' => true]
        ];
        $data['validation'] = \Config\Services::validation();

        return view('register_view', $data);
    }

    public function guardar()
    {
        $rules = [
            'nombre' => [
                'label' => 'Nombre',
                'rules' => 'required|min_length[3]|max_length[50]|regex_match[/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/]',
                'errors' => [
                    'regex_match' => 'El nombre solo puede contener letras.',
                    'max_length' => 'El nombre es muy largo (máximo 50 caracteres).'
                ]
            ],
            'email' => [
                'label' => 'Email',
                'rules' => 'required|valid_email|max_length[100]',
            ]
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $recaptchaResponse = $this->request->getPost('g-recaptcha-response');
        if (!$recaptchaResponse) {
            return redirect()->back()->withInput()->with('error', 'Por favor completa el captcha.');
        }

        $secretKey = getenv('RECAPTCHA_SECRETKEY');
        $client = \Config\Services::curlrequest();

        try {
            $verifyResponse = $client->request('POST', 'https://www.google.com/recaptcha/api/siteverify', [
                'form_params' => [
                    'secret'   => $secretKey,
                    'response' => $recaptchaResponse,
                    'remoteip' => $this->request->getIPAddress(),
                ]
            ]);

            $verifyBody = json_decode($verifyResponse->getBody());

            if (!$verifyBody->success) {
                return redirect()->back()->with('error', 'Captcha inválido.');
            }

            $client->request('POST', $this->supabaseUrl . '/rest/v1/usuarios', [
                'headers' => [
                    'apikey'        => $this->supabaseKey,
                    'Authorization' => 'Bearer ' . $this->supabaseKey,
                    'Content-Type'  => 'application/json',
                    'Prefer'        => 'return=minimal',
                ],
                'json' => [
                    'nombre' => esc($this->request->getPost('nombre')),
                    'email'  => esc($this->request->getPost('email')),
                ]
            ]);

            return redirect()->to('/registro')->with('success', 'Usuario registrado correctamente.');

        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Error de conexión.');
        }
    }

    public function eliminar($id)
    {
        $client = \Config\Services::curlrequest();
        try {
            $client->request('DELETE', $this->supabaseUrl . '/rest/v1/usuarios?id=eq.' . $id, [
                'headers' => [
                    'apikey'        => $this->supabaseKey,
                    'Authorization' => 'Bearer ' . $this->supabaseKey,
                ]
            ]);
            return redirect()->to('/registro')->with('success', 'Usuario eliminado.');
        } catch (\Exception $e) {
            return redirect()->to('/registro')->with('error', 'No se pudo eliminar.');
        }
    }

    public function validacion()
    {
        $data['breadcrumbs'] = [
            ['name' => 'Inicio', 'url' => base_url(), 'active' => false],
            ['name' => 'Validación', 'url' => '#', 'active' => true]
        ];
        return view('validacion_view', $data);
    }

    public function procesar_validacion()
    {
        $fechaNac = $this->request->getPost('fecha_nac');
        $edadInput = $this->request->getPost('edad');

        $rules = [
            'nombre' => 'required|min_length[3]|max_length[50]|regex_match[/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/]',
            'email' => 'required|valid_email',
            'telefono' => 'required|numeric|exact_length[10]',
            'edad' => 'required|integer|greater_than_equal_to[18]|less_than_equal_to[99]',
            'fecha_nac' => 'required|valid_date',
            'sitio_web' => 'required|valid_url',
            'password' => 'required|min_length[8]|regex_match[/^(?=.*[A-Z])(?=.*\d).+$/]',
            'pass_conf' => 'required|matches[password]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        if ($fechaNac && $edadInput) {
            $nacimiento = new \DateTime($fechaNac);
            $hoy = new \DateTime();
            $edadReal = $hoy->diff($nacimiento)->y;

            if ($edadReal != $edadInput) {
                return redirect()->back()->withInput()->with('errors', [
                    "Error de lógica: La fecha de nacimiento indica que tienes {$edadReal} años, no {$edadInput}."
                ]);
            }
        }

        return redirect()->to('/validacion')->with('success', 'Validación exitosa.');
    }

    public function servicios()
    {
        $data['breadcrumbs'] = [
            ['name' => 'Inicio', 'url' => base_url(), 'active' => false],
            ['name' => 'Servicios', 'url' => '#', 'active' => true]
        ];
        return view('servicios_view', $data);
    }

    public function detalles()
    {
        $data['breadcrumbs'] = [
            ['name' => 'Inicio', 'url' => base_url(), 'active' => false],
            ['name' => 'Servicios', 'url' => base_url('servicios'), 'active' => false],
            ['name' => 'Detalles', 'url' => '#', 'active' => true]
        ];
        return view('detalles_view', $data);
    }

    public function prueba_error()
    {
        throw new \RuntimeException("Esta es una prueba de la pantalla de error genérico.");
    }
}