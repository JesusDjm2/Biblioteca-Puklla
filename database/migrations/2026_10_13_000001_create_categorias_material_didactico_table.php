<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categorias_material_didactico', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->unique();
            $table->string('codigo', 50)->nullable()->unique();
            $table->text('descripcion')->nullable();
            $table->timestamps();
        });

        // Categorías iniciales (áreas curriculares).
        $now = now();
        DB::table('categorias_material_didactico')->insert([
            ['nombre' => 'Matemática',           'codigo' => 'MAT', 'descripcion' => null, 'created_at' => $now, 'updated_at' => $now],
            ['nombre' => 'Personal Social',      'codigo' => 'PSO', 'descripcion' => null, 'created_at' => $now, 'updated_at' => $now],
            ['nombre' => 'Ciencia y Tecnología', 'codigo' => 'CYT', 'descripcion' => null, 'created_at' => $now, 'updated_at' => $now],
            ['nombre' => 'Comunicación',         'codigo' => 'COM', 'descripcion' => null, 'created_at' => $now, 'updated_at' => $now],
            ['nombre' => 'Artes',                'codigo' => 'ART', 'descripcion' => null, 'created_at' => $now, 'updated_at' => $now],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('categorias_material_didactico');
    }
};
