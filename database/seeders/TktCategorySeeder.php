<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\TktCategory;

class TktCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Desactivar revisión de claves foráneas
        DB::statement('SET FOREIGN_KEY_CHECKS=0;'); 

        // Usar delete() para vaciar la tabla
        TktCategory::query()->delete(); 

        // Reiniciar el contador AUTO_INCREMENT
        DB::statement('ALTER TABLE tkt_categories AUTO_INCREMENT = 1;');

        // Volver a activar la revisión
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Inserta las nuevas categorías (ejemplos)
        TktCategory::create([
            'name' => 'Hardware',
            'description' => 'Problemas relacionados con componentes físicos (PC, impresora, monitor, etc.).',
            'status' => true
        ]);
        
        TktCategory::create([
            'name' => 'Software',
            'description' => 'Problemas con aplicaciones, sistemas operativos o licencias.',
            'status' => true
        ]);
        
        TktCategory::create([
            'name' => 'Redes',
            'description' => 'Problemas de conectividad, internet, Wi-Fi, VPN, etc.',
            'status' => true
        ]);
        
        TktCategory::create([
            'name' => 'Cuentas y Accesos',
            'description' => 'Problemas para iniciar sesión, restablecimiento de contraseñas, permisos.',
            'status' => true
        ]);
        
        TktCategory::create([
            'name' => 'Consulta General',
            'description' => 'Dudas o preguntas que no encajan en otras categorías.',
            'status' => true
        ]);
    }
}