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
        // 1. Verificar que haya sesión activa
        if (!session()->get('admin_logueado')) {
            return redirect()->to(base_url('login'))
                ->with('error', 'Debes iniciar sesión primero');
        }

        // 2. Validar JWT si existe en sesión
        $token = session()->get('jwt_token');
        if ($token) {
            $payload = JwtHelper::validate($token);
            if (!$payload) {
                session()->destroy();
                return redirect()->to(base_url('login'))
                    ->with('error', 'Tu sesión ha expirado. Inicia sesión nuevamente.');
            }
        }

        // 3. Verificar permisos de URL para usuarios tipo app
        if (session()->get('user_type') === 'app') {
            $segments = service('uri')->getSegments();
            $moduloId = $this->resolverModuloId($segments);

            if ($moduloId !== null) {
                $permisos = session()->get('user_permisos') ?? [];
                if (empty($permisos[$moduloId]['bitConsulta'])) {
                    session()->setFlashdata('error', 'No tienes permiso para acceder a ese módulo.');
                    return redirect()->to(base_url('admin/dashboard'));
                }
            }
        }
    }

    /**
     * Determina el ID de módulo según los segmentos de la URL.
     * Retorna null si la ruta no requiere validación de permiso.
     */
    private function resolverModuloId(array $segments): ?int
    {
        if (in_array('seguridad', $segments)) {
            $mapa = ['perfiles' => 1, 'modulos' => 2, 'permisos' => 3, 'usuarios' => 4];
            foreach ($segments as $seg) {
                if (isset($mapa[$seg])) {
                    return $mapa[$seg];
                }
            }
            return null;
        }

        if (in_array('principal1', $segments)) {
            return in_array('modulo2', $segments) ? 6 : 5;
        }

        if (in_array('principal2', $segments)) {
            return in_array('modulo2', $segments) ? 8 : 7;
        }

        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}
