<?php

namespace App\Console\Commands;

use App\Models\CategoriaFisica;
use App\Models\LibroFisico;
use Illuminate\Console\Command;

class ImportarLibrosFisicos extends Command
{
    protected $signature = 'libros:importar-fisicos
                            {archivo : Nombre del CSV (en storage/app/) o ruta absoluta}
                            {--limite=0 : Número máximo de registros a importar (0 = todos)}';

    protected $description = 'Importa libros físicos desde el catálogo CSV de la biblioteca';

    private array $categoriaCache = [];
    private array $codigoSeq      = [];

    public function handle(): int
    {
        $ruta = $this->argument('archivo');

        if (!file_exists($ruta)) {
            $ruta = storage_path('app/' . ltrim($ruta, '/\\'));
        }

        if (!file_exists($ruta)) {
            $this->error("Archivo no encontrado. Coloca el CSV en storage/app/ o indica la ruta completa.");
            return self::FAILURE;
        }

        $limite = (int) $this->option('limite');

        $handle = fopen($ruta, 'r');

        // Eliminar BOM UTF-8 si existe
        $bom = fread($handle, 3);
        if ($bom !== "\xEF\xBB\xBF") {
            fseek($handle, 0);
        }

        // Saltar fila de cabecera
        fgetcsv($handle, 0, ';');

        [$importados, $omitidos, $errores, $total] = [0, 0, 0, 0];

        $this->info("Importando registros del catálogo...");

        while (($fila = fgetcsv($handle, 0, ';')) !== false) {
            if ($limite > 0 && $total >= $limite) {
                break;
            }
            $total++;

            try {
                $this->procesarFila($fila) ? $importados++ : $omitidos++;
            } catch (\Exception $e) {
                $errores++;
                if ($this->getOutput()->isVerbose()) {
                    $this->warn("  Fila {$total}: " . $e->getMessage());
                }
            }

            if ($total % 300 === 0) {
                $this->line("  · Procesados: {$total}  Importados: {$importados}");
            }
        }

        fclose($handle);

        $this->newLine();
        $this->table(
            ['Total procesados', 'Importados', 'Omitidos', 'Errores'],
            [[$total, $importados, $omitidos, $errores]]
        );

        return self::SUCCESS;
    }

    // ─────────────────────────────────────────────────────────────────────────
    //  Procesamiento de una fila
    // ─────────────────────────────────────────────────────────────────────────

    private function procesarFila(array $cols): bool
    {
        $cols = array_pad($cols, 16, '');

        $tipo     = trim($cols[0]);
        $callNum  = trim($cols[1]);
        $dewey    = trim($cols[2]);
        $acces    = trim($cols[3]);
        $autorP   = trim($cols[5]);   // autor personal
        $autorC   = trim($cols[6]);   // autor corporativo / institución
        $tituloR  = trim($cols[7]);
        $paginasR = trim($cols[8]);
        $pubRaw   = trim($cols[11]);
        $anioRaw  = trim($cols[13]);

        if (empty($tipo) || empty($tituloR)) {
            return false;
        }

        $nombre = $this->titulo($tituloR);
        if (mb_strlen($nombre) < 2) {
            return false;
        }

        $codigo = $this->codigo($tipo, $callNum, $acces);
        if (empty($codigo)) {
            return false;
        }

        // Si el libro ya existe, no se toca (evita colisiones de accesión entre libros distintos)
        if (LibroFisico::where('codigo', $codigo)->exists()) {
            return false; // no se creó uno nuevo → omitido
        }

        $autor     = $this->autor($autorP, $autorC);
        $editorial = $this->editorial($pubRaw);
        $anio      = $this->anio($anioRaw);
        $paginas   = $this->paginas($paginasR);
        $catDewey  = $this->categoriaDewey($dewey);

        LibroFisico::create([
            'nombre'              => mb_substr($nombre, 0, 255),
            'autor'               => mb_substr($autor ?: 'Desconocido', 0, 255),
            'editorial'           => $editorial ? mb_substr($editorial, 0, 255) : null,
            'anio_publicacion'    => $anio ?? (int) date('Y'),
            'numero_paginas'      => $paginas,
            'codigo'              => $codigo,
            'categoria_fisica_id' => $catDewey?->id,
            'tipo_material'       => $this->tipoMaterial($tipo),
        ]);

        return true;
    }

    // ─────────────────────────────────────────────────────────────────────────
    //  Parsers de campos MARC
    // ─────────────────────────────────────────────────────────────────────────

    private function titulo(string $raw): string
    {
        // Extraer contenido después de ^t o ^T
        if (preg_match('/\^[tT](.+)/s', $raw, $m)) {
            $t = $m[1];
        } else {
            $t = $raw;
        }

        // Eliminar subcampos MARC restantes
        $t = preg_replace('/\^[a-zA-Z][^\^]*/s', '', $t);

        // Normalizar <El> <La> <Los> → El La Los
        $t = preg_replace('/<([^>]+)>/', '$1', $t);

        return trim(preg_replace('/\s+/', ' ', $t));
    }

