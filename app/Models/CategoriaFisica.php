<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoriaFisica extends Model
{
    use HasFactory;

    protected $table = 'categorias_fisicas';

    protected $fillable = [
        'nombre',
        'descripcion',
    ];

    public function librosFisicos()
    {
        return $this->belongsToMany(
            LibroFisico::class,
            'categoria_fisica_libro_fisico',
            'categoria_fisica_id',
            'libro_fisico_id'
        )->withTimestamps();
    }
}
