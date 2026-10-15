<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categorias_fisicas', function (Blueprint $table) {
            $table->string('codigo', 50)->nullable()->unique()->after('nombre');
        });
    }

    public function down(): void
    {
        Schema::table('categorias_fisicas', function (Blueprint $table) {
            $table->dropColumn('codigo');
        });
    }
};
