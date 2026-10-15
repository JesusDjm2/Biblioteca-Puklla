<?php

namespace App\Http\Controllers;

use App\Models\TaqeCategoria;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TaqeCategoriaAdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $categorias = TaqeCategoria::withCount('trabajos')->orderBy('orden')->get();

        return view('admin.taqe.categorias.index', compact('categorias'));
    }

    public function create()
    {
        $categoria = new TaqeCategoria([
            'color' => '#c78e41',
            'icono' => 'fa-folder',
            'orden' => (TaqeCategoria::max('orden') ?? 0) + 1,
            'activo' => true,
        ]);

        return view('admin.taqe.categorias.create', compact('categoria'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:150|unique:taqe_categorias,nombre',
            'descripcion' => 'nullable|string',
            'color' => 'nullable|string|max:20',
            'icono' => 'nullable|string|max:50',
            'orden' => 'nullable|integer',
            'activo' => 'nullable|boolean',
        ]);

        $data['activo'] = $request->boolean('activo', true);
        $data['slug'] = Str::slug($data['nombre']);

        TaqeCategoria::create($data);

        return redirect()->route('admin.taqe.categorias.index')
            ->with('success', "Categoría '{$data['nombre']}' creada exitosamente.");
    }

    public function edit(TaqeCategoria $categoria)
    {
        return view('admin.taqe.categorias.edit', compact('categoria'));
    }

    public function update(Request $request, TaqeCategoria $categoria)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:150|unique:taqe_categorias,nombre,' . $categoria->id,
            'descripcion' => 'nullable|string',
            'color' => 'nullable|string|max:20',
            'icono' => 'nullable|string|max:50',
            'orden' => 'nullable|integer',
            'activo' => 'nullable|boolean',
        ]);

        $data['activo'] = $request->boolean('activo', true);
        if ($data['nombre'] !== $categoria->nombre) {
            $data['slug'] = Str::slug($data['nombre']);
        }

        $categoria->update($data);

        return redirect()->route('admin.taqe.categorias.index')
            ->with('success', "Categoría '{$categoria->nombre}' actualizada correctamente.");
    }

    public function destroy(TaqeCategoria $categoria)
    {
        if ($categoria->trabajos()->count() > 0) {
            return back()->with('error', "No se puede eliminar la categoría '{$categoria->nombre}' porque contiene trabajos asociados. Reasigna los trabajos primero.");
        }

        $nombre = $categoria->nombre;
        $categoria->delete();

        return redirect()->route('admin.taqe.categorias.index')
            ->with('success', "Categoría '{$nombre}' eliminada.");
    }
}
