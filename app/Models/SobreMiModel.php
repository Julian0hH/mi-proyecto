<?php

namespace App\Models;

class SobreMiModel
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

    public function obtener(): array
    {
        $res = $this->request('GET', 'sobre_mi?select=*&limit=1');
        if ($res['code'] === 200 && !empty($res['body'])) {
            $row = $res['body'][0];
            if (is_string($row['habilidades'] ?? null)) {
                $row['habilidades'] = json_decode($row['habilidades'], true) ?: [];
            }
            return $row;
        }
        return [];
    }

    public function guardar(array $data): bool
    {
        $existing = $this->obtener();
        if (!empty($existing)) {
            $res = $this->request('PATCH', 'sobre_mi?id=eq.' . $existing['id'], $data);
            return in_array($res['code'], [200, 204]);
        }
        $res = $this->request('POST', 'sobre_mi', $data, ['Prefer: return=minimal']);
        return $res['code'] === 201;
    }
}
