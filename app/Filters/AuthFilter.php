<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
class AuthFilter implements FilterInterface
{
    private const ACCIONES = [
        'crear'    => 'agregar',
        'editar'   => 'editar',
        'eliminar' => 'eliminar',
        'detalle'  => 'detalle',
    ];

    public function before(RequestInterface $request, $arguments = null)
    {
        if (!session('logueado')) {
            return redirect()->to(base_url('login'))
                ->with('error', 'Debes iniciar sesión primero.');
        }

        $uri = trim($request->getUri()->getPath(), '/');

        if (!str_starts_with($uri, 'admin/') || in_array($uri, ['admin/login', 'admin/logout', 'admin/dashboard'])) {
            return;
        }

        $permisos = session('permisos') ?? [];
        $acceso   = null;
        $matchLen = 0;

        foreach ($permisos as $ruta => $bits) {
            if (($uri === $ruta || str_starts_with($uri, $ruta . '/')) && strlen($ruta) > $matchLen) {
                $acceso   = $bits;
                $matchLen = strlen($ruta);
            }
        }

        if ($acceso === null || !($acceso['consulta'] ?? false)) {
            session()->setFlashdata('error', 'No tienes acceso a esa sección.');
            return redirect()->to(base_url('admin/dashboard'));
        }

        $lastSegment = basename($uri);
        $bitRequerido = self::ACCIONES[$lastSegment] ?? null;

        if ($bitRequerido === null && ctype_digit($lastSegment)) {
            $bitRequerido = 'detalle';
        }

        if ($bitRequerido !== null && !($acceso[$bitRequerido] ?? false)) {
            session()->setFlashdata('error', 'No tienes permiso para esa acción.');
            return redirect()->back();
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}
