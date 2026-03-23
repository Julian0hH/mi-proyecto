<?php

namespace App\Controllers;

use App\Models\PerfilModel;
use App\Traits\InputSanitizer;

class PerfilController extends BaseController
{
    use InputSanitizer;

    private PerfilModel $model;

    public function __construct()
    {
        $this->model = new PerfilModel();
    }

    public function index()
    {
        $data['pageTitle']   = 'Perfiles de Acceso';
        $data['breadcrumbs'] = [
            ['name' => 'Admin',             'url' => base_url('admin/dashboard'), 'active' => false],
            ['name' => 'Seguridad',         'url' => '#',                         'active' => false],
            ['name' => 'Perfiles de Acceso','url' => '#',                         'active' => true],
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
        $nombre      = $this->sanitize($this->request->getPost('nombre'));
        $descripcion = $this->sanitize($this->request->getPost('descripcion') ?? '');

        if ($this->hasDangerous($nombre)) {
            return $this->response->setJSON(['success' => false, 'errors' => ['nombre' => 'Contenido no permitido']]);
        }

        $rules = ['nombre' => 'required|min_length[2]|max_length[100]'];
        if (!$this->validate($rules)) {
            return $this->response->setJSON(['success' => false, 'errors' => $this->validator->getErrors()]);
        }

        $ok = $this->model->crear([
            'nombre'      => $nombre,
            'descripcion' => $descripcion !== '' ? $descripcion : null,
            'activo'      => true,
        ]);

        return $this->response->setJSON([
            'success' => $ok,
            'mensaje' => $ok ? 'Perfil creado correctamente' : 'Error al crear el perfil',
        ]);
    }

    public function actualizar(int $id)
    {
        $nombre      = $this->sanitize($this->request->getPost('nombre'));
        $descripcion = $this->sanitize($this->request->getPost('descripcion') ?? '');

        if ($this->hasDangerous($nombre)) {
            return $this->response->setJSON(['success' => false, 'errors' => ['nombre' => 'Contenido no permitido']]);
        }

        if (empty($nombre) || strlen($nombre) < 2 || strlen($nombre) > 100) {
            return $this->response->setJSON(['success' => false, 'errors' => ['nombre' => 'Nombre entre 2 y 100 caracteres']]);
        }

        $ok = $this->model->actualizar($id, [
            'nombre'      => $nombre,
            'descripcion' => $descripcion !== '' ? $descripcion : null,
            'activo'      => (bool)$this->request->getPost('activo'),
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
