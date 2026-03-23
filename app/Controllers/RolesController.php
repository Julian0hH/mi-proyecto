<?php

declare(strict_types=1);

namespace App\Controllers;

class RolesController extends BaseController
{
    public function index()
    {
        return redirect()->to(base_url('admin/seguridad/usuarios'));
    }

    public function __call(string $method, array $args)
    {
        return redirect()->to(base_url('admin/seguridad/usuarios'));
    }
}
