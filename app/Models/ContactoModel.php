<?php

namespace App\Models;

class ContactoModel
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

    public function obtenerFiltrado(array $filters = [], int $page = 1, int $perPage = 5): array
    {
        $conditions = ['order=created_at.desc'];

        if (!empty($filters['busqueda'])) {
            $q = urlencode($filters['busqueda']);
            $conditions[] = "or=(nombre.ilike.*{$q}*,email.ilike.*{$q}*,asunto.ilike.*{$q}*)";
        }
        if (!empty($filters['estado'])) {
            $conditions[] = 'estado=eq.' . urlencode($filters['estado']);
        }
        if (!empty($filters['categoria'])) {
            $conditions[] = 'categoria=eq.' . urlencode($filters['categoria']);
        }
        if (!empty($filters['fecha_desde'])) {
            $conditions[] = 'created_at=gte.' . urlencode($filters['fecha_desde'] . 'T00:00:00');
        }
        if (!empty($filters['fecha_hasta'])) {
            $conditions[] = 'created_at=lte.' . urlencode($filters['fecha_hasta'] . 'T23:59:59');
        }

        // Obtener total sin paginación
        $baseQuery = 'contactos?select=id&' . implode('&', $conditions);
        $totalRes = $this->request('GET', $baseQuery);
        $total = ($totalRes['code'] === 200 && is_array($totalRes['body'])) ? count($totalRes['body']) : 0;

        // Paginación
        $offset = ($page - 1) * $perPage;
        $conditions[] = "limit=$perPage";
        $conditions[] = "offset=$offset";
        $endpoint = 'contactos?select=*&' . implode('&', $conditions);

        $res = $this->request('GET', $endpoint);
        $data = ($res['code'] === 200 && is_array($res['body'])) ? $res['body'] : [];

        return [
            'data'        => $data,
            'total'       => $total,
            'page'        => $page,
            'per_page'    => $perPage,
            'total_pages' => max(1, (int)ceil($total / $perPage)),
        ];
    }

    public function crear(array $data): bool
    {
        $res = $this->request('POST', 'contactos', $data, ['Prefer: return=minimal']);
        return $res['code'] === 201;
    }

    public function obtenerPorId(int $id): array
    {
        $res = $this->request('GET', "contactos?id=eq.$id&select=*");
        return ($res['code'] === 200 && !empty($res['body'])) ? $res['body'][0] : [];
    }

    public function actualizar(int $id, array $data): bool
    {
        $res = $this->request('PATCH', "contactos?id=eq.$id", $data);
        return in_array($res['code'], [200, 204]);
    }

    public function eliminar(int $id): bool
    {
        $res = $this->request('DELETE', "contactos?id=eq.$id");
        return in_array($res['code'], [200, 204]);
    }

    public function marcarLeido(int $id): bool
    {
        return $this->actualizar($id, ['leido' => true, 'estado' => 'leido']);
    }

    public function contarNoLeidos(): int
    {
        $res = $this->request('GET', 'contactos?leido=eq.false&select=id');
        return ($res['code'] === 200 && is_array($res['body'])) ? count($res['body']) : 0;
    }
}