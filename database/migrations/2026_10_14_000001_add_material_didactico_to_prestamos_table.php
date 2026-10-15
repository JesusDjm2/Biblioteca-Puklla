<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Un préstamo ahora puede ser de un libro físico o de un material didáctico.
 * libro_fisico_id pasa a ser opcional (se usa SQL directo porque el proyecto
 * no tiene doctrine/dbal para ->change()), y "cantidad" registra cuántas
 * unidades se prestan (los libros siempre se prestan de a 1 ejemplar).
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE prestamos MODIFY libro_fisico_id BIGINT UNSIGNED NULL');

        Schema::table('prestamos', function (Blueprint $table) {
            if (!Schema::hasColumn('prestamos', 'material_didactico_id')) {
                $table->foreignId('material_didactico_id')->nullable()->after('libro_fisico_id')
                    ->constrained('materiales_didacticos')->restrictOnDelete();
            }
            if (!Schema::hasColumn('prestamos', 'cantidad')) {
                $table->unsignedInteger('cantidad')->default(1)->after('numero_ejemplar');
            }
        });
    }

    public function down(): void
    {
        Schema::table('prestamos', function (Blueprint $table) {
            if (Schema::hasColumn('prestamos', 'material_didactico_id')) {
                $table->dropConstrainedForeignId('material_didactico_id');
            }
            if (Schema::hasColumn('prestamos', 'cantidad')) {
                $table->dropColumn('cantidad');
            }
        });

        DB::statement('ALTER TABLE prestamos MODIFY libro_fisico_id BIGINT UNSIGNED NOT NULL');
    }
};
