<?php

namespace App\Models;

class PermisoModel
{
    protected string $supabaseUrl;
    protected string $supabaseKey;

    public function __construct()
    {
        $this->supabaseUrl = getenv('SUPABASE_URL') ?: '';
        $this->supabaseKey = getenv('SUPABASE_SERVICE_ROLE_KEY')
                          ?: getenv('SUPABASE_SERVICE_KEY')
                          ?: '';
    }

    private function request(string $method, string $endpoint, array $data = [], array $extra = []): array
    {
        $ch = curl_init($this->supabaseUrl . '/rest/v1/' . $endpoint);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST  => strtoupper($method),
            CURLOPT_TIMEOUT        => 10,
            CURLOPT_HTTPHEADER     => array_merge([
                'apikey: '        . $this->supabaseKey,
                'Authorization: Bearer ' . $this->supabaseKey,
                'Content-Type: application/json',
            ], $extra),
        ]);
        if (!empty($data)) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }
        $response = curl_exec($ch);
        $code     = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return ['code' => $code, 'body' => json_decode($response, true)];
    }

    public function obtenerTodos(): array
    {
        $res = $this->request(
            'GET',
            'permisos?select=*,perfiles(nombre),modulos(nombre,ruta,grupo,orden)&order=perfil_id.asc,modulo_id.asc'
        );
        return ($res['code'] === 200 && is_array($res['body'])) ? $res['body'] : [];
    }

    public function obtenerPorPerfil(int $perfilId): array
    {
        $res = $this->request(
            'GET',
            'permisos?perfil_id=eq.' . $perfilId
            . '&select=*,modulos(id,nombre,ruta,icono,grupo,orden,activo)'
        );
        return ($res['code'] === 200 && is_array($res['body'])) ? $res['body'] : [];
    }

    public function construirSesion(array $filas): array
    {
        $permisosMap    = [];
        $sidebarModulos = [];

        foreach ($filas as $f) {
            $mod = $f['modulos'] ?? [];
            if (empty($mod['ruta']) || empty($mod['activo'])) {
                continue;
            }

            $ruta = $mod['ruta'];
            $permisosMap[$ruta] = [
                'consulta' => (bool)($f['bit_consulta'] ?? false),
                'agregar'  => (bool)($f['bit_agregar']  ?? false),
                'editar'   => (bool)($f['bit_editar']   ?? false),
                'eliminar' => (bool)($f['bit_eliminar'] ?? false),
                'detalle'  => (bool)($f['bit_detalle']  ?? false),
            ];

            if (!empty($f['bit_consulta'])) {
                $sidebarModulos[] = [
                    'id'     => $mod['id'],
                    'nombre' => $mod['nombre'],
                    'ruta'   => $ruta,
                    'icono'  => $mod['icono']  ?? 'bi-circle',
                    'grupo'  => $mod['grupo']  ?? 'General',
                    'orden'  => (int)($mod['orden'] ?? 0),
                ];
            }
        }

        usort($sidebarModulos, fn($a, $b) =>
            $a['grupo'] <=> $b['grupo'] ?: $a['orden'] <=> $b['orden']
        );

        return [
            'permisos'        => $permisosMap,
            'sidebar_modulos' => $sidebarModulos,
        ];
    }

    public function guardarPorPerfil(int $perfilId, array $filas): bool
    {
        $del = $this->request('DELETE', 'permisos?perfil_id=eq.' . $perfilId);
        if (!in_array($del['code'], [200, 204])) {
            return false;
        }
        if (empty($filas)) {
            return true;
        }
        $res = $this->request('POST', 'permisos', $filas, ['Prefer: return=minimal']);
        return $res['code'] === 201;
    }

    public function obtenerPorId(int $id): array
    {
        $res = $this->request('GET', 'permisos?id=eq.' . $id . '&limit=1');
        return ($res['code'] === 200 && !empty($res['body'])) ? $res['body'][0] : [];
    }

    public function crear(array $data): bool
    {
        $res = $this->request('POST', 'permisos', $data, ['Prefer: return=minimal']);
        return $res['code'] === 201;
    }

    public function actualizar(int $id, array $data): bool
    {
        $res = $this->request('PATCH', 'permisos?id=eq.' . $id, $data);
        return in_array($res['code'], [200, 204]);
    }

    public function eliminar(int $id): bool
    {
        $res = $this->request('DELETE', 'permisos?id=eq.' . $id);
        return in_array($res['code'], [200, 204]);
    }
}
