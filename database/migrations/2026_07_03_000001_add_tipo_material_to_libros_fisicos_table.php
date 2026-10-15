<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('libros_fisicos', function (Blueprint $table) {
            $table->string('tipo_material', 10)->nullable()->after('categoria_fisica_id');
        });
    }

    public function down(): void
    {
        Schema::table('libros_fisicos', function (Blueprint $table) {
            $table->dropColumn('tipo_material');
        });
    }
};
