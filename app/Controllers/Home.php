<?php
namespace App\Controllers;
use App\Models\MensajeModel;

class Home extends BaseController {

    public function index() {
        $model = new MensajeModel();
        $data['mensajes'] = $model->findAll(); 
        return view('home_view', $data);
    }

    public function guardar() {
        $model = new MensajeModel();

        $datos = [
            'nombre'  => $this->request->getPost('nombre'),
            'mensaje' => $this->request->getPost('mensaje')
        ];

        $model->insert($datos);
        return redirect()->to('/'); 
    }
}