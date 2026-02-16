<?php

namespace App\Controllers;

use App\Models\CarruselModel;

class CarruselController extends BaseController
{
    protected $carruselModel;

    public function __construct()
    {
        $this->carruselModel = new CarruselModel();
    }

    public function index()
    {
        $data['breadcrumbs'] = [
            ['name' => 'Inicio', 'url' => base_url(), 'active' => false],
            ['name' => 'Carrusel', 'url' => '#', 'active' => true]
        ];
        return view('carrusel_view', $data);
    }

    public function listar()
    {
        try {
            $imagenes = $this->carruselModel->obtenerImagenes();
            return $this->response->setJSON([
                'status' => 'success',
                'data' => $imagenes
            ]);
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    public function subir()
    {
        try {
            $file = $this->request->getFile('imagen');
            $titulo = $this->request->getPost('titulo');
            $descripcion = $this->request->getPost('descripcion');

            if (!$file->isValid()) {
                return $this->response->setStatusCode(400)->setJSON([
                    'status' => 'error',
                    'message' => 'Archivo no válido'
                ]);
            }

            $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
            if (!in_array($file->getMimeType(), $allowedTypes)) {
                return $this->response->setStatusCode(400)->setJSON([
                    'status' => 'error',
                    'message' => 'Solo se permiten imágenes JPG, PNG o WEBP'
                ]);
            }

            $resultado = $this->carruselModel->subirImagen($file, $titulo, $descripcion);

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Imagen subida correctamente',
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
            $titulo = $this->request->getPost('titulo');
            $descripcion = $this->request->getPost('descripcion');

            $this->carruselModel->actualizarImagen($id, $titulo, $descripcion);

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Imagen actualizada correctamente'
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
            $this->carruselModel->eliminarImagen($id);

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Imagen eliminada correctamente'
            ]);
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }
}