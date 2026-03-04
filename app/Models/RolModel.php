<?php

namespace App\Models;

class RolModel
{
    protected string $supabaseUrl;
    protected string $supabaseKey;

    public function __construct()
    {
        $this->supabaseUrl = getenv('SUPABASE_URL') ?: '';
        $this->supabaseKey = getenv('SUPABASE_SERVICE_ROLE_KEY')
                          ?: getenv('SUPABASE_SERVICE_KEY')
                          ?: getenv('SUPABASE_KEY')
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
                'apikey: ' . $this->supabaseKey,
                'Authorization: Bearer ' . $this->supabaseKey,
                'Content-Type: application/json',
            ], $extra),
        ]);
        if (!empty($data)) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }
        $response = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return ['code' => $code, 'body' => json_decode($response, true)];
    }

    public function obtenerTodos(): array
    {
        $res = $this->request('GET', 'roles?select=*&order=id.asc');
        return ($res['code'] === 200 && is_array($res['body'])) ? $res['body'] : [];
    }

    public function obtenerPorId(int $id): array
    {
        $res = $this->request('GET', "roles?id=eq.$id&select=*");
        return ($res['code'] === 200 && !empty($res['body'])) ? $res['body'][0] : [];
    }

    public function crear(array $data): bool
    {
        $res = $this->request('POST', 'roles', $data, ['Prefer: return=representation']);
        return in_array($res['code'], [200, 201]);
    }

    public function actualizar(int $id, array $data): bool
    {
        $res = $this->request('PATCH', "roles?id=eq.$id", $data);
        return in_array($res['code'], [200, 204]);
    }

    public function eliminar(int $id): bool
    {
        $res = $this->request('DELETE', "roles?id=eq.$id");
        return in_array($res['code'], [200, 204]);
    }

    public function obtenerUsuariosTodos(): array
    {
        $res = $this->request('GET', 'usuarios?select=id,nombre,email,rol_id,activo,created_at&order=created_at.desc');
        if ($res['code'] !== 200 || !is_array($res['body'])) {
            return [];
        }
        $roles = $this->obtenerTodos();
        $rolesMap = array_column($roles, null, 'id');
        foreach ($res['body'] as &$u) {
            $u['rol_nombre'] = $rolesMap[$u['rol_id']]['nombre'] ?? 'sin rol';
        }
        return $res['body'];
    }

    public function asignarRol(string $userId, int $rolId): bool
    {
        $res = $this->request('PATCH', "usuarios?id=eq.$userId", ['rol_id' => $rolId]);
        return in_array($res['code'], [200, 204]);
    }

    public function toggleActivo(string $userId, bool $activo): bool
    {
        $res = $this->request('PATCH', "usuarios?id=eq.$userId", ['activo' => $activo]);
        return in_array($res['code'], [200, 204]);
    }
}