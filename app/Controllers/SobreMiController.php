<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\SobreMiModel;
use CodeIgniter\HTTP\ResponseInterface;

class SobreMiController extends BaseController
{
    private SobreMiModel $model;

    public function __construct()
    {
        helper(['form', 'url']);
        $this->model = new SobreMiModel();
    }

    public function index(): string
    {
        try {
            return view('admin/sobre_mi_view', [
                'sobre_mi'    => $this->model->obtener(),
                'breadcrumbs' => [
                    ['name' => 'Admin',    'url' => base_url('admin/dashboard'), 'active' => false],
                    ['name' => 'Sobre Mí', 'url' => '#',                         'active' => true],
                ],
            ]);
        } catch (\Throwable $e) {
            log_message('critical', 'SobreMiController::index ' . $e->getMessage());
            return view('errors/html/error_500');
        }
    }

    public function guardar(): ResponseInterface
    {
        try {
            $rules = [
                'titulo'                  => 'required|min_length[5]|max_length[200]',
                'subtitulo'               => 'permit_empty|max_length[300]',
                'descripcion'             => 'required|min_length[20]',
                'experiencia_anos'        => 'required|integer|greater_than_equal_to[0]',
                'proyectos_completados'   => 'required|integer|greater_than_equal_to[0]',
                'clientes_satisfechos'    => 'required|integer|greater_than_equal_to[0]',
                'email_contacto'          => 'permit_empty|valid_email',
            ];

            if (!$this->validate($rules)) {
                return $this->response->setJSON(['success' => false, 'errors' => $this->validator->getErrors()]);
            }

            $habilidades = [];
            $nombres = $this->request->getPost('habilidad_nombre') ?: [];
            $niveles = $this->request->getPost('habilidad_nivel') ?: [];
            foreach ($nombres as $i => $nombre) {
                $nombre = trim($nombre);
                if ($nombre !== '') {
                    $habilidades[] = ['nombre' => $nombre, 'nivel' => max(0, min(100, (int)($niveles[$i] ?? 50)))];
                }
            }

            $ok = $this->model->guardar([
                'titulo'                => $this->request->getPost('titulo'),
                'subtitulo'             => $this->request->getPost('subtitulo'),
                'descripcion'           => $this->request->getPost('descripcion'),
                'experiencia_anos'      => (int)$this->request->getPost('experiencia_anos'),
                'proyectos_completados' => (int)$this->request->getPost('proyectos_completados'),
                'clientes_satisfechos'  => (int)$this->request->getPost('clientes_satisfechos'),
                'linkedin_url'          => $this->request->getPost('linkedin_url') ?: null,
                'github_url'            => $this->request->getPost('github_url') ?: null,
                'email_contacto'        => $this->request->getPost('email_contacto') ?: null,
                'habilidades'           => json_encode($habilidades),
            ]);

            return $this->response->setJSON([
                'success' => $ok,
                'mensaje' => $ok ? 'Perfil actualizado correctamente' : 'Error al guardar los datos',
            ]);
        } catch (\Throwable $e) {
            log_message('critical', 'SobreMiController::guardar ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'errors' => ['Error interno del servidor']]);
        }
    }
}
