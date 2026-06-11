<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Libro extends Model
{
    use HasFactory;

    protected $fillable = ['nombre', 'autor', 'identificador'];
    public function categorias()
    {
        return $this->belongsToMany(Categoria::class);
    }
}
