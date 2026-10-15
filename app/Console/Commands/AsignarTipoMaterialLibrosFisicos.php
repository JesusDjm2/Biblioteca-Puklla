<?php

namespace App\Console\Commands;

use App\Models\LibroFisico;
use Illuminate\Console\Command;
use ReflectionClass;

class AsignarTipoMaterialLibrosFisicos extends Command
{
    protected $signature = 'libros:asignar-tipo-material
                            {archivo=catalogo.csv : Nombre del CSV (en storage/app/) o ruta absoluta}';

    protected $description = 'Asigna tipo_material a los libros físicos existentes, releyendo el catálogo CSV original';

    /**
     * Libros recuperados en la Fase 1 (colisiones de accesión): no son la primera
     * fila de su código en el CSV, se resuelven aparte con su tipo ya identificado.
     */
    private const RECUPERADOS = [
        'BIB-679-B' => 'BIB',
        'BIB-692-B' => 'BIB',
        'BIB-698-B' => 'BIB',
        'BIB-1262-B' => 'BIB',
        'BIB-1120-B' => 'BIB',
        'BIB-1081-B' => 'BIB',
        'DOC-940-B' => 'DOC',
        'BIB-270-B' => 'BIB',
        'BIB-645-B' => 'BIB',
        'DOC-99-B' => 'DOC',
        'SW-938-B' => 'SW',
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

        $importer = new ImportarLibrosFisicos();
        $ref = new ReflectionClass($importer);
        $mCodigo = $ref->getMethod('codigo');
        $mCodigo->setAccessible(true);
        $mTitulo = $ref->getMethod('titulo');
        $mTitulo->setAccessible(true);
        $mTipoMaterial = $ref->getMethod('tipoMaterial');
        $mTipoMaterial->setAccessible(true);

        $handle = fopen($ruta, 'r');
        $bom = fread($handle, 3);
        if ($bom !== "\xEF\xBB\xBF") {
            fseek($handle, 0);
        }
        fgetcsv($handle, 0, ';');

        $vistos = [];
        $asignados = 0;
        $noEncontrados = 0;

        while (($fila = fgetcsv($handle, 0, ';')) !== false) {
            $cols = array_pad($fila, 16, '');
            $tipo = trim($cols[0]);
            $callNum = trim($cols[1]);
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
                continue;
            }
            $vistos[$codigo] = true;

            $libro = LibroFisico::where('codigo', $codigo)->first();
            if (!$libro) {
                $noEncontrados++;
                continue;
            }

            $libro->update(['tipo_material' => $mTipoMaterial->invoke($importer, $tipo)]);
            $asignados++;
        }
        fclose($handle);

        foreach (self::RECUPERADOS as $codigo => $tipo) {
            $libro = LibroFisico::where('codigo', $codigo)->first();
            if (!$libro) {
                $this->warn("  Recuperado no encontrado: {$codigo}");
                continue;
            }
            $libro->update(['tipo_material' => $mTipoMaterial->invoke($importer, $tipo)]);
            $asignados++;
        }

        $this->newLine();
        $this->table(
            ['Asignados', 'Códigos no encontrados'],
            [[$asignados, $noEncontrados]]
        );

        return self::SUCCESS;
    }
}
