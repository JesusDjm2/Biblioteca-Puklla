<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('prestamos', 'numero_ejemplar')) {
            Schema::table('prestamos', function (Blueprint $table) {
                $table->unsignedInteger('numero_ejemplar')->nullable()->after('libro_fisico_id');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('prestamos', 'numero_ejemplar')) {
            Schema::table('prestamos', function (Blueprint $table) {
                $table->dropColumn('numero_ejemplar');
            });
        }
    }
};
