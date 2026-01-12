<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index(): string
    {
       
        $data = [
            'titulo'   => 'Mi Primer Proyecto',
            'usuario'  => 'Desarrollador',
            'fecha'    => date('d/m/Y')
        ];

        return view('hola_mundo', $data);
    }
}