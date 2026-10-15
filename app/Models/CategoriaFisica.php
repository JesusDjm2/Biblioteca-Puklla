<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoriaFisica extends Model
{
    use HasFactory;

    protected $table = 'categorias_fisicas';

    protected $fillable = [
        'nombre',
        'codigo',
        'descripcion',
    ];

    public function librosFisicos()
    {
        return $this->hasMany(LibroFisico::class);
    }

    /**
     * Color de acento asociado a un código de categoría Dewey (DEW-NNN), usado para
     * colorear tarjetas, menús y encabezados. Se agrupa por centena (primer dígito),
     * independientemente de si NNN es una decena (370) o una centena (300).
     */
    public static function colorForCodigo(?string $codigo): string
    {
        if ($codigo && preg_match('/^DEW-(\d)/', $codigo, $m)) {
            return [
                '0' => '#6366f1',
                '1' => '#a855f7',
                '2' => '#eab308',
                '3' => '#22c55e',
                '4' => '#14b8a6',
                '5' => '#06b6d4',
                '6' => '#f97316',
                '7' => '#ef4444',
                '8' => '#ec4899',
                '9' => '#92400e',
            ][$m[1]] ?? '#cb9042';
        }

        return '#cb9042';
    }

    /**
     * Tipos de material físico (independientes de la clasificación Dewey por materia):
     * describen qué es el ítem, no de qué trata. Clave = valor guardado en
     * libros_fisicos.tipo_material.
     */
    public const TIPOS_MATERIAL = [
        'TEX-PRI' => 'Texto escolar - Primaria',
        'TEX-SEC' => 'Texto escolar - Secundaria',
        'REF'     => 'Obra de referencia',
        'DOC'     => 'Documento / informe',
        'ASO'     => 'Publicación institucional',
        'CDS'     => 'Material audiovisual (CD/DVD)',
        'SW'      => 'Sala de lectura',
        'BIB-GEN' => 'Bibliografía general',
    ];

    public static function nombreParaTipoMaterial(?string $tipo): ?string
    {
        return $tipo ? (self::TIPOS_MATERIAL[$tipo] ?? $tipo) : null;
    }

    public static function colorForTipoMaterial(?string $tipo): string
    {
        return match ($tipo) {
            'TEX-PRI' => '#10b981',
            'TEX-SEC' => '#0ea5e9',
            'REF'     => '#f59e0b',
            'DOC'     => '#64748b',
            'ASO'     => '#7c3aed',
            'CDS'     => '#0891b2',
            'SW'      => '#be185d',
            'BIB-GEN' => '#78716c',
            default   => '#cb9042',
        };
    }

    /**
     * Divisiones estándar de la Clasificación Decimal Dewey (2.° resumen, 100 divisiones).
     * Clave = decena (000, 010, ... 990). Se usa para nombrar categorías al reclasificar
     * o importar libros por decena en vez de por centena.
     */
    public const DEWEY_DIVISIONES = [
        0 => 'Informática, información y obras generales',
        10 => 'Bibliografía',
        20 => 'Biblioteconomía y ciencias de la información',
        30 => 'Enciclopedias y libros de datos',
        40 => 'Sin asignar',
        50 => 'Publicaciones seriadas: revistas y boletines',
        60 => 'Asociaciones, organizaciones y museología',
        70 => 'Medios de comunicación, periodismo y edición',
        80 => 'Colecciones generales, misceláneas',
        90 => 'Manuscritos y libros raros',

        100 => 'Filosofía',
        110 => 'Metafísica',
        120 => 'Epistemología, causalidad, género humano',
        130 => 'Parapsicología y ocultismo',
        140 => 'Escuelas de pensamiento filosófico específicas',
        150 => 'Psicología',
        160 => 'Lógica',
        170 => 'Ética (filosofía moral)',
        180 => 'Filosofía antigua, medieval y oriental',
        190 => 'Filosofía occidental moderna',

        200 => 'Religión',
        210 => 'Filosofía y teoría de la religión',
        220 => 'La Biblia',
        230 => 'Cristianismo y teología cristiana',
        240 => 'Práctica y observancia cristiana',
        250 => 'Práctica pastoral cristiana y órdenes religiosas',
        260 => 'Iglesia cristiana, organización social y culto',
        270 => 'Historia del cristianismo',
        280 => 'Denominaciones cristianas',
        290 => 'Otras religiones y religión comparada',

        300 => 'Ciencias sociales, sociología y antropología',
        310 => 'Estadística general',
        320 => 'Ciencia política',
        330 => 'Economía',
        340 => 'Derecho',
        350 => 'Administración pública y ciencia militar',
        360 => 'Problemas y servicios sociales',
        370 => 'Educación',
        380 => 'Comercio, comunicaciones y transporte',
        390 => 'Costumbres, etiqueta y folclore',

        400 => 'Lenguaje',
        410 => 'Lingüística',
        420 => 'Inglés y anglosajón antiguo',
        430 => 'Alemán y lenguas afines',
        440 => 'Francés y lenguas afines',
        450 => 'Italiano, rumano y lenguas afines',
        460 => 'Español y portugués',
        470 => 'Latín e idiomas itálicos',
        480 => 'Griego clásico y moderno',
        490 => 'Otras lenguas',

        500 => 'Ciencia',
        510 => 'Matemáticas',
        520 => 'Astronomía',
        530 => 'Física',
        540 => 'Química',
        550 => 'Ciencias de la Tierra y geología',
        560 => 'Fósiles y vida prehistórica',
        570 => 'Ciencias de la vida; biología',
        580 => 'Plantas (botánica)',
        590 => 'Animales (zoología)',

        600 => 'Tecnología',
        610 => 'Medicina y salud',
        620 => 'Ingeniería',
        630 => 'Agricultura',
        640 => 'Economía doméstica y vida familiar',
        650 => 'Administración y relaciones públicas',
        660 => 'Ingeniería química',
        670 => 'Manufactura',
        680 => 'Manufactura para usos específicos',
        690 => 'Construcción',

        700 => 'Artes',
        710 => 'Urbanismo y paisajismo',
        720 => 'Arquitectura',
        730 => 'Escultura, cerámica y metalistería',
        740 => 'Dibujo y artes decorativas',
        750 => 'Pintura',
        760 => 'Artes gráficas',
        770 => 'Fotografía',
        780 => 'Música',
        790 => 'Deportes, juegos y entretenimiento',

        800 => 'Literatura',
        810 => 'Literatura estadounidense en inglés',
        820 => 'Literatura inglesa y anglosajona antigua',
        830 => 'Literatura alemana y afines',
        840 => 'Literatura francesa y afines',
        850 => 'Literatura italiana, rumana y afines',
        860 => 'Literatura española y portuguesa',
        870 => 'Literatura latina e itálica',
        880 => 'Literatura griega clásica y moderna',
        890 => 'Literatura de otras lenguas',

        900 => 'Historia',
        910 => 'Geografía y viajes',
        920 => 'Biografía y genealogía',
        930 => 'Historia del mundo antiguo (hasta ca. 499)',
        940 => 'Historia de Europa',
        950 => 'Historia de Asia',
        960 => 'Historia de África',
        970 => 'Historia de América del Norte',
        980 => 'Historia de América del Sur',
        990 => 'Historia de otras áreas',
    ];

    /**
     * Extrae la decena Dewey (0, 10, 20, ..., 990) de un número Dewey crudo del catálogo.
     * Devuelve null si el valor no es un número Dewey válido.
     */
    public static function decenaDesdeNumeroDewey(string $dewey): ?int
    {
        // Normaliza separadores decimales no estándar del catálogo (coma o guion
        // en vez de punto, ej. "343,62" o "305-235"), comunes en el CSV original.
        $normalizado = str_replace([',', '-'], '.', trim($dewey));
        $num = preg_replace('/[^0-9.]/', '', $normalizado);
        if ($num === '' || !is_numeric($num)) {
            return null;
        }

        $valor = (float) $num;
        if ($valor < 0 || $valor >= 1000) {
            return null; // fuera del rango válido de Dewey (000-999): dato corrupto/ambiguo
        }

        return (int) floor($valor / 10) * 10;
    }

    public static function nombreParaDecena(int $decena): string
    {
        return self::DEWEY_DIVISIONES[$decena] ?? "División Dewey {$decena}";
    }

    public static function codigoParaDecena(int $decena): string
    {
        return 'DEW-' . str_pad((string) $decena, 3, '0', STR_PAD_LEFT);
    }
}
