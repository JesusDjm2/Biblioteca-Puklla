<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('videos', function (Blueprint $table) {
            $table->unsignedInteger('orden')->default(0)->after('categoria_id');
            $table->index(['categoria_id', 'orden']);
        });

        // Orden inicial = el que se veía hasta ahora en cada categoría (más recientes primero).
        $posicion = [];
        DB::table('videos')->orderByDesc('created_at')->orderByDesc('id')->get(['id', 'categoria_id'])
            ->each(function ($v) use (&$posicion) {
                $clave = (string) $v->categoria_id;
                $posicion[$clave] = ($posicion[$clave] ?? 0) + 1;
                DB::table('videos')->where('id', $v->id)->update(['orden' => $posicion[$clave]]);
            });
    }

    public function down(): void
    {
        Schema::table('videos', function (Blueprint $table) {
            $table->dropIndex(['categoria_id', 'orden']);
            $table->dropColumn('orden');
        });
    }
};
