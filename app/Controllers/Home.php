<?php

namespace App\Controllers;

use App\Models\UsuarioModel;
use CodeIgniter\Database\Exceptions\DatabaseException;

class Home extends BaseController
{
    public function index()
    {
        return view('home_welcome');
    }

    public function registro()
    {
        try {
            $db = \Config\Database::connect();
            $db->initialize();
        } catch (DatabaseException $e) {
            return redirect()->to('/')->with('error', 'La base de datos no está disponible.');
        }

        $model = new UsuarioModel();
        $data['usuarios'] = $model->findAll();
        $data['sitekey'] = env('recaptcha.sitekey');

        return view('register_view', $data);
    }

    public function guardar()
    {
        $recaptchaResponse = $this->request->getPost('g-recaptcha-response');
        $secretKey = env('recaptcha.secretkey');

        $url = 'https://www.google.com/recaptcha/api/siteverify';
        $params = [
            'secret' => $secretKey,
            'response' => $recaptchaResponse
        ];

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

        $model = new UsuarioModel();
        $model->save([
            'nombre' => $this->request->getPost('nombre'),
            'email'  => $this->request->getPost('email'),
        ]);

        return redirect()->to('/registro')->with('success', 'Guardado correctamente.');
    }

    public function eliminar($id)
    {
        $model = new UsuarioModel();
        $model->delete($id);
        return redirect()->to('/registro')->with('success', 'Eliminado correctamente.');
    }
}