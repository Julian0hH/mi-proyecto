<?php

namespace App\Controllers;

/**
 * DebugController — diagnóstico temporal.
 * BUG CORREGIDO #4: el endpoint original no tenía ninguna protección.
 * Cualquier visitante podía ver las claves de Supabase accediendo a /debug/env.
 * Ahora requiere sesión de admin activa.
 */
class DebugController extends BaseController
{
    public function env()
    {
        // Protección: solo accesible con sesión admin activa
        if (!session()->get('admin_logueado')) {
            return $this->response->setStatusCode(403)->setJSON([
                'error' => 'Acceso denegado'
            ]);
        }

        // Protección adicional: solo en entorno development
        if (getenv('CI_ENVIRONMENT') !== 'development') {
            return $this->response->setStatusCode(403)->setJSON([
                'error' => 'Solo disponible en entorno development'
            ]);
        }

        $vars = [
            'CI_ENVIRONMENT',
            'SUPABASE_URL',
            'SUPABASE_KEY',
            'SUPABASE_SERVICE_ROLE_KEY',
            'SUPABASE_SERVICE_KEY',
            'database.default.hostname',
            'database.default.username',
            'database.default.database',
            'database.default.port',
        ];

        $resultado = [];
        foreach ($vars as $var) {
            $val = getenv($var);
            if ($val === false || $val === '') {
                $resultado[$var] = '❌ NO CONFIGURADA';
            } else {
                $resultado[$var] = '✅ OK → ' . substr($val, 0, 12) . '...';
            }
        }

        $supabaseUrl = getenv('SUPABASE_URL');
        $supabaseKey = getenv('SUPABASE_SERVICE_ROLE_KEY')
                    ?: getenv('SUPABASE_KEY')
                    ?: '';

        $supabaseTest = 'Sin clave configurada';
        if ($supabaseUrl && $supabaseKey) {
            $ch = curl_init("{$supabaseUrl}/rest/v1/proyectos?select=id&limit=1");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 8);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'apikey: ' . $supabaseKey,
                'Authorization: Bearer ' . $supabaseKey,
            ]);
            $resp     = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlErr  = curl_error($ch);
            curl_close($ch);

            $supabaseTest = "HTTP {$httpCode}";
            if ($curlErr) {
                $supabaseTest .= " | cURL error: {$curlErr}";
            } else {
                $supabaseTest .= " | body: " . substr($resp, 0, 120);
            }
        }

        return $this->response->setJSON([
            'php_version'     => PHP_VERSION,
            'ci_version'      => \CodeIgniter\CodeIgniter::CI_VERSION,
            'env_vars'        => $resultado,
            'supabase_test'   => $supabaseTest,
            'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'N/A',
        ]);
    }
}