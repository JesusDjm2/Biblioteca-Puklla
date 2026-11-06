@extends('layouts.padre')
@section('titulo', '- '.$video->nombre)
@section('og_titulo', $video->nombre.' | Videos Puklla Virtual')
@section('og_descripcion', $video->descripcion ?: 'Video de '.($video->categoria->nombre ?? 'la EESP Pukllasunchis').' en Puklla Virtual.')
@section('og_imagen', $video->miniatura_url)
@section('og_tipo', 'video.other')

@php
    $fuentes = [
        'youtube' => ['nombre' => 'YouTube', 'icono' => 'fab fa-youtube'],
        'drive' => ['nombre' => 'Google Drive', 'icono' => 'fab fa-google-drive'],
        'archivo' => ['nombre' => 'Puklla Virtual', 'icono' => 'fas fa-film'],
    ];
    $fuente = $fuentes[$video->fuente] ?? ['nombre' => 'Video', 'icono' => 'fas fa-film'];
    $categoria = $video->categoria;

    // Datos estructurados para buscadores (las claves "@…" se arman por partes
    // para que Blade no las interprete como directivas).
    $at = '@';
    $jsonLd = array_filter([
        $at.'context' => 'https://schema.org',
        $at.'type' => 'VideoObject',
        'name' => $video->nombre,
        'description' => $video->descripcion ?: $video->nombre,
        'thumbnailUrl' => [$video->miniatura_url],
        'uploadDate' => $video->created_at?->toIso8601String(),
        'embedUrl' => $video->embed_url,
        'contentUrl' => $video->archivo_url,
        'url' => $video->url_publica,
        'publisher' => [$at.'type' => 'Organization', 'name' => 'EESP Pukllasunchis'],
    ]);
@endphp

@section('estilos')
    @include('partials.detalle-estilos')
    <script type="application/ld+json">{!! json_encode($jsonLd, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG) !!}</script>
@endsection

