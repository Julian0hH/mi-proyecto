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
        $this->supabaseKey = getenv('SUPABASE_SERVICE_KEY');
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

        $proyectos = json_decode($response, true);

        foreach ($proyectos as &$proyecto) {
            $imagenes = json_decode($proyecto['imagenes'], true) ?? [];
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

        return json_decode($response, true)[0];
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

        if ($httpCode !== 204) {
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
            $imagenes = json_decode($proyecto['imagenes'], true) ?? [];
            
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
        $nombreArchivo = uniqid() . '_' . $file->getName();
        $rutaStorage = "{$this->folderName}/{$nombreArchivo}";
        $contenido = file_get_contents($file->getTempName());

        $url = "{$this->supabaseUrl}/storage/v1/object/{$this->bucketName}/{$rutaStorage}";

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $contenido);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'apikey: ' . $this->supabaseKey,
            'Authorization: Bearer ' . $this->supabaseKey,
            'Content-Type: ' . $file->getMimeType()
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            throw new \Exception("Error al subir imagen: HTTP {$httpCode}");
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