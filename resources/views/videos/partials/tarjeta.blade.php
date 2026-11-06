{{--
    Tarjeta pública de un video. La miniatura lleva a la página del video
    (donde se reproduce, se comparte y se cuenta la vista).
    Parámetros: $video, $mostrarCategoria (bool, opcional).
--}}
@once
    <style>
        .video-card__thumb > a { display: block; width: 100%; height: 100%; position: relative; }
        .video-card__thumb video { width: 100%; height: 100%; object-fit: cover; display: block; pointer-events: none; background: #1a1612; }
        .video-card__fuente {
            position: absolute; left: 8px; bottom: 8px; z-index: 1;
            display: inline-flex; align-items: center; gap: 4px;
            font-size: .68rem; font-weight: 600; color: #fff;
            background: rgba(20, 16, 12, .72); padding: 2px 8px; border-radius: 999px;
        }
        .video-card__pie {
            margin-top: auto; padding-top: 6px;
            display: flex; align-items: center; justify-content: space-between; gap: 8px; flex-wrap: wrap;
        }
        .video-card__pie .video-card__cat { margin-top: 0; min-width: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; max-width: 100%; }
        .video-card__vistas { font-size: .72rem; color: #8a7a6a; display: inline-flex; align-items: center; gap: 4px; white-space: nowrap; }
    </style>
@endonce

@php
    $fuentesTarjeta = [
        'youtube' => ['YouTube', 'fab fa-youtube'],
        'drive' => ['Drive', 'fab fa-google-drive'],
        'archivo' => ['Video', 'fas fa-film'],
    ];
    $fuenteTarjeta = $fuentesTarjeta[$video->fuente] ?? null;
@endphp

<div class="video-card">
    <div class="video-card__thumb">
        <a href="{{ $video->url_publica }}" aria-label="Ver «{{ $video->nombre }}»">
            @if ($video->fuente === 'archivo')
                {{-- Primer cuadro del video como miniatura (sin cargar el video completo). --}}
                <video src="{{ $video->archivo_url }}#t=1" preload="metadata" muted playsinline aria-hidden="true" tabindex="-1"></video>
            @else
                <img src="{{ $video->miniatura_url }}" alt="{{ $video->nombre }}" loading="lazy"
                    onerror="this.onerror=null;this.src='{{ asset('img/biblioteca-muestra.jpg') }}'">
            @endif
            <span class="video-card__overlay"><i class="fas fa-play-circle"></i></span>
            @if ($fuenteTarjeta)
                <span class="video-card__fuente"><i class="{{ $fuenteTarjeta[1] }}"></i> {{ $fuenteTarjeta[0] }}</span>
            @endif
        </a>
    </div>
    <div class="video-card__body">
        <div class="cmp-fila">
            <h3 class="video-card__title" title="{{ $video->nombre }}">
                <a href="{{ $video->url_publica }}" class="text-reset text-decoration-none">{{ $video->nombre }}</a>
            </h3>
            @include('partials.compartir', ['url' => $video->url_publica, 'titulo' => $video->nombre, 'texto' => $video->descripcion])
        </div>
        @if ($video->descripcion)
            <p class="video-card__desc">{{ Str::limit($video->descripcion, 80) }}</p>
        @endif
        <div class="video-card__pie">
            @if (!empty($mostrarCategoria) && $video->categoria)
                <a href="{{ route('videoscat.show', $video->categoria->url) }}" class="video-card__cat">
                    <i class="fa fa-folder-open"></i> {{ $video->categoria->nombre }}
                </a>
            @endif
            <span class="video-card__vistas" title="{{ number_format((int) $video->vistas) }} {{ (int) $video->vistas === 1 ? 'vista' : 'vistas' }}">
                <i class="far fa-eye"></i> {{ $video->vistas_texto }}
            </span>
        </div>
    </div>
</div>
