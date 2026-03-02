<?php

declare(strict_types=1);

namespace App\Controllers;

use CodeIgniter\HTTP\ResponseInterface;

class AdminController extends BaseController
{
    private string $supabaseUrl;
    private string $supabaseKey;

    public function __construct()
    {
        helper(['form', 'url']);
        $this->supabaseUrl = getenv('SUPABASE_URL') ?: '';
        $this->supabaseKey = getenv('SUPABASE_SERVICE_ROLE_KEY') ?: getenv('SUPABASE_SERVICE_KEY') ?: '';
    }

    private function supaRequest(string $method, string $endpoint, array $data = []): array
    {
        $ch = curl_init($this->supabaseUrl . '/rest/v1/' . $endpoint);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST  => $method,
            CURLOPT_TIMEOUT        => 8,
            CURLOPT_HTTPHEADER     => [
                'apikey: ' . $this->supabaseKey,
                'Authorization: Bearer ' . $this->supabaseKey,
                'Content-Type: application/json',
            ],
        ]);
        if (!empty($data)) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }
        $body = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return ['code' => $code, 'body' => json_decode($body, true)];
    }

    public function dashboard(): string
    {
        try {
            $stats = ['proyectos' => 0, 'contactos' => 0, 'usuarios' => 0, 'carrusel' => 0, 'contactos_nuevos' => 0, 'notificaciones' => 0];

            $queries = [
                'proyectos'        => 'proyectos?select=id&activo=eq.true',
                'contactos'        => 'contactos?select=id',
                'usuarios'         => 'usuarios?select=id',
                'carrusel'         => 'carrusel?select=id',
                'contactos_nuevos' => 'contactos?select=id&leido=eq.false',
                'notificaciones'   => 'notificaciones?select=id&leido=eq.false',
            ];

            foreach ($queries as $key => $endpoint) {
                try {
                    $res = $this->supaRequest('GET', $endpoint);
                    if ($res['code'] === 200 && is_array($res['body'])) {
                        $stats[$key] = count($res['body']);
                    }
                } catch (\Throwable $e) {
                    log_message('warning', "Dashboard stats [$key]: " . $e->getMessage());
                }
            }

            $actividadRes = $this->supaRequest('GET', 'contactos?select=*&order=created_at.desc&limit=5');
            $actividad = ($actividadRes['code'] === 200 && is_array($actividadRes['body'])) ? $actividadRes['body'] : [];

            $notiRes = $this->supaRequest('GET', 'notificaciones?select=*&order=created_at.desc&limit=8');
            $notificaciones = ($notiRes['code'] === 200 && is_array($notiRes['body'])) ? $notiRes['body'] : [];

            return view('admin/dashboard_view', [
                'stats'          => $stats,
                'actividad'      => $actividad,
                'notificaciones' => $notificaciones,
                'breadcrumbs'    => [
                    ['name' => 'Admin', 'url' => base_url('admin/dashboard'), 'active' => false],
                    ['name' => 'Dashboard', 'url' => '#', 'active' => true],
                ],
            ]);
        } catch (\Throwable $e) {
            log_message('critical', 'AdminController::dashboard ' . $e->getMessage());
            return view('errors/html/error_500');
        }
    }

    public function simularError(): ResponseInterface
    {
        $env  = getenv('CI_ENVIRONMENT') ?: 'production';
        $tipo = $this->request->getGet('tipo') ?: 'generico';

        $errores = [
            'generico' => ['tipo' => 'Error Genérico Controlado',  'mensaje' => 'Se simuló un error interno del servidor (500).',     'codigo' => 500],
            'db'       => ['tipo' => 'Error de Base de Datos',     'mensaje' => 'Timeout de conexión con Supabase (simulado).',       'codigo' => 503],
            'auth'     => ['tipo' => 'Error de Autenticación',     'mensaje' => 'Token expirado o inválido (simulado, 401).',         'codigo' => 401],
            'notfound' => ['tipo' => 'Recurso No Encontrado',      'mensaje' => 'El recurso solicitado no existe (simulado, 404).',   'codigo' => 404],
        ];

        $error = $errores[$tipo] ?? $errores['generico'];
        log_message('error', "[SIMULADO] {$error['tipo']}: {$error['mensaje']} | ENV: $env");

        return $this->response->setJSON([
            'simulado'           => true,
            'tipo'               => $error['tipo'],
            'mensaje'            => $error['mensaje'],
            'codigo_http'        => $error['codigo'],
            'entorno'            => $env,
            'detalles_visibles'  => ($env === 'development'),
            'stack_trace'        => ($env === 'development') ? 'Stack trace completo disponible en logs de desarrollo' : 'Ocultado en producción por seguridad',
            'timestamp'          => date('Y-m-d H:i:s'),
            'nota'               => 'Este error fue generado de forma controlada y NO afecta la aplicación.',
        ])->setStatusCode($error['codigo']);
    }

    public function notificacionesJson(): ResponseInterface
    {
        try {
            $res      = $this->supaRequest('GET', 'notificaciones?select=*&order=created_at.desc&limit=10');
            $noti     = ($res['code'] === 200 && is_array($res['body'])) ? $res['body'] : [];
            $noLeidas = count(array_filter($noti, fn($n) => !($n['leido'] ?? true)));
            return $this->response->setJSON(['success' => true, 'data' => $noti, 'no_leidas' => $noLeidas]);
        } catch (\Throwable $e) {
            return $this->response->setJSON(['success' => false, 'data' => [], 'no_leidas' => 0]);
        }
    }

    public function marcarNotificacionLeida(int $id): ResponseInterface
    {
        $res = $this->supaRequest('PATCH', "notificaciones?id=eq.$id", ['leido' => true]);
        return $this->response->setJSON(['success' => in_array($res['code'], [200, 204])]);
    }

    public function marcarTodasLeidas(): ResponseInterface
    {
        $res = $this->supaRequest('PATCH', 'notificaciones?leido=eq.false', ['leido' => true]);
        return $this->response->setJSON(['success' => in_array($res['code'], [200, 204])]);
    }
}
