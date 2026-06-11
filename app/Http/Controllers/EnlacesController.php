<?php

namespace App\Http\Controllers;

use App\Models\Canciones;
use App\Models\Libro;
use App\Models\Seccion;
use App\Models\User;
use App\Models\Video;
use Illuminate\Http\Request;

class EnlacesController extends Controller
{
    public function index()
    {
        $libros = Libro::latest()->take(8)->get();
        $videos = Video::latest()->take(6)->get();
        return view('welcome', compact('libros', 'videos'));
    }
    public function busqueda(Request $request)
    {
        $query = $request->input('query');
        // Buscar en Canciones
        $canciones = Canciones::where('nombre', 'like', "%$query%")
            ->orWhere('autor', 'like', "%$query%")
            ->get();
        $cancionesCount = $canciones->count();

        // Buscar en Videos
        $videos = Video::where('nombre', 'like', "%$query%")
            ->get();
        $videosCount = $videos->count();

        // Buscar en Libros
        $libros = Libro::where('nombre', 'like', "%$query%")
            ->orWhere('autor', 'like', "%$query%")
            ->get();
        $librosCount = $libros->count();

        $hasResults = !$canciones->isEmpty() || !$videos->isEmpty() || !$libros->isEmpty();

        // Devolver resultados a la vista buscador
        return view('buscador', [
            'canciones' => $canciones,
            'videos' => $videos,
            'libros' => $libros,
            'query' => $query,
            'hasResults' => $hasResults,
            'cancionesCount' => $cancionesCount,
            'videosCount' => $videosCount,
            'librosCount' => $librosCount,
        ]);
    }
    public function admin()
    {
        $admins = User::all();
        return view('auth.admins.index', compact('admins'));
    }
}
