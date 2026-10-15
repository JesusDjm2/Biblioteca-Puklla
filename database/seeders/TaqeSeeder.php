<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TaqeCategoria;
use App\Models\TaqeTrabajo;

class TaqeSeeder extends Seeder
{
    public function run(): void
    {
        $cats = [
            [
                'nombre' => 'Saberes y Prácticas Ancestrales',
                'slug' => 'saberes-ancestrales',
                'descripcion' => 'Investigaciones sobre la siembra, abuelos, cosmovisión y el ciclo agrofestivo.',
                'color' => '#c78e41',
                'icono' => 'fa-leaf',
                'orden' => 1,
            ],
            [
                'nombre' => 'Estrategias y Materiales Pedagógicos',
                'slug' => 'materiales-pedagogicos',
                'descripcion' => 'Herramientas de planificación, programación y cartillas para el aula bilingüe.',
                'color' => '#2e7d32',
                'icono' => 'fa-book-reader',
                'orden' => 2,
            ],
            [
                'nombre' => 'Cuentos, Mitos y Tradición Oral',
                'slug' => 'tradicion-oral',
                'descripcion' => 'Narraciones, relatos orales y leyendas recopiladas en quechua y castellano.',
                'color' => '#1565c0',
                'icono' => 'fa-commenting',
                'orden' => 3,
            ],
            [
                'nombre' => 'Salud Comunitaria y Plantas Medicinales',
                'slug' => 'plantas-medicinales',
                'descripcion' => 'Saberes médicos ancestrales, curación tradicional y uso de flora medicinal.',
                'color' => '#6a1b9a',
                'icono' => 'fa-heartbeat',
                'orden' => 4,
            ],
        ];

        $catMap = [];
        foreach ($cats as $c) {
            $catMap[$c['slug']] = TaqeCategoria::firstOrCreate(['slug' => $c['slug']], $c);
        }

        $trabajos = [
            [
                'categoria_slug' => 'saberes-ancestrales',
                'titulo' => 'Los Apus tienen vida',
                'autores' => 'Ruth Mery Quispicusi, Bill Fredy Arones',
                'descripcion' => 'Investigación y animación en stopmotion que visibiliza la relación comunitaria con los Apus tutelares y el respeto a la Madre Tierra.',
                'formato_principal' => 'video',
                'idioma' => 'castellano',
                'año_periodo' => '2024',
                'ciclo_carrera' => 'II Ciclo Educación Inicial EIB',
                'url_video' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                'destacado' => true,
            ],
            [
                'categoria_slug' => 'materiales-pedagogicos',
                'titulo' => 'Cartillas de Señas e Indicadores Naturales',
                'autores' => 'Franklin Medina',
                'descripcion' => 'Cartillas pedagógicas que sistematizan las señas del clima, floración de plantas y comportamiento animal para la siembra comunitaria.',
                'formato_principal' => 'pdf',
                'idioma' => 'bilingue',
                'año_periodo' => '2024',
                'ciclo_carrera' => 'IV Ciclo Educación Primaria EIB',
                'destacado' => true,
            ],
            [
                'categoria_slug' => 'saberes-ancestrales',
                'titulo' => 'Wankay de despedida a la siembra de maíz',
                'autores' => 'Estudiantes de Práctica Pedagógica EIB',
                'descripcion' => 'Registro sonoro y canto tradicional quechua de despedida al trabajo colectivo de siembra en las parcelas familiares.',
                'formato_principal' => 'audio',
                'idioma' => 'quechua',
                'año_periodo' => '2022',
                'ciclo_carrera' => 'III Ciclo EIB',
                'destacado' => true,
            ],
            [
                'categoria_slug' => 'plantas-medicinales',
                'titulo' => 'Curación de Huesos a través de plantas medicinales',
                'autores' => 'Jakeline Aparicio, Luz Mery Conza',
                'descripcion' => 'Documentación de prácticas de curación tradicional y aplicación de plantas medicinales para fracturas y dolores articulares.',
                'formato_principal' => 'pdf',
                'idioma' => 'bilingue',
                'año_periodo' => '2024',
                'ciclo_carrera' => 'V Ciclo Educación Inicial EIB',
                'destacado' => true,
            ],
            [
                'categoria_slug' => 'tradicion-oral',
                'titulo' => 'Cuentos y Leyendas de Nuestras Comunidades',
                'autores' => 'Judith Turpo, Maribel Coyo',
                'descripcion' => 'Compilado ilustrado de relatos orales transmitidos por abuelas y abuelos en comunidades quechuahablantes del Cusco.',
                'formato_principal' => 'multimedia',
                'idioma' => 'bilingue',
                'año_periodo' => '2023',
                'ciclo_carrera' => 'II Ciclo Educación Primaria EIB',
                'destacado' => false,
            ],
            [
                'categoria_slug' => 'tradicion-oral',
                'titulo' => 'Historia de la Laguna Auquis Anyarate',
                'autores' => 'Abraham Gamarra',
                'descripcion' => 'Testimonio en lengua originaria quechua sobre los misterios, memoria y encantos de la laguna Auquis.',
                'formato_principal' => 'video',
                'idioma' => 'quechua',
                'año_periodo' => '2022',
                'ciclo_carrera' => 'I Ciclo Quechua',
                'destacado' => false,
            ],
            [
                'categoria_slug' => 'plantas-medicinales',
                'titulo' => 'Narraciones sobre Plantas Medicinales Autóctonas',
                'autores' => 'Felipe Huisa',
                'descripcion' => 'Sistematización y diagramación gráfica de recetas ancestrales a base de hierbas medicinales de los valles andinos.',
                'formato_principal' => 'pdf',
                'idioma' => 'quechua',
                'año_periodo' => '2023',
                'ciclo_carrera' => 'III Ciclo EIB',
                'destacado' => false,
            ],
            [
                'categoria_slug' => 'saberes-ancestrales',
                'titulo' => 'El Chúyay de las ovejas: Ritos de fertilidad y protección',
                'autores' => 'Marina Chocata, María Rosa Challhua, Verónica Qqenta',
                'descripcion' => 'Registro sonoro y etnográfico de los cantos y rituales de agradecimiento al rebaño en el ciclo ganadero andino.',
                'formato_principal' => 'audio',
                'idioma' => 'quechua',
                'año_periodo' => '2024',
                'ciclo_carrera' => 'IV Ciclo Educación Inicial EIB',
                'destacado' => true,
            ],
        ];

        foreach ($trabajos as $t) {
            $catSlug = $t['categoria_slug'];
            unset($t['categoria_slug']);
            $cat = $catMap[$catSlug] ?? null;
            if (!$cat) continue;

            $t['categoria_id'] = $cat->id;
            TaqeTrabajo::firstOrCreate(['titulo' => $t['titulo']], $t);
        }
    }
}
