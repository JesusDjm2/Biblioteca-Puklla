<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * El N° de ejemplar pasó de correlativo automático (entero) a una etiqueta
 * elegida libremente por el bibliotecario (ej. "A", "B", "Sala"), así que la
 * columna necesita ser texto. No se usa Schema::table()->change() porque el
 * proyecto no tiene doctrine/dbal instalado.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE prestamos MODIFY numero_ejemplar VARCHAR(50) NULL');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE prestamos MODIFY numero_ejemplar INT UNSIGNED NULL');
    }
};
