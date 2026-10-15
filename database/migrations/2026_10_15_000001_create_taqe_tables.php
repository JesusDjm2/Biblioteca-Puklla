<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('taqe_categorias', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 150);
            $table->string('slug', 160)->unique();
            $table->text('descripcion')->nullable();
            $table->string('color', 20)->default('#c78e41');
            $table->string('icono', 50)->default('fa-folder');
            $table->integer('orden')->default(0);
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });

        Schema::create('taqe_trabajos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('categoria_id')->constrained('taqe_categorias')->onDelete('cascade');
            $table->string('titulo', 255);
            $table->string('slug', 265)->unique();
            $table->string('autores', 255);
            $table->text('descripcion')->nullable();
            $table->enum('formato_principal', ['pdf', 'audio', 'video', 'imagen', 'multimedia'])->default('pdf');
            $table->enum('idioma', ['castellano', 'quechua', 'bilingue'])->default('castellano');
            $table->string('año_periodo', 50)->nullable();
            $table->string('ciclo_carrera', 150)->nullable();
            $table->string('archivo_pdf', 255)->nullable();
            $table->string('archivo_audio', 255)->nullable();
            $table->string('url_video', 255)->nullable();
            $table->string('portada', 255)->nullable();
            $table->unsignedInteger('vistas')->default(0);
            $table->unsignedInteger('descargas')->default(0);
            $table->boolean('destacado')->default(false);
            $table->enum('estado', ['publicado', 'borrador'])->default('publicado');
            $table->timestamps();

            $table->index('formato_principal');
            $table->index('idioma');
            $table->index('estado');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('taqe_trabajos');
        Schema::dropIfExists('taqe_categorias');
    }
};
