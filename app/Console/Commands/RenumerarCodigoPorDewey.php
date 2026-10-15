<?php

namespace App\Console\Commands;

use App\Models\CategoriaFisica;
use App\Models\LibroFisico;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RenumerarCodigoPorDewey extends Command
{
    protected $signature = 'libros:renumerar-codigo-por-dewey {--confirmar : Ejecuta el cambio (sin esta opción solo muestra una vista previa)}';

    protected $description = 'Reemplaza el código de accesión histórico de cada libro físico por uno basado en su clasificación: '
        . 'tipo de material (REF-0001, SW-0001…) para colecciones propias, o decena Dewey (300-0001…) para bibliografía general.';

    public function handle(): int
    {
        $grupos = $this->agruparLibros();

        $this->table(
            ['Prefijo', 'Cantidad', 'Ejemplo'],
            collect($grupos)->map(fn ($libros, $prefijo) => [
                $prefijo,
                count($libros),
                $prefijo . '-' . str_pad(1, 4, '0', STR_PAD_LEFT) . ' … ' . $prefijo . '-' . str_pad(count($libros), 4, '0', STR_PAD_LEFT),
            ])->values()->all()
        );

        if (!$this->option('confirmar')) {
            $this->warn('Vista previa únicamente. Vuelve a ejecutar con --confirmar para aplicar los cambios.');
            return self::SUCCESS;
        }

        if (!$this->confirm('Esto reemplaza el código de ' . LibroFisico::count() . ' libros y NO se puede deshacer desde la app (solo con el backup SQL). ¿Continuar?')) {
            return self::SUCCESS;
        }

        DB::transaction(function () use ($grupos) {
            // Fase 1: placeholders únicos temporales, para evitar choques con el índice único
            // mientras se reasignan los códigos definitivos.
            foreach ($grupos as $libros) {
                foreach ($libros as $libro) {
                    $libro->update(['codigo' => 'TMP-' . $libro->id]);
                }
            }

            // Fase 2: código definitivo por grupo, en orden autor/nombre.
            foreach ($grupos as $prefijo => $libros) {
                foreach ($libros as $i => $libro) {
                    $codigo = $prefijo . '-' . str_pad($i + 1, 4, '0', STR_PAD_LEFT);
                    $libro->update(['codigo' => mb_substr($codigo, 0, 50)]);
                }
            }
        });

        $this->info('Códigos renumerados correctamente.');
        return self::SUCCESS;
    }

    /**
     * Agrupa los libros por prefijo (tipo de material propio, decena Dewey, o "PF"
     * si no tiene ninguno de los dos), ordenados por autor y nombre dentro de cada grupo
     * — el mismo criterio que usaría un bibliotecario para ordenar el anaquel.
     */
    private function agruparLibros(): array
    {
        $libros = LibroFisico::with('categoriaFisica')
            ->orderBy('autor')
            ->orderBy('nombre')
            ->get();

        $grupos = [];
        foreach ($libros as $libro) {
            $grupos[$this->prefijo($libro)][] = $libro;
        }

        ksort($grupos);
        return $grupos;
    }

    private function prefijo(LibroFisico $libro): string
    {
        if ($libro->tipo_material && $libro->tipo_material !== 'BIB-GEN') {
            return $libro->tipo_material;
        }

        if ($libro->categoriaFisica && preg_match('/^DEW-(\d+)$/', $libro->categoriaFisica->codigo, $m)) {
            return $m[1];
        }

        return 'PF';
    }
}
