<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Seccion;
use Illuminate\Http\Request;

class CategoriaController extends Controller
{
    public function index(Request $request)
    {
        $query = Categoria::query()
            ->with('seccion')
            ->withCount('libros')
            ->orderBy('nombre');

        if ($request->filled('q')) {
            $term = '%'.addcslashes($request->input('q'), '%_\\').'%';
            $query->where(function ($q) use ($term) {
                $q->where('nombre', 'like', $term)
                    ->orWhere('url', 'like', $term);
            });
        }

        if ($request->filled('seccion_id')) {
            $query->where('seccion_id', (int) $request->input('seccion_id'));
        }

        $categorias = $query->paginate(25)->withQueryString();
        $seccionesFiltro = Seccion::orderBy('nombre')->get();

        return view('libros.categorias.index', compact('categorias', 'seccionesFiltro'));
    }
    public function create()
    {
        $secciones = Seccion::all();
        return view('libros.categorias.create', compact('secciones'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'url' => 'required|string|max:255',
            'seccion_id' => 'required|exists:seccions,id',
        ]);

        Categoria::create($request->all());

        return redirect()->route('categorias.index')->with('success', 'Categoría creada exitosamente.');
    }

    public function detallesCat($id)
    {
        /* $categoria = Categoria::with('libros')->findOrFail($id); */
        $categoria = Categoria::with([
            'libros' => function ($query) {
                $query->orderBy('nombre', 'asc');
            }
        ])->findOrFail($id); 
        return view('libros.categorias.detalles', compact('categoria'));
    }

    public function show($url)
    {
        $secciones = Seccion::withCount('categorias')
            ->with(['categorias' => function ($q) {
                $q->withCount('libros')->orderBy('nombre');
            }])
            ->orderBy('nombre')
            ->get();

        $categoria = Categoria::where('url', $url)->firstOrFail();
        $libros = $categoria->libros()->orderBy('nombre')->get();

        return view('libros.categorias.show', compact('categoria', 'secciones', 'libros'));
    }

    // Mostrar el formulario para editar una categoría existente
    public function edit($id)
    {
        $categoria = Categoria::findOrFail($id);
        $secciones = Seccion::all(); // Asumiendo que necesitas secciones para un dropdown
        return view('libros.categorias.edit', compact('categoria', 'secciones'));
    }

    // Actualizar una categoría existente en la base de datos
    public function update(Request $request, $id)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'url' => 'required|string|max:255',
            'seccion_id' => 'required|exists:seccions,id',
        ]);

        $categoria = Categoria::findOrFail($id);
        $categoria->update($request->all());

        return redirect()->route('categorias.index')->with('success', 'Categoría actualizada exitosamente.');
    }

    // Eliminar una categoría existente
    public function destroy($id)
    {
        $categoria = Categoria::findOrFail($id);
        $categoria->delete();

        return redirect()->route('categorias.index')->with('success', 'Categoría eliminada exitosamente.');
    }
}
