<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('libros_fisicos', function (Blueprint $table) {
            $table->string('editorial')->nullable()->after('autor');
            $table->unsignedSmallInteger('numero_paginas')->nullable()->after('anio_publicacion');
        });
    }

    public function down(): void
    {
        Schema::table('libros_fisicos', function (Blueprint $table) {
            $table->dropColumn(['editorial', 'numero_paginas']);
        });
    }
};
