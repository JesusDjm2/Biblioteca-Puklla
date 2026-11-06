{{--
    Tarjeta de un resultado de búsqueda. Se usa igual en el carrusel ("Todo")
    y en la cuadrícula de la pestaña de un módulo.
    Parámetros: $modulo (clave de BuscadorGlobal::MODULOS), $item (modelo).
--}}
@switch($modulo)

    {{-- ── Biblioteca virtual ── --}}
    @case('libros')
        <article class="lib-book-card rb-tarjeta">
            <div class="lib-book-card__cover">
                <img src="https://drive.google.com/thumbnail?id={{ $item->identificador }}&sz=w400" alt="" loading="lazy"
                    onerror="this.onerror=null;this.src='{{ asset('img/biblioteca-muestra.jpg') }}'">
                <div class="lib-book-card__overlay">
                    <button type="button" class="lib-book-card__btn"
                        onclick="abrirVistaPreviaLibro({{ \Illuminate\Support\Js::from($item->identificador) }}, {{ \Illuminate\Support\Js::from($item->nombre) }})">
                        <i class="fa fa-eye"></i> Vista previa
                    </button>
                </div>
            </div>
            <div class="lib-book-card__info cmp-fila">
                <div>
                    <h3 class="lib-book-card__title rb-resaltar" title="{{ $item->nombre }}">
                        <a href="{{ $item->url_publica }}" class="text-reset text-decoration-none">{{ $item->nombre }}</a>
                    </h3>
                    @if ($item->autor)
                        <p class="lib-book-card__author rb-resaltar"><i class="fa fa-user"></i> {{ $item->autor }}</p>
                    @endif
                </div>
                @include('partials.compartir', ['url' => $item->url_publica, 'titulo' => $item->nombre])
            </div>
        </article>
        @break

    {{-- ── Biblioteca física ── --}}
    @case('fisicos')
        @php
            $cat = $item->categoriaFisica;
            $colorCat = \App\Models\CategoriaFisica::colorForCodigo($cat->codigo ?? null);
        @endphp
        <article class="lib-book-card cf-book-card--flat rb-tarjeta" style="--cat-color: {{ $colorCat }};">
            <div class="cf-book-card__accent"></div>
            <div class="lib-book-card__info">
                @if ($item->ubicacion)
                    <div class="cf-book-card__top">@include('partials.ubicacion-libro', ['libro' => $item])</div>
                @endif
                <h3 class="lib-book-card__title rb-resaltar" title="{{ $item->nombre }}">{{ $item->nombre }}</h3>
                @if ($item->autor)
                    <p class="lib-book-card__author rb-resaltar"><i class="fa fa-user"></i> {{ $item->autor }}</p>
                @endif
                <div class="cf-book-card__meta">
                    @if ($item->anio_publicacion)
                        <span class="cf-meta-pill"><i class="fa fa-calendar-alt"></i> {{ $item->anio_publicacion }}</span>
                    @endif
                    @if ($cat)
                        <span class="cf-cat-pill"><i class="fa fa-tag"></i> {{ Str::limit($cat->nombre, 20) }}</span>
                    @endif
                </div>
                @if ($item->codigo)
                    <div class="cf-book-card__code"><i class="fa fa-barcode"></i> {{ $item->codigo }}</div>
                @endif
            </div>
        </article>
        @break

    {{-- ── Repositorio Institucional ── --}}
    @case('repositorio')
        <article class="rb-card rb-card--doc rb-tarjeta">
            <a href="{{ route('repositorio.show', $item->slug) }}" class="rb-card__portada rb-card__portada--doc" tabindex="-1" aria-hidden="true">
                <i class="fa-solid fa-file-pdf"></i>
                @if ($item->tipo_documento)
                    <span class="rb-card__etiqueta">{{ $item->tipo_documento }}</span>
                @endif
            </a>
            <div class="rb-card__cuerpo">
                <h3 class="rb-card__titulo rb-resaltar rb-card__titulo--3">
                    <a href="{{ route('repositorio.show', $item->slug) }}">{{ $item->titulo }}</a>
                </h3>
                <p class="rb-card__sub rb-resaltar"><i class="fa fa-user"></i> {{ Str::limit($item->autores_texto, 70) }}</p>
                <p class="rb-card__pie">
                    @if ($item->anio)<span><i class="fa-regular fa-calendar"></i> {{ $item->anio }}</span>@endif
                    @if ($item->grado_academico)<span>{{ $item->grado_academico }}</span>@endif
                </p>
            </div>
        </article>
        @break

    {{-- ── Taq'e ── --}}
    @case('taqe')
        <article class="rb-card rb-tarjeta">
            <a href="{{ route('taqe.show', $item->slug) }}" class="rb-card__portada" tabindex="-1" aria-hidden="true">
                <img src="{{ $item->portada_url }}" alt="" loading="lazy">
                <span class="rb-card__etiqueta"><i class="fa-regular fa-calendar"></i> {{ $item->fecha_corta ?? 'Sin fecha' }}</span>
            </a>
            <div class="rb-card__cuerpo">
                @if ($item->categoria)
                    <span class="rb-card__cat">{{ $item->categoria->nombre }}</span>
                @endif
                <h3 class="rb-card__titulo rb-resaltar"><a href="{{ route('taqe.show', $item->slug) }}">{{ $item->titulo }}</a></h3>
                @if ($item->autores)
                    <p class="rb-card__sub rb-resaltar"><i class="fa fa-user"></i> {{ Str::limit($item->autores, 60) }}</p>
                @endif
                @if ($item->lugar)
                    <p class="rb-card__pie"><span><i class="fa fa-location-dot"></i> {{ Str::limit($item->lugar, 40) }}</span></p>
                @endif
            </div>
        </article>
        @break

    {{-- ── Videos ── --}}
    @case('videos')
        <article class="rb-card rb-tarjeta">
            <a href="{{ $item->url_publica }}" class="rb-card__portada rb-card__portada--video" tabindex="-1" aria-hidden="true">
                @if ($item->fuente === 'archivo')
                    <video src="{{ $item->archivo_url }}#t=1" preload="metadata" muted playsinline></video>
                @else
                    <img src="{{ $item->miniatura_url }}" alt="" loading="lazy"
                        onerror="this.onerror=null;this.src='{{ asset('img/biblioteca-muestra.jpg') }}'">
                @endif
                <span class="rb-card__play"><i class="fa-solid fa-play"></i></span>
            </a>
            <div class="rb-card__cuerpo">
                <div class="cmp-fila">
                    <h3 class="rb-card__titulo rb-resaltar"><a href="{{ $item->url_publica }}">{{ $item->nombre }}</a></h3>
                    @include('partials.compartir', ['url' => $item->url_publica, 'titulo' => $item->nombre, 'texto' => $item->descripcion])
                </div>
                <p class="rb-card__pie">
                    @if ($item->categoria)<span><i class="fa fa-folder-open"></i> {{ Str::limit($item->categoria->nombre, 24) }}</span>@endif
                    <span><i class="fa-regular fa-eye"></i> {{ $item->vistas_texto }}</span>
                </p>
            </div>
        </article>
        @break

    {{-- ── Canciones ── --}}
    @case('canciones')
        @php
            $plataformas = ['youtube' => ['YouTube', 'fa-brands fa-youtube'], 'spotify' => ['Spotify', 'fa-brands fa-spotify'], 'drive' => ['Drive', 'fa-brands fa-google-drive']];
            $plat = $plataformas[$item->plataforma] ?? null;
        @endphp
        <article class="rb-card rb-tarjeta">
            <a href="{{ $item->enlace ?? route('cancionesPuklla') }}" target="_blank" rel="noopener" class="rb-card__portada rb-card__portada--audio rb-card__portada--{{ $item->plataforma }}" tabindex="-1" aria-hidden="true">
                @if ($item->plataforma === 'youtube')
                    <img src="https://img.youtube.com/vi/{{ $item->youtube }}/hqdefault.jpg" alt="" loading="lazy">
                @else
                    <i class="{{ $plat[1] ?? 'fa-solid fa-music' }}"></i>
                @endif
                <span class="rb-card__play"><i class="fa-solid fa-play"></i></span>
            </a>
            <div class="rb-card__cuerpo">
                <h3 class="rb-card__titulo rb-resaltar">
                    <a href="{{ $item->enlace ?? route('cancionesPuklla') }}" target="_blank" rel="noopener">{{ $item->nombre }}</a>
                </h3>
                @if ($item->autor)
                    <p class="rb-card__sub rb-resaltar"><i class="fa fa-user"></i> {{ $item->autor }}</p>
                @endif
                <p class="rb-card__pie">
                    @if ($plat)<span><i class="{{ $plat[1] }}"></i> {{ $plat[0] }}</span>@endif
                    @if ($item->categoria)<span>{{ Str::limit($item->categoria->nombre, 20) }}</span>@endif
                </p>
            </div>
        </article>
        @break

    {{-- ── Material didáctico (se presta en biblioteca; no tiene página propia) ── --}}
    @case('material')
        @php $disponibles = $item->unidades_disponibles; @endphp
        <article class="rb-card rb-tarjeta">
            <div class="rb-card__portada rb-card__portada--material">
                @if ($img = $item->imagen_principal_url)
                    <img src="{{ $img }}" alt="" loading="lazy">
                @else
                    <i class="fa-solid fa-shapes"></i>
                @endif
                @if ($item->codigo)<span class="rb-card__etiqueta">{{ $item->codigo }}</span>@endif
            </div>
            <div class="rb-card__cuerpo">
                @if ($item->categoria)
                    <span class="rb-card__cat">{{ $item->categoria->nombre }}</span>
                @endif
                <h3 class="rb-card__titulo rb-resaltar">{{ $item->nombre }}</h3>
                <p class="rb-card__pie">
                    <span class="rb-disp {{ $disponibles > 0 ? 'is-si' : 'is-no' }}">
                        <i class="fa-solid {{ $disponibles > 0 ? 'fa-circle-check' : 'fa-clock' }}"></i>
                        {{ $disponibles > 0 ? $disponibles.' de '.$item->cantidad.' disponibles' : 'Todo prestado' }}
                    </span>
                </p>
                <p class="rb-card__nota">Se solicita en la biblioteca.</p>
            </div>
        </article>
        @break

@endswitch
