<?php

namespace App\Filters;

use App\Libraries\JwtHelper;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if (!session()->get('admin_logueado')) {
            return redirect()->to(base_url('login'))
                ->with('error', 'Debes iniciar sesión primero');
        }

        $token = session()->get('jwt_token');
        if ($token) {
            $payload = JwtHelper::validate($token);
            if (!$payload) {
                session()->destroy();
                return redirect()->to(base_url('login'))
                    ->with('error', 'Tu sesión ha expirado. Inicia sesión nuevamente.');
            }
        }

        if (session()->get('user_type') === 'app') {
            $bits = $this->resolverPermisosPorUrl();

            if ($bits !== null && empty($bits['bitConsulta'])) {
                session()->setFlashdata('error', 'No tienes permiso para acceder a ese módulo.');
                return redirect()->to(base_url('admin/dashboard'));
            }
        }
    }

    private function resolverPermisosPorUrl(): ?array
    {
        $rutas = session()->get('user_rutas') ?? [];
        if (empty($rutas)) {
            return null;
        }

        $segments = service('uri')->getSegments();
        if (isset($segments[0]) && $segments[0] === 'admin') {
            array_shift($segments);
        }
        $path = implode('/', $segments);

        $matched   = null;
        $matchLen  = 0;
        foreach ($rutas as $ruta => $bits) {
            if (
                $ruta !== '' &&
                (str_starts_with($path, $ruta . '/') || $path === $ruta) &&
                strlen($ruta) > $matchLen
            ) {
                $matched  = $bits;
                $matchLen = strlen($ruta);
            }
        }

        return $matched;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}
