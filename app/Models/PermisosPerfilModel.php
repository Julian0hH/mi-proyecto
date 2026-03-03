<?php

namespace App\Models;

class PermisosPerfilModel
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
        $res = $this->request('GET', 'permisos_perfil?select=*,modulos_seg(strNombreModulo),perfiles(strNombrePerfil)&order=id.asc');
        return ($res['code'] === 200 && is_array($res['body'])) ? $res['body'] : [];
    }

    /** Retorna todos los permisos de un perfil indexados por idModulo */
    public function obtenerPorPerfil(int $idPerfil): array
    {
        $res = $this->request('GET', 'permisos_perfil?idPerfil=eq.' . $idPerfil . '&select=*');
        $rows = ($res['code'] === 200 && is_array($res['body'])) ? $res['body'] : [];
        $indexed = [];
        foreach ($rows as $row) {
            $indexed[$row['idModulo']] = $row;
        }
        return $indexed;
    }

    /**
     * Upsert de permisos para un perfil: borra todos los permisos del perfil
     * y vuelve a insertar los nuevos.
     * $rows = [ ['idModulo'=>1,'idPerfil'=>2,'bitAgregar'=>true,...], ... ]
     */
    public function guardarPorPerfil(int $idPerfil, array $rows): bool
    {
        // Eliminar permisos existentes del perfil
        $del = $this->request('DELETE', 'permisos_perfil?idPerfil=eq.' . $idPerfil);
        if (!in_array($del['code'], [200, 204])) {
            return false;
        }
        if (empty($rows)) {
            return true;
        }
        // Insertar nuevos
        $res = $this->request('POST', 'permisos_perfil', $rows, ['Prefer: return=minimal']);
        return $res['code'] === 201;
    }

    public function obtenerPorId(int $id): array
    {
        $res = $this->request('GET', 'permisos_perfil?id=eq.' . $id . '&limit=1');
        return ($res['code'] === 200 && !empty($res['body'])) ? $res['body'][0] : [];
    }

    public function crear(array $data): bool
    {
        $res = $this->request('POST', 'permisos_perfil', $data, ['Prefer: return=minimal']);
        return $res['code'] === 201;
    }

    public function actualizar(int $id, array $data): bool
    {
        $res = $this->request('PATCH', 'permisos_perfil?id=eq.' . $id, $data);
        return in_array($res['code'], [200, 204]);
    }

    public function eliminar(int $id): bool
    {
        $res = $this->request('DELETE', 'permisos_perfil?id=eq.' . $id);
        return in_array($res['code'], [200, 204]);
    }
}
