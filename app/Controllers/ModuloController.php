<?php

namespace App\Controllers;

use App\Models\ModuloModel;
use App\Models\PermisoModel;
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

        $nuevo = $this->model->crearYObtener([
            'nombre' => $nombre,
            'ruta'   => $ruta,
            'icono'  => $icono ?: 'bi-circle',
            'grupo'  => $grupo ?: 'General',
            'orden'  => $orden,
            'activo' => true,
        ]);

        if (!empty($nuevo)) {
            $this->asignarPermisoAdmin($nuevo['id']);
            $this->refrescarSesion();
        }

        return $this->response->setJSON([
            'success' => !empty($nuevo),
            'mensaje' => !empty($nuevo) ? 'Módulo creado correctamente' : 'Error al crear el módulo',
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

    // ── Helpers privados ─────────────────────────────────────────

    /** Asigna permisos completos al perfil Administrador para el módulo recién creado */
    private function asignarPermisoAdmin(int $moduloId): void
    {
        // Buscar el perfil Administrador
        $ch = curl_init(
            getenv('SUPABASE_URL') . '/rest/v1/perfiles?nombre=eq.Administrador&select=id&limit=1'
        );
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'apikey: '        . (getenv('SUPABASE_SERVICE_ROLE_KEY') ?: getenv('SUPABASE_SERVICE_KEY')),
                'Authorization: Bearer ' . (getenv('SUPABASE_SERVICE_ROLE_KEY') ?: getenv('SUPABASE_SERVICE_KEY')),
                'Content-Type: application/json',
            ],
        ]);
        $body = json_decode(curl_exec($ch), true);
        curl_close($ch);

        if (empty($body[0]['id'])) {
            return;
        }

        $permisoModel = new PermisoModel();
        $permisoModel->crear([
            'perfil_id'    => (int)$body[0]['id'],
            'modulo_id'    => $moduloId,
            'bit_consulta' => true,
            'bit_agregar'  => true,
            'bit_editar'   => true,
            'bit_eliminar' => true,
            'bit_detalle'  => true,
        ]);
    }

    /** Reconstruye permisos y sidebar en la sesión del usuario logueado */
    private function refrescarSesion(): void
    {
        $perfilId = session('perfil_id');
        if (!$perfilId) {
            return;
        }

        $permisoModel = new PermisoModel();
        $filas  = $permisoModel->obtenerPorPerfil((int)$perfilId);
        $sesion = $permisoModel->construirSesion($filas);

        session()->set([
            'permisos'        => $sesion['permisos'],
            'sidebar_modulos' => $sesion['sidebar_modulos'],
        ]);
    }
}
