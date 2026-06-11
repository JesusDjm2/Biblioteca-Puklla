<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Reassign categoria_libro from duplicate libros to the canonical (lowest id) libro
        DB::statement('
            INSERT IGNORE INTO categoria_libro (categoria_id, libro_id, created_at, updated_at)
            SELECT cl.categoria_id, l_keep.id, NOW(), NOW()
            FROM categoria_libro cl
            JOIN libros l_dup  ON l_dup.id  = cl.libro_id
            JOIN libros l_keep ON l_keep.identificador = l_dup.identificador
                               AND l_keep.id < l_dup.id
        ');

        // Delete duplicate libros, keep the one with the lowest id per identificador
        DB::statement('
            DELETE l1 FROM libros l1
            INNER JOIN libros l2 ON l1.identificador = l2.identificador AND l1.id > l2.id
        ');

        // Add unique constraint
        Schema::table('libros', function (Blueprint $table) {
            $table->unique('identificador', 'libros_identificador_unique');
        });
    }

    public function down(): void
    {
        Schema::table('libros', function (Blueprint $table) {
            $table->dropUnique('libros_identificador_unique');
        });
    }
};
