<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoriaMaterialDidactico extends Model
{
    use HasFactory;

    protected $table = 'categorias_material_didactico';

    protected $fillable = [
        'nombre',
        'codigo',
        'descripcion',
    ];

    public function materiales()
    {
        return $this->hasMany(MaterialDidactico::class, 'categoria_material_didactico_id');
    }
}
