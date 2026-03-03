<?php

namespace App\Controllers;

use App\Models\ModuloSegModel;

class ModuloController extends BaseController
{
    private ModuloSegModel $model;

    public function __construct()
    {
        $this->model = new ModuloSegModel();
    }

    public function index()
    {
        $data['pageTitle']   = 'Módulos';
        $data['breadcrumbs'] = [
            ['name' => 'Admin',     'url' => base_url('admin/dashboard'), 'active' => false],
            ['name' => 'Seguridad', 'url' => '#',                         'active' => false],
            ['name' => 'Módulos',   'url' => '#',                         'active' => true],
        ];
        return view('admin/seguridad/modulos_view', $data);
    }

    public function listar()
    {
        $modulos = $this->model->obtenerTodos();
        return $this->response->setJSON(['success' => true, 'data' => $modulos]);
    }

    public function crear()
    {
        $rules = [
            'strNombreModulo' => 'required|min_length[3]|max_length[100]',
        ];
        if (!$this->validate($rules)) {
            return $this->response->setJSON(['success' => false, 'errors' => $this->validator->getErrors()]);
        }

        $ok = $this->model->crear([
            'strNombreModulo' => trim($this->request->getPost('strNombreModulo')),
        ]);

        return $this->response->setJSON([
            'success' => $ok,
            'mensaje' => $ok ? 'Módulo creado correctamente' : 'Error al crear el módulo',
        ]);
    }

    public function actualizar(int $id)
    {
        $rules = [
            'strNombreModulo' => 'required|min_length[3]|max_length[100]',
        ];
        if (!$this->validate($rules)) {
            return $this->response->setJSON(['success' => false, 'errors' => $this->validator->getErrors()]);
        }

        $ok = $this->model->actualizar($id, [
            'strNombreModulo' => trim($this->request->getPost('strNombreModulo')),
        ]);

        return $this->response->setJSON([
            'success' => $ok,
            'mensaje' => $ok ? 'Módulo actualizado correctamente' : 'Error al actualizar el módulo',
        ]);
    }

    public function eliminar(int $id)
    {
        $ok = $this->model->eliminar($id);
        return $this->response->setJSON([
            'success' => $ok,
            'mensaje' => $ok ? 'Módulo eliminado correctamente' : 'Error al eliminar el módulo',
        ]);
    }
}
