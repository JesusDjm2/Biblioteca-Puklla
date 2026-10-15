<?php

namespace App\Http\Controllers;

use App\Models\CategoriaMaterialDidactico;
use App\Models\MaterialDidactico;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MaterialDidacticoController extends Controller
{
    public function index(Request $request)
    {
        $query = MaterialDidactico::query()
            ->with('categoria')
            ->withSum('prestamosActivos', 'cantidad')
            ->orderBy('codigo');

        if ($request->filled('categoria_id')) {
            $query->where('categoria_material_didactico_id', $request->integer('categoria_id'));
        }

        if ($request->filled('q')) {
            $term = '%'.addcslashes($request->input('q'), '%_\\').'%';
            $query->where(function ($q) use ($term) {
                $q->where('nombre', 'like', $term)
                    ->orWhere('codigo', 'like', $term)
                    ->orWhere('descripcion', 'like', $term);
            });
        }

        $materiales = $query->paginate(25)->withQueryString();
        $categorias = CategoriaMaterialDidactico::query()
            ->withCount('materiales')
            ->withSum('materiales', 'cantidad')
            ->orderBy('nombre')
            ->get();

        return view('material-didactico.materiales.index', compact('materiales', 'categorias'));
    }

    public function create(Request $request)
    {
        $material = new MaterialDidactico([
            'categoria_material_didactico_id' => $request->integer('categoria_id') ?: null,
            'cantidad' => 1,
        ]);
        $categorias = CategoriaMaterialDidactico::orderBy('nombre')->get();

        return view('material-didactico.materiales.create', compact('material', 'categorias'));
    }

    public function store(Request $request)
    {
        $data = $this->validar($request);

        // Si no se indica código, se genera a partir del código de la categoría (MAT-001, ...).
        if (empty($data['codigo'])) {
            $data['codigo'] = MaterialDidactico::siguienteCodigo(
                CategoriaMaterialDidactico::findOrFail($data['categoria_material_didactico_id'])
            );
        }

        MaterialDidactico::create($data);

        return redirect()->route('biblioteca-fisica.materiales.index')
            ->with('success', 'Material "'.$data['nombre'].'" registrado con código '.$data['codigo'].'.');
    }

    public function edit(MaterialDidactico $material)
    {
        $categorias = CategoriaMaterialDidactico::orderBy('nombre')->get();

        return view('material-didactico.materiales.edit', compact('material', 'categorias'));
    }

    public function update(Request $request, MaterialDidactico $material)
    {
        $data = $this->validar($request, $material);

        if (empty($data['codigo'])) {
            $data['codigo'] = $material->codigo;
        }

        $prestadas = $material->unidades_prestadas;
        if ($data['cantidad'] < $prestadas) {
            return back()->withInput()->withErrors([
                'cantidad' => 'Hay '.$prestadas.' unidad(es) prestada(s); la cantidad no puede ser menor.',
            ]);
        }

        $material->update($data);

        return redirect()->route('biblioteca-fisica.materiales.index')
            ->with('success', 'Material didáctico actualizado correctamente.');
    }

    public function destroy(MaterialDidactico $material)
    {
        if ($material->prestamos()->exists()) {
            return redirect()->route('biblioteca-fisica.materiales.index')
                ->with('error', 'No se puede eliminar "'.$material->nombre.'" porque tiene préstamos registrados.');
        }

        $material->delete();

        return redirect()->route('biblioteca-fisica.materiales.index')
            ->with('success', 'Material didáctico eliminado correctamente.');
    }

    /**
     * Buscador del formulario de préstamos (respuesta con la misma forma que libros.buscar-ajax).
     */
    public function buscarAjax(Request $request)
    {
        $term = trim((string) $request->input('q', ''));
        $porPagina = 30;
        $pagina = max(1, $request->integer('page', 1));

        $query = MaterialDidactico::query()
            ->with('categoria')
            ->withSum('prestamosActivos', 'cantidad')
            ->orderBy('nombre');

        if ($term !== '') {
            $like = '%'.addcslashes($term, '%_\\').'%';
            $query->where(function ($q) use ($like) {
                $q->where('nombre', 'like', $like)
                    ->orWhere('codigo', 'like', $like);
            });
        }

        $materiales = $query->skip(($pagina - 1) * $porPagina)->take($porPagina + 1)->get();

        return response()->json([
            'results' => $materiales->take($porPagina)->map(fn (MaterialDidactico $material) => PrestamoController::materialParaJs($material) + [
                'disabled' => $material->unidades_disponibles < 1,
            ])->values(),
            'pagination' => ['more' => $materiales->count() > $porPagina],
        ]);
    }

    private function validar(Request $request, ?MaterialDidactico $material = null): array
    {
        return $request->validate([
            'categoria_material_didactico_id' => 'required|exists:categorias_material_didactico,id',
            'codigo' => ['nullable', 'string', 'max:50', Rule::unique('materiales_didacticos', 'codigo')->ignore($material?->id)],
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string|max:2000',
            'cantidad' => 'required|integer|min:0|max:100000',
        ], [], [
            'categoria_material_didactico_id' => 'categoría',
        ]);
    }
}
