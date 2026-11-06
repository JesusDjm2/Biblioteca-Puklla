<?php

namespace App\Http\Controllers;

use App\Models\Canciones;
use App\Models\CancionesCategoria;
use App\Models\Video;
use Illuminate\Http\Request;

class CancionesController extends Controller
{
    public function index(Request $request)
    {
        $query = Canciones::query()->with('categoria')->latest('id');

        if ($request->filled('q')) {
            $term = '%'.addcslashes($request->input('q'), '%_\\').'%';
            $query->where(fn ($q) => $q->where('nombre', 'like', $term)->orWhere('autor', 'like', $term));
        }

        if ($request->filled('categoria_id')) {
            $query->where('categoria_id', $request->integer('categoria_id'));
        }

        if (in_array($request->input('plataforma'), Canciones::PLATAFORMAS, true)) {
            $campo = $request->input('plataforma');
            $query->whereNotNull($campo)->where($campo, '!=', '');
        }

        return view('canciones.index', [
            'canciones' => $query->paginate(25)->withQueryString(),
            'categorias' => CancionesCategoria::orderBy('nombre')->get(),
        ]);
    }

    /** Página pública con todas las canciones. */
    public function canciones()
    {
        $categorias = CancionesCategoria::withCount('canciones')->orderBy('nombre', 'asc')->get();
        $canciones = Canciones::all();

        return view('canciones.lista', compact('categorias', 'canciones'));
    }

    public function create(Request $request)
    {
        // Desde una categoría se llega con ?categoria_id=X: queda preseleccionada.
        $cancion = new Canciones([
            'categoria_id' => CancionesCategoria::whereKey($request->integer('categoria_id'))->value('id'),
        ]);

        return view('canciones.create', [
            'cancion' => $cancion,
            'categorias' => CancionesCategoria::orderBy('nombre')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $cancion = Canciones::create($this->validar($request));

        return $this->volver($request)->with('success', "Canción '{$cancion->nombre}' creada correctamente.");
    }

    public function edit($id)
    {
        return view('canciones.edit', [
            'cancion' => Canciones::findOrFail($id),
            'categorias' => CancionesCategoria::orderBy('nombre')->get(),
        ]);
    }

    public function update(Request $request, $id)
    {
        $cancion = Canciones::findOrFail($id);
        $cancion->update($this->validar($request));

        return $this->volver($request)->with('success', "Canción '{$cancion->nombre}' actualizada correctamente.");
    }

    public function destroy(Request $request, $id)
    {
        $cancion = Canciones::findOrFail($id);
        $cancion->delete();

        return $this->volver($request)->with('success', "Canción '{$cancion->nombre}' eliminada.");
    }

    /** Tras guardar: vuelve a Categorías si se partió de ahí; si no, al listado de canciones. */
    protected function volver(Request $request)
    {
        return $request->input('volver') === 'categorias'
            ? redirect()->route('cancionescat.index')
            : redirect()->route('canciones.index');
    }

    /**
     * Valida la canción y guarda solo la plataforma elegida. Acepta el enlace
     * completo (YouTube, Drive o Spotify) o solo su identificador.
     */
    protected function validar(Request $request): array
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'autor' => 'required|string|max:255',
            'categoria_id' => 'required|exists:canciones_categorias,id',
            'plataforma' => 'required|in:'.implode(',', Canciones::PLATAFORMAS),
            'youtube' => 'required_if:plataforma,youtube|nullable|string|max:255',
            'drive' => 'required_if:plataforma,drive|nullable|string|max:255',
            'spotify' => 'required_if:plataforma,spotify|nullable|string|max:255',
        ], [
            'plataforma.required' => 'Elige dónde está publicada la canción (YouTube, Drive o Spotify).',
            'youtube.required_if' => 'Pega el enlace o el identificador de YouTube.',
            'drive.required_if' => 'Pega el enlace o el identificador de Google Drive.',
            'spotify.required_if' => 'Pega el enlace o el identificador de Spotify.',
        ]);

        $plataforma = $request->input('plataforma');

        return [
            'nombre' => $request->input('nombre'),
            'autor' => $request->input('autor'),
            'categoria_id' => $request->integer('categoria_id'),
            'youtube' => $plataforma === 'youtube' ? Video::extraerYoutubeId($request->input('youtube')) : null,
            'drive' => $plataforma === 'drive' ? Video::extraerDriveId($request->input('drive')) : null,
            'spotify' => $plataforma === 'spotify' ? Canciones::extraerSpotifyId($request->input('spotify')) : null,
        ];
    }
}
