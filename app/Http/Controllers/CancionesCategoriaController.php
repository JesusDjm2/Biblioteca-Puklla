<?php

namespace App\Http\Controllers;

use App\Models\CancionesCategoria;
use Illuminate\Http\Request;

class CancionesCategoriaController extends Controller
{
    public function index()
    {
        $categorias = CancionesCategoria::withCount([
                'canciones',
                'canciones as youtube_count' => fn ($q) => $q->whereNotNull('youtube')->where('youtube', '!=', ''),
                'canciones as drive_count' => fn ($q) => $q->whereNotNull('drive')->where('drive', '!=', ''),
                'canciones as spotify_count' => fn ($q) => $q->whereNotNull('spotify')->where('spotify', '!=', ''),
            ])
            ->with(['canciones' => fn ($q) => $q->select('id', 'categoria_id', 'nombre', 'autor', 'youtube', 'drive', 'spotify')->latest('id')])
            ->orderBy('nombre')
            ->get();

        return view('canciones.categorias.index', [
            'categorias' => $categorias,
            'totalCanciones' => $categorias->sum('canciones_count'),
        ]);
    }

    public function create()
    {
        return view('canciones.categorias.create');
    }

    public function store(Request $request)
    {
        $categoria = CancionesCategoria::create($this->validar($request));

        return redirect()->route('cancionescat.index')->with('success', "Categoría '{$categoria->nombre}' creada.");
    }

    public function edit($id)
    {
        $categoria = CancionesCategoria::findOrFail($id);

        return view('canciones.categorias.edit', compact('categoria'));
    }

    public function update(Request $request, $id)
    {
        $categoria = CancionesCategoria::findOrFail($id);
        $categoria->update($this->validar($request, $categoria->id));

        return redirect()->route('cancionescat.index')->with('success', "Categoría '{$categoria->nombre}' actualizada.");
    }

    public function destroy($id)
    {
        $categoria = CancionesCategoria::findOrFail($id);

        if ($categoria->canciones()->exists()) {
            return redirect()->route('cancionescat.index')
                ->with('error', "No se puede eliminar '{$categoria->nombre}' porque tiene canciones. Muévelas a otra categoría o elimínalas primero.");
        }

        $categoria->delete();

        return redirect()->route('cancionescat.index')->with('success', "Categoría '{$categoria->nombre}' eliminada.");
    }

    private function validar(Request $request, ?int $id = null): array
    {
        return $request->validate([
            'nombre' => 'required|string|max:255',
            'url' => 'required|string|max:255|unique:canciones_categorias,url'.($id ? ','.$id : ''),
        ], [
            'url.unique' => 'Ya existe una categoría con esa URL; usa un nombre distinto.',
        ]);
    }
}
