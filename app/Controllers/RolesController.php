<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\RolModel;
use App\Models\UsuarioModel;
use CodeIgniter\HTTP\ResponseInterface;

class RolesController extends BaseController
{
    private RolModel $model;
    private UsuarioModel $usuarioModel;

    public function __construct()
    {
        helper(['form', 'url']);
        $this->model        = new RolModel();
        $this->usuarioModel = new UsuarioModel();
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

    public function crearRol(): ResponseInterface
    {
        try {
            $nombre = trim($this->request->getPost('nombre') ?? '');
            $desc   = trim($this->request->getPost('descripcion') ?? '');

            if (empty($nombre) || strlen($nombre) < 2 || strlen($nombre) > 50) {
                return $this->response->setJSON(['success' => false, 'mensaje' => 'El nombre es requerido (2–50 caracteres)']);
            }
            if (!preg_match('/^[a-zA-Z0-9_\-]+$/', $nombre)) {
                return $this->response->setJSON(['success' => false, 'mensaje' => 'Solo letras, números, guiones y guiones bajos']);
            }
            if (strlen($desc) > 200) {
                return $this->response->setJSON(['success' => false, 'mensaje' => 'La descripción excede 200 caracteres']);
            }

            $modulos  = ['proyectos','carrusel','usuarios','roles','servicios','sobre_mi','contactos','notificaciones'];
            $permisos = array_fill_keys($modulos, false);
            $permPost = $this->request->getPost('permisos') ?? [];
            foreach ($modulos as $m) {
                $permisos[$m] = isset($permPost[$m]);
            }

            $ok = $this->model->crear([
                'nombre'      => strtolower($nombre),
                'descripcion' => strip_tags($desc),
                'permisos'    => json_encode($permisos),
            ]);

            return $this->response->setJSON(['success' => $ok, 'mensaje' => $ok ? 'Rol creado correctamente' : 'Error al crear el rol']);
        } catch (\Throwable $e) {
            log_message('error', 'RolesController::crearRol ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'mensaje' => 'Error interno del servidor']);
        }
    }

    public function eliminarRol(int $id): ResponseInterface
    {
        try {
            $ok = $this->model->eliminar($id);
            return $this->response->setJSON(['success' => $ok, 'mensaje' => $ok ? 'Rol eliminado' : 'Error al eliminar']);
        } catch (\Throwable $e) {
            return $this->response->setJSON(['success' => false, 'mensaje' => 'Error interno del servidor']);
        }
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

    public function crearUsuario(): ResponseInterface
    {
        try {
            $nombre   = trim($this->request->getPost('nombre') ?? '');
            $email    = trim($this->request->getPost('email') ?? '');
            $password = $this->request->getPost('password') ?? '';
            $rolId    = (int)($this->request->getPost('rol_id') ?? 0);
            $activo   = $this->request->getPost('activo') === '1';

            if (empty($nombre) || strlen($nombre) < 2 || strlen($nombre) > 100) {
                return $this->response->setJSON(['success' => false, 'mensaje' => 'Nombre requerido (2–100 caracteres)']);
            }
            if (!preg_match('/^[a-zA-ZáéíóúÁÉÍÓÚüÜñÑ\s\-]+$/', $nombre)) {
                return $this->response->setJSON(['success' => false, 'mensaje' => 'Nombre: solo letras, espacios y guiones']);
            }
            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 150) {
                return $this->response->setJSON(['success' => false, 'mensaje' => 'Email inválido o demasiado largo']);
            }
            if (empty($password) || strlen($password) < 6) {
                return $this->response->setJSON(['success' => false, 'mensaje' => 'La contraseña debe tener al menos 6 caracteres']);
            }
            if ($rolId < 1) {
                return $this->response->setJSON(['success' => false, 'mensaje' => 'Selecciona un rol válido']);
            }

            $ok = $this->usuarioModel->crear([
                'nombre'        => strip_tags($nombre),
                'email'         => strtolower($email),
                'password_hash' => password_hash($password, PASSWORD_DEFAULT),
                'rol_id'        => $rolId,
                'activo'        => $activo,
            ]);

            return $this->response->setJSON(['success' => $ok, 'mensaje' => $ok ? 'Usuario creado correctamente' : 'Error al crear el usuario']);
        } catch (\Throwable $e) {
            log_message('error', 'RolesController::crearUsuario ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'mensaje' => 'Error interno del servidor']);
        }
    }

    public function actualizarUsuario(string $id): ResponseInterface
    {
        try {
            $nombre   = trim($this->request->getPost('nombre') ?? '');
            $email    = trim($this->request->getPost('email') ?? '');
            $password = $this->request->getPost('password') ?? '';
            $rolId    = (int)($this->request->getPost('rol_id') ?? 0);
            $activo   = $this->request->getPost('activo') === '1';

            if (empty($nombre) || strlen($nombre) < 2 || strlen($nombre) > 100) {
                return $this->response->setJSON(['success' => false, 'mensaje' => 'Nombre requerido (2–100 caracteres)']);
            }
            if (!preg_match('/^[a-zA-ZáéíóúÁÉÍÓÚüÜñÑ\s\-]+$/', $nombre)) {
                return $this->response->setJSON(['success' => false, 'mensaje' => 'Nombre: solo letras, espacios y guiones']);
            }
            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 150) {
                return $this->response->setJSON(['success' => false, 'mensaje' => 'Email inválido']);
            }
            if (!empty($password) && strlen($password) < 6) {
                return $this->response->setJSON(['success' => false, 'mensaje' => 'La contraseña debe tener al menos 6 caracteres']);
            }
            if ($rolId < 1) {
                return $this->response->setJSON(['success' => false, 'mensaje' => 'Selecciona un rol válido']);
            }

            $data = [
                'nombre' => strip_tags($nombre),
                'email'  => strtolower($email),
                'rol_id' => $rolId,
                'activo' => $activo,
            ];
            if (!empty($password)) {
                $data['password_hash'] = password_hash($password, PASSWORD_DEFAULT);
            }

            $ok = $this->usuarioModel->actualizar($id, $data);
            return $this->response->setJSON(['success' => $ok, 'mensaje' => $ok ? 'Usuario actualizado' : 'Error al actualizar']);
        } catch (\Throwable $e) {
            log_message('error', 'RolesController::actualizarUsuario ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'mensaje' => 'Error interno del servidor']);
        }
    }

    public function eliminarUsuario(string $id): ResponseInterface
    {
        try {
            $ok = $this->usuarioModel->eliminar($id);
            return $this->response->setJSON(['success' => $ok, 'mensaje' => $ok ? 'Usuario eliminado' : 'Error al eliminar']);
        } catch (\Throwable $e) {
            return $this->response->setJSON(['success' => false, 'mensaje' => 'Error interno del servidor']);
        }
    }
}
