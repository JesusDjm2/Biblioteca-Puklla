<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LibroFisico extends Model
{
    use HasFactory;

    protected $table = 'libros_fisicos';

    protected $fillable = [
        'nombre',
        'autor',
        'anio_publicacion',
        'codigo',
    ];

    protected $casts = [
        'anio_publicacion' => 'integer',
    ];

    public function categoriasFisicas()
    {
        return $this->belongsToMany(
            CategoriaFisica::class,
            'categoria_fisica_libro_fisico',
            'libro_fisico_id',
            'categoria_fisica_id'
        )->withTimestamps();
    }
}
