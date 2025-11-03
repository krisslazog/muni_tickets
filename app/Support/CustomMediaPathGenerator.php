<?php

namespace App\Support;

use Illuminate\Support\Str;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\Support\PathGenerator\PathGenerator;

/**
 * Este generador crea una ruta como:
 * /tktticket/11/archivo.jpg
 * /tktticket/11/otro_archivo.pdf
 */
class CustomMediaPathGenerator implements PathGenerator
{
    /**
     * Esta es la función principal que define la carpeta base.
     * Resultado: "tktticket/11"
     */
    protected function getBasePath(Media $media): string
    {
        // 1. Obtiene el nombre del modelo (ej: "TktTicket")
        $modelBaseName = class_basename($media->model_type);

        // 3. Retorna la ruta: [nombre_de_modulo]/[id_del_ticket]
        return $modelBaseName . '/' . $media->model_id;
    }

    /**
     * Obtiene la ruta para el archivo principal.
     * Resultado: "tktticket/11/" (El archivo se guardará aquí)
     */
    public function getPath(Media $media): string
    {
        // Devuelve la ruta base para que los archivos se guarden directamente allí
        return $this->getBasePath($media) . '/'.$media->id.'/';
    }

    /**
     * Obtiene la ruta para las conversiones (thumbnails).
     * Resultado: "tktticket/11/conversions/"
     */
    public function getPathForConversions(Media $media): string
    {
        // Mantiene las conversiones en una subcarpeta para no ensuciar
        return $this->getBasePath($media) . '/conversions/';
    }

    /**
     * Obtiene la ruta para las imágenes responsive.
     * Resultado: "tktticket/11/responsive-images/"
     */
    public function getPathForResponsiveImages(Media $media): string
    {
        return $this->getBasePath($media) . '/responsive-images/';
    }
}