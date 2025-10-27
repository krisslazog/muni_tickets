<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\TktPriority; // Asegúrate que el namespace del modelo sea correcto

class TktPrioritySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Desactivar revisión de claves foráneas
        DB::statement('SET FOREIGN_KEY_CHECKS=0;'); 

        // Usar delete() en lugar de truncate() para vaciar la tabla
        TktPriority::query()->delete(); 

        // Reiniciar el contador AUTO_INCREMENT (opcional pero recomendado después de delete)
        DB::statement('ALTER TABLE tkt_priorities AUTO_INCREMENT = 1;');

        // Volver a activar la revisión (importante)
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Inserta las nuevas prioridades
        TktPriority::create([
            'name' => 'Baja',
            'description' => 'Incidencia menor o consulta. No afecta la operatividad principal.',
            'status' => true
        ]);
        
        TktPriority::create([
            'name' => 'Media',
            'description' => 'Incidencia que afecta parcialmente las funciones, pero permite continuar operando.', // <-- DESCRIPCIÓN AÑADIDA
            'status' => true
        ]);
        
        TktPriority::create([
            'name' => 'Alta',
            'description' => 'Incidencia importante que afecta significativamente la operatividad. Requiere atención prioritaria.', // <-- DESCRIPCIÓN AÑADIDA
            'status' => true
        ]);
        
        TktPriority::create([
            'name' => 'Urgente',
            'description' => 'Incidencia crítica que detiene por completo la operatividad o causa pérdida de datos. Requiere atención inmediata.', // <-- DESCRIPCIÓN AÑADIDA
            'status' => true
        ]);
    }
}