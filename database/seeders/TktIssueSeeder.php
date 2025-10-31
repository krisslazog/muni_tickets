<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TktIssue;
use Illuminate\Support\Facades\DB;

class TktIssueSeeder extends Seeder
{
    public function run(): void
    {
        // Limpia la tabla y reinicia el autoincremento de forma segura
        DB::statement('SET FOREIGN_KEY_CHECKS=0;'); 
        TktIssue::query()->delete(); 
        DB::statement('ALTER TABLE tkt_issues AUTO_INCREMENT = 1;');
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Crea las opciones del combo:
        TktIssue::create(['name' => 'Problema con el inicio de sesión', 'description' => 'El usuario no puede acceder al sistema.']);
        TktIssue::create(['name' => 'Fallo de Correo Electrónico (Email)', 'description' => 'El buzón no funciona o el envío/recepción falla.']);
        TktIssue::create(['name' => 'Error de Impresora/Escáner', 'description' => 'Dispositivo que no responde o falla de configuración.']);
        TktIssue::create(['name' => 'Solicitud de Mantenimiento de Equipo', 'description' => 'Solicitud de revisión de hardware (PC, monitor, etc.).']);
        TktIssue::create(['name' => 'Reporte de Internet Lento/Caído', 'description' => 'Problemas con la conexión de red o velocidad.']);
        TktIssue::create(['name' => 'Otros - Problema de Aplicación', 'description' => 'Cualquier otro error no listado en una aplicación específica.']);
    }
}