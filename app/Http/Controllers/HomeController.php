<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Seccion;
use App\Models\Categoria;
use App\Models\Libro;
use App\Models\LibroFisico;
use App\Models\CategoriaFisica;
use App\Models\Video;
use App\Models\VideoCategoria;
use App\Models\Canciones;
use App\Models\CancionesCategoria;
use App\Models\Prestamo;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $stats = [
            'secciones' => Seccion::count(),
            'categorias' => Categoria::count(),
            'libros' => Libro::count(),
            'libros_fisicos' => LibroFisico::count(),
            'categorias_fisicas' => CategoriaFisica::count(),
            'video_categorias' => VideoCategoria::count(),
            'videos' => Video::count(),
            'canciones_categorias' => CancionesCategoria::count(),
            'canciones' => Canciones::count(),
            'prestamos_total' => Prestamo::count(),
            'prestamos_activos' => Prestamo::activos()->count(),
            'prestamos_atrasados' => Prestamo::atrasados()->count(),
        ];

        return view('home', compact('stats'));
    }
}
