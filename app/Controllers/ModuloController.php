<?php

namespace App\Controllers;

use App\Models\ModuloModel;
use App\Traits\InputSanitizer;

class ModuloController extends BaseController
{
    use InputSanitizer;

    private ModuloModel $model;

    public function __construct()
    {
        $this->model = new ModuloModel();
    }

    public function index()
    {
        $data['pageTitle']   = 'Páginas Protegidas';
        $data['breadcrumbs'] = [
            ['name' => 'Admin',             'url' => base_url('admin/dashboard'), 'active' => false],
            ['name' => 'Seguridad',         'url' => '#',                         'active' => false],
            ['name' => 'Páginas Protegidas','url' => '#',                         'active' => true],
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
        $nombre = $this->sanitize($this->request->getPost('nombre'));
        $ruta   = $this->sanitize($this->request->getPost('ruta') ?? '');
        $icono  = $this->sanitize($this->request->getPost('icono') ?? 'bi-circle');
        $grupo  = $this->sanitize($this->request->getPost('grupo') ?? 'General');
        $orden  = (int)$this->request->getPost('orden');

        if ($this->hasDangerous($nombre)) {
            return $this->response->setJSON(['success' => false, 'errors' => ['nombre' => 'Contenido no permitido']]);
        }
        if ($ruta !== '' && $this->hasDangerous($ruta)) {
            return $this->response->setJSON(['success' => false, 'errors' => ['ruta' => 'Contenido no permitido']]);
        }

        $rules = [
            'nombre' => 'required|min_length[3]|max_length[100]',
            'ruta'   => 'required|min_length[3]|max_length[200]',
        ];
        if (!$this->validate($rules)) {
            return $this->response->setJSON(['success' => false, 'errors' => $this->validator->getErrors()]);
        }

        $ok = $this->model->crear([
            'nombre' => $nombre,
            'ruta'   => $ruta,
            'icono'  => $icono ?: 'bi-circle',
            'grupo'  => $grupo ?: 'General',
            'orden'  => $orden,
            'activo' => true,
        ]);

        return $this->response->setJSON([
            'success' => $ok,
            'mensaje' => $ok ? 'Módulo creado correctamente' : 'Error al crear el módulo',
        ]);
    }

    public function actualizar(int $id)
    {
        $nombre = $this->sanitize($this->request->getPost('nombre'));
        $ruta   = $this->sanitize($this->request->getPost('ruta') ?? '');
        $icono  = $this->sanitize($this->request->getPost('icono') ?? 'bi-circle');
        $grupo  = $this->sanitize($this->request->getPost('grupo') ?? 'General');
        $orden  = (int)$this->request->getPost('orden');

        if ($this->hasDangerous($nombre)) {
            return $this->response->setJSON(['success' => false, 'errors' => ['nombre' => 'Contenido no permitido']]);
        }
        if ($ruta !== '' && $this->hasDangerous($ruta)) {
            return $this->response->setJSON(['success' => false, 'errors' => ['ruta' => 'Contenido no permitido']]);
        }

        $rules = [
            'nombre' => 'required|min_length[3]|max_length[100]',
            'ruta'   => 'required|min_length[3]|max_length[200]',
        ];
        if (!$this->validate($rules)) {
            return $this->response->setJSON(['success' => false, 'errors' => $this->validator->getErrors()]);
        }

        $ok = $this->model->actualizar($id, [
            'nombre' => $nombre,
            'ruta'   => $ruta,
            'icono'  => $icono ?: 'bi-circle',
            'grupo'  => $grupo ?: 'General',
            'orden'  => $orden,
            'activo' => (bool)$this->request->getPost('activo'),
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
