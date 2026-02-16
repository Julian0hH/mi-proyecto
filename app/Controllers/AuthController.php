<?php

namespace App\Controllers;

class AuthController extends BaseController
{
    public function login()
    {
        if (session()->get('admin_logueado')) {
            return redirect()->to(base_url('admin/proyectos'));
        }

        $data['breadcrumbs'] = [
            ['name' => 'Inicio', 'url' => base_url(), 'active' => false],
            ['name' => 'Login', 'url' => '#', 'active' => true]
        ];

        return view('login_view', $data);
    }

    public function procesarLogin()
    {
        $validation = \Config\Services::validation();

        $rules = [
            'email' => [
                'rules' => 'required|valid_email',
                'errors' => [
                    'required' => 'El email es obligatorio',
                    'valid_email' => 'Ingresa un email válido'
                ]
            ],
            'password' => [
                'rules' => 'required|min_length[6]',
                'errors' => [
                    'required' => 'La contraseña es obligatoria',
                    'min_length' => 'La contraseña debe tener al menos 6 caracteres'
                ]
            ],
            'g-recaptcha-response' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Debes completar la verificación de seguridad'
                ]
            ]
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                           ->withInput()
                           ->with('errors', $validation->getErrors());
        }

        $recaptchaResponse = $this->request->getPost('g-recaptcha-response');
        if (!$this->verificarCaptcha($recaptchaResponse)) {
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Verificación de seguridad fallida. Intenta nuevamente.');
        }

        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        $adminEmail = getenv('ADMIN_EMAIL');
        $adminPassword = getenv('ADMIN_PASSWORD');

        if ($email === $adminEmail && $password === $adminPassword) {
            session()->set([
                'admin_logueado' => true,
                'admin_email' => $email
            ]);

            return redirect()->to(base_url('admin/proyectos'))
                           ->with('success', '¡Bienvenido, Admin!');
        } else {
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Credenciales incorrectas');
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

    public function logout()
    {
        session()->destroy();
        return redirect()->to(base_url('login'))
                       ->with('success', 'Sesión cerrada correctamente');
    }
}