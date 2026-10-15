<?php

namespace App\Http\Controllers;

use App\Models\CategoriaFisica;
use App\Models\LibroFisico;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class LibroFisicoController extends Controller
{
    public function index(Request $request)
    {
        $query = LibroFisico::query()
            ->with('categoriaFisica')
            ->orderBy('nombre');

        if ($request->filled('q')) {
            $term = '%'.addcslashes($request->input('q'), '%_\\').'%';
            $query->where(function ($q) use ($term) {
                $q->where('nombre', 'like', $term)
                    ->orWhere('autor', 'like', $term)
                    ->orWhere('codigo', 'like', $term);
            });
        }

        if ($request->filled('categoria_fisica_id')) {
            $query->where('categoria_fisica_id', (int) $request->input('categoria_fisica_id'));
        }

        if ($request->filled('ubicacion')) {
            $query->where('ubicacion', $request->input('ubicacion'));
        }

        $query->withCount(['prestamos as ejemplares_prestados_count' => fn ($q) => $q->activos()]);

        $libros = $query->paginate(25)->withQueryString();
        $categoriasFiltro = CategoriaFisica::orderBy('nombre')->get();

        return view('libros-fisicos.index', compact('libros', 'categoriasFiltro'));
    }

    public function create()
    {
        $categoriasFisicas = CategoriaFisica::orderBy('nombre')->get();

        return view('libros-fisicos.create', compact('categoriasFisicas'));
    }

    public function store(Request $request)
    {
        $request->merge(['categoria_fisica_id' => $request->input('categoria_fisica_id') ?: null]);

        $request->validate([
            'nombre' => 'required|string|max:255',
            'autor' => 'required|string|max:255',
            'editorial' => 'nullable|string|max:255',
            'anio_publicacion' => 'required|integer|min:1000|max:' . (date('Y') + 1),
            'numero_paginas' => 'nullable|integer|min:1|max:65535',
            'codigo' => 'required|string|max:100|unique:libros_fisicos,codigo',
            'cantidad_ejemplares' => 'required|integer|min:1|max:9999',
            'etiquetas_ejemplares' => 'nullable|array',
            'etiquetas_ejemplares.*' => 'nullable|string|max:50',
            'categoria_fisica_id' => 'nullable|exists:categorias_fisicas,id',
            'tipo_material' => ['required', Rule::in(array_keys(CategoriaFisica::TIPOS_MATERIAL))],
            'ubicacion' => ['required', Rule::in(array_keys(LibroFisico::UBICACIONES))],
        ]);

        $etiquetas = LibroFisico::normalizarEtiquetasEjemplares(
            $request->integer('cantidad_ejemplares'),
            $request->input('etiquetas_ejemplares', [])
        );
        $this->validarEtiquetasUnicas($etiquetas);

        LibroFisico::create($request->only(['nombre', 'autor', 'editorial', 'anio_publicacion', 'numero_paginas', 'codigo', 'cantidad_ejemplares', 'categoria_fisica_id', 'tipo_material', 'ubicacion']) + ['etiquetas_ejemplares' => $etiquetas]);

        return redirect()->route('biblioteca-fisica.libros.index')
            ->with('success', 'Libro físico registrado correctamente.');
    }

    /**
     * Dos ejemplares con la misma etiqueta ("A" y "A") serían indistinguibles
     * al prestar, así que se rechazan antes de guardar.
     */
    private function validarEtiquetasUnicas(array $etiquetas): void
    {
        $repetidas = collect($etiquetas)->duplicates()->unique()->values();

        if ($repetidas->isNotEmpty()) {
            throw ValidationException::withMessages([
                'etiquetas_ejemplares' => 'Hay etiquetas de ejemplar repetidas: '.$repetidas->implode(', ').'. Cada ejemplar debe tener una etiqueta distinta.',
            ]);
        }
    }

    public function edit(LibroFisico $libro_fisico)
    {
        $categoriasFisicas = CategoriaFisica::orderBy('nombre')->get();
        $prestadosActivos = $libro_fisico->prestamosActivos()->count();

        // Etiquetas que no se pueden renombrar/quitar ahora mismo porque su
        // ejemplar está prestado (ver guardia equivalente en update()).
        $etiquetasEnPrestamo = $libro_fisico->prestamosActivos()->pluck('numero_ejemplar')->filter()->values()->all();

        return view('libros-fisicos.edit', [
            'libro' => $libro_fisico,
            'categoriasFisicas' => $categoriasFisicas,
            'prestadosActivos' => $prestadosActivos,
            'etiquetasEnPrestamo' => $etiquetasEnPrestamo,
        ]);
    }

    public function update(Request $request, LibroFisico $libro_fisico)
    {
        $request->merge(['categoria_fisica_id' => $request->input('categoria_fisica_id') ?: null]);

        $request->validate([
            'nombre' => 'required|string|max:255',
            'autor' => 'required|string|max:255',
            'editorial' => 'nullable|string|max:255',
            'anio_publicacion' => 'required|integer|min:1000|max:' . (date('Y') + 1),
            'numero_paginas' => 'nullable|integer|min:1|max:65535',
            'codigo' => ['required', 'string', 'max:100', Rule::unique('libros_fisicos', 'codigo')->ignore($libro_fisico->id)],
            'cantidad_ejemplares' => 'required|integer|min:1|max:9999',
            'etiquetas_ejemplares' => 'nullable|array',
            'etiquetas_ejemplares.*' => 'nullable|string|max:50',
            'categoria_fisica_id' => 'nullable|exists:categorias_fisicas,id',
            'tipo_material' => ['required', Rule::in(array_keys(CategoriaFisica::TIPOS_MATERIAL))],
            'ubicacion' => ['required', Rule::in(array_keys(LibroFisico::UBICACIONES))],
        ]);

        $prestamosActivos = $libro_fisico->prestamosActivos()->get();
        if ($request->integer('cantidad_ejemplares') < $prestamosActivos->count()) {
            return back()->withInput()->with('error',
                "No se puede reducir a {$request->input('cantidad_ejemplares')} ejemplares: actualmente hay {$prestamosActivos->count()} prestados.");
        }

        $etiquetas = LibroFisico::normalizarEtiquetasEjemplares(
            $request->integer('cantidad_ejemplares'),
            $request->input('etiquetas_ejemplares', [])
        );
        $this->validarEtiquetasUnicas($etiquetas);

        // Un ejemplar prestado no puede perder ni cambiar su etiqueta mientras está
        // fuera: dejaría el préstamo apuntando a una etiqueta que ya no existe.
        $etiquetasEnPrestamo = $prestamosActivos->pluck('numero_ejemplar')->filter()->all();
        $etiquetasQueDesaparecen = array_diff($etiquetasEnPrestamo, $etiquetas);
        if (! empty($etiquetasQueDesaparecen)) {
            return back()->withInput()->with('error',
                'No puedes renombrar/quitar la etiqueta "'.implode('", "', $etiquetasQueDesaparecen).
                '": ese ejemplar está prestado actualmente. Registra la devolución primero.');
        }

        $libro_fisico->update($request->only(['nombre', 'autor', 'editorial', 'anio_publicacion', 'numero_paginas', 'codigo', 'cantidad_ejemplares', 'categoria_fisica_id', 'tipo_material', 'ubicacion']) + ['etiquetas_ejemplares' => $etiquetas]);

        return redirect()->route('biblioteca-fisica.libros.index')
            ->with('success', 'Libro físico actualizado correctamente.');
    }

    public function destroy(LibroFisico $libro_fisico)
    {
        if ($libro_fisico->prestamos()->exists()) {
            return redirect()->route('biblioteca-fisica.libros.index')
                ->with('error', 'No se puede eliminar el libro mientras tenga préstamos registrados (activos o históricos).');
        }

        $libro_fisico->delete();

        return redirect()->route('biblioteca-fisica.libros.index')
            ->with('success', 'Libro físico eliminado correctamente.');
    }

    // ─── Vista pública del catálogo físico ───────────────────────────────────

    public function catalogo(Request $request)
    {
        $query = LibroFisico::query()
            ->with('categoriaFisica')
            ->withCount(['prestamos as ejemplares_prestados_count' => fn ($q) => $q->activos()])
            ->orderBy('nombre');

        if ($request->filled('q')) {
            $term = '%' . addcslashes($request->input('q'), '%_\\') . '%';
            $query->where(function ($q) use ($term) {
                $q->where('nombre', 'like', $term)
                    ->orWhere('autor', 'like', $term)
                    ->orWhere('editorial', 'like', $term);
            });
        }

        if ($request->filled('cat')) {
            $query->where('categoria_fisica_id', (int) $request->input('cat'));
        }

        $libros     = $query->paginate(24)->withQueryString();
        $categorias = CategoriaFisica::withCount('librosFisicos')
            ->has('librosFisicos')
            ->orderBy('nombre')
            ->get();

        $totalLibros = LibroFisico::count();

        return view('catalogo-fisico.index', compact('libros', 'categorias', 'totalLibros'));
    }

    // ─── Búsqueda AJAX (Select2) para elegir un libro sin cargar todo el catálogo ─

    public function buscarAjax(Request $request): JsonResponse
    {
        $term = trim((string) $request->input('q', ''));
        $page = max(1, (int) $request->input('page', 1));
        $perPage = 20;

        $query = LibroFisico::query()->with('categoriaFisica')->orderBy('nombre');

        if ($term !== '') {
            $like = '%'.addcslashes($term, '%_\\').'%';
            $query->where(function ($q) use ($like) {
                $q->where('nombre', 'like', $like)
                    ->orWhere('autor', 'like', $like)
                    ->orWhere('codigo', 'like', $like);
            });
        }

        $query->withCount(['prestamos as ejemplares_prestados_count' => fn ($q) => $q->activos()]);

        $total = (clone $query)->count();
        $libros = $query->forPage($page, $perPage)->get();

        return response()->json([
            'results' => $libros->map(fn (LibroFisico $l) => $this->libroParaSelect2($l)),
            'pagination' => ['more' => ($page * $perPage) < $total],
        ]);
    }

    /**
     * Representación de un libro físico para Select2: además de id/text trae
     * todos los campos del libro para poder pintar la tarjeta de detalle
     * en el formulario de préstamos sin pedirlos de nuevo al servidor.
     */
    private function libroParaSelect2(LibroFisico $l): array
    {
        return [
            'id' => $l->id,
            'text' => "{$l->nombre} — {$l->autor} ({$l->ejemplares_disponibles} disp.)",
            'disabled' => $l->ejemplares_disponibles < 1,
            'nombre' => $l->nombre,
            'autor' => $l->autor,
            'editorial' => $l->editorial,
            'anio_publicacion' => $l->anio_publicacion,
            'numero_paginas' => $l->numero_paginas,
            'codigo' => $l->codigo,
            'cantidad_ejemplares' => $l->cantidad_ejemplares,
            'ejemplares_disponibles' => $l->ejemplares_disponibles,
            'etiquetas_ejemplares_disponibles' => $l->etiquetas_ejemplares_disponibles,
            'categoria' => $l->categoriaFisica ? [
                'nombre' => $l->categoriaFisica->nombre,
                'color' => CategoriaFisica::colorForCodigo($l->categoriaFisica->codigo),
            ] : null,
            'tipo_material' => $l->tipo_material ? [
                'nombre' => CategoriaFisica::nombreParaTipoMaterial($l->tipo_material),
                'color' => CategoriaFisica::colorForTipoMaterial($l->tipo_material),
            ] : null,
            'ubicacion' => LibroFisico::nombreParaUbicacion($l->ubicacion),
        ];
    }

    // ─── Sugerencia de código basada en la categoría / Dewey ─────────────────

    public function sugerirCodigo(Request $request): JsonResponse
    {
        // Continúa la misma secuencia limpia (PREFIJO-0001, 0002…) que ya tienen
        // todos los libros existentes desde la renumeración por Dewey/tipo de material.
        $prefix = $this->prefijoDesdeClasificacion(
            $request->input('tipo_material'),
            (int) $request->input('categoria_fisica_id')
        );

        $maximo = LibroFisico::where('codigo', 'like', $prefix . '-%')
            ->get(['codigo'])
            ->map(function ($l) use ($prefix) {
                $parte = substr($l->codigo, strlen($prefix) + 1);
                return (int) ltrim($parte, '0') ?: 0;
            })
            ->max() ?? 0;

        $codigo = $prefix . '-' . str_pad($maximo + 1, 4, '0', STR_PAD_LEFT);

        // Asegurar que no supere 50 chars (límite de la columna)
        return response()->json(['codigo' => mb_substr($codigo, 0, 50)]);
    }

    /**
     * Prefijo del código sugerido: los tipos de material con colección propia
     * (referencia, textos escolares, sala de lectura…) se identifican por su
     * propio código; el resto ("Bibliografía general") se ubica por su decena
     * Dewey si tiene una, igual que un libro de estantería clasificado por materia.
     */
    private function prefijoDesdeClasificacion(?string $tipoMaterial, int $categoriaId): string
    {
        if ($tipoMaterial && $tipoMaterial !== 'BIB-GEN') {
            return $tipoMaterial;
        }

        $cat = $categoriaId ? CategoriaFisica::find($categoriaId) : null;
        if ($cat && preg_match('/^DEW-(\d+)$/', $cat->codigo, $m)) {
            return $m[1];
        }

        return 'PF';
    }
}
