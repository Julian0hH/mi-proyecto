<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\ServicioModel;
use CodeIgniter\HTTP\ResponseInterface;

class ServiciosAdminController extends BaseController
{
    private ServicioModel $model;

    public function __construct()
    {
        helper(['form', 'url']);
        $this->model = new ServicioModel();
    }

    public function index(): string
    {
        try {
            return view('admin/servicios_admin_view', [
                'servicios'   => $this->model->obtenerTodos(),
                'breadcrumbs' => [
                    ['name' => 'Admin',    'url' => base_url('admin/dashboard'), 'active' => false],
                    ['name' => 'Servicios','url' => '#',                          'active' => true],
                ],
            ]);
        } catch (\Throwable $e) {
            log_message('critical', 'ServiciosAdminController::index ' . $e->getMessage());
            return view('errors/html/error_500');
        }
    }

    public function listar(): ResponseInterface
    {
        return $this->response->setJSON(['success' => true, 'data' => $this->model->obtenerTodos()]);
    }

    public function crear(): ResponseInterface
    {
        try {
            $rules = [
                'titulo'      => 'required|min_length[3]|max_length[200]',
                'descripcion' => 'required|min_length[10]',
                'icono'       => 'permit_empty|max_length[100]',
                'color'       => 'permit_empty|in_list[primary,secondary,success,danger,warning,info,dark]',
                'orden'       => 'permit_empty|integer',
            ];
            if (!$this->validate($rules)) {
                return $this->response->setJSON(['success' => false, 'errors' => $this->validator->getErrors()]);
            }

            $ok = $this->model->crear([
                'titulo'            => $this->request->getPost('titulo'),
                'descripcion'       => $this->request->getPost('descripcion'),
                'descripcion_larga' => $this->request->getPost('descripcion_larga') ?: null,
                'icono'             => $this->request->getPost('icono') ?: 'bi-gear',
                'color'             => $this->request->getPost('color') ?: 'primary',
                'orden'             => (int)($this->request->getPost('orden') ?: 0),
                'activo'            => true,
            ]);

            return $this->response->setJSON([
                'success' => $ok,
                'mensaje' => $ok ? 'Servicio creado correctamente' : 'Error al crear servicio (verifica la conexión con Supabase)',
            ]);
        } catch (\Throwable $e) {
            log_message('error', 'ServiciosAdmin::crear ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'errors' => ['Error interno del servidor']]);
        }
    }

    public function actualizar(int $id): ResponseInterface
    {
        try {
            $ok = $this->model->actualizar($id, [
                'titulo'            => $this->request->getPost('titulo'),
                'descripcion'       => $this->request->getPost('descripcion'),
                'descripcion_larga' => $this->request->getPost('descripcion_larga') ?: null,
                'icono'             => $this->request->getPost('icono') ?: 'bi-gear',
                'color'             => $this->request->getPost('color') ?: 'primary',
                'orden'             => (int)($this->request->getPost('orden') ?: 0),
                'activo'            => (bool)$this->request->getPost('activo'),
            ]);

            return $this->response->setJSON([
                'success' => $ok,
                'mensaje' => $ok ? 'Servicio actualizado' : 'Error al actualizar servicio (verifica la conexión con Supabase)',
            ]);
        } catch (\Throwable $e) {
            log_message('error', 'ServiciosAdmin::actualizar ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'errors'  => ['Error interno del servidor']
            ]);
        }
    }

    public function eliminar(int $id): ResponseInterface
    {
        $ok = $this->model->eliminar($id);
        return $this->response->setJSON([
            'success' => $ok,
            'mensaje' => $ok ? 'Servicio eliminado' : 'Error al eliminar (verifica la conexión con Supabase)',
        ]);
    }
}