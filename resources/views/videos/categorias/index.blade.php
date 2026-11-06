@extends('layouts.app')
@section('titulo', 'Videos — Categorías')
@section('contenido')
    @include('partials.videos-crud-header', [
        'active' => 'categorias',
        'stats' => [
            ['valor' => $videoCategorias->count(), 'singular' => 'categoría', 'plural' => 'categorías', 'icono' => 'fa-folder'],
            ['valor' => $totalVideos, 'singular' => 'video', 'plural' => 'videos', 'icono' => 'fa-film'],
        ],
        'botones' => [
            ['route' => route('videoscat.create'), 'label' => 'Nueva categoría', 'icono' => 'fa-folder', 'clase' => 'btn-outline-secondary'],
            ['route' => route('videos.import.form'), 'label' => 'Importar', 'icono' => 'fa-upload', 'clase' => 'btn-outline-info'],
            ['route' => route('videos.create', ['volver' => 'categorias']), 'label' => 'Agregar video', 'icono' => 'fa-plus'],
        ],
    ])

    <p class="bv-crud-meta mb-3">Elige una categoría para agregar un video directamente en ella, o revisa sus videos recientes.</p>

    @include('material-didactico.partials.alertas')

    @if ($videoCategorias->isEmpty())
        <div class="bv-form-card text-center text-muted">
            Aún no hay categorías. <a href="{{ route('videoscat.create') }}">Crea la primera</a> para empezar a agregar videos.
        </div>
    @elseif ($videoCategorias->count() > 6)
        <div class="cg-buscar">
            <input type="search" id="cg-q" class="form-control form-control-sm bv-toolbar-control"
                placeholder="Buscar categoría…" autocomplete="off" aria-label="Buscar categoría">
        </div>
    @endif

    @php
        $paleta = ['#dc3545', '#c78e41', '#1f8a70', '#3f6ad8', '#8e44ad', '#d35400', '#16a2b8', '#6c757d'];
        $iconosFuente = ['youtube' => 'fa-youtube-play', 'drive' => 'fa-google', 'archivo' => 'fa-file-video-o'];
    @endphp

    <div class="cg-grid">
        @foreach ($videoCategorias as $cat)
            <article class="cg-card" style="--cg: {{ $paleta[$loop->index % count($paleta)] }};"
                data-nombre="{{ Str::lower(Str::ascii($cat->nombre)) }}">
                <header class="cg-card__cab">
                    <span class="cg-card__icono"><i class="fa fa-film"></i></span>
                    <div class="cg-card__titulos">
                        <h2 class="cg-card__nombre">{{ $cat->nombre }}</h2>
                        <p class="cg-card__desc"><code>{{ $cat->url }}</code></p>
                    </div>
                    <div class="dropdown">
                        <button class="cg-card__menu" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="Más opciones">
                            <i class="fa fa-ellipsis-v"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-right">
                            <a class="dropdown-item" href="{{ route('videoscat.edit', $cat->id) }}"><i class="fa fa-edit fa-fw"></i> Editar categoría</a>
                            <a class="dropdown-item" href="{{ route('videoscat.show', $cat->url) }}" target="_blank" rel="noopener"><i class="fa fa-external-link fa-fw"></i> Ver en la web</a>
                            <div class="dropdown-divider"></div>
                            <button type="button" class="dropdown-item text-danger btn-delete"
                                data-url="{{ route('videoscat.destroy', $cat->id) }}"
                                data-nombre="{{ $cat->nombre }}" data-total="{{ $cat->videos_count }}">
                                <i class="fa fa-trash fa-fw"></i> Eliminar categoría
                            </button>
                        </div>
                    </div>
                </header>

                <div class="cg-card__cifras">
                    <span><strong>{{ $cat->videos_count }}</strong> {{ $cat->videos_count === 1 ? 'video' : 'videos' }}</span>
                    @if ($cat->videos_sum_vistas)
                        <span class="cg-chip cg-chip--gris" title="Vistas en la web"><i class="fa fa-eye"></i> {{ number_format($cat->videos_sum_vistas) }}</span>
                    @endif
                    @if ($cat->youtube_count)
                        <span class="cg-chip cg-chip--gris" title="En YouTube"><i class="fa fa-youtube-play"></i> {{ $cat->youtube_count }}</span>
                    @endif
                    @if ($cat->drive_count)
                        <span class="cg-chip cg-chip--gris" title="En Google Drive"><i class="fa fa-google"></i> {{ $cat->drive_count }}</span>
                    @endif
                    @if ($cat->archivo_count)
                        <span class="cg-chip cg-chip--gris" title="Subidos al servidor"><i class="fa fa-file-video-o"></i> {{ $cat->archivo_count }}</span>
                    @endif
                </div>

                <ul class="cg-card__lista">
                    @forelse ($cat->videos->take(3) as $video)
                        <li>
                            <a href="{{ route('videos.edit', ['Video' => $video->id, 'volver' => 'categorias']) }}" title="Editar «{{ $video->nombre }}»">
                                <span class="cg-card__item"><i class="fa {{ $iconosFuente[$video->fuente] ?? 'fa-film' }}"></i> {{ $video->nombre }}</span>
                                <span class="cg-card__fecha">{{ $video->created_at?->format('d/m/Y') ?? '' }}</span>
                            </a>
                        </li>
                    @empty
                        <li class="cg-card__vacia">Todavía no hay videos en esta categoría.</li>
                    @endforelse
                </ul>

                <footer class="cg-card__pie">
                    <a href="{{ route('videos.create', ['categoria_id' => $cat->id, 'volver' => 'categorias']) }}" class="btn btn-sm cg-btn-crear">
                        <i class="fa fa-plus"></i> Nuevo video
                    </a>
                    <a href="{{ route('videos.index', ['categoria_id' => $cat->id]) }}" class="btn btn-sm btn-outline-secondary {{ $cat->videos_count ? '' : 'disabled' }}">
                        <i class="fa fa-list"></i> Ver {{ $cat->videos_count > 3 ? 'los '.$cat->videos_count : 'videos' }}
                    </a>
                </footer>
            </article>
        @endforeach
    </div>

    <form id="deleteForm" method="POST" class="d-none">
        @csrf
        @method('DELETE')
    </form>

    @include('partials.cg-scripts', ['entidad' => 'video(s)', 'titulo' => 'Eliminar categoría de videos'])
@endsection
