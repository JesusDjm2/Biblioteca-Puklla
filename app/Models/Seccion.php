<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Seccion extends Model
{
    use HasFactory;
    protected $fillable = ['nombre', 'url'];
    public function categorias()
    {
        return $this->hasMany(Categoria::class);
    }
}
