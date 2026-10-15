@extends('layouts.padre')
@section('titulo', '- Resultados de búsqueda')

@section('estilos')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrollTrigger.min.js"></script>
    <style>
        /* ═══════════════════════════════════════════════════════
           RESULTADOS DE BÚSQUEDA – complementos al sistema "lib-*"
        ═══════════════════════════════════════════════════════ */

        .rb-section { margin-bottom: 2.4rem; }
        .rb-section:last-child { margin-bottom: 0; }

        /* ── Tarjeta genérica para videos / canciones ── */
        .rb-media-card {
            background: #fff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(42,37,32,.07);
            border: 1px solid rgba(200,142,65,.1);
            transition: box-shadow .28s ease, transform .28s ease;
            height: 100%;
            display: flex;
            flex-direction: column;
        }
        .rb-media-card:hover {
            box-shadow: 0 8px 28px rgba(42,37,32,.14);
            transform: translateY(-3px);
        }
        .rb-media-card__thumb {
            position: relative;
            width: 100%;
            aspect-ratio: 16 / 9;
            overflow: hidden;
            background: #1a1612;
            flex-shrink: 0;
        }
        .rb-media-card__thumb img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
            transition: transform .4s ease;
        }
        .rb-media-card:hover .rb-media-card__thumb img { transform: scale(1.05); }
        .rb-media-card__thumb iframe {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            border: 0;
        }
        .rb-media-card__play {
            position: absolute;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.6rem;
            color: rgba(255,255,255,.92);
            text-shadow: 0 2px 10px rgba(0,0,0,.5);
            background: rgba(20,16,12,.18);
            transition: background .25s ease, transform .25s ease;
            pointer-events: none;
        }
        .rb-media-card:hover .rb-media-card__play { background: rgba(20,16,12,.32); transform: scale(1.06); }
        .rb-media-card__thumb--drive .rb-media-card__play { color: #38bdf8; }
        .rb-media-card__info {
            padding: 10px 12px 12px;
            border-top: 2px solid rgba(200,142,65,.2);
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 5px;
        }
        .rb-media-card__title {
            font-family: "Lato", system-ui, -apple-system, sans-serif;
            font-size: .95rem;
            font-weight: 600;
            color: #2a2520;
            margin: 0;
            line-height: 1.4;
        }
        .rb-media-card__desc {
            font-size: .78rem;
            color: #9c8c7c;
            margin: 0;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            line-height: 1.4;
        }
        .rb-media-card__author {
            font-size: .78rem;
            color: #b8a898;
            margin: 0;
        }

        /* ── Tarjeta Spotify ── */
        .rb-spotify-card { border-radius: 12px; overflow: hidden; box-shadow: 0 2px 10px rgba(42,37,32,.07); }

        /* ── Buscador de refinamiento ── */
        .rb-refine { margin-bottom: 2rem; }

        /* ── Estado vacío ── */
        .rb-empty {
            text-align: center;
            padding: 3.4rem 1rem;
            color: #b8a898;
            background: #fff;
            border: 1px solid rgba(200,142,65,.13);
            border-radius: 12px;
        }
        .rb-empty i { font-size: 2.8rem; margin-bottom: .9rem; display: block; color: var(--puklla-accent); opacity: .5; }
        .rb-empty p { font-size: .95rem; margin: 0; color: #7a6a5a; }

        /* ── Resaltado de coincidencias ── */
        .rb-hl {
            background: #FFEB3B;
            color: #2a2520;
            border-radius: 2px;
            padding: 0 2px;
            font-weight: 600;
        }
    </style>
@endsection

@section('contenido')

    {{-- ═══ HERO ═══ --}}
    <div class="lib-hero">
        <img src="{{ asset('img/fondo/tramites-2-2026.jpeg') }}" alt="Resultados de búsqueda Pukllasunchis"
             class="lib-hero__bg" loading="eager">
        <div class="lib-hero__overlay"></div>
        <div class="lib-hero__content">
            <p class="lib-hero__eyebrow">ESPP Pukllasunchis · Resultados de búsqueda</p>
            <h1 class="lib-hero__title lib-hero__title--cat">&ldquo;{{ $query }}&rdquo;</h1>
            <div class="lib-hero__divider"></div>
            @if ($hasResults)
                @php
                    $resumen = [];
                    if ($librosCount > 0)       $resumen[] = $librosCount . ' ' . ($librosCount === 1 ? 'texto' : 'textos');
                    if ($librosFisicosCount > 0) $resumen[] = $librosFisicosCount . ' ' . ($librosFisicosCount === 1 ? 'libro físico' : 'libros físicos');
                    if ($videosCount > 0)       $resumen[] = $videosCount . ' ' . ($videosCount === 1 ? 'video' : 'videos');
                    if ($taqeTrabajosCount > 0) $resumen[] = $taqeTrabajosCount . ' ' . ($taqeTrabajosCount === 1 ? "trabajo Taq'e" : "trabajos Taq'e");
                    if ($cancionesCount > 0)    $resumen[] = $cancionesCount . ' ' . ($cancionesCount === 1 ? 'canción' : 'canciones');
                @endphp
                <div class="lib-hero__meta">
                    <span><i class="fa fa-check-circle"></i> {{ implode(' · ', $resumen) }}</span>
                </div>
            @else
                <div class="lib-hero__meta">
                    <span><i class="fa fa-circle-xmark"></i> Sin resultados</span>
                </div>
            @endif
        </div>
    </div>

    {{-- ═══ BODY ═══ --}}
    <div class="container py-4 py-lg-5">

        {{-- Refinar búsqueda --}}
        <form action="{{ route('busqueda') }}" method="GET" class="lib-controls rb-refine">
            <div class="lib-search">
                <i class="fa fa-search lib-search__icon"></i>
                <input type="text" name="query" id="rb-query" class="lib-search__input"
                       value="{{ $query }}" placeholder="Buscar textos, videos, trabajos de investigación Taq'e o canciones…"
                       autocomplete="off">
            </div>
        </form>

        @if (!$hasResults)
            <div class="rb-empty">
                <i class="fa fa-search"></i>
                <p>No se encontraron resultados para <strong>&ldquo;{{ $query }}&rdquo;</strong>. Intenta con otra búsqueda.</p>
            </div>
        @else

            {{-- ── Videos ── --}}
            @if ($videos->isNotEmpty())
                <div class="rb-section">
                    <div class="lib-content-head">
                        <h2 class="lib-content-head__title">Videos</h2>
                        <p class="lib-content-head__sub">{{ $videosCount }} {{ $videosCount === 1 ? 'resultado' : 'resultados' }}</p>
                    </div>
                    <div class="row g-3 rb-grid">
                        @foreach ($videos as $video)
                            <div class="col-6 col-md-4 rb-item">
                                @if ($video->youtube)
                                    <div class="rb-media-card">
                                        <a class="rb-media-card__thumb" href="https://www.youtube.com/watch?v={{ $video->youtube }}" target="_blank" rel="noopener">
                                            <img src="https://img.youtube.com/vi/{{ $video->youtube }}/hqdefault.jpg" alt="{{ $video->nombre }}" loading="lazy">
                                            <span class="rb-media-card__play"><i class="fab fa-youtube"></i></span>
                                        </a>
                                        <div class="rb-media-card__info">
                                            <h3 class="rb-media-card__title">{{ $video->nombre }}</h3>
                                            @if ($video->descripcion)
                                                <p class="rb-media-card__desc">{{ $video->descripcion }}</p>
                                            @endif
                                        </div>
                                    </div>
                                @elseif ($video->drive)
                                    <div class="rb-media-card">
                                        <div class="rb-media-card__thumb rb-media-card__thumb--drive">
                                            <iframe src="https://drive.google.com/file/d/{{ $video->drive }}/preview" loading="lazy" allowfullscreen></iframe>
                                        </div>
                                        <div class="rb-media-card__info">
                                            <h3 class="rb-media-card__title">{{ $video->nombre }}</h3>
                                            @if ($video->descripcion)
                                                <p class="rb-media-card__desc">{{ $video->descripcion }}</p>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- ── Libros ── --}}
            @if ($libros->isNotEmpty())
                <div class="rb-section">
                    <div class="lib-content-head">
                        <h2 class="lib-content-head__title">Textos</h2>
                        <p class="lib-content-head__sub">{{ $librosCount }} {{ $librosCount === 1 ? 'resultado' : 'resultados' }}</p>
                    </div>
                    <div class="row g-3 rb-grid">
                        @foreach ($libros as $libro)
                            @php
                                $urlVistaPrevia = "https://drive.google.com/thumbnail?id={$libro->identificador}&sz=w600";
                            @endphp
                            <div class="col-6 col-lg-3 lib-book-col rb-item">
                                <div class="lib-book-card">
                                    <div class="lib-book-card__cover">
                                        <img src="{{ $urlVistaPrevia }}" alt="{{ $libro->nombre }}" loading="lazy">
                                        <div class="lib-book-card__overlay">
                                            <button type="button" class="lib-book-card__btn"
                                                onclick="abrirVistaPreviaLibro({{ \Illuminate\Support\Js::from($libro->identificador) }}, {{ \Illuminate\Support\Js::from($libro->nombre) }})">
                                                <i class="fa fa-eye"></i> Vista previa
                                            </button>
                                        </div>
                                    </div>
                                    <div class="lib-book-card__info">
                                        <h3 class="lib-book-card__title" title="{{ $libro->nombre }}">{{ $libro->nombre }}</h3>
                                        <p class="lib-book-card__author">
                                            <i class="fa fa-user"></i> {{ $libro->autor }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- ── Libros físicos ── --}}
            @if ($librosFisicos->isNotEmpty())
                <div class="rb-section">
                    <div class="lib-content-head">
                        <h2 class="lib-content-head__title">Libros físicos</h2>
                        <p class="lib-content-head__sub">{{ $librosFisicosCount }} {{ $librosFisicosCount === 1 ? 'resultado' : 'resultados' }}</p>
                    </div>
                    <div class="row g-3 rb-grid">
                        @foreach ($librosFisicos as $libroFisico)
                            @php
                                $primeraCategoria = $libroFisico->categoriaFisica;
                                $colorCategoria    = \App\Models\CategoriaFisica::colorForCodigo($primeraCategoria->codigo ?? null);
                            @endphp
                            <div class="col-6 col-lg-3 lib-book-col rb-item">
                                <div class="lib-book-card cf-book-card--flat" style="--cat-color: {{ $colorCategoria }};">
                                    <div class="cf-book-card__accent"></div>
                                    <div class="lib-book-card__info">
                                        <h3 class="lib-book-card__title" title="{{ $libroFisico->nombre }}">{{ $libroFisico->nombre }}</h3>
                                        <p class="lib-book-card__author">
                                            <i class="fa fa-user"></i> {{ $libroFisico->autor }}
                                        </p>
                                        <div class="cf-book-card__meta">
                                            @if ($libroFisico->anio_publicacion)
                                                <span class="cf-meta-pill">
                                                    <i class="fa fa-calendar-alt"></i> {{ $libroFisico->anio_publicacion }}
                                                </span>
                                            @endif
                                            @if ($primeraCategoria)
                                                <span class="cf-cat-pill">
                                                    <i class="fa fa-tag"></i>
                                                    {{ \Illuminate\Support\Str::limit($primeraCategoria->nombre, 20) }}
                                                </span>
                                            @endif
                                        </div>
                                        @if ($libroFisico->codigo)
                                            <div class="cf-book-card__code">
                                                <i class="fa fa-barcode"></i> {{ $libroFisico->codigo }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <a href="{{ route('catalogo-fisico') }}" class="lib-sidebar-anchor mt-1">
                        <i class="fa fa-book"></i> Ver Biblioteca Física completa
                    </a>
                </div>
            @endif

            {{-- ── Taq'e (Trabajos de Estudiantes) ── --}}
            @if ($taqeTrabajos->isNotEmpty())
                <div class="rb-section">
                    <div class="lib-content-head">
                        <h2 class="lib-content-head__title">Taq'e · Trabajos de estudiantes</h2>
                        <p class="lib-content-head__sub">{{ $taqeTrabajosCount }} {{ $taqeTrabajosCount === 1 ? 'resultado' : 'resultados' }}</p>
                    </div>
                    <div class="row g-3 rb-grid">
                        @foreach ($taqeTrabajos as $trabajo)
                            <div class="col-6 col-lg-3 lib-book-col rb-item">
                                <div class="lib-book-card" style="display:flex; flex-direction:column; height:100%; border-radius:12px; overflow:hidden; background:#fff; box-shadow:0 2px 10px rgba(42,37,32,.07); border:1px solid rgba(200,142,65,.14);">
                                    <div class="lib-book-card__cover" style="position:relative; aspect-ratio:16/10; overflow:hidden; background:#2a2520;">
                                        <img src="{{ $trabajo->portada_url }}" alt="{{ $trabajo->titulo }}" loading="lazy" style="width:100%; height:100%; object-fit:cover;">
                                        <div style="position:absolute; top:8px; left:8px; z-index:2;">
                                            <span style="font-size:0.7rem; font-weight:700; padding:3px 8px; border-radius:6px; background:rgba(20,16,12,0.85); color:#fff; display:inline-flex; align-items:center; gap:4px; backdrop-filter:blur(4px);">
                                                <i class="{{ $trabajo->icono }}"></i> {{ $trabajo->formato_label }}
                                            </span>
                                        </div>
                                        <div class="lib-book-card__overlay">
                                            <a href="{{ route('taqe.show', $trabajo->slug) }}" class="lib-book-card__btn">
                                                <i class="fa fa-eye"></i> Ver trabajo
                                            </a>
                                        </div>
                                    </div>
                                    <div class="lib-book-card__info" style="padding:12px 14px; flex:1; display:flex; flex-direction:column; gap:6px;">
                                        @if($trabajo->categoria)
                                            <div style="font-size:0.72rem; color:var(--puklla-accent, #c88e41); font-weight:700; text-transform:uppercase; letter-spacing:0.5px;">
                                                <i class="fa fa-folder-open"></i> {{ $trabajo->categoria->nombre }}
                                            </div>
                                        @endif
                                        <h3 class="lib-book-card__title" title="{{ $trabajo->titulo }}" style="font-size:0.92rem; font-weight:700; line-height:1.35; margin:0; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden;">
                                            <a href="{{ route('taqe.show', $trabajo->slug) }}" style="color:#2a2520; text-decoration:none;">
                                                {{ $trabajo->titulo }}
                                            </a>
                                        </h3>
                                        <p class="lib-book-card__author" style="font-size:0.78rem; color:#8c7b6d; margin:0;">
                                            <i class="fa fa-user"></i> {{ $trabajo->autores }}
                                        </p>
                                        <div style="margin-top:auto; padding-top:6px; display:flex; align-items:center; justify-content:space-between; font-size:0.74rem; color:#a89787;">
                                            <span><i class="fa fa-calendar-alt"></i> {{ $trabajo->anio ?? 'S/F' }}</span>
                                            <span><i class="fa fa-language"></i> {{ $trabajo->idioma_label }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <a href="{{ route('taqe.index') }}" class="lib-sidebar-anchor mt-3">
                        <i class="fa fa-archive"></i> Ver Repositorio Taq'e completo
                    </a>
                </div>
            @endif

            {{-- ── Canciones ── --}}
            @if ($canciones->isNotEmpty())
                <div class="rb-section">
                    <div class="lib-content-head">
                        <h2 class="lib-content-head__title">Canciones</h2>
                        <p class="lib-content-head__sub">{{ $cancionesCount }} {{ $cancionesCount === 1 ? 'resultado' : 'resultados' }}</p>
                    </div>
                    <div class="row g-3 rb-grid">
                        @foreach ($canciones as $cancion)
                            @if ($cancion->youtube)
                                <div class="col-6 col-md-4 rb-item">
                                    <div class="rb-media-card">
                                        <a class="rb-media-card__thumb" href="https://www.youtube.com/watch?v={{ $cancion->youtube }}" target="_blank" rel="noopener">
                                            <img src="https://img.youtube.com/vi/{{ $cancion->youtube }}/hqdefault.jpg" alt="{{ $cancion->nombre }}" loading="lazy">
                                            <span class="rb-media-card__play"><i class="fab fa-youtube"></i></span>
                                        </a>
                                        <div class="rb-media-card__info">
                                            <h3 class="rb-media-card__title">{{ $cancion->nombre }}</h3>
                                            <p class="rb-media-card__author"><i class="fa fa-user"></i> {{ $cancion->autor }}</p>
                                        </div>
                                    </div>
                                </div>
                            @elseif ($cancion->drive)
                                <div class="col-6 col-md-4 rb-item">
                                    <div class="rb-media-card">
                                        <div class="rb-media-card__thumb rb-media-card__thumb--drive">
                                            <iframe src="https://drive.google.com/file/d/{{ $cancion->drive }}/preview" loading="lazy" allowfullscreen></iframe>
                                        </div>
                                        <div class="rb-media-card__info">
                                            <h3 class="rb-media-card__title">{{ $cancion->nombre }}</h3>
                                            <p class="rb-media-card__author"><i class="fa fa-user"></i> {{ $cancion->autor }}</p>
                                        </div>
                                    </div>
                                </div>
                            @elseif ($cancion->spotify)
                                <div class="col-6 col-md-4 rb-item">
                                    <div class="rb-spotify-card">
                                        <iframe src="https://open.spotify.com/embed/track/{{ $cancion->spotify }}" width="100%"
                                            height="152" frameborder="0"
                                            allow="autoplay; clipboard-write; encrypted-media; fullscreen; picture-in-picture"
                                            loading="lazy"></iframe>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endif

        @endif
    </div>{{-- /container --}}

    {{-- ═══ SCRIPTS ═══ --}}
    <script>
    (function () {
        gsap.registerPlugin(ScrollTrigger);

        /* ── Hero parallax ── */
        gsap.to('.lib-hero__bg', {
            y: '20%', ease: 'none',
            scrollTrigger: { trigger: '.lib-hero', start: 'top top', end: 'bottom top', scrub: 1.2 }
        });

        /* ── Hero text reveal ── */
        gsap.set('.lib-hero__eyebrow', { y: 22 });
        gsap.set('.lib-hero__title',   { y: 30 });
        gsap.set('.lib-hero__meta',    { y: 16 });

        gsap.timeline({ defaults: { ease: 'power3.out' } })
            .to('.lib-hero__eyebrow', { opacity: 1, y: 0, duration: .75 }, .2)
            .to('.lib-hero__title',   { opacity: 1, y: 0, duration: .85 }, .4)
            .to('.lib-hero__divider', { width: 56,  duration: .5  },       .8)
            .to('.lib-hero__meta',    { opacity: 1, y: 0, duration: .6  }, 1.0);

        /* ── Refinar búsqueda: reveal ── */
        gsap.from('.rb-refine', { y: -10, opacity: 0, duration: .4, ease: 'power2.out', delay: .1 });

        /* ── Secciones y tarjetas: reveal (solo y, sin opacity para evitar transparencias) ── */
        document.querySelectorAll('.rb-grid').forEach(function (grid) {
            gsap.from(grid.querySelectorAll('.rb-item'), {
                y: 26, duration: .4, stagger: .045, ease: 'power2.out',
                scrollTrigger: { trigger: grid, start: 'top 90%', once: true }
            });
        });
        document.querySelectorAll('.lib-content-head').forEach(function (head) {
            gsap.from(head, {
                y: 16, duration: .4, ease: 'power2.out',
                scrollTrigger: { trigger: head, start: 'top 92%', once: true }
            });
        });

        /* ── Resaltado de coincidencias en los títulos ── */
        (function () {
            var q = (document.getElementById('rb-query') || {}).value || '';
            if (!q.trim()) return;

            function escRx(s) { return s.replace(/[.*+?^${}()|[\]\\]/g, '\\$&'); }
            function norm(s) { return s.normalize('NFD').replace(/[̀-ͯ]/g, '').toLowerCase(); }

            var termNorm = norm(q);
            var termRx   = new RegExp(escRx(termNorm), 'gi');

            function resaltarNodo(el) {
                var nodos = [];
                (function walk(n) {
                    if (n.nodeType === 3) { nodos.push(n); }
                    else if (n.nodeType === 1 && n.tagName !== 'MARK' && n.tagName !== 'I') {
                        n.childNodes.forEach(walk);
                    }
                })(el);

                nodos.forEach(function (tn) {
                    var txt  = tn.textContent;
                    var norm = txt.normalize('NFD').replace(/[̀-ͯ]/g, '');
                    if (!termRx.test(norm)) return;
                    termRx.lastIndex = 0;

                    var result = '';
                    var last   = 0;
                    var normMatch;
                    var rx2 = new RegExp(escRx(termNorm), 'gi');
                    while ((normMatch = rx2.exec(norm)) !== null) {
                        var start = normMatch.index;
                        var end   = start + normMatch[0].length;
                        result += esc(txt.slice(last, start));
                        result += '<mark class="rb-hl">' + esc(txt.slice(start, end)) + '</mark>';
                        last = end;
                    }
                    result += esc(txt.slice(last));

                    var span = document.createElement('span');
                    span.innerHTML = result;
                    tn.parentNode.replaceChild(span, tn);
                });
            }

            function esc(s) {
                return s.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
            }

            document.querySelectorAll('.rb-media-card__title, .lib-book-card__title, .rb-media-card__author, .lib-book-card__author').forEach(resaltarNodo);
        })();
    })();
    </script>

@endsection
