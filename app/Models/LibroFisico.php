<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LibroFisico extends Model
{
    use HasFactory;

    protected $table = 'libros_fisicos';

    /**
     * Locales físicos de la institución donde puede ubicarse un ejemplar.
     * Clave = valor guardado en libros_fisicos.ubicacion.
     */
    public const UBICACIONES = [
        'san_blas'   => 'EESP Pukllasunchis - San Blas',
        'awaqpinta'  => 'Biblioteca Awaqpinta N°563',
    ];

    protected $fillable = [
        'nombre',
        'autor',
        'editorial',
        'anio_publicacion',
        'numero_paginas',
        'codigo',
        'cantidad_ejemplares',
        'etiquetas_ejemplares',
        'categoria_fisica_id',
        'tipo_material',
        'ubicacion',
    ];

    protected $casts = [
        'anio_publicacion' => 'integer',
        'numero_paginas' => 'integer',
        'cantidad_ejemplares' => 'integer',
        'etiquetas_ejemplares' => 'array',
    ];

    public function categoriaFisica()
    {
        return $this->belongsTo(CategoriaFisica::class);
    }

    public static function nombreParaUbicacion(?string $ubicacion): ?string
    {
        return $ubicacion ? (self::UBICACIONES[$ubicacion] ?? $ubicacion) : null;
    }

    public function prestamos()
    {
        return $this->hasMany(Prestamo::class, 'libro_fisico_id');
    }

    public function prestamosActivos()
    {
        return $this->hasMany(Prestamo::class, 'libro_fisico_id')->activos();
    }

    /**
     * Usa el conteo precargado por withCount() si está disponible (evita N+1 en listados).
     */
    public function getEjemplaresPrestadosAttribute(): int
    {
        return $this->attributes['ejemplares_prestados_count'] ?? $this->prestamosActivos()->count();
    }

    public function getEjemplaresDisponiblesAttribute(): int
    {
        return max(0, $this->cantidad_ejemplares - $this->ejemplares_prestados);
    }

    /**
     * Normaliza una lista de etiquetas a exactamente $cantidad elementos:
     * usa lo escrito por el bibliotecario donde exista y rellena los
     * espacios vacíos o faltantes con el correlativo (1, 2, 3…).
     */
    public static function normalizarEtiquetasEjemplares(int $cantidad, array $etiquetas): array
    {
        $normalizadas = [];

        for ($i = 0; $i < $cantidad; $i++) {
            $etiqueta = trim((string) ($etiquetas[$i] ?? ''));
            $normalizadas[] = $etiqueta !== '' ? $etiqueta : (string) ($i + 1);
        }

        return $normalizadas;
    }

    /**
     * Etiquetas de cada ejemplar físico (1 por copia), ya normalizadas al
     * tamaño actual de cantidad_ejemplares aunque la columna guardada esté
     * desactualizada o vacía.
     */
    public function getEtiquetasEjemplaresListaAttribute(): array
    {
        return self::normalizarEtiquetasEjemplares($this->cantidad_ejemplares, $this->etiquetas_ejemplares ?? []);
    }

    /**
     * Etiquetas de ejemplar actualmente en préstamo.
     */
    public function getEtiquetasEjemplaresOcupadasAttribute(): array
    {
        return $this->prestamosActivos()
            ->whereNotNull('numero_ejemplar')
            ->pluck('numero_ejemplar')
            ->all();
    }

    /**
     * Etiquetas de ejemplar libres para prestar. Solo tiene sentido cuando hay
     * más de un ejemplar; con uno solo no se pide identificarlo.
     */
    public function getEtiquetasEjemplaresDisponiblesAttribute(): array
    {
        if ($this->cantidad_ejemplares <= 1) {
            return [];
        }

        return collect($this->etiquetas_ejemplares_lista)
            ->diff($this->etiquetas_ejemplares_ocupadas)
            ->values()
            ->all();
    }
}