    private function autor(string $personal, string $corp): string
    {
        $partes = [];

        // ── Autores personales (col 5) ───────────────────────────────────────
        if (!empty($personal)) {
            if (str_contains($personal, '^')) {
                // Separar por ^a o ^A (cada bloque es un autor)
                $bloques = preg_split('/\^[aA]/', $personal, -1, PREG_SPLIT_NO_EMPTY);
                foreach (array_slice($bloques, 0, 2) as $bloque) {
                    // apellido^nNombre (^n o ^N)
                    preg_match('/^([^\^]+?)(?:\^[nN]([^\^]*))?(?:\^|$)/s', $bloque, $m);
                    $ap  = trim(preg_replace('/\^[a-zA-Z].*/s', '', $m[1] ?? ''));
                    $nom = trim(preg_replace('/\^[a-zA-Z].*/s', '', $m[2] ?? ''));

                    if ($ap && !in_array(strtolower($ap), ['y otros', 'et al.', 'xy otros'])) {
                        $partes[] = $nom ? "{$nom} {$ap}" : $ap;
                    }
                }
            } else {
                // Texto plano sin etiquetas MARC
                $partes[] = trim($personal);
            }
        }

        // ── Autor corporativo (col 6) solo si no hay personal ────────────────
        if (empty($partes) && !empty($corp)) {
            if (preg_match('/\^[aA]([^\^]+)/', $corp, $m)) {
                $inst = trim(preg_replace('/\^[a-zA-Z][^\^]*/s', '', $m[1]));
                if ($inst) {
                    $partes[] = $inst;
                }
            } elseif (!str_contains($corp, '^')) {
                $partes[] = trim($corp);
            }
        }

        return implode(', ', array_filter($partes));
    }

    private function editorial(string $raw): ?string
    {
        // ^cLugar^eEditorial^pPaís  →  extraer ^e
        if (preg_match('/\^[eE]([^\^]+)/', $raw, $m)) {
            $ed = trim(preg_replace('/\^[a-zA-Z].*/s', '', $m[1]));
            return $ed ?: null;
        }
        return null;
    }

    private function anio(string $raw): ?int
    {
        // ^m1968  o  ^m[1998]  o  ^m[s.f.]
        if (preg_match('/\^[mM]\[?(\d{4})\]?/', $raw, $m)) {
            $y = (int) $m[1];
            return ($y >= 1400 && $y <= (int) date('Y') + 1) ? $y : null;
        }
        return null;
    }

    private function paginas(string $raw): ?int
    {
        // "48 p"  "^p129 p"  "487 P"  — NO capturar "9 T" (tomos)
        if (preg_match('/(\d+)\s*p\b/i', $raw, $m)) {
            $n = (int) $m[1];
            return ($n > 0 && $n < 9999) ? $n : null;
        }
        return null;
    }

    private function codigo(string $tipo, string $callNum, string $acces): string
    {
        // Descartar números en notación científica (artefacto de Excel, ej: 6.24045E+17)
        if (preg_match('/^[\d.]+[eE][+\-]?\d+$/i', $acces)) {
            $acces = '';
        }

        $acces   = preg_replace('/[^a-zA-Z0-9]/', '', $acces);
        $tipo    = strtoupper(preg_replace('/[^a-zA-Z]/', '', $tipo));
        $callNum = strtoupper(preg_replace('/[^a-zA-Z0-9]/', '', $callNum));

        if ($acces !== '') {
            return mb_substr("{$tipo}-{$acces}", 0, 50);
        }

        if ($callNum !== '') {
            $base = "{$tipo}-{$callNum}";
            $this->codigoSeq[$base] = ($this->codigoSeq[$base] ?? 0) + 1;
            return mb_substr($base . '-' . $this->codigoSeq[$base], 0, 50);
        }

        return '';
    }

    // ─────────────────────────────────────────────────────────────────────────
    //  Categorías
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Categoría Dewey (materia) a partir del número Dewey crudo, por decena.
     * Null si el libro no trae un número Dewey parseable (se clasifica solo por tipoMaterial()).
     */
    private function categoriaDewey(string $dewey): ?CategoriaFisica
    {
        $decena = CategoriaFisica::decenaDesdeNumeroDewey($dewey);
        if ($decena === null) {
            return null;
        }

        $codigo = CategoriaFisica::codigoParaDecena($decena);
        if (isset($this->categoriaCache[$codigo])) {
            return $this->categoriaCache[$codigo];
        }

        $cat = CategoriaFisica::firstOrCreate(
            ['codigo' => $codigo],
            [
                'nombre' => CategoriaFisica::nombreParaDecena($decena),
                'descripcion' => 'Clasificación Dewey ' . $decena . '-' . ($decena + 9),
            ]
        );

        return $this->categoriaCache[$codigo] = $cat;
    }

    /**
     * Tipo de material físico (independiente de la materia Dewey), a partir de la
     * columna "tipo" del catálogo MARC.
     */
    private function tipoMaterial(string $tipo): string
    {
        return match (strtoupper(trim($tipo))) {
            'DOC'                   => 'DOC',
            'TP', 'TI', 'TP/FON'    => 'TEX-PRI',
            'TS', 'TS/FON'          => 'TEX-SEC',
            'REF', 'REF/FON'        => 'REF',
            'ASO'                   => 'ASO',
            'CDS'                   => 'CDS',
            'SW'                    => 'SW',
            default                 => 'BIB-GEN',
        };
    }
}
