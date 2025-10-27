<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\TktStatus;

class TktStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Desactivar revisión de claves foráneas
        DB::statement('SET FOREIGN_KEY_CHECKS=0;'); 

        // Usar delete() para vaciar la tabla
        TktStatus::query()->delete(); 

        // Reiniciar el contador AUTO_INCREMENT
        DB::statement('ALTER TABLE tkt_statuses AUTO_INCREMENT = 1;');

        // Volver a activar la revisión
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Inserta los nuevos estados (ejemplos comunes)
        TktStatus::create([
            'name' => 'Abierto',
            'description' => 'El ticket ha sido recibido y está pendiente de asignación o revisión.',
            'status' => true // Indica si el estado mismo está activo
        ]);
        
        TktStatus::create([
            'name' => 'En Progreso',
            'description' => 'Un agente está trabajando activamente en la resolución del ticket.',
            'status' => true
        ]);
        
        TktStatus::create([
            'name' => 'En Espera',
            'description' => 'Se necesita más información del solicitante o de un tercero.',
            'status' => true
        ]);
        
        TktStatus::create([
            'name' => 'Resuelto',
            'description' => 'La incidencia ha sido solucionada. Pendiente de cierre por el solicitante.',
            'status' => true
        ]);
        
        TktStatus::create([
            'name' => 'Cerrado',
            'description' => 'El ticket ha sido completado y cerrado.',
            'status' => true
        ]);
        
        TktStatus::create([
            'name' => 'Cancelado',
            'description' => 'El ticket ha sido cancelado antes de su resolución.',
            'status' => true
        ]);
    }
}