<?php

namespace App\Models;

class NotificacionModel
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

    public function obtenerRecientes(int $limite = 10): array
    {
        $res = $this->request('GET', "notificaciones?select=*&order=created_at.desc&limit=$limite");
        return ($res['code'] === 200 && is_array($res['body'])) ? $res['body'] : [];
    }

    public function contarNoLeidas(): int
    {
        $res = $this->request('GET', 'notificaciones?leido=eq.false&select=id');
        return ($res['code'] === 200 && is_array($res['body'])) ? count($res['body']) : 0;
    }

    public function crear(array $data): bool
    {
        $res = $this->request('POST', 'notificaciones', $data, ['Prefer: return=minimal']);
        return $res['code'] === 201;
    }

    public function marcarLeida(int $id): bool
    {
        $res = $this->request('PATCH', "notificaciones?id=eq.$id", ['leido' => true]);
        return in_array($res['code'], [200, 204]);
    }

    public function marcarTodasLeidas(): bool
    {
        $res = $this->request('PATCH', 'notificaciones?leido=eq.false', ['leido' => true]);
        return in_array($res['code'], [200, 204]);
    }

    public function eliminar(int $id): bool
    {
        $res = $this->request('DELETE', "notificaciones?id=eq.$id");
        return in_array($res['code'], [200, 204]);
    }
}
