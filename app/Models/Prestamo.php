<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prestamo extends Model
{
    use HasFactory;

    protected $table = 'prestamos';

    protected $fillable = [
        'libro_fisico_id',
        'material_didactico_id',
        'numero_ejemplar',
        'cantidad',
        'nombre_prestatario',
        'dni_prestatario',
        'contacto_prestatario',
        'fecha_prestamo',
        'fecha_limite',
        'fecha_devolucion',
        'observaciones',
    ];

    protected $casts = [
        'fecha_prestamo' => 'date',
        'fecha_limite' => 'date',
        'fecha_devolucion' => 'date',
        'cantidad' => 'integer',
    ];

    public function libroFisico()
    {
        return $this->belongsTo(LibroFisico::class, 'libro_fisico_id');
    }

    public function materialDidactico()
    {
        return $this->belongsTo(MaterialDidactico::class, 'material_didactico_id');
    }

    public function getEsMaterialAttribute(): bool
    {
        return $this->material_didactico_id !== null;
    }

    /**
     * Libro o material prestado, para mostrarlo sin importar el tipo de préstamo.
     */
    public function getItemNombreAttribute(): string
    {
        return $this->es_material
            ? ($this->materialDidactico?->nombre ?? '—')
            : ($this->libroFisico?->nombre ?? '—');
    }

    public function getItemCodigoAttribute(): ?string
    {
        return $this->es_material ? $this->materialDidactico?->codigo : $this->libroFisico?->codigo;
    }

    /**
     * El estado no se guarda en columna: se calcula siempre a partir de
     * fecha_devolucion y fecha_limite para evitar inconsistencias.
     */
    public function getEstadoAttribute(): string
    {
        if ($this->fecha_devolucion !== null) {
            return 'devuelto';
        }

        return $this->fecha_limite->lt(today()) ? 'atrasado' : 'prestado';
    }

    public function getEstadoLabelAttribute(): string
    {
        return match ($this->estado) {
            'atrasado' => 'Atrasado',
            'devuelto' => 'Devuelto',
            default => 'Prestado',
        };
    }

    public function getEstadoBadgeClassAttribute(): string
    {
        return match ($this->estado) {
            'atrasado' => 'badge-danger',
            'devuelto' => 'badge-success',
            default => 'badge-warning',
        };
    }

    public function scopeActivos($query)
    {
        return $query->whereNull('fecha_devolucion');
    }

    public function scopeDevueltos($query)
    {
        return $query->whereNotNull('fecha_devolucion');
    }

    public function scopeAtrasados($query)
    {
        return $query->whereNull('fecha_devolucion')
            ->whereDate('fecha_limite', '<', today());
    }
}
