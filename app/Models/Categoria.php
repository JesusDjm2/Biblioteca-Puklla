<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    use HasFactory;
    protected $fillable = [
        'nombre', 
        'url', 
        'seccion_id'
    ];
    public function seccion()
    {
        return $this->belongsTo(Seccion::class);
    }
    public function libros()
    {
        return $this->belongsToMany(Libro::class);
    }    
}
