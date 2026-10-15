<?php

namespace App\Http\Controllers;

use App\Models\CategoriaMaterialDidactico;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CategoriaMaterialDidacticoController extends Controller
{
    /**
     * Redirige al tab unificado de categorías dentro de la vista principal de materiales.
     */
    public function index(Request $request)
    {
        return redirect(route('biblioteca-fisica.materiales.index') . '#categorias');
    }

    public function create()
    {
        return view('material-didactico.categorias.create', ['categoria' => new CategoriaMaterialDidactico()]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:120|unique:categorias_material_didactico,nombre',
            'codigo' => 'nullable|string|max:50|unique:categorias_material_didactico,codigo',
            'descripcion' => 'nullable|string|max:2000',
        ]);

        CategoriaMaterialDidactico::create($request->only(['nombre', 'codigo', 'descripcion']));

        return redirect(route('biblioteca-fisica.materiales.index') . '#categorias')
            ->with('success', 'Categoría de material didáctico creada correctamente.');
    }

    public function edit(CategoriaMaterialDidactico $material_categoria)
    {
        return view('material-didactico.categorias.edit', ['categoria' => $material_categoria]);
    }

    public function update(Request $request, CategoriaMaterialDidactico $material_categoria)
    {
        $request->validate([
            'nombre' => ['required', 'string', 'max:120', Rule::unique('categorias_material_didactico', 'nombre')->ignore($material_categoria->id)],
            'codigo' => ['nullable', 'string', 'max:50', Rule::unique('categorias_material_didactico', 'codigo')->ignore($material_categoria->id)],
            'descripcion' => 'nullable|string|max:2000',
        ]);

        $material_categoria->update($request->only(['nombre', 'codigo', 'descripcion']));

        return redirect(route('biblioteca-fisica.materiales.index') . '#categorias')
            ->with('success', 'Categoría de material didáctico actualizada correctamente.');
    }

    public function destroy(CategoriaMaterialDidactico $material_categoria)
    {
        if ($material_categoria->materiales()->exists()) {
            return redirect(route('biblioteca-fisica.materiales.index') . '#categorias')
                ->with('error', 'No se puede eliminar la categoría mientras tenga materiales asociados.');
        }

        $material_categoria->delete();

        return redirect(route('biblioteca-fisica.materiales.index') . '#categorias')
            ->with('success', 'Categoría de material didáctico eliminada correctamente.');
    }
}
