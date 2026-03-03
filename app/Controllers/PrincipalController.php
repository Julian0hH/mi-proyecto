<?php

namespace App\Controllers;

class PrincipalController extends BaseController
{
    public function p1_1()
    {
        $data['pageTitle']   = 'Pipeline de Ventas';
        $data['breadcrumbs'] = [
            ['name' => 'Admin',    'url' => base_url('admin/dashboard'), 'active' => false],
            ['name' => 'Ventas',   'url' => '#',                         'active' => false],
            ['name' => 'Pipeline', 'url' => '#',                         'active' => true],
        ];
        $data['titulo']    = 'Pipeline de Ventas';
        $data['subtitulo'] = 'Gestión y seguimiento del proceso comercial';
        $data['icono']     = 'bi-funnel-fill';
        $data['color']     = 'primary';
        return view('admin/principal/p1_1_view', $data);
    }

    public function p1_2()
    {
        $data['pageTitle']   = 'Clientes y Leads';
        $data['breadcrumbs'] = [
            ['name' => 'Admin',           'url' => base_url('admin/dashboard'), 'active' => false],
            ['name' => 'Ventas',          'url' => '#',                         'active' => false],
            ['name' => 'Clientes y Leads','url' => '#',                         'active' => true],
        ];
        $data['titulo']    = 'Clientes y Leads';
        $data['subtitulo'] = 'Base de datos de clientes potenciales y actuales';
        $data['icono']     = 'bi-people-fill';
        $data['color']     = 'success';
        return view('admin/principal/p1_2_view', $data);
    }

    public function p2_1()
    {
        $data['pageTitle']   = 'Gestión de Proyectos';
        $data['breadcrumbs'] = [
            ['name' => 'Admin',                'url' => base_url('admin/dashboard'), 'active' => false],
            ['name' => 'Operaciones',          'url' => '#',                         'active' => false],
            ['name' => 'Gestión de Proyectos', 'url' => '#',                         'active' => true],
        ];
        $data['titulo']    = 'Gestión de Proyectos';
        $data['subtitulo'] = 'Seguimiento de tareas, entregables y equipos de trabajo';
        $data['icono']     = 'bi-kanban-fill';
        $data['color']     = 'warning';
        return view('admin/principal/p2_1_view', $data);
    }

    public function p2_2()
    {
        $data['pageTitle']   = 'Reportes y Analítica';
        $data['breadcrumbs'] = [
            ['name' => 'Admin',              'url' => base_url('admin/dashboard'), 'active' => false],
            ['name' => 'Operaciones',        'url' => '#',                         'active' => false],
            ['name' => 'Reportes y Analítica','url' => '#',                        'active' => true],
        ];
        $data['titulo']    = 'Reportes y Analítica';
        $data['subtitulo'] = 'KPIs, métricas de negocio e indicadores de rendimiento';
        $data['icono']     = 'bi-bar-chart-line-fill';
        $data['color']     = 'info';
        return view('admin/principal/p2_2_view', $data);
    }
}
