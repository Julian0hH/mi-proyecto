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
                'status' => 'success',
                'data' => $proyectos
            ]);
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    public function crear()
    {
        try {
            $data = [
                'titulo' => $this->request->getPost('titulo'),
                'descripcion' => $this->request->getPost('descripcion'),
                'link' => $this->request->getPost('link'),
                'tecnologias' => $this->request->getPost('tecnologias')
            ];

            $imagenes = [];
            $files = $this->request->getFileMultiple('imagenes');

            if ($files) {
                foreach ($files as $file) {
                    if ($file->isValid() && !$file->hasMoved()) {
                        $rutaStorage = $this->proyectosModel->subirImagen($file);
                        $imagenes[] = $rutaStorage;
                    }
                }
            }

            $data['imagenes'] = json_encode($imagenes);

            $resultado = $this->proyectosModel->crearProyecto($data);

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Proyecto creado correctamente',
                'data' => $resultado
            ]);
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    public function actualizar($id)
    {
        try {
            $data = [
                'titulo' => $this->request->getPost('titulo'),
                'descripcion' => $this->request->getPost('descripcion'),
                'link' => $this->request->getPost('link'),
                'tecnologias' => $this->request->getPost('tecnologias')
            ];

            $this->proyectosModel->actualizarProyecto($id, $data);

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Proyecto actualizado correctamente'
            ]);
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    public function eliminar($id)
    {
        try {
            $this->proyectosModel->eliminarProyecto($id);

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Proyecto eliminado correctamente'
            ]);
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }
}