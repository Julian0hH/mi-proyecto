<?php

namespace App\Controllers;

use App\Models\PerfilModel;

class PerfilController extends BaseController
{
    private PerfilModel $model;

    public function __construct()
    {
        $this->model = new PerfilModel();
    }

    public function index()
    {
        $data['pageTitle']   = 'Perfiles';
        $data['breadcrumbs'] = [
            ['name' => 'Admin',      'url' => base_url('admin/dashboard'), 'active' => false],
            ['name' => 'Seguridad',  'url' => '#',                         'active' => false],
            ['name' => 'Perfiles',   'url' => '#',                         'active' => true],
        ];
        return view('admin/seguridad/perfiles_view', $data);
    }

    public function listar()
    {
        $perfiles = $this->model->obtenerTodos();
        return $this->response->setJSON(['success' => true, 'data' => $perfiles]);
    }

    public function crear()
    {
        $rules = [
            'strNombrePerfil' => 'required|min_length[3]|max_length[100]',
        ];
        if (!$this->validate($rules)) {
            return $this->response->setJSON(['success' => false, 'errors' => $this->validator->getErrors()]);
        }

        $ok = $this->model->crear([
            'strNombrePerfil'  => trim($this->request->getPost('strNombrePerfil')),
            'bitAdministrador' => (bool)$this->request->getPost('bitAdministrador'),
        ]);

        return $this->response->setJSON([
            'success' => $ok,
            'mensaje' => $ok ? 'Perfil creado correctamente' : 'Error al crear el perfil',
        ]);
    }

    public function actualizar(int $id)
    {
        $rules = [
            'strNombrePerfil' => 'required|min_length[3]|max_length[100]',
        ];
        if (!$this->validate($rules)) {
            return $this->response->setJSON(['success' => false, 'errors' => $this->validator->getErrors()]);
        }

        $ok = $this->model->actualizar($id, [
            'strNombrePerfil'  => trim($this->request->getPost('strNombrePerfil')),
            'bitAdministrador' => (bool)$this->request->getPost('bitAdministrador'),
        ]);

        return $this->response->setJSON([
            'success' => $ok,
            'mensaje' => $ok ? 'Perfil actualizado correctamente' : 'Error al actualizar el perfil',
        ]);
    }

    public function eliminar(int $id)
    {
        $ok = $this->model->eliminar($id);
        return $this->response->setJSON([
            'success' => $ok,
            'mensaje' => $ok ? 'Perfil eliminado correctamente' : 'Error al eliminar el perfil',
        ]);
    }
}
