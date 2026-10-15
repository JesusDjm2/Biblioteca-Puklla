<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('libros_fisicos', 'etiquetas_ejemplares')) {
            Schema::table('libros_fisicos', function (Blueprint $table) {
                $table->json('etiquetas_ejemplares')->nullable()->after('cantidad_ejemplares');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('libros_fisicos', 'etiquetas_ejemplares')) {
            Schema::table('libros_fisicos', function (Blueprint $table) {
                $table->dropColumn('etiquetas_ejemplares');
            });
        }
    }
};
