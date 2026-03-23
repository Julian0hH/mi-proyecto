<?php

namespace App\Controllers;

use App\Models\PerfilModel;
use App\Models\ModuloModel;
use App\Models\PermisoModel;

class PermisosPerfilController extends BaseController
{
    private PermisoModel $model;
    private PerfilModel  $perfilModel;
    private ModuloModel  $moduloModel;

    public function __construct()
    {
        $this->model       = new PermisoModel();
        $this->perfilModel = new PerfilModel();
        $this->moduloModel = new ModuloModel();
    }

    public function index()
    {
        $data['pageTitle']   = 'Permisos por Perfil';
        $data['breadcrumbs'] = [
            ['name' => 'Admin',          'url' => base_url('admin/dashboard'), 'active' => false],
            ['name' => 'Seguridad',      'url' => '#',                         'active' => false],
            ['name' => 'Permisos',       'url' => '#',                         'active' => true],
        ];
        $data['perfiles'] = $this->perfilModel->obtenerTodos();
        $data['modulos']  = $this->moduloModel->obtenerActivos();
        return view('admin/seguridad/permisos_perfil_view', $data);
    }

    public function cargarPorPerfil(int $perfilId)
    {
        $filas = $this->model->obtenerPorPerfil($perfilId);

        $indexed = [];
        foreach ($filas as $f) {
            $indexed[$f['modulo_id']] = [
                'bit_consulta' => (bool)$f['bit_consulta'],
                'bit_agregar'  => (bool)$f['bit_agregar'],
                'bit_editar'   => (bool)$f['bit_editar'],
                'bit_eliminar' => (bool)$f['bit_eliminar'],
                'bit_detalle'  => (bool)$f['bit_detalle'],
            ];
        }

        return $this->response->setJSON(['success' => true, 'data' => $indexed]);
    }

    public function guardar()
    {
        $perfilId = (int)$this->request->getPost('perfil_id');
        if (!$perfilId) {
            return $this->response->setJSON(['success' => false, 'mensaje' => 'Selecciona un perfil']);
        }

        $modulos = $this->moduloModel->obtenerActivos();
        $rows    = [];

        foreach ($modulos as $mod) {
            $mid    = $mod['id'];
            $prefix = "mod_{$mid}_";

            $consulta = (bool)$this->request->getPost($prefix . 'consulta');
            $agregar  = (bool)$this->request->getPost($prefix . 'agregar');
            $editar   = (bool)$this->request->getPost($prefix . 'editar');
            $eliminar = (bool)$this->request->getPost($prefix . 'eliminar');
            $detalle  = (bool)$this->request->getPost($prefix . 'detalle');

            // Solo insertar si al menos un bit está marcado
            if ($consulta || $agregar || $editar || $eliminar || $detalle) {
                $rows[] = [
                    'perfil_id'    => $perfilId,
                    'modulo_id'    => $mid,
                    'bit_consulta' => $consulta,
                    'bit_agregar'  => $agregar,
                    'bit_editar'   => $editar,
                    'bit_eliminar' => $eliminar,
                    'bit_detalle'  => $detalle,
                ];
            }
        }

        $ok = $this->model->guardarPorPerfil($perfilId, $rows);

        return $this->response->setJSON([
            'success' => $ok,
            'mensaje' => $ok ? 'Permisos guardados correctamente' : 'Error al guardar los permisos',
        ]);
    }
}
