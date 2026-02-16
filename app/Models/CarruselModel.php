<?php

namespace App\Models;

use CodeIgniter\Model;

class CarruselModel extends Model
{
    protected $supabaseUrl;
    protected $supabaseKey;
    protected $bucketName = 'portfolio_images';
    protected $folderName = 'public';

    public function __construct()
    {
        parent::__construct();
        $this->supabaseUrl = getenv('SUPABASE_URL');
        $this->supabaseKey = getenv('SUPABASE_SERVICE_KEY');
    }

    public function obtenerImagenes()
    {
        $url = "{$this->supabaseUrl}/rest/v1/carrusel?select=*&order=orden.asc";

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
            throw new \Exception("Error al obtener imágenes: HTTP {$httpCode}");
        }

        $imagenes = json_decode($response, true);

        foreach ($imagenes as &$imagen) {
            $imagen['url'] = $this->getPublicUrl($imagen['ruta_storage']);
        }

        return $imagenes;
    }

    public function subirImagen($file, $titulo, $descripcion)
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

        $urlTabla = "{$this->supabaseUrl}/rest/v1/carrusel";
        $data = [
            'titulo' => $titulo,
            'descripcion' => $descripcion,
            'ruta_storage' => $rutaStorage
        ];

        $ch = curl_init($urlTabla);
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
            throw new \Exception("Error al registrar imagen en BD: HTTP {$httpCode}");
        }

        return json_decode($response, true)[0];
    }

    public function actualizarImagen($id, $titulo, $descripcion)
    {
        $url = "{$this->supabaseUrl}/rest/v1/carrusel?id=eq.{$id}";
        $data = [
            'titulo' => $titulo,
            'descripcion' => $descripcion
        ];

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
            throw new \Exception("Error al actualizar imagen: HTTP {$httpCode}");
        }
    }

    public function eliminarImagen($id)
    {
        $url = "{$this->supabaseUrl}/rest/v1/carrusel?id=eq.{$id}&select=ruta_storage";
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'apikey: ' . $this->supabaseKey,
            'Authorization: Bearer ' . $this->supabaseKey
        ]);

        $response = curl_exec($ch);
        curl_close($ch);

        $imagen = json_decode($response, true)[0] ?? null;
        if (!$imagen) {
            throw new \Exception("Imagen no encontrada");
        }

        $urlStorage = "{$this->supabaseUrl}/storage/v1/object/{$this->bucketName}/{$imagen['ruta_storage']}";
        
        $ch = curl_init($urlStorage);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'apikey: ' . $this->supabaseKey,
            'Authorization: Bearer ' . $this->supabaseKey
        ]);

        curl_exec($ch);
        curl_close($ch);

        $urlTabla = "{$this->supabaseUrl}/rest/v1/carrusel?id=eq.{$id}";
        
        $ch = curl_init($urlTabla);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'apikey: ' . $this->supabaseKey,
            'Authorization: Bearer ' . $this->supabaseKey
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 204) {
            throw new \Exception("Error al eliminar imagen de BD: HTTP {$httpCode}");
        }
    }

    private function getPublicUrl($ruta)
    {
        return "{$this->supabaseUrl}/storage/v1/object/public/{$this->bucketName}/{$ruta}";
    }
}