<?php

namespace App\Http\Controllers;

use App\Models\TaqeCategoria;
use App\Models\TaqeTrabajo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TaqeTrabajoAdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $query = TaqeTrabajo::with('categoria')->latest();

        if ($request->filled('q')) {
            $term = trim($request->q);
            $query->where(function ($q) use ($term) {
                $q->where('titulo', 'like', "%{$term}%")
                    ->orWhere('autores', 'like', "%{$term}%")
                    ->orWhere('descripcion', 'like', "%{$term}%");
            });
        }

        if ($request->filled('categoria_id')) {
            $query->where('categoria_id', $request->integer('categoria_id'));
        }

        if ($request->filled('formato')) {
            $query->where('formato_principal', $request->formato);
        }

        if ($request->filled('idioma')) {
            $query->where('idioma', $request->idioma);
        }

        $trabajos = $query->paginate(20)->withQueryString();
        $categorias = TaqeCategoria::activas()->get();

        return view('admin.taqe.trabajos.index', [
            'trabajos' => $trabajos,
            'categorias' => $categorias,
            'formatos' => TaqeTrabajo::FORMATOS,
            'idiomas' => TaqeTrabajo::IDIOMAS,
        ]);
    }

    public function create()
    {
        $trabajo = new TaqeTrabajo([
            'formato_principal' => 'pdf',
            'idioma' => 'castellano',
            'estado' => 'publicado',
            'año_periodo' => date('Y'),
        ]);
        $categorias = TaqeCategoria::activas()->get();

        return view('admin.taqe.trabajos.create', [
            'trabajo' => $trabajo,
            'categorias' => $categorias,
            'formatos' => TaqeTrabajo::FORMATOS,
            'idiomas' => TaqeTrabajo::IDIOMAS,
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validar($request);

        // Subida de archivos
        if ($request->hasFile('archivo_pdf')) {
            $data['archivo_pdf'] = $request->file('archivo_pdf')->store('taqe/documentos', 'public');
        }

        if ($request->hasFile('archivo_audio')) {
            $data['archivo_audio'] = $request->file('archivo_audio')->store('taqe/audios', 'public');
        }

        if ($request->hasFile('portada')) {
            $data['portada'] = $request->file('portada')->store('taqe/portadas', 'public');
        }

        $trabajo = TaqeTrabajo::create($data);

        return redirect()->route('admin.taqe.trabajos.index')
            ->with('success', "El trabajo '{$trabajo->titulo}' fue registrado exitosamente.");
    }

    public function edit(TaqeTrabajo $trabajo)
    {
        $categorias = TaqeCategoria::activas()->get();

        return view('admin.taqe.trabajos.edit', [
            'trabajo' => $trabajo,
            'categorias' => $categorias,
            'formatos' => TaqeTrabajo::FORMATOS,
            'idiomas' => TaqeTrabajo::IDIOMAS,
        ]);
    }

    public function update(Request $request, TaqeTrabajo $trabajo)
    {
        $data = $this->validar($request, $trabajo->id);

        if ($request->hasFile('archivo_pdf')) {
            if ($trabajo->archivo_pdf && Storage::disk('public')->exists($trabajo->archivo_pdf)) {
                Storage::disk('public')->delete($trabajo->archivo_pdf);
            }
            $data['archivo_pdf'] = $request->file('archivo_pdf')->store('taqe/documentos', 'public');
        }

        if ($request->hasFile('archivo_audio')) {
            if ($trabajo->archivo_audio && Storage::disk('public')->exists($trabajo->archivo_audio)) {
                Storage::disk('public')->delete($trabajo->archivo_audio);
            }
            $data['archivo_audio'] = $request->file('archivo_audio')->store('taqe/audios', 'public');
        }

        if ($request->hasFile('portada')) {
            if ($trabajo->portada && Storage::disk('public')->exists($trabajo->portada)) {
                Storage::disk('public')->delete($trabajo->portada);
            }
            $data['portada'] = $request->file('portada')->store('taqe/portadas', 'public');
        }

        $trabajo->update($data);

        return redirect()->route('admin.taqe.trabajos.index')
            ->with('success', "El trabajo '{$trabajo->titulo}' fue actualizado correctamente.");
    }

    public function destroy(TaqeTrabajo $trabajo)
    {
        $nombre = $trabajo->titulo;

        // Limpiar archivos físicos asociados si están en storage
        foreach (['archivo_pdf', 'archivo_audio', 'portada'] as $campo) {
            if ($trabajo->$campo && Storage::disk('public')->exists($trabajo->$campo)) {
                Storage::disk('public')->delete($trabajo->$campo);
            }
        }

        $trabajo->delete();

        return redirect()->route('admin.taqe.trabajos.index')
            ->with('success', "El trabajo '{$nombre}' fue eliminado.");
    }

    protected function validar(Request $request, ?int $id = null): array
    {
        return $request->validate([
            'categoria_id' => 'required|exists:taqe_categorias,id',
            'titulo' => 'required|string|max:255',
            'autores' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'formato_principal' => 'required|in:pdf,audio,video,imagen,multimedia',
            'idioma' => 'required|in:castellano,quechua,bilingue',
            'año_periodo' => 'nullable|string|max:50',
            'ciclo_carrera' => 'nullable|string|max:150',
            'url_video' => 'nullable|url|max:255',
            'destacado' => 'nullable|boolean',
            'estado' => 'required|in:publicado,borrador',
            'archivo_pdf' => 'nullable|file|mimes:pdf|max:51200', // max 50MB
            'archivo_audio' => 'nullable|file|mimes:mp3,wav,ogg,m4a,aac|max:51200',
            'portada' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);
    }
}
