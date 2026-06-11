<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Remove duplicate seccions (keep lowest id per nombre)
        DB::statement('
            DELETE s1 FROM seccions s1
            INNER JOIN seccions s2 ON s1.nombre = s2.nombre AND s1.id > s2.id
        ');

        // Reassign categorias that point to a deleted (duplicate) seccion → canonical id
        DB::statement('
            UPDATE categorias c
            JOIN seccions s_keep ON s_keep.nombre = (
                SELECT nombre FROM seccions WHERE id = c.seccion_id LIMIT 1
            )
            SET c.seccion_id = s_keep.id
            WHERE s_keep.id < c.seccion_id
        ');

        Schema::table('seccions', function (Blueprint $table) {
            $table->unique('nombre', 'seccions_nombre_unique');
            $table->unique('url',    'seccions_url_unique');
        });

        // Move categoria_libro entries from duplicate categories to the canonical one,
        // then delete the duplicates
        DB::statement('
            INSERT IGNORE INTO categoria_libro (categoria_id, libro_id, created_at, updated_at)
            SELECT c_keep.id, cl.libro_id, NOW(), NOW()
            FROM categoria_libro cl
            JOIN categorias c_dup  ON c_dup.id  = cl.categoria_id
            JOIN categorias c_keep ON c_keep.nombre = c_dup.nombre AND c_keep.id < c_dup.id
        ');

        DB::statement('
            DELETE c1 FROM categorias c1
            INNER JOIN categorias c2 ON c1.nombre = c2.nombre AND c1.id > c2.id
        ');

        Schema::table('categorias', function (Blueprint $table) {
            $table->unique('nombre', 'categorias_nombre_unique');
            $table->unique('url',    'categorias_url_unique');
        });
    }

    public function down(): void
    {
        Schema::table('categorias', function (Blueprint $table) {
            $table->dropUnique('categorias_nombre_unique');
            $table->dropUnique('categorias_url_unique');
        });

        Schema::table('seccions', function (Blueprint $table) {
            $table->dropUnique('seccions_nombre_unique');
            $table->dropUnique('seccions_url_unique');
        });
    }
};
