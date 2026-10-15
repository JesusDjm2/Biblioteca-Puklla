<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('materiales_didacticos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('categoria_material_didactico_id')
                ->constrained('categorias_material_didactico')
                ->restrictOnDelete();
            $table->string('codigo', 50)->unique();
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->unsignedInteger('cantidad')->default(1);
            $table->timestamps();
            $table->index('nombre');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('materiales_didacticos');
    }
};
