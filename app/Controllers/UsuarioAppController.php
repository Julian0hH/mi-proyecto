<?php

namespace App\Controllers;

use App\Models\UsuarioModel;
use App\Models\PerfilModel;
use App\Traits\InputSanitizer;

class UsuarioAppController extends BaseController
{
    use InputSanitizer;

    private UsuarioModel $model;
    private PerfilModel  $perfilModel;

    public function __construct()
    {
        $this->model       = new UsuarioModel();
        $this->perfilModel = new PerfilModel();
    }

    public function index()
    {
        $data['pageTitle']   = 'Usuarios del Sistema';
        $data['breadcrumbs'] = [
            ['name' => 'Admin',              'url' => base_url('admin/dashboard'), 'active' => false],
            ['name' => 'Seguridad',          'url' => '#',                         'active' => false],
            ['name' => 'Usuarios del Sistema','url' => '#',                        'active' => true],
        ];
        $data['perfiles'] = $this->perfilModel->obtenerActivos();
        return view('admin/seguridad/usuarios_app_view', $data);
    }

    public function listar()
    {
        $usuarios = $this->model->obtenerTodos();
        return $this->response->setJSON(['success' => true, 'data' => $usuarios]);
    }

    public function crear()
    {
        $nombre   = $this->sanitize($this->request->getPost('nombre'));
        $email    = $this->sanitize($this->request->getPost('email'));
        $telefono = $this->sanitize($this->request->getPost('telefono') ?? '');
        $pwd      = $this->request->getPost('password') ?? '';
        $perfilId = (int)$this->request->getPost('perfil_id');

        foreach (['nombre' => $nombre, 'email' => $email, 'telefono' => $telefono] as $field => $val) {
            if ($this->hasDangerous($val)) {
                return $this->response->setJSON(['success' => false, 'errors' => [$field => 'Contenido no permitido']]);
            }
        }

        $rules = [
            'nombre'   => 'required|min_length[3]|max_length[150]',
            'email'    => 'required|valid_email|max_length[200]',
            'password' => 'required|min_length[6]|max_length[100]',
            'perfil_id'=> 'required|integer',
            'telefono' => 'permit_empty|max_length[20]',
        ];
        if (!$this->validate($rules)) {
            return $this->response->setJSON(['success' => false, 'errors' => $this->validator->getErrors()]);
        }

        $ok = $this->model->crear([
            'nombre'        => $nombre,
            'email'         => $email,
            'password_hash' => password_hash($pwd, PASSWORD_BCRYPT),
            'perfil_id'     => $perfilId,
            'activo'        => (bool)$this->request->getPost('activo'),
            'telefono'      => $telefono !== '' ? $telefono : null,
        ]);

        return $this->response->setJSON([
            'success' => $ok,
            'mensaje' => $ok ? 'Usuario creado correctamente' : 'Error al crear el usuario',
        ]);
    }

    public function actualizar(int $id)
    {
        $nombre   = $this->sanitize($this->request->getPost('nombre'));
        $email    = $this->sanitize($this->request->getPost('email'));
        $telefono = $this->sanitize($this->request->getPost('telefono') ?? '');
        $perfilId = (int)$this->request->getPost('perfil_id');

        foreach (['nombre' => $nombre, 'email' => $email, 'telefono' => $telefono] as $field => $val) {
            if ($this->hasDangerous($val)) {
                return $this->response->setJSON(['success' => false, 'errors' => [$field => 'Contenido no permitido']]);
            }
        }

        $rules = [
            'nombre'   => 'required|min_length[3]|max_length[150]',
            'email'    => 'required|valid_email|max_length[200]',
            'perfil_id'=> 'required|integer',
            'telefono' => 'permit_empty|max_length[20]',
        ];
        if (!$this->validate($rules)) {
            return $this->response->setJSON(['success' => false, 'errors' => $this->validator->getErrors()]);
        }

        $data = [
            'nombre'    => $nombre,
            'email'     => $email,
            'perfil_id' => $perfilId,
            'activo'    => (bool)$this->request->getPost('activo'),
            'telefono'  => $telefono !== '' ? $telefono : null,
        ];

        $newPwd = $this->request->getPost('password');
        if (!empty($newPwd)) {
            if (strlen($newPwd) < 6) {
                return $this->response->setJSON(['success' => false, 'errors' => ['password' => 'Mínimo 6 caracteres']]);
            }
            $data['password_hash'] = password_hash($newPwd, PASSWORD_BCRYPT);
        }

        $ok = $this->model->actualizar($id, $data);

        return $this->response->setJSON([
            'success' => $ok,
            'mensaje' => $ok ? 'Usuario actualizado correctamente' : 'Error al actualizar el usuario',
        ]);
    }

    public function eliminar(int $id)
    {
        $ok = $this->model->eliminar($id);
        return $this->response->setJSON([
            'success' => $ok,
            'mensaje' => $ok ? 'Usuario eliminado correctamente' : 'Error al eliminar el usuario',
        ]);
    }
}
