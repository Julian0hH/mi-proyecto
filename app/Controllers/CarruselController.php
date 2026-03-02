<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\CarruselModel;
use CodeIgniter\HTTP\ResponseInterface;

class CarruselController extends BaseController
{
    private CarruselModel $model;

    public function __construct()
    {
        helper(['form', 'url']);
        $this->model = new CarruselModel();
    }

    public function index(): string
    {
        return view('carrusel_view', [
            'breadcrumbs' => [
                ['name' => 'Admin',    'url' => base_url('admin/dashboard'), 'active' => false],
                ['name' => 'Carrusel', 'url' => '#',                         'active' => true],
            ],
        ]);
    }

    public function listar(): ResponseInterface
    {
        try {
            $imagenes = $this->model->obtenerImagenes();
            return $this->response->setJSON(['success' => true, 'data' => $imagenes]);
        } catch (\Throwable $e) {
            log_message('error', 'CarruselController::listar ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'data' => [], 'message' => $e->getMessage()]);
        }
    }

    public function subir(): ResponseInterface
    {
        try {
            $files = $this->request->getFileMultiple('imagenes');
            if (!$files || !isset($files[0]) || !$files[0]->isValid()) {
                return $this->response->setJSON(['success' => false, 'message' => 'No se recibió ninguna imagen válida']);
            }

            $titulo      = $this->request->getPost('titulo') ?: 'Sin título';
            $descripcion = $this->request->getPost('descripcion') ?: '';

            $resultado = $this->model->subirImagen($files[0], $titulo, $descripcion);

            return $this->response->setJSON(['success' => true, 'data' => $resultado]);
        } catch (\Throwable $e) {
            log_message('error', 'CarruselController::subir ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function actualizar(int $id): ResponseInterface
    {
        try {
            $titulo      = $this->request->getPost('titulo') ?: 'Sin título';
            $descripcion = $this->request->getPost('descripcion') ?: '';

            $this->model->actualizarImagen($id, $titulo, $descripcion);

            return $this->response->setJSON(['success' => true]);
        } catch (\Throwable $e) {
            log_message('error', 'CarruselController::actualizar ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function eliminar(int $id): ResponseInterface
    {
        try {
            $this->model->eliminarImagen($id);
            return $this->response->setJSON(['success' => true]);
        } catch (\Throwable $e) {
            log_message('error', 'CarruselController::eliminar ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}
