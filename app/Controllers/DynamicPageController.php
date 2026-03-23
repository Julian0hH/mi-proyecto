<?php

namespace App\Controllers;

use App\Models\ModuloModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class DynamicPageController extends BaseController
{
    public function show(string $slug)
    {
        $ruta   = 'admin/' . ltrim($slug, '/');
        $model  = new ModuloModel();
        $modulo = $model->obtenerPorRuta($ruta);

        if (empty($modulo) || !($modulo['activo'] ?? false)) {
            throw new PageNotFoundException("La página '$ruta' no existe o está inactiva.");
        }

        $data['pageTitle']   = $modulo['nombre'];
        $data['breadcrumbs'] = [
            ['name' => 'Admin',               'url' => base_url('admin/dashboard'), 'active' => false],
            ['name' => $modulo['grupo'] ?? '', 'url' => '#',                         'active' => false],
            ['name' => $modulo['nombre'],      'url' => '#',                         'active' => true],
        ];
        $data['modulo'] = $modulo;
        $data['ruta']   = $ruta;

        return view('admin/pagina_dinamica_view', $data);
    }
}
