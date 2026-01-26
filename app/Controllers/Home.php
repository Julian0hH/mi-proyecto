<?php

namespace App\Controllers;

class Home extends BaseController
{
    private string $supabaseUrl;
    private string $supabaseKey;

    public function __construct()
    {
        // CI4: usar SOLO env()
        $this->supabaseUrl = env('supabase.url');
        $this->supabaseKey = env('supabase.service_role_key');

        if (empty($this->supabaseUrl)) {
            die('Error: La URL de Supabase no está configurada en las variables de entorno.');
        }

        if (empty($this->supabaseKey)) {
            die('Error: La key de Supabase no está configurada en las variables de entorno.');
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
            die('Error técnico: ' . $e->getMessage());
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

        $verify = file_get_contents(
            'https://www.google.com/recaptcha/api/siteverify?' .
            http_build_query([
                'secret'   => $secretKey,
                'response' => $recaptchaResponse
            ])
        );

        $response = json_decode($verify);

        if (!$response->success) {
            return redirect()->back()->with('error', 'Validación reCAPTCHA fallida.');
        }

        $client = \Config\Services::curlrequest();

        try {
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
}
