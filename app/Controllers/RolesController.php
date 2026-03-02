<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\RolModel;
use CodeIgniter\HTTP\ResponseInterface;

class RolesController extends BaseController
{
    private RolModel $model;

    public function __construct()
    {
        helper(['form', 'url']);
        $this->model = new RolModel();
    }

    public function index(): string
    {
        try {
            return view('admin/roles_view', [
                'roles'       => $this->model->obtenerTodos(),
                'usuarios'    => $this->model->obtenerUsuariosTodos(),
                'breadcrumbs' => [
                    ['name' => 'Admin',          'url' => base_url('admin/dashboard'), 'active' => false],
                    ['name' => 'Roles y Permisos','url' => '#',                         'active' => true],
                ],
            ]);
        } catch (\Throwable $e) {
            log_message('critical', 'RolesController::index ' . $e->getMessage());
            return view('errors/html/error_500');
        }
    }

    public function listarRoles(): ResponseInterface
    {
        return $this->response->setJSON(['success' => true, 'data' => $this->model->obtenerTodos()]);
    }

    public function actualizarRol(int $id): ResponseInterface
    {
        try {
            $permisosPost = $this->request->getPost('permisos') ?? [];
    
            $modulos = ['proyectos','carrusel','usuarios','roles','servicios','sobre_mi','contactos','notificaciones'];
            $permisos = [];
    
            foreach ($modulos as $m) {
                $permisos[$m] = isset($permisosPost[$m]);
            }
    
            $this->model->actualizar($id, [
                'descripcion' => $this->request->getPost('descripcion'),
                'permisos'    => json_encode($permisos),
            ]);
    
            return $this->response->setJSON([
                'success' => true,
                'mensaje' => 'Rol actualizado'
            ]);
    
        } catch (\Throwable $e) {
            return $this->response->setJSON([
                'success' => false,
                'errors' => [$e->getMessage()]
            ]);
        }
    }

    public function asignarRolUsuario(): ResponseInterface
    {
        try {
            $userId = $this->request->getPost('usuario_id');
            $rolId  = (int)$this->request->getPost('rol_id');
            if (empty($userId) || $rolId < 1) {
                return $this->response->setJSON(['success' => false, 'mensaje' => 'Datos inválidos']);
            }
            $ok = $this->model->asignarRol($userId, $rolId);
            return $this->response->setJSON(['success' => $ok, 'mensaje' => $ok ? 'Rol asignado correctamente' : 'Error al asignar rol']);
        } catch (\Throwable $e) {
            return $this->response->setJSON(['success' => false, 'errors' => [$e->getMessage()]]);
        }
    }

    public function toggleUsuario(): ResponseInterface
    {
        try {
            $userId = $this->request->getPost('usuario_id');
            $activo = $this->request->getPost('activo') === '1';
            $ok     = $this->model->toggleActivo($userId, $activo);
            return $this->response->setJSON(['success' => $ok, 'mensaje' => $ok ? 'Estado actualizado' : 'Error']);
        } catch (\Throwable $e) {
            return $this->response->setJSON(['success' => false]);
        }
    }

    public function listarUsuarios(): ResponseInterface
    {
        return $this->response->setJSON(['success' => true, 'data' => $this->model->obtenerUsuariosTodos()]);
    }
}
