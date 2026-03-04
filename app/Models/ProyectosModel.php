<?php

namespace App\Models;

use CodeIgniter\Model;

class ProyectosModel extends Model
{
    protected $supabaseUrl;
    protected $supabaseKey;
    protected $bucketName = 'portfolio_images';
    protected $folderName = 'proyectos';

    public function __construct()
    {
        parent::__construct();
        $this->supabaseUrl = getenv('SUPABASE_URL');
        $this->supabaseKey = getenv('SUPABASE_SERVICE_ROLE_KEY')
                          ?: getenv('SUPABASE_SERVICE_KEY')
                          ?: getenv('SUPABASE_KEY')
                          ?: '';
    }

    public function obtenerProyectos()
    {
        $url = "{$this->supabaseUrl}/rest/v1/proyectos?select=*&order=created_at.desc";

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'apikey: ' . $this->supabaseKey,
            'Authorization: Bearer ' . $this->supabaseKey,
            'Content-Type: application/json'
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            throw new \Exception("Error al obtener proyectos: HTTP {$httpCode}");
        }

        // BUG CORREGIDO: si curl falla o Supabase devuelve body vacío,
        // json_decode() retorna null y el foreach lanza Fatal Error en PHP 8.
        $proyectos = json_decode($response, true);
        if (!is_array($proyectos)) {
            throw new \Exception("Respuesta inválida de Supabase al obtener proyectos");
        }

        foreach ($proyectos as &$proyecto) {
            // PostgREST ya decodifica JSONB → llega como array; json_decode() en PHP 8 lanza TypeError con arrays
            $raw = $proyecto['imagenes'] ?? [];
            $imagenes = is_array($raw) ? $raw : (json_decode($raw, true) ?? []);
            $proyecto['imagenes_urls'] = array_map(
                fn($ruta) => $this->getPublicUrl($ruta),
                $imagenes
            );
        }

        return $proyectos;
    }

    public function crearProyecto($data)
    {
        $url = "{$this->supabaseUrl}/rest/v1/proyectos";

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'apikey: ' . $this->supabaseKey,
            'Authorization: Bearer ' . $this->supabaseKey,
            'Content-Type: application/json',
            'Prefer: return=representation'
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 201) {
            throw new \Exception("Error al crear proyecto: HTTP {$httpCode}");
        }

        // BUG CORREGIDO: json_decode()[0] lanza TypeError en PHP 8 si el body no es un array.
        $result = json_decode($response, true);
        if (!is_array($result) || empty($result[0])) {
            throw new \Exception("Respuesta inválida de Supabase al crear proyecto");
        }
        return $result[0];
    }

    public function actualizarProyecto($id, $data)
    {
        $url = "{$this->supabaseUrl}/rest/v1/proyectos?id=eq.{$id}";

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'apikey: ' . $this->supabaseKey,
            'Authorization: Bearer ' . $this->supabaseKey,
            'Content-Type: application/json'
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if (!in_array($httpCode, [200, 204])) {
            throw new \Exception("Error al actualizar proyecto: HTTP {$httpCode}");
        }
    }

    public function eliminarProyecto($id)
    {
        $url = "{$this->supabaseUrl}/rest/v1/proyectos?id=eq.{$id}&select=imagenes";
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'apikey: ' . $this->supabaseKey,
            'Authorization: Bearer ' . $this->supabaseKey
        ]);

        $response = curl_exec($ch);
        curl_close($ch);

        $proyecto = json_decode($response, true)[0] ?? null;
        
        if ($proyecto) {
            $raw = $proyecto['imagenes'] ?? [];
            $imagenes = is_array($raw) ? $raw : (json_decode($raw, true) ?? []);
            
            foreach ($imagenes as $ruta) {
                $this->eliminarImagen($ruta);
            }
        }

        $urlDelete = "{$this->supabaseUrl}/rest/v1/proyectos?id=eq.{$id}";
        
        $ch = curl_init($urlDelete);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'apikey: ' . $this->supabaseKey,
            'Authorization: Bearer ' . $this->supabaseKey
        ]);

        curl_exec($ch);
        curl_close($ch);
    }

    public function subirImagen($file)
    {
        $ext           = strtolower(pathinfo($file->getName(), PATHINFO_EXTENSION) ?: 'jpg');
        $nombreArchivo = uniqid() . '.' . $ext;
        $rutaStorage   = "{$this->folderName}/{$nombreArchivo}";
        $contenido = file_get_contents($file->getTempName());
        $tamano    = strlen($contenido);

        $url = "{$this->supabaseUrl}/storage/v1/object/{$this->bucketName}/{$rutaStorage}";

        $stream = fopen('php://temp', 'r+');
        fwrite($stream, $contenido);
        rewind($stream);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_PUT, true);
        curl_setopt($ch, CURLOPT_INFILE, $stream);
        curl_setopt($ch, CURLOPT_INFILESIZE, $tamano);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'apikey: ' . $this->supabaseKey,
            'Authorization: Bearer ' . $this->supabaseKey,
            'Content-Type: ' . $file->getMimeType(),
            'x-upsert: true',
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlErr  = curl_error($ch);
        curl_close($ch);
        fclose($stream);

        if (!in_array($httpCode, [200, 201])) {
            throw new \Exception("Error al subir imagen: HTTP {$httpCode}" . ($curlErr ? " - {$curlErr}" : ""));
        }

        return $rutaStorage;
    }

    private function eliminarImagen($ruta)
    {
        $url = "{$this->supabaseUrl}/storage/v1/object/{$this->bucketName}/{$ruta}";
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'apikey: ' . $this->supabaseKey,
            'Authorization: Bearer ' . $this->supabaseKey
        ]);

        curl_exec($ch);
        curl_close($ch);
    }

    private function getPublicUrl($ruta)
    {
        return "{$this->supabaseUrl}/storage/v1/object/public/{$this->bucketName}/{$ruta}";
    }
}