<?php

namespace App\Models;

class UsuarioAppModel
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
                'apikey: ' . $this->supabaseKey,
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
        $res = $this->request('GET', 'usuarios_app?select=*,perfiles(strNombrePerfil)&order=id.asc');
        return ($res['code'] === 200 && is_array($res['body'])) ? $res['body'] : [];
    }

    public function obtenerPorId(int $id): array
    {
        $res = $this->request('GET', 'usuarios_app?id=eq.' . $id . '&select=*,perfiles(strNombrePerfil)&limit=1');
        return ($res['code'] === 200 && !empty($res['body'])) ? $res['body'][0] : [];
    }

    public function buscarPorUsuario(string $nombre): array
    {
        $res = $this->request('GET', 'usuarios_app?strNombreUsuario=eq.' . urlencode($nombre) . '&select=*,perfiles(strNombrePerfil,bitAdministrador)&limit=1');
        return ($res['code'] === 200 && !empty($res['body'])) ? $res['body'][0] : [];
    }

    public function buscarPorCorreo(string $email): array
    {
        $res = $this->request('GET', 'usuarios_app?strCorreo=eq.' . urlencode($email) . '&select=*,perfiles(strNombrePerfil,bitAdministrador)&limit=1');
        return ($res['code'] === 200 && !empty($res['body'])) ? $res['body'][0] : [];
    }

    public function crear(array $data): bool
    {
        $res = $this->request('POST', 'usuarios_app', $data, ['Prefer: return=minimal']);
        return $res['code'] === 201;
    }

    public function actualizar(int $id, array $data): bool
    {
        $res = $this->request('PATCH', 'usuarios_app?id=eq.' . $id, $data);
        return in_array($res['code'], [200, 204]);
    }

    public function eliminar(int $id): bool
    {
        $res = $this->request('DELETE', 'usuarios_app?id=eq.' . $id);
        return in_array($res['code'], [200, 204]);
    }

    /** Sube imagen a Supabase Storage bucket "usuarios" */
    public function subirImagen(string $fileContent, string $fileName, string $mimeType): string|false
    {
        $url = $this->supabaseUrl . '/storage/v1/object/usuarios/' . $fileName;
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST  => 'POST',
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_HTTPHEADER     => [
                'apikey: ' . $this->supabaseKey,
                'Authorization: Bearer ' . $this->supabaseKey,
                'Content-Type: ' . $mimeType,
                'x-upsert: true',
            ],
            CURLOPT_POSTFIELDS => $fileContent,
        ]);
        $response = curl_exec($ch);
        $code     = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if (in_array($code, [200, 201])) {
            return $this->supabaseUrl . '/storage/v1/object/public/usuarios/' . $fileName;
        }
        return false;
    }
}
