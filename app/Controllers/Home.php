<?php

namespace App\Controllers;

class Home extends BaseController
{
    private ?string $supabaseUrl = null;
    private ?string $supabaseKey = null;

    public function __construct()
    {
        $this->supabaseUrl = getenv('SUPABASE_URL');
        $this->supabaseKey = getenv('SUPABASE_SERVICE_ROLE_KEY');

        if (empty($this->supabaseUrl) || empty($this->supabaseKey)) {
            die('Error: Credenciales de Supabase no configuradas.');
        }
    }

    public function index()
    {
        $data['breadcrumbs'] = [
            ['name' => 'Inicio', 'url' => base_url(), 'active' => true]
        ];
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
                    'Content-Type'  => 'application/json',
                ]
            ]);

            $data['usuarios'] = json_decode($response->getBody(), true);
        } catch (\Exception $e) {
            $data['error'] = 'Error al cargar usuarios.';
        }

        $data['sitekey'] = getenv('RECAPTCHA_SITEKEY');
        $data['breadcrumbs'] = [
            ['name' => 'Inicio', 'url' => base_url(), 'active' => false],
            ['name' => 'Registro', 'url' => base_url('registro'), 'active' => true]
        ];

        return view('register_view', $data);
    }

    public function guardar()
    {
        $recaptchaResponse = $this->request->getPost('g-recaptcha-response');
        
        if (!$recaptchaResponse) {
            return redirect()->back()->with('error', 'Debes completar el captcha.');
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
                return redirect()->back()->with('error', 'Error de validación del captcha.');
            }

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
                ]
            ]);

            return redirect()->to('/registro')->with('success', 'Usuario guardado correctamente.');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Ocurrió un error técnico: ' . $e->getMessage());
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
            return redirect()->to('/registro')->with('error', 'Error al eliminar usuario.');
        }
    }

    public function servicios()
    {
        $data['breadcrumbs'] = [
            ['name' => 'Inicio', 'url' => base_url(), 'active' => false],
            ['name' => 'Servicios', 'url' => '#', 'active' => true]
        ];
        return view('servicios_view', $data);
    }

    public function validacion()
    {
        $data['breadcrumbs'] = [
            ['name' => 'Inicio', 'url' => base_url(), 'active' => false],
            ['name' => 'Validación', 'url' => '#', 'active' => true]
        ];
        return view('validacion_view', $data);
    }

    public function detalles()
    {
        return view('detalles_view');
    }

    public function procesar_validacion()
    {
        return redirect()->to('/validacion')->with('success', 'Datos procesados (Simulación).');
    }
}