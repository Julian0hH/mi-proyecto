<?php

namespace App\Models;

class UsuarioModel
{
    protected string $supabaseUrl;
    protected string $supabaseKey;

    public function __construct()
    {
        $this->supabaseUrl = getenv('SUPABASE_URL') ?: '';
        $this->supabaseKey = getenv('SUPABASE_SERVICE_ROLE_KEY') ?: getenv('SUPABASE_SERVICE_KEY') ?: '';
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

    public function buscarPorEmail(string $email): array
    {
        $res = $this->request(
            'GET',
            'usuarios?email=eq.' . urlencode($email) . '&select=*,roles(nombre)&activo=eq.true&limit=1'
        );
        return ($res['code'] === 200 && !empty($res['body'])) ? $res['body'][0] : [];
    }

    public function obtenerTodos(): array
    {
        $res = $this->request('GET', 'usuarios?select=*,roles(nombre)&order=created_at.desc');
        return ($res['code'] === 200 && is_array($res['body'])) ? $res['body'] : [];
    }

    public function obtenerPorId(string $id): array
    {
        $res = $this->request('GET', 'usuarios?id=eq.' . $id . '&select=*,roles(nombre)&limit=1');
        return ($res['code'] === 200 && !empty($res['body'])) ? $res['body'][0] : [];
    }

    public function actualizarUltimoLogin(string $id): void
    {
        $this->request('PATCH', 'usuarios?id=eq.' . $id, ['ultimo_login' => date('c')]);
    }

    public function crear(array $data): bool
    {
        $res = $this->request('POST', 'usuarios', $data, ['Prefer: return=minimal']);
        return $res['code'] === 201;
    }

    public function actualizar(string $id, array $data): bool
    {
        $res = $this->request('PATCH', 'usuarios?id=eq.' . $id, $data);
        return in_array($res['code'], [200, 204]);
    }

    public function eliminar(string $id): bool
    {
        $res = $this->request('DELETE', 'usuarios?id=eq.' . $id);
        return in_array($res['code'], [200, 204]);
    }
}
