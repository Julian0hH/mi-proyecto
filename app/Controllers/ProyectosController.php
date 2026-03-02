<?php

namespace App\Controllers;

use App\Models\ProyectosModel;

class ProyectosController extends BaseController
{
    protected $proyectosModel;

    public function __construct()
    {
        $this->proyectosModel = new ProyectosModel();
    }

    public function index()
    {
        $data['breadcrumbs'] = [
            ['name' => 'Inicio', 'url' => base_url(), 'active' => false],
            ['name' => 'Proyectos', 'url' => '#', 'active' => true]
        ];
        return view('proyectos_public_view', $data);
    }

    public function admin()
    {
        if (!session()->get('admin_logueado')) {
            return redirect()->to(base_url('login'))->with('error', 'Debes iniciar sesión');
        }

        $data['breadcrumbs'] = [
            ['name' => 'Inicio', 'url' => base_url(), 'active' => false],
            ['name' => 'Mis Proyectos', 'url' => '#', 'active' => true]
        ];
        return view('proyectos_admin_view', $data);
    }

    public function listar()
    {
        try {
            $proyectos = $this->proyectosModel->obtenerProyectos();
            return $this->response->setJSON([
                // BUG CORREGIDO #1: era 'status' => 'success', el frontend espera 'success' => true
                'success' => true,
                'data'    => $proyectos
            ]);
        } catch (\Exception $e) {
            log_message('error', 'ProyectosController::listar ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function crear()
    {
        try {
            $tecStr = $this->request->getPost('tecnologias') ?: '';
            $data = [
                'titulo'      => $this->request->getPost('titulo'),
                'descripcion' => $this->request->getPost('descripcion'),
                'link'        => $this->request->getPost('link') ?: null,
                'tecnologias' => array_values(array_filter(array_map('trim', explode(',', $tecStr)))),
            ];

            // BUG CORREGIDO #2: NO hacer json_encode aquí.
            // El model hace json_encode($data) internamente → si ya viene como string
            // llega doble-codificado a Supabase y el campo JSONB falla con HTTP 400.
            $imagenes = [];
            $files    = $this->request->getFileMultiple('imagenes');

            if ($files) {
                foreach ($files as $file) {
                    if ($file->isValid() && !$file->hasMoved()) {
                        $imagenes[] = $this->proyectosModel->subirImagen($file);
                    }
                }
            }

            // Pasar array PHP, no string JSON
            $data['imagenes'] = $imagenes;

            $resultado = $this->proyectosModel->crearProyecto($data);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Proyecto creado correctamente',
                'data'    => $resultado
            ]);
        } catch (\Exception $e) {
            log_message('error', 'ProyectosController::crear ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function actualizar($id)
    {
        try {
            $tecStr = $this->request->getPost('tecnologias') ?: '';
            $data = [
                'titulo'      => $this->request->getPost('titulo'),
                'descripcion' => $this->request->getPost('descripcion'),
                'link'        => $this->request->getPost('link') ?: null,
                'tecnologias' => array_values(array_filter(array_map('trim', explode(',', $tecStr)))),
            ];

            $imagenes = [];
            $files    = $this->request->getFileMultiple('imagenes');

            if ($files) {
                foreach ($files as $file) {
                    if ($file->isValid() && !$file->hasMoved()) {
                        $imagenes[] = $this->proyectosModel->subirImagen($file);
                    }
                }
            }

            // BUG CORREGIDO #2 (mismo): pasar array PHP sin json_encode previo
            if (!empty($imagenes)) {
                $data['imagenes'] = $imagenes;
            }

            $this->proyectosModel->actualizarProyecto($id, $data);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Proyecto actualizado correctamente'
            ]);
        } catch (\Exception $e) {
            log_message('error', 'ProyectosController::actualizar ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function eliminar($id)
    {
        try {
            $this->proyectosModel->eliminarProyecto($id);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Proyecto eliminado correctamente'
            ]);
        } catch (\Exception $e) {
            log_message('error', 'ProyectosController::eliminar ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
}