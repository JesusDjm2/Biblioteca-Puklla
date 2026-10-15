<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('libros_fisicos', function (Blueprint $table) {
            $table->unsignedInteger('cantidad_ejemplares')->default(1)->after('numero_paginas');
        });
    }

    public function down(): void
    {
        Schema::table('libros_fisicos', function (Blueprint $table) {
            $table->dropColumn('cantidad_ejemplares');
        });
    }
};
