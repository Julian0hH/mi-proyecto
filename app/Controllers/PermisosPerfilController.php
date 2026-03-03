<?php

namespace App\Controllers;

use App\Models\PerfilModel;
use App\Models\ModuloSegModel;
use App\Models\PermisosPerfilModel;

class PermisosPerfilController extends BaseController
{
    private PermisosPerfilModel $model;
    private PerfilModel         $perfilModel;
    private ModuloSegModel      $moduloModel;

    public function __construct()
    {
        $this->model       = new PermisosPerfilModel();
        $this->perfilModel = new PerfilModel();
        $this->moduloModel = new ModuloSegModel();
    }

    public function index()
    {
        $data['pageTitle']   = 'Permisos por Perfil';
        $data['breadcrumbs'] = [
            ['name' => 'Admin',            'url' => base_url('admin/dashboard'), 'active' => false],
            ['name' => 'Seguridad',        'url' => '#',                         'active' => false],
            ['name' => 'Permisos-Perfil',  'url' => '#',                         'active' => true],
        ];
        $data['perfiles'] = $this->perfilModel->obtenerTodos();
        $data['modulos']  = $this->moduloModel->obtenerTodos();
        return view('admin/seguridad/permisos_perfil_view', $data);
    }

    /** Retorna los permisos del perfil seleccionado (para carga AJAX) */
    public function cargarPorPerfil(int $idPerfil)
    {
        $permisos = $this->model->obtenerPorPerfil($idPerfil);
        return $this->response->setJSON(['success' => true, 'data' => $permisos]);
    }

    /** Guarda (reemplaza) todos los permisos de un perfil */
    public function guardar()
    {
        $idPerfil = (int)$this->request->getPost('idPerfil');
        if (!$idPerfil) {
            return $this->response->setJSON(['success' => false, 'mensaje' => 'Selecciona un perfil']);
        }

        $modulos  = $this->moduloModel->obtenerTodos();
        $rows     = [];

        foreach ($modulos as $mod) {
            $mid    = $mod['id'];
            $prefix = "mod_{$mid}_";

            $bitAgregar  = (bool)$this->request->getPost($prefix . 'agregar');
            $bitEditar   = (bool)$this->request->getPost($prefix . 'editar');
            $bitConsulta = (bool)$this->request->getPost($prefix . 'consulta');
            $bitEliminar = (bool)$this->request->getPost($prefix . 'eliminar');
            $bitDetalle  = (bool)$this->request->getPost($prefix . 'detalle');

            // Solo insertar si al menos un permiso está marcado
            if ($bitAgregar || $bitEditar || $bitConsulta || $bitEliminar || $bitDetalle) {
                $rows[] = [
                    'idModulo'    => $mid,
                    'idPerfil'    => $idPerfil,
                    'bitAgregar'  => $bitAgregar,
                    'bitEditar'   => $bitEditar,
                    'bitConsulta' => $bitConsulta,
                    'bitEliminar' => $bitEliminar,
                    'bitDetalle'  => $bitDetalle,
                ];
            }
        }

        $ok = $this->model->guardarPorPerfil($idPerfil, $rows);

        return $this->response->setJSON([
            'success' => $ok,
            'mensaje' => $ok ? 'Permisos guardados correctamente' : 'Error al guardar los permisos',
        ]);
    }
}
