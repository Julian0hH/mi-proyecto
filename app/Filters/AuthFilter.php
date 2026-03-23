<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
class AuthFilter implements FilterInterface
{
    private const ACCIONES = [
        'crear'      => 'agregar',
        'subir'      => 'agregar',
        'editar'     => 'editar',
        'actualizar' => 'editar',
        'guardar'    => 'editar',
        'eliminar'   => 'eliminar',
        'detalle'    => 'detalle',
        'ver'        => 'detalle',
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

        $segments     = explode('/', $uri);
        $lastSegment  = end($segments);
        $bitRequerido = self::ACCIONES[$lastSegment] ?? null;

        // Si el último segmento es un número, revisar el penúltimo para el verbo
        // Ej: admin/servicios/eliminar/123 → penúltimo = 'eliminar'
        if (ctype_digit($lastSegment)) {
            $penultimo    = $segments[count($segments) - 2] ?? '';
            $bitRequerido = self::ACCIONES[$penultimo] ?? 'detalle';
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
