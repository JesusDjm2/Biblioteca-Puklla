<?php

namespace App\Http\Controllers;

use App\Models\CategoriaFisica;
use App\Models\LibroFisico;
use App\Models\MaterialDidactico;
use App\Models\Prestamo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PrestamoController extends Controller
{
    public function index(Request $request)
    {
        $query = Prestamo::query()
            ->with(['libroFisico', 'materialDidactico'])
            ->latest('fecha_prestamo');

        if ($request->filled('q')) {
            $term = '%'.addcslashes($request->input('q'), '%_\\').'%';
            $query->where(function ($q) use ($term) {
                $q->where('nombre_prestatario', 'like', $term)
                    ->orWhere('dni_prestatario', 'like', $term);
            });
        }

        if ($request->filled('libro_fisico_id')) {
            $query->where('libro_fisico_id', (int) $request->input('libro_fisico_id'));
        }

        if ($request->filled('material_didactico_id')) {
            $query->where('material_didactico_id', (int) $request->input('material_didactico_id'));
        }

        match ($request->input('tipo')) {
            'libro' => $query->whereNotNull('libro_fisico_id'),
            'material' => $query->whereNotNull('material_didactico_id'),
            default => null,
        };

        if ($request->filled('estado')) {
            match ($request->input('estado')) {
                'devuelto' => $query->whereNotNull('fecha_devolucion'),
                'atrasado' => $query->whereNull('fecha_devolucion')->whereDate('fecha_limite', '<', today()),
                'prestado' => $query->whereNull('fecha_devolucion')->whereDate('fecha_limite', '>=', today()),
                default => null,
            };
        }

        $prestamos = $query->paginate(25)->withQueryString();

        // Solo se precarga el libro ya seleccionado en el filtro (si hay uno);
        // el resto de opciones las trae Select2 vía AJAX (evita cargar ~9,000 libros).
        $libroFiltroSeleccionado = $request->filled('libro_fisico_id')
            ? LibroFisico::find($request->input('libro_fisico_id'))
            : null;

        $materialFiltroSeleccionado = $request->filled('material_didactico_id')
            ? MaterialDidactico::find($request->input('material_didactico_id'))
            : null;

        // Libros (1 unidad por ejemplar) + unidades de material didáctico.
        $totalEjemplares = (int) LibroFisico::sum('cantidad_ejemplares') + (int) MaterialDidactico::sum('cantidad');
        $prestadosActualmente = (int) Prestamo::activos()->sum('cantidad');
        $atrasados = Prestamo::atrasados()->count();
        $disponibles = max(0, $totalEjemplares - $prestadosActualmente);

        return view('prestamos.index', compact(
            'prestamos',
            'libroFiltroSeleccionado',
            'materialFiltroSeleccionado',
            'totalEjemplares',
            'prestadosActualmente',
            'atrasados',
            'disponibles'
        ));
    }

    public function create()
    {
        // Solo se precargan los libros ya elegidos (repoblado tras un error de validación);
        // el resto de opciones las trae el buscador vía AJAX (evita cargar ~9,000 libros).
        $idsSeleccionados = collect(old('libro_fisico_id', []))
            ->map(fn ($id) => (int) $id)
            ->filter()
            ->values();

        $librosSeleccionados = $idsSeleccionados->isEmpty()
            ? collect()
            : LibroFisico::with('categoriaFisica')
                ->whereIn('id', $idsSeleccionados)
                ->get()
                ->sortBy(fn (LibroFisico $libro) => $idsSeleccionados->search($libro->id))
                ->values()
                ->map(fn (LibroFisico $libro) => [
                    'id' => $libro->id,
                    'nombre' => $libro->nombre,
                    'autor' => $libro->autor,
                    'codigo' => $libro->codigo,
                    'ejemplares_disponibles' => $libro->ejemplares_disponibles,
                    'cantidad_ejemplares' => $libro->cantidad_ejemplares,
                    'etiquetas_ejemplares_disponibles' => $libro->etiquetas_ejemplares_disponibles,
                    'numero_ejemplar_actual' => old('numero_ejemplar.'.$libro->id),
                    'categoria' => $libro->categoriaFisica ? [
                        'nombre' => $libro->categoriaFisica->nombre,
                        'color' => CategoriaFisica::colorForCodigo($libro->categoriaFisica->codigo),
                    ] : null,
                    'tipo_material' => $libro->tipo_material ? [
                        'nombre' => CategoriaFisica::nombreParaTipoMaterial($libro->tipo_material),
                        'color' => CategoriaFisica::colorForTipoMaterial($libro->tipo_material),
                    ] : null,
                ]);

        $idsMateriales = collect(old('material_didactico_id', []))
            ->map(fn ($id) => (int) $id)
            ->filter()
            ->values();

        $materialesSeleccionados = $idsMateriales->isEmpty()
            ? collect()
            : MaterialDidactico::with('categoria')
                ->withSum('prestamosActivos', 'cantidad')
                ->whereIn('id', $idsMateriales)
                ->get()
                ->sortBy(fn (MaterialDidactico $material) => $idsMateriales->search($material->id))
                ->values()
                ->map(fn (MaterialDidactico $material) => $this->materialParaJs($material) + [
                    'cantidad_actual' => old('cantidad_material.'.$material->id),
                ]);

        $hayLibros = LibroFisico::query()->exists() || MaterialDidactico::query()->exists();

        return view('prestamos.create', compact('librosSeleccionados', 'materialesSeleccionados', 'hayLibros'));
    }

    /**
     * Datos de un material que usa el buscador del formulario de préstamo.
     */
    public static function materialParaJs(MaterialDidactico $material): array
    {
        return [
            'id' => $material->id,
            'nombre' => $material->nombre,
            'codigo' => $material->codigo,
            'cantidad' => $material->cantidad,
            'unidades_disponibles' => $material->unidades_disponibles,
            'categoria' => $material->categoria?->nombre,
        ];
    }

    public function store(Request $request)
    {
        $request->validate([
            'libro_fisico_id' => 'nullable|array',
            'libro_fisico_id.*' => 'distinct|exists:libros_fisicos,id',
            'material_didactico_id' => 'nullable|array',
            'material_didactico_id.*' => 'distinct|exists:materiales_didacticos,id',
            'cantidad_material' => 'nullable|array',
            'cantidad_material.*' => 'nullable|integer|min:1|max:100000',
            'numero_ejemplar' => 'nullable|array',
            'numero_ejemplar.*' => 'nullable|string|max:50',
            'nombre_prestatario' => 'required|string|max:255',
            'dni_prestatario' => 'required|string|max:20',
            'contacto_prestatario' => 'nullable|string|max:255',
            'fecha_prestamo' => 'required|date',
            'fecha_limite' => 'required|date|after_or_equal:fecha_prestamo',
            'observaciones' => 'nullable|string|max:2000',
        ]);

        if (! $request->filled('libro_fisico_id') && ! $request->filled('material_didactico_id')) {
            throw ValidationException::withMessages([
                'libro_fisico_id' => 'Debes seleccionar al menos un libro o material didáctico.',
            ]);
        }

        $datosPrestatario = $request->only([
            'nombre_prestatario',
            'dni_prestatario',
            'contacto_prestatario',
            'fecha_prestamo',
            'fecha_limite',
            'observaciones',
        ]);

        $cantidad = DB::transaction(function () use ($request, $datosPrestatario) {
            $librosIds = $request->input('libro_fisico_id', []);
            $materialesIds = $request->input('material_didactico_id', []);
            $cantidadesMaterial = $request->input('cantidad_material', []);
            $numerosEjemplar = $request->input('numero_ejemplar', []);

            foreach ($librosIds as $libroId) {
                $libro = LibroFisico::lockForUpdate()->findOrFail($libroId);
                $prestamosActivos = $libro->prestamos()->activos()->lockForUpdate()->get();

                if ($prestamosActivos->count() >= $libro->cantidad_ejemplares) {
                    throw ValidationException::withMessages([
                        'libro_fisico_id' => 'No hay ejemplares disponibles de "'.$libro->nombre.'" para prestar.',
                    ]);
                }

                $numeroEjemplar = null;

                if ($libro->cantidad_ejemplares > 1) {
                    $numeroEjemplar = trim((string) ($numerosEjemplar[$libroId] ?? ''));

                    if ($numeroEjemplar === '' || ! in_array($numeroEjemplar, $libro->etiquetas_ejemplares_lista, true)) {
                        throw ValidationException::withMessages([
                            "numero_ejemplar.{$libroId}" => 'Indica qué ejemplar de "'.$libro->nombre.'" se presta.',
                        ]);
                    }

                    if ($prestamosActivos->contains('numero_ejemplar', $numeroEjemplar)) {
                        throw ValidationException::withMessages([
                            "numero_ejemplar.{$libroId}" => 'El ejemplar "'.$numeroEjemplar.'" de "'.$libro->nombre.'" ya está prestado.',
                        ]);
                    }
                }

                Prestamo::create($datosPrestatario + [
                    'libro_fisico_id' => $libro->id,
                    'numero_ejemplar' => $numeroEjemplar,
                    'cantidad' => 1,
                ]);
            }

            foreach ($materialesIds as $materialId) {
                $material = MaterialDidactico::lockForUpdate()->findOrFail($materialId);
                $unidades = (int) ($cantidadesMaterial[$materialId] ?? 1);
                $prestadas = (int) $material->prestamosActivos()->lockForUpdate()->sum('cantidad');
                $disponibles = max(0, $material->cantidad - $prestadas);

                if ($unidades < 1 || $unidades > $disponibles) {
                    throw ValidationException::withMessages([
                        "cantidad_material.{$materialId}" => $disponibles > 0
                            ? 'Solo hay '.$disponibles.' unidad(es) disponible(s) de "'.$material->nombre.'".'
                            : 'No hay unidades disponibles de "'.$material->nombre.'" para prestar.',
                    ]);
                }

                Prestamo::create($datosPrestatario + [
                    'material_didactico_id' => $material->id,
                    'cantidad' => $unidades,
                ]);
            }

            return count($librosIds) + count($materialesIds);
        });

        return redirect()->route('biblioteca-fisica.prestamos.index')
            ->with('success', $cantidad === 1
                ? 'Préstamo registrado correctamente.'
                : "Se registraron {$cantidad} préstamos correctamente.");
    }

    public function edit(Prestamo $prestamo)
    {
        $prestamo->load(['libroFisico', 'materialDidactico']);

        $numerosEjemplarDisponibles = [];
        $maxUnidadesMaterial = null;

        if ($prestamo->es_material) {
            // Disponibles + las que ya tiene este préstamo (si sigue activo).
            $maxUnidadesMaterial = $prestamo->materialDidactico->unidades_disponibles
                + ($prestamo->fecha_devolucion === null ? $prestamo->cantidad : 0);
        } else {
            // Etiquetas libres + la que ya tiene asignada este préstamo (si no, no aparecería en la lista).
            $numerosEjemplarDisponibles = $prestamo->libroFisico->etiquetas_ejemplares_disponibles;
            if ($prestamo->numero_ejemplar && ! in_array($prestamo->numero_ejemplar, $numerosEjemplarDisponibles, true)) {
                $numerosEjemplarDisponibles[] = $prestamo->numero_ejemplar;
                sort($numerosEjemplarDisponibles);
            }
        }

        return view('prestamos.edit', compact('prestamo', 'numerosEjemplarDisponibles', 'maxUnidadesMaterial'));
    }

    public function update(Request $request, Prestamo $prestamo)
    {
        $request->validate([
            'numero_ejemplar' => 'nullable|string|max:50',
            'cantidad' => 'nullable|integer|min:1|max:100000',
            'nombre_prestatario' => 'required|string|max:255',
            'dni_prestatario' => 'required|string|max:20',
            'contacto_prestatario' => 'nullable|string|max:255',
            'fecha_prestamo' => 'required|date',
            'fecha_limite' => 'required|date|after_or_equal:fecha_prestamo',
            'observaciones' => 'nullable|string|max:2000',
        ]);

        $numeroEjemplar = $prestamo->numero_ejemplar;
        $cantidad = $prestamo->cantidad;
        $libro = $prestamo->libroFisico;

        if ($prestamo->es_material) {
            $material = $prestamo->materialDidactico;
            $cantidad = (int) $request->input('cantidad', $prestamo->cantidad);

            if ($prestamo->fecha_devolucion === null) {
                $prestadasPorOtros = (int) $material->prestamosActivos()
                    ->where('id', '!=', $prestamo->id)
                    ->sum('cantidad');
                $maximo = max(0, $material->cantidad - $prestadasPorOtros);

                if ($cantidad > $maximo) {
                    throw ValidationException::withMessages([
                        'cantidad' => 'Solo hay '.$maximo.' unidad(es) disponible(s) de "'.$material->nombre.'".',
                    ]);
                }
            }
        } elseif ($libro->cantidad_ejemplares > 1) {
            $numeroEjemplar = trim((string) $request->input('numero_ejemplar', ''));

            if ($numeroEjemplar === '' || ! in_array($numeroEjemplar, $libro->etiquetas_ejemplares_lista, true)) {
                throw ValidationException::withMessages([
                    'numero_ejemplar' => 'Indica qué ejemplar de "'.$libro->nombre.'" se presta.',
                ]);
            }

            $ocupadoPorOtroPrestamo = $libro->prestamos()->activos()
                ->where('numero_ejemplar', $numeroEjemplar)
                ->where('id', '!=', $prestamo->id)
                ->exists();

            if ($ocupadoPorOtroPrestamo) {
                throw ValidationException::withMessages([
                    'numero_ejemplar' => 'El ejemplar "'.$numeroEjemplar.'" de "'.$libro->nombre.'" ya está prestado.',
                ]);
            }
        }

        $prestamo->update($request->only([
            'nombre_prestatario',
            'dni_prestatario',
            'contacto_prestatario',
            'fecha_prestamo',
            'fecha_limite',
            'observaciones',
        ]) + ['numero_ejemplar' => $numeroEjemplar, 'cantidad' => $cantidad]);

        return redirect()->route('biblioteca-fisica.prestamos.index')
            ->with('success', 'Préstamo actualizado correctamente.');
    }

    public function destroy(Prestamo $prestamo)
    {
        $prestamo->delete();

        return redirect()->route('biblioteca-fisica.prestamos.index')
            ->with('success', 'Préstamo eliminado correctamente.');
    }

    public function devolver(Request $request, Prestamo $prestamo)
    {
        if ($prestamo->fecha_devolucion !== null) {
            return back()->with('error', 'Este préstamo ya fue registrado como devuelto.');
        }

        $prestamo->update(['fecha_devolucion' => today()]);

        return redirect()->route('biblioteca-fisica.prestamos.index')
            ->with('success', 'Devolución registrada correctamente.');
    }
}
