<?php

namespace App\Controllers;

class Home extends BaseController
{
    private $supabaseUrl;
    private $supabaseKey;

    public function __construct()
    {
        $this->supabaseUrl = env('supabase.url');
        $this->supabaseKey = env('supabase.service_role_key');
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
            $response = $client->request('GET', $this->supabaseUrl . '/rest/v1/usuarios?select=*', [
                'headers' => [
                    'apikey'        => $this->supabaseKey,
                    'Authorization' => 'Bearer ' . $this->supabaseKey,
                    'Content-Type'  => 'application/json'
                ]
            ]);
            $data['usuarios'] = json_decode($response->getBody(), true);
        } 
        catch (\Exception $e) {
            die("Error técnico: " . $e->getMessage());
        }        
        $data['sitekey'] = env('recaptcha.sitekey');
        $data['breadcrumbs'] = [
            ['name' => 'Inicio', 'url' => base_url(), 'active' => false],
            ['name' => 'Registro', 'url' => base_url('registro'), 'active' => true]
        ];
        return view('register_view', $data);
    }

    public function guardar()
    {
        $recaptchaResponse = $this->request->getPost('g-recaptcha-response');
        $secretKey = env('recaptcha.secretkey');
        $url = 'https://www.google.com/recaptcha/api/siteverify';
        $params = ['secret' => $secretKey, 'response' => $recaptchaResponse];
        $options = [
            'http' => [
                'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                'method'  => 'POST',
                'content' => http_build_query($params)
            ]
        ];
        $context  = stream_context_create($options);
        $result = file_get_contents($url, false, $context);
        $responseKeys = json_decode($result);
        if (!$responseKeys->success) {
            return redirect()->back()->with('error', 'Validación reCAPTCHA fallida.');
        }
        $client = \Config\Services::curlrequest();
        try {
            $client->request('POST', $this->supabaseUrl . '/rest/v1/usuarios', [
                'headers' => [
                    'apikey'        => $this->supabaseKey,
                    'Authorization' => 'Bearer ' . $this->supabaseKey,
                    'Content-Type'  => 'application/json',
                    'Prefer'        => 'return=minimal'
                ],
                'json' => [
                    'nombre' => $this->request->getPost('nombre'),
                    'email'  => $this->request->getPost('email'),
                ]
            ]);
            return redirect()->to('/registro')->with('success', 'Usuario guardado en Supabase.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al guardar datos.');
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
            return redirect()->to('/registro')->with('success', 'Eliminado correctamente.');
        } catch (\Exception $e) {
            return redirect()->to('/registro')->with('error', 'Error al eliminar.');
        }
    }

    public function servicios()
    {
        $data['breadcrumbs'] = [
            ['name' => 'Inicio', 'url' => base_url(), 'active' => false],
            ['name' => 'Servicios', 'url' => base_url('servicios'), 'active' => true]
        ];
        return view('servicios_view', $data);
    }

    public function detalles()
    {
        $data['breadcrumbs'] = [
            ['name' => 'Inicio', 'url' => base_url(), 'active' => false],
            ['name' => 'Servicios', 'url' => base_url('servicios'), 'active' => false],
            ['name' => 'Detalles', 'url' => base_url('detalles'), 'active' => true]
        ];
        return view('detalles_view', $data);
    }

    public function validacion()
    {
        $data['breadcrumbs'] = [
            ['name' => 'Inicio', 'url' => base_url(), 'active' => false],
            ['name' => 'Validación UI', 'url' => base_url('validacion'), 'active' => true]
        ];
        return view('validacion_view', $data);
    }

    public function procesar_validacion()
    {
        $rules = [
            'nombre'    => 'required|alpha_space|min_length[3]',
            'email'     => 'required|valid_email',
            'telefono'  => 'required|numeric|exact_length[10]',
            'edad'      => 'required|is_natural_no_zero|greater_than_equal_to[18]|less_than_equal_to[99]',
            'fecha_nac' => 'required|valid_date[Y-m-d]',
            'sitio_web' => 'required|valid_url',
            'password'  => 'required|min_length[8]|regex_match[/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/]',
            'pass_conf' => 'required|matches[password]'
        ];
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        return redirect()->to('/validacion')->with('success', 'Datos validados correctamente.');
    }
}