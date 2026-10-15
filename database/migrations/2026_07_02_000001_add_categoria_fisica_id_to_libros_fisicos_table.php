<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('libros_fisicos', function (Blueprint $table) {
            $table->foreignId('categoria_fisica_id')->nullable()->after('id')
                ->constrained('categorias_fisicas')->nullOnDelete();
        });

        DB::statement('
            UPDATE libros_fisicos lf
            JOIN categoria_fisica_libro_fisico p ON p.libro_fisico_id = lf.id
            SET lf.categoria_fisica_id = p.categoria_fisica_id
        ');
    }

    public function down(): void
    {
        Schema::table('libros_fisicos', function (Blueprint $table) {
            $table->dropConstrainedForeignId('categoria_fisica_id');
        });
    }
};
