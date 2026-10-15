<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prestamos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('libro_fisico_id')->constrained('libros_fisicos')->restrictOnDelete();
            $table->string('nombre_prestatario');
            $table->string('dni_prestatario', 20);
            $table->string('contacto_prestatario')->nullable();
            $table->date('fecha_prestamo');
            $table->date('fecha_limite');
            $table->date('fecha_devolucion')->nullable();
            $table->text('observaciones')->nullable();
            $table->timestamps();
            $table->index('libro_fisico_id');
            $table->index('fecha_devolucion');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prestamos');
    }
};
