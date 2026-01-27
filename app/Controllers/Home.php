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
            return redirect()->to('/registro')->with('success', 'OK');
        } catch (\Throwable $e) {
            return redirect()->back()->withInput()->with('error', 'Service unavailable.');
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
        $edadReal = (new \DateTime())->diff($nacimiento)->y;
        
        if ($edadReal !== $edadInput) {
            return redirect()->back()->withInput()->with('errors', ['Integrity error: age mismatch.']);
        }
    
        return redirect()->to('/validacion')->with('success', 'Validated');
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