<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('categoria_fisica_libro_fisico');
    }

    public function down(): void
    {
        Schema::create('categoria_fisica_libro_fisico', function (Blueprint $table) {
            $table->id();
            $table->foreignId('categoria_fisica_id')->constrained('categorias_fisicas')->cascadeOnDelete();
            $table->foreignId('libro_fisico_id')->constrained('libros_fisicos')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['categoria_fisica_id', 'libro_fisico_id'], 'catfis_libfis_unique');
        });

        DB::statement('
            INSERT INTO categoria_fisica_libro_fisico (categoria_fisica_id, libro_fisico_id, created_at, updated_at)
            SELECT categoria_fisica_id, id, NOW(), NOW()
            FROM libros_fisicos
            WHERE categoria_fisica_id IS NOT NULL
        ');
    }
};
