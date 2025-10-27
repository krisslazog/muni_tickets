<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Area;

class AreaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Desactivar claves foráneas
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Vaciar la tabla y reiniciar ID
        Area::query()->delete();
        DB::statement('ALTER TABLE areas AUTO_INCREMENT = 1;'); // Asume que la tabla se llama 'areas'

        // Volver a activar claves foráneas
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Crear las áreas con descripciones
        Area::create(['name' => 'PROCURADURÍA PÚBLICA MUNICIPAL', 'description' => 'Defensa legal de los intereses municipales.']);
        Area::create(['name' => 'SECRETARIA GENERAL', 'description' => 'Gestión administrativa y documental central.']);
        Area::create(['name' => 'OFICINA DE ADMINISTRACIÓN DOCUMENTARIA, TRANSPARENCIA Y ARCHIVO', 'description' => 'Manejo de documentos, archivo y portal de transparencia.']);
        Area::create(['name' => 'OFICINA DE COMUNICACIONES E IMAGEN INSTITUCIONAL', 'description' => 'Gestión de la comunicación y la imagen de la municipalidad.']);
        Area::create(['name' => 'GERENCIA MUNICIPAL', 'description' => 'Dirección ejecutiva de la administración municipal.']);
        Area::create(['name' => 'OFICINA GENERAL DE ADMINISTRACIÓN', 'description' => 'Administración general de recursos y procesos internos.']);
        Area::create(['name' => 'OFICINA DE ABASTECIMIENTO Y CONTROL PATRIMONIAL', 'description' => 'Gestión de compras, bienes y patrimonio municipal.']);
        Area::create(['name' => 'OFICINA DE CONTABILIDAD', 'description' => 'Registro y control contable de las operaciones municipales.']);
        Area::create(['name' => 'OFICINA DE TESORERIA', 'description' => 'Gestión de ingresos, egresos y fondos municipales.']);
        Area::create(['name' => 'OFICINA GENERAL DE RECURSOS HUMANOS', 'description' => 'Gestión del personal municipal.']);
        Area::create(['name' => 'OFICINA DE SERVICIOS GENERALES, OPERACIONES Y MAESTRANZA', 'description' => 'Mantenimiento, logística y operaciones generales.']);
        Area::create(['name' => 'OFICINA DE TECNOLOGÍA DE LA INFORMACIÓN', 'description' => 'Soporte y gestión de sistemas informáticos y tecnológicos.']);
        Area::create(['name' => 'OFICINA DE PLANEAMIENTO, MODERNIZACIÓN Y COOPERACIÓN TÉCNICA', 'description' => 'Planificación estratégica, modernización y relaciones de cooperación.']);
        Area::create(['name' => 'OFICINA DE PRESUPUESTO Y PROGRAMACIÓN DE INVERSIONES', 'description' => 'Elaboración y control del presupuesto y proyectos de inversión.']);
        Area::create(['name' => 'OFICINA GENERAL DE ASESORÍA JURÍDICA', 'description' => 'Asesoramiento legal a las áreas municipales.']);
        Area::create(['name' => 'GERENCIA DE ADMINISTRACIÓN TRIBUTARIA', 'description' => 'Gestión de los tributos municipales.']);
        Area::create(['name' => 'SUBGERENCIA DE REGISTRO, FISCALIZACIÓN TRIBUTARIA Y ORIENTACIÓN AL CONTRIBUYENTE', 'description' => 'Registro de contribuyentes, fiscalización y atención.']);
        Area::create(['name' => 'SUBGERENCIA DE RECAUDACIÓN TRIBUTARIA', 'description' => 'Cobro de impuestos y tasas municipales.']);
        Area::create(['name' => 'SUBGERENCIA DE EJECUTORIA COACTIVA', 'description' => 'Cobranza coactiva de deudas tributarias.']);
        Area::create(['name' => 'GERENCIA DE DESARROLLO URBANO', 'description' => 'Planificación y control del crecimiento urbano.']);
        Area::create(['name' => 'SUBGERENCIA DE OBRAS PÚBLICAS E INVERSIONES', 'description' => 'Ejecución y supervisión de obras públicas.']);
        Area::create(['name' => 'SUBGERENCIA DE OBRAS PRIVADAS, CATASTRO Y GESTIÓN DEL TERRITORIO', 'description' => 'Licencias de construcción, catastro y ordenamiento territorial.']);
        Area::create(['name' => 'SUBGERENCIA DE GESTIÓN DEL RIESGO DE DESASTRES', 'description' => 'Prevención y atención de emergencias y desastres.']);
        Area::create(['name' => 'GERENCIA DE SEGURIDAD CIUDADANA Y VIAL', 'description' => 'Coordinación de acciones de seguridad y tránsito.']);
        Area::create(['name' => 'SUBGERENCIA DE SERENAZGO', 'description' => 'Vigilancia y patrullaje municipal.']);
        Area::create(['name' => 'SUBGERENCIA DE TRÁNSITO, TRANSPORTE Y SEGURIDAD VIAL', 'description' => 'Regulación del tránsito y transporte local.']);
        Area::create(['name' => 'GERENCIA DE GESTIÓN AMBIENTAL', 'description' => 'Protección y conservación del medio ambiente local.']);
        Area::create(['name' => 'SUBGERENCIA DE LIMPIEZA PÚBLICA', 'description' => 'Recojo de residuos sólidos y limpieza de espacios públicos.']);
        Area::create(['name' => 'SUBGERENCIA DE ORNATO Y ÁREAS VERDES', 'description' => 'Mantenimiento de parques, jardines y ornato público.']);
        Area::create(['name' => 'GERENCIA DE DESARROLLO ECONÓMICO', 'description' => 'Promoción de actividades económicas y empleo.']);
        Area::create(['name' => 'SUBGERENCIA DE COMERCIALIZACIÓN Y PROMOCIÓN EMPRESARIAL', 'description' => 'Fomento del comercio y apoyo a emprendedores.']);
        Area::create(['name' => 'SUBGERENCIA DE FISCALIZACIÓN ADMINISTRATIVA', 'description' => 'Control del cumplimiento de normativas municipales.']);
        Area::create(['name' => 'GERENCIA DE LA MUJER Y DESARROLLO SOCIAL', 'description' => 'Programas y servicios sociales, con enfoque de género.']);
        Area::create(['name' => 'SUBGERENCIA DE SALUD Y BIENESTAR SOCIAL', 'description' => 'Servicios de salud y programas de bienestar.']);
        Area::create(['name' => 'SUBGERENCIA DE PARTICIPACIÓN VECINAL', 'description' => 'Fomento de la organización y participación ciudadana.']);
        Area::create(['name' => 'SUBGERENCIA DE PROGRAMAS SOCIALES ALIMENTARIOS', 'description' => 'Gestión de programas de asistencia alimentaria.']);
        Area::create(['name' => 'SUBGERENCIA DE JUVENTUD, EDUCACIÓN, CULTURA Y DEPORTE', 'description' => 'Promoción de actividades para jóvenes y la comunidad en general.']);
    }
}