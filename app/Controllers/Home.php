<?php

namespace App\Controllers;

class Home extends BaseController
{
    private ?string $supabaseUrl = null;
    private ?string $supabaseKey = null;
    private bool $configError = false;

    public function __construct()
    {
        helper(['form', 'url', 'date', 'text']);
        $this->supabaseUrl = getenv('SUPABASE_URL');
        $this->supabaseKey = getenv('SUPABASE_SERVICE_ROLE_KEY');

        if (empty($this->supabaseUrl) || empty($this->supabaseKey)) {
            $this->configError = true;
        }
    }

    public function index()
    {
        $data['breadcrumbs'] = [['name' => 'Inicio', 'url' => base_url(), 'active' => true]];
        return view('home_welcome', $data);
    }

    public function registro()
    {
        if ($this->configError) return view('errors/html/production');

        $client = \Config\Services::curlrequest();
        $data['usuarios'] = [];

        try {
            $response = $client->request('GET', $this->supabaseUrl . '/rest/v1/usuarios?select=*', [
                'headers' => [
                    'apikey' => $this->supabaseKey,
                    'Authorization' => 'Bearer ' . $this->supabaseKey,
                ],
                'http_errors' => false
            ]);
            if ($response->getStatusCode() === 200) {
                $data['usuarios'] = json_decode($response->getBody(), true);
            }
        } catch (\Throwable $e) { }

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
            'nombre' => 'required|min_length[3]|max_length[50]|regex_match[/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/]',
            'email'  => 'required|valid_email|max_length[100]',
            'g-recaptcha-response' => 'required'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $recaptchaResponse = $this->request->getPost('g-recaptcha-response');
        if (!$this->verificarCaptcha($recaptchaResponse)) {
            return redirect()->back()->withInput()->with('error', 'Captcha inválido.');
        }

        try {
            $client = \Config\Services::curlrequest();
            $client->request('POST', $this->supabaseUrl . '/rest/v1/usuarios', [
                'headers' => [
                    'apikey' => $this->supabaseKey,
                    'Authorization' => 'Bearer ' . $this->supabaseKey,
                    'Content-Type' => 'application/json',
                    'Prefer' => 'return=minimal',
                ],
                'json' => [
                    'nombre' => esc($this->request->getPost('nombre')),
                    'email'  => esc($this->request->getPost('email')),
                ]
            ]);
            return redirect()->to('/registro')->with('success', 'Usuario registrado correctamente.');
        } catch (\Throwable $e) {
            return redirect()->back()->withInput()->with('error', 'Error de conexión.');
        }
    }

    public function eliminar($id)
    {
        try {
            $client = \Config\Services::curlrequest();
            $client->request('DELETE', $this->supabaseUrl . '/rest/v1/usuarios?id=eq.' . $id, [
                'headers' => [
                    'apikey' => $this->supabaseKey,
                    'Authorization' => 'Bearer ' . $this->supabaseKey,
                ]
            ]);
            return redirect()->to('/registro')->with('success', 'Usuario eliminado.');
        } catch (\Throwable $e) {
            return redirect()->to('/registro')->with('error', 'No se pudo eliminar.');
        }
    }

    public function validacion()
    {
        $data['breadcrumbs'] = [
            ['name' => 'Inicio', 'url' => base_url(), 'active' => false],
            ['name' => 'Validación UI', 'url' => '#', 'active' => true]
        ];
        return view('validacion_view', $data);
    }

    public function procesar_validacion()
    {
        $rules = [
            'nombre'    => 'required|min_length[2]|regex_match[/^[a-zA-Z\s]+$/]',
            'apellido'  => 'required|min_length[2]|regex_match[/^[a-zA-Z\s]+$/]',
            'sexo'      => 'required|in_list[masculino,femenino,otro]',
            'email'     => 'required|valid_email',
            'pais'      => 'required',
            'telefono'  => 'required|numeric|min_length[7]|max_length[15]',
            'edad'      => 'required|integer|greater_than_equal_to[18]',
            'fecha_nac' => 'required|valid_date',
            'sitio_web' => 'required|valid_url_strict',
            'archivo'   => 'uploaded[archivo]|max_size[archivo,2048]|is_image[archivo]',
            'password'  => 'required|min_length[8]|regex_match[/^(?=.*[A-Z])(?=.*\d).+$/]',
            'pass_conf' => 'required|matches[password]',
            'terminos'  => 'required'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $fechaNac = $this->request->getPost('fecha_nac');
        $edadInput = $this->request->getPost('edad');

        if ($fechaNac && $edadInput) {
            try {
                $nacimiento = new \DateTime($fechaNac);
                $hoy = new \DateTime();
                $edadReal = $hoy->diff($nacimiento)->y;
                if ($edadReal != $edadInput) {
                    return redirect()->back()->withInput()->with('errors', ["Error lógico: La fecha indica {$edadReal} años."]);
                }
            } catch (\Exception $e) {
                return redirect()->back()->withInput()->with('error', 'Fecha inválida.');
            }
        }
        return redirect()->to('/validacion')->with('success', 'Validación correcta.');
    }

    public function servicios()
    {
        $data['breadcrumbs'] = [
            ['name' => 'Inicio', 'url' => base_url(), 'active' => false],
            ['name' => 'Catálogo', 'url' => '#', 'active' => false],
            ['name' => 'Servicios', 'url' => '#', 'active' => true]
        ];
        return view('servicios_view', $data);
    }

    public function detalles()
    {
        $data['breadcrumbs'] = [
            ['name' => 'Inicio', 'url' => base_url(), 'active' => false],
            ['name' => 'Catálogo', 'url' => '#', 'active' => false],
            ['name' => 'Servicios', 'url' => base_url('servicios'), 'active' => false],
            ['name' => 'Detalles', 'url' => '#', 'active' => true]
        ];
        return view('detalles_view', $data);
    }

    public function contratar()
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

    public function prueba_error() { throw new \RuntimeException("Error de prueba."); }

    private function verificarCaptcha($response)
    {
        $secret = getenv('RECAPTCHA_SECRETKEY');
        if(empty($secret)) return true;
        $client = \Config\Services::curlrequest();
        try {
            $verify = $client->request('POST', 'https://www.google.com/recaptcha/api/siteverify', [
                'form_params' => ['secret' => $secret, 'response' => $response, 'remoteip' => $this->request->getIPAddress()]
            ]);
            $body = json_decode($verify->getBody());
            return $body->success;
        } catch (\Exception $e) { return false; }
    }
}