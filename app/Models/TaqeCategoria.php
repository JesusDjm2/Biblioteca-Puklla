<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class TaqeCategoria extends Model
{
    use HasFactory;

    protected $table = 'taqe_categorias';

    protected $fillable = [
        'nombre',
        'slug',
        'descripcion',
        'color',
        'icono',
        'orden',
        'activo',
    ];

    protected $casts = [
        'orden' => 'integer',
        'activo' => 'boolean',
    ];

    protected static function booted()
    {
        static::creating(function ($cat) {
            if (empty($cat->slug)) {
                $base = Str::slug($cat->nombre);
                $slug = $base;
                $i = 1;
                while (static::where('slug', $slug)->exists()) {
                    $slug = "{$base}-" . (++$i);
                }
                $cat->slug = $slug;
            }
        });
    }

    public function trabajos()
    {
        return $this->hasMany(TaqeTrabajo::class, 'categoria_id')->orderBy('id', 'desc');
    }

    public function trabajosPublicados()
    {
        return $this->hasMany(TaqeTrabajo::class, 'categoria_id')
            ->where('estado', 'publicado')
            ->orderBy('id', 'desc');
    }

    public function scopeActivas($query)
    {
        return $query->where('activo', true)->orderBy('orden', 'asc');
    }
}
