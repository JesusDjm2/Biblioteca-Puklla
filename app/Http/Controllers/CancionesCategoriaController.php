<?php

namespace App\Http\Controllers;

use App\Models\CancionesCategoria;
use Illuminate\Http\Request;

class CancionesCategoriaController extends Controller
{
    public function index()
    {
        $categorias = CancionesCategoria::all();
        return view('canciones.categorias.index', compact('categorias'));
    }
    public function create()
    {
        return view('canciones.categorias.create');
    }
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'url' => 'required|string|max:255|unique:canciones_categorias,url',
        ]);

        CancionesCategoria::create([
            'nombre' => $request->nombre,
            'url' => $request->url,
        ]);

        return redirect()->route('cancionescat.index')->with('success', 'Categoría de canción creada exitosamente.');
    }
    public function edit($id)
    {
        $categoria = CancionesCategoria::findOrFail($id);

        return view('canciones.categorias.edit', compact('categoria'));
    }
    public function update(Request $request, $id)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'url' => 'required|string|max:255|unique:canciones_categorias,url,' . $id,
        ]);

        $categoria = CancionesCategoria::findOrFail($id);
        $categoria->update([
            'nombre' => $request->nombre,
            'url' => $request->url,
        ]);

        return redirect()->route('cancionescat.index')->with('success', 'Categoría de canción actualizada exitosamente.');
    }
    public function destroy($id)
    {
        $categoria = CancionesCategoria::findOrFail($id);
        $categoria->delete();

        return redirect()->route('cancionescat.index')->with('success', 'Categoría de canción eliminada exitosamente.');
    }
}
