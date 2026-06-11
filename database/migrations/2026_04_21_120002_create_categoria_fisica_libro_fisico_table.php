<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categoria_fisica_libro_fisico', function (Blueprint $table) {
            $table->id();
            $table->foreignId('categoria_fisica_id')->constrained('categorias_fisicas')->cascadeOnDelete();
            $table->foreignId('libro_fisico_id')->constrained('libros_fisicos')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['categoria_fisica_id', 'libro_fisico_id'], 'catfis_libfis_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('categoria_fisica_libro_fisico');
    }
};
