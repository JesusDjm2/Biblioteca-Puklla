<?php

namespace App\Http\Controllers;

use App\Models\CategoriaFisica;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CategoriaFisicaController extends Controller
{
    public function index(Request $request)
    {
        $query = CategoriaFisica::query()
            ->withCount('librosFisicos')
            ->orderBy('nombre');

        if ($request->filled('q')) {
            $term = '%'.addcslashes($request->input('q'), '%_\\').'%';
            $query->where(function ($q) use ($term) {
                $q->where('nombre', 'like', $term)
                    ->orWhere('descripcion', 'like', $term);
            });
        }

        $categorias = $query->paginate(25)->withQueryString();

        return view('categorias-fisicas.index', compact('categorias'));
    }

    public function create()
    {
        return view('categorias-fisicas.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'codigo' => 'nullable|string|max:50|unique:categorias_fisicas,codigo',
            'descripcion' => 'nullable|string|max:2000',
        ]);

        CategoriaFisica::create($request->only(['nombre', 'codigo', 'descripcion']));

        return redirect()->route('biblioteca-fisica.categorias.index')
            ->with('success', 'Categoría creada correctamente.');
    }

    public function edit(CategoriaFisica $categoria_fisica)
    {
        return view('categorias-fisicas.edit', ['categoria' => $categoria_fisica]);
    }

    public function update(Request $request, CategoriaFisica $categoria_fisica)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'codigo' => ['nullable', 'string', 'max:50', Rule::unique('categorias_fisicas', 'codigo')->ignore($categoria_fisica->id)],
            'descripcion' => 'nullable|string|max:2000',
        ]);

        $categoria_fisica->update($request->only(['nombre', 'codigo', 'descripcion']));

        return redirect()->route('biblioteca-fisica.categorias.index')
            ->with('success', 'Categoría actualizada correctamente.');
    }

    public function destroy(CategoriaFisica $categoria_fisica)
    {
        if ($categoria_fisica->librosFisicos()->exists()) {
            return redirect()->route('biblioteca-fisica.categorias.index')
                ->with('error', 'No se puede eliminar la categoría mientras tenga libros físicos asociados.');
        }

        $categoria_fisica->delete();

        return redirect()->route('biblioteca-fisica.categorias.index')
            ->with('success', 'Categoría eliminada correctamente.');
    }
}
