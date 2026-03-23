<?php

namespace App\Controllers;

use App\Models\UsuarioAppModel;
use App\Models\PerfilModel;
use App\Traits\InputSanitizer;

class UsuarioAppController extends BaseController
{
    use InputSanitizer;

    private UsuarioAppModel $model;
    private PerfilModel     $perfilModel;

    public function __construct()
    {
        $this->model       = new UsuarioAppModel();
        $this->perfilModel = new PerfilModel();
    }

    public function index()
    {
        $data['pageTitle']   = 'Usuarios';
        $data['breadcrumbs'] = [
            ['name' => 'Admin',    'url' => base_url('admin/dashboard'), 'active' => false],
            ['name' => 'Seguridad','url' => '#',                         'active' => false],
            ['name' => 'Usuarios', 'url' => '#',                         'active' => true],
        ];
        $data['perfiles'] = $this->perfilModel->obtenerTodos();
        return view('admin/seguridad/usuarios_app_view', $data);
    }

    public function listar()
    {
        $usuarios = $this->model->obtenerTodos();
        return $this->response->setJSON(['success' => true, 'data' => $usuarios]);
    }

    public function crear()
    {
        $usuario  = $this->sanitize($this->request->getPost('strNombreUsuario'));
        $correo   = $this->sanitize($this->request->getPost('strCorreo'));
        $celular  = $this->sanitize($this->request->getPost('strNumeroCelular'));
        $pwd      = $this->request->getPost('strPwd') ?? '';
        $idPerfil = (int)$this->request->getPost('idPerfil');

        foreach (['strNombreUsuario' => $usuario, 'strCorreo' => $correo, 'strNumeroCelular' => $celular] as $field => $val) {
            if ($this->hasDangerous($val)) {
                return $this->response->setJSON(['success' => false, 'errors' => [$field => 'Contenido no permitido']]);
            }
        }

        $rules = [
            'strNombreUsuario' => 'required|min_length[3]|max_length[100]',
            'strCorreo'        => 'permit_empty|valid_email|max_length[150]',
            'strPwd'           => 'required|min_length[6]|max_length[100]',
            'idPerfil'         => 'required|integer',
            'strNumeroCelular' => 'permit_empty|max_length[20]',
        ];
        if (!$this->validate($rules)) {
            return $this->response->setJSON(['success' => false, 'errors' => $this->validator->getErrors()]);
        }

        $imagen = '';
        $imgFile = $this->request->getFile('imagen');
        if ($imgFile && $imgFile->isValid() && !$imgFile->hasMoved()) {
            $url = $this->subirImagen($imgFile);
            if ($url) $imagen = $url;
        }

        $ok = $this->model->crear([
            'strNombreUsuario' => $usuario,
            'strCorreo'        => $correo,
            'strPwd'           => password_hash($pwd, PASSWORD_BCRYPT),
            'idPerfil'         => $idPerfil,
            'idEstadoUsuario'  => (bool)$this->request->getPost('idEstadoUsuario'),
            'strNumeroCelular' => $celular,
            'imagen'           => $imagen,
        ]);

        return $this->response->setJSON([
            'success' => $ok,
            'mensaje' => $ok ? 'Usuario creado correctamente' : 'Error al crear el usuario',
        ]);
    }

    public function actualizar(int $id)
    {
        $usuario  = $this->sanitize($this->request->getPost('strNombreUsuario'));
        $correo   = $this->sanitize($this->request->getPost('strCorreo'));
        $celular  = $this->sanitize($this->request->getPost('strNumeroCelular'));
        $idPerfil = (int)$this->request->getPost('idPerfil');

        foreach (['strNombreUsuario' => $usuario, 'strCorreo' => $correo, 'strNumeroCelular' => $celular] as $field => $val) {
            if ($this->hasDangerous($val)) {
                return $this->response->setJSON(['success' => false, 'errors' => [$field => 'Contenido no permitido']]);
            }
        }

        $rules = [
            'strNombreUsuario' => 'required|min_length[3]|max_length[100]',
            'strCorreo'        => 'permit_empty|valid_email|max_length[150]',
            'idPerfil'         => 'required|integer',
            'strNumeroCelular' => 'permit_empty|max_length[20]',
        ];
        if (!$this->validate($rules)) {
            return $this->response->setJSON(['success' => false, 'errors' => $this->validator->getErrors()]);
        }

        $data = [
            'strNombreUsuario' => $usuario,
            'strCorreo'        => $correo,
            'idPerfil'         => $idPerfil,
            'idEstadoUsuario'  => (bool)$this->request->getPost('idEstadoUsuario'),
            'strNumeroCelular' => $celular,
        ];

        $newPwd = $this->request->getPost('strPwd');
        if (!empty($newPwd)) {
            if (strlen($newPwd) < 6) {
                return $this->response->setJSON(['success' => false, 'errors' => ['strPwd' => 'Mínimo 6 caracteres']]);
            }
            if (strlen($newPwd) > 100) {
                return $this->response->setJSON(['success' => false, 'errors' => ['strPwd' => 'Máximo 100 caracteres']]);
            }
            $data['strPwd'] = password_hash($newPwd, PASSWORD_BCRYPT);
        }

        $imgFile = $this->request->getFile('imagen');
        if ($imgFile && $imgFile->isValid() && !$imgFile->hasMoved()) {
            $url = $this->subirImagen($imgFile);
            if ($url) {
                $data['imagen'] = $url;
            }
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

    private function subirImagen($file): string|false
    {
        $ext      = $file->getClientExtension();
        $nombre   = 'usr_' . time() . '_' . random_int(100, 999) . '.' . $ext;
        $contenido = file_get_contents($file->getTempName());
        $mime     = $file->getClientMimeType();

        return $this->model->subirImagen($contenido, $nombre, $mime);
    }
}
