<?php

namespace App\Console\Commands;

use App\Models\CategoriaFisica;
use App\Models\LibroFisico;
use Illuminate\Console\Command;
use ReflectionClass;

class ReclasificarDeweyLibrosFisicos extends Command
{
    protected $signature = 'libros:reclasificar-dewey
                            {archivo=catalogo.csv : Nombre del CSV (en storage/app/) o ruta absoluta}';

    protected $description = 'Reclasifica los libros físicos de centena Dewey (DEW-300) a decena (DEW-370), releyendo el catálogo CSV original';

    /**
     * Libros recuperados en la Fase 1 (colisiones de accesión): no aparecen como
     * la primera fila de su código en el CSV, así que se reclasifican aparte con
     * su número Dewey ya identificado durante esa fase.
     */
    private const RECUPERADOS = [
        'BIB-679-B' => '220',
        'BIB-692-B' => '800.1',
        'BIB-698-B' => '200',
        'BIB-1262-B' => '372.7',
        'BIB-1120-B' => '869.5',
        'BIB-1081-B' => '848',
        'DOC-940-B' => '155.4',
        'BIB-270-B' => '305.235',
        'BIB-645-B' => '374.48',
        'DOC-99-B' => '370',
        'SW-938-B' => '869.5',
    ];

    public function handle(): int
    {
        $ruta = $this->argument('archivo');
        if (!file_exists($ruta)) {
            $ruta = storage_path('app/' . ltrim($ruta, '/\\'));
        }
        if (!file_exists($ruta)) {
            $this->error('Archivo no encontrado.');
            return self::FAILURE;
        }

        // Reutiliza la lógica exacta de generación de "codigo" del importador original,
        // incluyendo su contador interno para accesiones vacías (codigoSeq), y el mismo
        // filtrado de filas, para que el contador avance idéntico al import original.
        $importer = new ImportarLibrosFisicos();
        $ref = new ReflectionClass($importer);
        $mCodigo = $ref->getMethod('codigo');
        $mCodigo->setAccessible(true);
        $mTitulo = $ref->getMethod('titulo');
        $mTitulo->setAccessible(true);

        $handle = fopen($ruta, 'r');
        $bom = fread($handle, 3);
        if ($bom !== "\xEF\xBB\xBF") {
            fseek($handle, 0);
        }
        fgetcsv($handle, 0, ';');

        $vistos = [];
        $reclasificados = 0;
        $sinDewey = 0;
        $noEncontrados = 0;

        while (($fila = fgetcsv($handle, 0, ';')) !== false) {
            $cols = array_pad($fila, 16, '');
            $tipo = trim($cols[0]);
            $callNum = trim($cols[1]);
            $dewey = trim($cols[2]);
            $acces = trim($cols[3]);
            $tituloR = trim($cols[7]);

            if (empty($tipo) || empty($tituloR)) {
                continue;
            }

            $nombre = $mTitulo->invoke($importer, $tituloR);
            if (mb_strlen($nombre) < 2) {
                continue;
            }

            $codigo = $mCodigo->invoke($importer, $tipo, $callNum, $acces);
            if (empty($codigo)) {
                continue;
            }

            if (isset($vistos[$codigo])) {
                continue; // colisión: esta fila no fue la que creó el libro
            }
            $vistos[$codigo] = true;

            $libro = LibroFisico::where('codigo', $codigo)->first();
            if (!$libro) {
                $noEncontrados++;
                continue;
            }

            $this->reclasificarLibro($libro, $dewey, $reclasificados, $sinDewey);
        }
        fclose($handle);

        // Los 11 libros recuperados en la Fase 1 (segunda fila de una colisión de accesión)
        foreach (self::RECUPERADOS as $codigo => $dewey) {
            $libro = LibroFisico::where('codigo', $codigo)->first();
            if (!$libro) {
                $this->warn("  Recuperado no encontrado: {$codigo}");
                continue;
            }
            $this->reclasificarLibro($libro, $dewey, $reclasificados, $sinDewey);
        }

        // Retirar las categorías de centena (DEW-000..DEW-900) que quedaron sin libros
        $retiradas = 0;
        foreach (CategoriaFisica::where('codigo', 'like', 'DEW-%')->get() as $cat) {
            if (preg_match('/^DEW-\d00$/', $cat->codigo) && $cat->librosFisicos()->doesntExist()) {
                $cat->delete();
                $retiradas++;
            }
        }

        $this->newLine();
        $this->table(
            ['Reclasificados', 'Sin Dewey (quedan sin categoría)', 'Códigos no encontrados', 'Categorías de centena retiradas'],
            [[$reclasificados, $sinDewey, $noEncontrados, $retiradas]]
        );

        return self::SUCCESS;
    }

    private function reclasificarLibro(LibroFisico $libro, string $dewey, int &$reclasificados, int &$sinDewey): void
    {
        $decena = CategoriaFisica::decenaDesdeNumeroDewey($dewey);

        if ($decena === null) {
            $libro->update(['categoria_fisica_id' => null]);
            $sinDewey++;
            return;
        }

        $cat = CategoriaFisica::updateOrCreate(
            ['codigo' => CategoriaFisica::codigoParaDecena($decena)],
            [
                'nombre' => CategoriaFisica::nombreParaDecena($decena),
                'descripcion' => 'Clasificación Dewey ' . $decena . '-' . ($decena + 9),
            ]
        );

        $libro->update(['categoria_fisica_id' => $cat->id]);
        $reclasificados++;
    }
}
