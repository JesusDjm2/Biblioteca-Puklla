<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('libros_fisicos', 'ubicacion')) {
            Schema::table('libros_fisicos', function (Blueprint $table) {
                $table->string('ubicacion', 20)->nullable()->after('tipo_material');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('libros_fisicos', 'ubicacion')) {
            Schema::table('libros_fisicos', function (Blueprint $table) {
                $table->dropColumn('ubicacion');
            });
        }
    }
};
