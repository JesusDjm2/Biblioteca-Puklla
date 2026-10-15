<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaterialDidactico extends Model
{
    use HasFactory;

    protected $table = 'materiales_didacticos';

    protected $fillable = [
        'categoria_material_didactico_id',
        'codigo',
        'nombre',
        'descripcion',
        'cantidad',
    ];

    protected $casts = [
        'cantidad' => 'integer',
    ];

    public function categoria()
    {
        return $this->belongsTo(CategoriaMaterialDidactico::class, 'categoria_material_didactico_id');
    }

    public function prestamos()
    {
        return $this->hasMany(Prestamo::class, 'material_didactico_id');
    }

    public function prestamosActivos()
    {
        return $this->hasMany(Prestamo::class, 'material_didactico_id')->activos();
    }

    /**
     * Unidades actualmente prestadas. Usa la suma precargada por
     * withSum('prestamosActivos', 'cantidad') si está disponible (evita N+1 en listados).
     */
    public function getUnidadesPrestadasAttribute(): int
    {
        return (int) ($this->attributes['prestamos_activos_sum_cantidad']
            ?? $this->prestamosActivos()->sum('cantidad'));
    }

    public function getUnidadesDisponiblesAttribute(): int
    {
        return max(0, $this->cantidad - $this->unidades_prestadas);
    }

    /**
     * Siguiente código libre para una categoría: {CODIGO_CATEGORIA}-NNN
     * (o MD-NNN si la categoría no tiene código).
     */
    public static function siguienteCodigo(CategoriaMaterialDidactico $categoria): string
    {
        $prefijo = ($categoria->codigo ?: 'MD').'-';

        $max = static::where('codigo', 'like', addcslashes($prefijo, '%_\\').'%')
            ->pluck('codigo')
            ->map(fn ($c) => (int) substr($c, strlen($prefijo)))
            ->max() ?? 0;

        do {
            $max++;
            $codigo = $prefijo.str_pad((string) $max, 3, '0', STR_PAD_LEFT);
        } while (static::where('codigo', $codigo)->exists());

        return $codigo;
    }
}
