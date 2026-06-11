<?php

namespace App\Http\Controllers;

use App\Models\Canciones;
use App\Models\CancionesCategoria;
use Illuminate\Http\Request;

class CancionesController extends Controller
{
    public function index()
    {
        $canciones = Canciones::all();
        return view('canciones.index', compact('canciones'));
    }
    public function canciones()
    {
        $categorias = CancionesCategoria::withCount('canciones')->orderBy('nombre', 'asc')->get();        
        $canciones = Canciones::all();
        return view('canciones.lista', compact('categorias', 'canciones'));
    }
    public function create()
    {
        $categorias = CancionesCategoria::all();
        
        return view('canciones.create', compact('categorias'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'autor' => 'required|string|max:255',
            'youtube' => 'nullable|string|max:255',
            'drive' => 'nullable|string|max:255',
            'spotify' => 'nullable|string|max:255',
            'categoria_id' => 'required|exists:canciones_categorias,id',
        ]);

        $cancion = new Canciones();
        $cancion->nombre = $request->nombre;
        $cancion->autor = $request->autor;
        $cancion->youtube = $request->youtube;
        $cancion->drive = $request->drive;
        $cancion->spotify = $request->spotify;
        $cancion->categoria_id = $request->categoria_id;
        $cancion->save();

        return redirect()->route('canciones.index')->with('success', 'Canción creada correctamente.');
    }
    public function edit($id)
    {
        $cancion = Canciones::findOrFail($id);
        $categorias = CancionesCategoria::all(); // Opcional: podrías limitar las categorías según tus necesidades

        return view('canciones.edit', compact('cancion', 'categorias'));
    }
    public function update(Request $request, $id)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'autor' => 'required|string|max:255',
            'youtube' => 'nullable|string|max:255',
            'drive' => 'nullable|string|max:255',
            'spotify' => 'nullable|string|max:255',
            'categoria_id' => 'required|exists:canciones_categorias,id',
        ]);

        $cancion = Canciones::findOrFail($id);
        $cancion->nombre = $request->nombre;
        $cancion->autor = $request->autor;
        $cancion->youtube = $request->youtube;
        $cancion->drive = $request->drive;
        $cancion->spotify = $request->spotify;
        $cancion->categoria_id = $request->categoria_id;
        $cancion->save();

        return redirect()->route('canciones.index')->with('success', 'Canción actualizada correctamente.');
    }


}
