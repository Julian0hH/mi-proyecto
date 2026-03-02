<?php

namespace App\Models;

class RecuperacionModel
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

    public function crearToken(string $email): string
    {
        // Invalidar tokens anteriores del mismo email
        $this->request('PATCH', "recuperacion_passwords?email=eq.$email&usado=eq.false", ['usado' => true]);

        $token = str_pad((string)random_int(100000, 999999), 6, '0', STR_PAD_LEFT);
        $expires = date('Y-m-d\TH:i:s', strtotime('+15 minutes'));

        $this->request('POST', 'recuperacion_passwords', [
            'email'      => $email,
            'token'      => $token,
            'expires_at' => $expires,
            'usado'      => false,
        ], ['Prefer: return=minimal']);

        return $token;
    }

    public function verificarToken(string $email, string $token): bool
    {
        $ahora = date('Y-m-d\TH:i:s');
        $res = $this->request('GET', "recuperacion_passwords?email=eq.$email&token=eq.$token&usado=eq.false&expires_at=gte.$ahora&select=id");
        return ($res['code'] === 200 && !empty($res['body']));
    }

    public function invalidarToken(string $email, string $token): void
    {
        $this->request('PATCH', "recuperacion_passwords?email=eq.$email&token=eq.$token", ['usado' => true]);
    }

    public function actualizarPassword(string $email, string $newHash): bool
    {
        $res = $this->request('PATCH', "usuarios?email=eq.$email", ['password_hash' => $newHash]);
        return in_array($res['code'], [200, 204]);
    }

    public function buscarUsuarioPorEmail(string $email): array
    {
        $res = $this->request('GET', "usuarios?email=eq.$email&select=id,nombre,email,activo");
        return ($res['code'] === 200 && !empty($res['body'])) ? $res['body'][0] : [];
    }
}
