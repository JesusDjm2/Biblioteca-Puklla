@extends('layouts.app')
@section('titulo', 'Canciones — Categorías')
@section('contenido')
    @include('partials.canciones-crud-header', [
        'active' => 'categorias',
        'stats' => [
            ['valor' => $categorias->count(), 'singular' => 'categoría', 'plural' => 'categorías', 'icono' => 'fa-folder'],
            ['valor' => $totalCanciones, 'singular' => 'canción', 'plural' => 'canciones', 'icono' => 'fa-music'],
        ],
        'botones' => [
            ['route' => route('cancionescat.create'), 'label' => 'Nueva categoría', 'icono' => 'fa-folder', 'clase' => 'btn-outline-secondary'],
            ['route' => route('canciones.create', ['volver' => 'categorias']), 'label' => 'Nueva canción', 'icono' => 'fa-plus'],
        ],
    ])

    <p class="bv-crud-meta mb-3">Elige una categoría para registrar una canción directamente en ella, o revisa sus canciones recientes.</p>

    @include('material-didactico.partials.alertas')

    @if ($categorias->isEmpty())
        <div class="bv-form-card text-center text-muted">
            Aún no hay categorías. <a href="{{ route('cancionescat.create') }}">Crea la primera</a> para empezar a registrar canciones.
        </div>
    @elseif ($categorias->count() > 6)
        <div class="cg-buscar">
            <input type="search" id="cg-q" class="form-control form-control-sm bv-toolbar-control"
                placeholder="Buscar categoría…" autocomplete="off" aria-label="Buscar categoría">
        </div>
    @endif

    @php
        $paleta = ['#28a745', '#c78e41', '#3f6ad8', '#8e44ad', '#dc3545', '#16a2b8', '#d35400', '#6c757d'];
        $iconos = ['youtube' => 'fa-youtube-play', 'drive' => 'fa-google', 'spotify' => 'fa-spotify'];
    @endphp

    <div class="cg-grid">
        @foreach ($categorias as $cat)
            <article class="cg-card" style="--cg: {{ $paleta[$loop->index % count($paleta)] }};"
                data-nombre="{{ Str::lower(Str::ascii($cat->nombre)) }}">
                <header class="cg-card__cab">
                    <span class="cg-card__icono"><i class="fa fa-music"></i></span>
                    <div class="cg-card__titulos">
                        <h2 class="cg-card__nombre">{{ $cat->nombre }}</h2>
                        <p class="cg-card__desc"><code>{{ $cat->url }}</code></p>
                    </div>
                    <div class="dropdown">
                        <button class="cg-card__menu" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="Más opciones">
                            <i class="fa fa-ellipsis-v"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-right">
                            <a class="dropdown-item" href="{{ route('cancionescat.edit', $cat->id) }}"><i class="fa fa-edit fa-fw"></i> Editar categoría</a>
                            <a class="dropdown-item" href="{{ route('cancionesPuklla') }}" target="_blank" rel="noopener"><i class="fa fa-external-link fa-fw"></i> Ver en la web</a>
                            <div class="dropdown-divider"></div>
                            <button type="button" class="dropdown-item text-danger btn-delete"
                                data-url="{{ route('cancionescat.destroy', $cat->id) }}"
                                data-nombre="{{ $cat->nombre }}" data-total="{{ $cat->canciones_count }}">
                                <i class="fa fa-trash fa-fw"></i> Eliminar categoría
                            </button>
                        </div>
                    </div>
                </header>

                <div class="cg-card__cifras">
                    <span><strong>{{ $cat->canciones_count }}</strong> {{ $cat->canciones_count === 1 ? 'canción' : 'canciones' }}</span>
                    @if ($cat->youtube_count)
                        <span class="cg-chip cg-chip--gris" title="En YouTube"><i class="fa fa-youtube-play"></i> {{ $cat->youtube_count }}</span>
                    @endif
                    @if ($cat->spotify_count)
                        <span class="cg-chip cg-chip--gris" title="En Spotify"><i class="fa fa-spotify"></i> {{ $cat->spotify_count }}</span>
                    @endif
                    @if ($cat->drive_count)
                        <span class="cg-chip cg-chip--gris" title="En Google Drive"><i class="fa fa-google"></i> {{ $cat->drive_count }}</span>
                    @endif
                </div>

                <ul class="cg-card__lista">
                    @forelse ($cat->canciones->take(3) as $cancion)
                        <li>
                            <a href="{{ route('canciones.edit', ['cancione' => $cancion->id, 'volver' => 'categorias']) }}" title="Editar «{{ $cancion->nombre }}»">
                                <span class="cg-card__item"><i class="fa {{ $iconos[$cancion->plataforma] ?? 'fa-music' }}"></i> {{ $cancion->nombre }}</span>
                                <span class="cg-card__fecha">{{ Str::limit($cancion->autor, 22) }}</span>
                            </a>
                        </li>
                    @empty
                        <li class="cg-card__vacia">Todavía no hay canciones en esta categoría.</li>
                    @endforelse
                </ul>

                <footer class="cg-card__pie">
                    <a href="{{ route('canciones.create', ['categoria_id' => $cat->id, 'volver' => 'categorias']) }}" class="btn btn-sm cg-btn-crear">
                        <i class="fa fa-plus"></i> Nueva canción
                    </a>
                    <a href="{{ route('canciones.index', ['categoria_id' => $cat->id]) }}" class="btn btn-sm btn-outline-secondary {{ $cat->canciones_count ? '' : 'disabled' }}">
                        <i class="fa fa-list"></i> Ver {{ $cat->canciones_count > 3 ? 'las '.$cat->canciones_count : 'canciones' }}
                    </a>
                </footer>
            </article>
        @endforeach
    </div>

    <form id="deleteForm" method="POST" class="d-none">
        @csrf
        @method('DELETE')
    </form>

    @include('partials.cg-scripts', ['entidad' => 'canción(es)', 'titulo' => 'Eliminar categoría de canciones'])
@endsection
