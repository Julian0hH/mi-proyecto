<?php

namespace App\Controllers;

class AuthController extends BaseController
{
    public function login()
{
    if (session()->get('admin_logueado')) {
        return redirect()->to(base_url('admin/proyectos'));
    }

    return view('login_view'); 
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
            ]
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                           ->withInput()
                           ->with('errors', $validation->getErrors());
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

    public function logout()
    {
        session()->destroy();
        return redirect()->to(base_url('login'))
                       ->with('success', 'Sesión cerrada correctamente');
    }
}