@section('contenido')
    <header class="det-hero">
        <img src="{{ $video->miniatura_url }}" alt="" class="det-hero__fondo" aria-hidden="true">
        <div class="container">
            @include('partials.migas', [
                'claro' => true,
                'items' => array_values(array_filter([
                    ['label' => 'Inicio', 'url' => route('index'), 'icono' => 'fas fa-home'],
                    ['label' => 'Videos', 'url' => route('videos')],
                    $categoria ? ['label' => $categoria->nombre, 'url' => route('videoscat.show', $categoria->url)] : null,
                    ['label' => $video->nombre, 'url' => $video->url_publica],
                ])),
            ])

            <div class="det-hero__cuerpo">
                @if ($categoria)
                    <a href="{{ route('videoscat.show', $categoria->url) }}" class="det-chip">
                        <i class="fas fa-folder-open"></i> {{ $categoria->nombre }}
                    </a>
                @endif
                <h1 class="det-hero__titulo">{{ $video->nombre }}</h1>
                <div class="det-hero__meta">
                    <span><i class="{{ $fuente['icono'] }}"></i> {{ $fuente['nombre'] }}</span>
                    @if ($video->created_at)
                        <span><i class="far fa-calendar"></i>
                            <time datetime="{{ $video->created_at->toDateString() }}">{{ $video->created_at->translatedFormat('j \d\e F \d\e Y') }}</time>
                        </span>
                    @endif
                    <span title="{{ number_format((int) $video->vistas) }} vistas"><i class="far fa-eye"></i> {{ $video->vistas_texto }}</span>
                    <span><i class="fas fa-university"></i> EESP Pukllasunchis</span>
                </div>
            </div>
        </div>
    </header>

    <div class="container det-main">
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="det-reproductor" id="detReproductor">
                    @if ($video->fuente === 'youtube')
                        {{-- Portada liviana: YouTube (≈1 MB de scripts) se carga solo al pulsar reproducir. --}}
                        <button type="button" class="det-portada" id="detPortada"
                            data-embed="{{ $video->embed_url }}&autoplay=1" aria-label="Reproducir «{{ $video->nombre }}»">
                            <img src="https://img.youtube.com/vi/{{ $video->youtube }}/maxresdefault.jpg"
                                onerror="this.onerror=null;this.src='{{ $video->miniatura_url }}'" alt="" fetchpriority="high">
                            <span class="det-portada__play"><i class="fas fa-play"></i></span>
                        </button>
                    @elseif ($video->embed_url)
                        <iframe src="{{ $video->embed_url }}" title="{{ $video->nombre }}" allowfullscreen
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"></iframe>
                    @elseif ($video->archivo)
                        <video controls preload="metadata" playsinline>
                            <source src="{{ $video->archivo_url }}" type="{{ $video->archivo_mime }}">
                            Tu navegador no puede reproducir este video.
                        </video>
                    @endif
                </div>

                <section class="det-panel" aria-labelledby="det-acerca">
                    <h2 class="det-panel__titulo" id="det-acerca">Acerca de este video</h2>
                    @if ($video->descripcion)
                        <p class="det-desc">{{ $video->descripcion }}</p>
                    @else
                        <p class="det-desc det-desc--vacia">Este video no tiene descripción.</p>
                    @endif

                    <div class="det-acciones">
                        @include('partials.compartir', [
                            'variante' => 'barra',
                            'url' => $video->url_publica,
                            'titulo' => $video->nombre,
                            'texto' => $video->descripcion,
                        ])
                        @if ($video->fuente === 'youtube')
                            <a href="https://www.youtube.com/watch?v={{ $video->youtube }}" target="_blank" rel="noopener" class="det-btn">
                                <i class="fab fa-youtube"></i> Ver en YouTube
                            </a>
                        @elseif ($video->fuente === 'drive')
                            <a href="https://drive.google.com/file/d/{{ $video->drive }}/view" target="_blank" rel="noopener" class="det-btn">
                                <i class="fab fa-google-drive"></i> Abrir en Drive
                            </a>
                        @endif
                    </div>
                </section>
            </div>

            <aside class="col-lg-4">
                <div class="det-aside">
                    <section class="det-panel det-panel--primero" aria-labelledby="det-mas">
                        <h2 class="det-panel__titulo" id="det-mas">Más de esta sección</h2>
                        @if ($relacionados->isNotEmpty())
                            <ol class="det-lista">
                                @foreach ($relacionados as $rel)
                                    <li>
                                        <a href="{{ $rel->url_publica }}" class="det-rel">
                                            <span class="det-rel__miniatura">
                                                <img src="{{ $rel->miniatura_url }}" alt="" loading="lazy">
                                                <span class="det-rel__play"><i class="fas fa-play"></i></span>
                                            </span>
                                            <span>
                                                <span class="det-rel__titulo">{{ $rel->nombre }}</span>
                                                <span class="det-rel__sub">{{ $rel->created_at?->diffForHumans() }}</span>
                                            </span>
                                        </a>
                                    </li>
                                @endforeach
                            </ol>
                        @else
                            <p class="det-vacio">No hay más videos en esta sección.</p>
                        @endif

                        @if ($categoria)
                            <a href="{{ route('videoscat.show', $categoria->url) }}" class="det-btn det-btn--primario w-100 mt-3">
                                Ver toda la sección <i class="fas fa-arrow-right"></i>
                            </a>
                        @endif
                    </section>
                </div>
            </aside>
        </div>
    </div>

    <script>
        (function () {
            var portada = document.getElementById('detPortada');
            if (!portada) return;
            portada.addEventListener('click', function () {
                var iframe = document.createElement('iframe');
                iframe.src = portada.dataset.embed;
                iframe.title = document.querySelector('.det-hero__titulo').textContent;
                iframe.allow = 'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture';
                iframe.allowFullscreen = true;
                portada.replaceWith(iframe);
                iframe.focus();
            });
        })();
    </script>
@endsection
