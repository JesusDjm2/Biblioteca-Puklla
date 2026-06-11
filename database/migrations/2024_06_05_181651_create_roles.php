<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Role;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $admin = Role::create(['name' => 'admin']);
        $biblioteca = Role::create(['name' => 'biblioteca']);
        $videos = Role::create(['name' => 'videos']);
        $audios = Role::create(['name' => 'audios']);
        $sisicha = Role::create(['name' => 'sisicha']);
        $fredy = Role::create(['name' => 'fredy']);
        $alumno = Role::create(['name' => 'alumno']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
