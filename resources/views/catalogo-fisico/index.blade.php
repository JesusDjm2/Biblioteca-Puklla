@extends('layouts.padre')
@section('titulo', '- Catálogo Físico')

@section('estilos')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrollTrigger.min.js"></script>
    <style>
        /* ═══════════════════════════════════════════════════════
           CATÁLOGO FÍSICO – complementos al sistema "lib-*"
        ═══════════════════════════════════════════════════════ */

        /* ── Buscador principal dentro de lib-controls ── */
        .cf-controls-info {
            font-size: .78rem;
            color: #9c8c7c;
            white-space: nowrap;
        }
        .cf-controls-info strong { color: #2a2520; }

        /* ── Hero: overlay azulado (igual que antes del rediseño) ── */
        .lib-hero .lib-hero__overlay.cf-hero-overlay {
            background: linear-gradient(160deg, rgba(16,40,80,.82) 0%, rgba(8,20,50,.70) 100%);
        }
        .cf-hero-cat {
            font-family: "Lato", system-ui, -apple-system, sans-serif;
            font-style: italic;
            font-size: clamp(1.05rem, 2.6vw, 1.5rem);
            color: var(--cat-color, #fff);
            margin: -.4rem 0 .5rem;
            opacity: 0;
        }

        /* ── Sidebar: lista plana de secciones, coloreadas por categoría ── */
        .cf-cat-list { list-style: none; margin: 0; padding: 0; }
        .cf-cat-item + .cf-cat-item { margin-top: 2px; }
        .cf-cat-list .lib-sidebar__cat-link {
            border-left: 3px solid transparent;
            gap: 8px;
        }
        .cf-cat-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: var(--cat-color, #cb9042);
            flex-shrink: 0;
        }
        .cf-cat-list .lib-sidebar__cat-link.is-active {
            background: color-mix(in srgb, var(--cat-color, #cb9042) 12%, #fff);
            color: color-mix(in srgb, var(--cat-color, #cb9042) 60%, #2a2520);
            border-left-color: var(--cat-color, #cb9042);
        }
        .cf-cat-list .lib-sidebar__cat-link.is-active .lib-sidebar__cat-count {
            color: color-mix(in srgb, var(--cat-color, #cb9042) 55%, #2a2520);
        }
        .cf-sidebar-clear {
            display: inline-flex;
            margin-top: .9rem;
        }

        /* ── Estado vacío ── */
        .cf-empty {
            text-align: center;
            padding: 3.2rem 1rem;
            color: #b8a898;
            background: #fff;
            border: 1px solid rgba(200,142,65,.13);
            border-radius: 12px;
        }
        .cf-empty i { font-size: 2.6rem; margin-bottom: .8rem; display: block; color: var(--puklla-accent); opacity: .5; }
        .cf-empty p { font-size: .9rem; margin-bottom: 1rem; }

        /* ── Paginación con acento Puklla ── */
        .cf-pagination { margin-top: 1.6rem; }
        .cf-pagination .pagination { justify-content: center; flex-wrap: wrap; }
        .cf-pagination .page-link {
            font-size: .8rem;
            padding: .35rem .68rem;
            color: #7a6040;
            border-color: rgba(200,142,65,.25);
        }
        .cf-pagination .page-link:hover { color: var(--puklla-accent); }
        .cf-pagination .page-item.active .page-link {
            background: var(--puklla-accent);
            border-color: var(--puklla-accent);
        }

        /* ── Resaltado de coincidencias ── */
        .cf-hl {
            background: rgba(203,144,66,.35);
            color: #2a2520;
            border-radius: 2px;
            padding: 0 1px;
            font-weight: inherit;
        }

        @media (max-width: 575px) {
            .cf-controls-info { display: none; }
        }
    </style>
@endsection

@section('contenido')

    @php
        $catActivo = request('cat') ? $categorias->firstWhere('id', request('cat')) : null;
    @endphp

    {{-- ═══ HERO ═══ --}}
    <div class="lib-hero">
        <img src="{{ asset('img/fondo/tramites-2026.webp') }}" alt="Biblioteca Pukllasunchis"
             class="lib-hero__bg" loading="eager">
        <div class="lib-hero__overlay cf-hero-overlay"></div>
        <div class="lib-hero__content">
            <p class="lib-hero__eyebrow">ESPP Pukllasunchis</p>
            <h1 class="lib-hero__title">Biblioteca Física</h1>
            @if ($catActivo)
                <p class="cf-hero-cat" style="--cat-color: {{ \App\Models\CategoriaFisica::colorForCodigo($catActivo->codigo) }};">
                    {{ $catActivo->nombre }}
                </p>
            @endif
            <div class="lib-hero__divider"></div>
            <div class="lib-hero__meta">
                <span><i class="fa fa-book"></i> {{ number_format($totalLibros) }} libros</span>
                <span><i class="fa fa-folder-open"></i> {{ $categorias->count() }} secciones</span>
            </div>
            <div class="mt-3">
                <a href="{{ route('libros') }}"
                   style="display:inline-flex;align-items:center;gap:.45rem;
                          background:rgba(255,255,255,.15);backdrop-filter:blur(6px);
                          border:1px solid rgba(255,255,255,.35);border-radius:999px;
                          color:#fff;font-size:.82rem;font-weight:600;
                          padding:.45rem 1.1rem;text-decoration:none;
                          transition:background .2s,transform .18s;"
                   onmouseover="this.style.background='rgba(255,255,255,.28)';this.style.transform='translateY(-2px)'"
                   onmouseout="this.style.background='rgba(255,255,255,.15)';this.style.transform=''">
                    <i class="fa fa-arrow-left" style="font-size:.72rem;opacity:.8;"></i>
                    <i class="fa fa-book-open"></i> Ver Biblioteca Virtual
                </a>
            </div>
        </div>
    </div>

    {{-- FAB móvil --}}
    <button id="lib-sidebar-fab" class="lib-sidebar-fab" aria-label="Ver secciones">
        <i class="fa fa-folder-open"></i> Secciones
    </button>

    {{-- ═══ BODY ═══ --}}
    <div class="container py-4 py-lg-5">
        <div class="row g-4">

            {{-- ── Sidebar ── --}}
            <div class="col-lg-3 order-2 order-lg-1">
                <aside class="lib-sidebar" id="lib-sidebar">
                    <h4 class="lib-sidebar__title">
                        <i class="fa fa-folder-open"></i> Secciones
                    </h4>

                    <ul class="cf-cat-list">
                        @foreach ($categorias as $cat)
                            @php
                                $isActive = request('cat') == $cat->id;
                                $catUrl   = route('catalogo-fisico', array_filter([
                                    'cat' => $cat->id,
                                    'q'   => request('q'),
                                ]));
                            @endphp
                            <li class="cf-cat-item">
                                <a href="{{ $catUrl }}" class="lib-sidebar__cat-link {{ $isActive ? 'is-active' : '' }}"
                                   style="--cat-color: {{ \App\Models\CategoriaFisica::colorForCodigo($cat->codigo) }};">
                                    <span class="cf-cat-dot"></span>
                                    <span class="lib-sidebar__cat-name">{{ $cat->nombre }}</span>
                                    <span class="lib-sidebar__cat-count">{{ number_format($cat->libros_fisicos_count) }}</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>

                    @if (request('cat') || request('q'))
                        <a href="{{ route('catalogo-fisico') }}" class="lib-sidebar-anchor cf-sidebar-clear">
                            <i class="fa fa-times-circle"></i> Limpiar filtros
                        </a>
                    @endif
                </aside>
            </div>

            {{-- ── Contenido ── --}}
            <div class="col-lg-9 order-1 order-lg-2">

                {{-- Heading --}}
                <div class="lib-content-head">
                    <h2 class="lib-content-head__title">Catálogo Físico</h2>
                    <p class="lib-content-head__sub">
                        @if ($libros->total() > 0)
                            Mostrando {{ $libros->firstItem() }}–{{ $libros->lastItem() }}
                            de {{ number_format($libros->total()) }} libro(s)
                            @if (request('q'))
                                para "{{ request('q') }}"
                            @endif
                            @if ($catActivo)
                                en {{ $catActivo->nombre }}
                            @endif
                        @else
                            Sin resultados
                            @if (request('q')) para "{{ request('q') }}" @endif
                        @endif
                    </p>
                </div>

                {{-- Controles: buscador + info de página --}}
                <form method="GET" action="{{ route('catalogo-fisico') }}" id="mainSearchForm" class="lib-controls">
                    @if (request('cat'))
                        <input type="hidden" name="cat" value="{{ request('cat') }}">
                    @endif
                    <div class="lib-search">
                        <i class="fa fa-search lib-search__icon"></i>
                        <input type="text" name="q" id="cf-main-q"
                               class="lib-search__input"
                               value="{{ request('q') }}"
                               placeholder="Buscar por título, autor o editorial…"
                               autocomplete="off">
                    </div>
                    <span class="cf-controls-info">
                        Página <strong>{{ $libros->currentPage() }}</strong> / {{ $libros->lastPage() }}
                    </span>
                </form>

                {{-- Grid de libros --}}
                @if ($libros->isNotEmpty())
                    <div class="row g-3" id="cf-grid">
                        @foreach ($libros as $libro)
                            @php
                                $firstCat = $libro->categoriaFisica;
                                $catColor  = \App\Models\CategoriaFisica::colorForCodigo($firstCat->codigo ?? null);
                                $disponibles = $libro->ejemplares_disponibles;
                            @endphp
                            <div class="col-6 col-md-4 lib-book-col cf-card-item">
                                <div class="lib-book-card cf-book-card--flat" style="--cat-color: {{ $catColor }};">
                                    <div class="cf-book-card__accent"></div>

                                    <div class="lib-book-card__info">
                                        <h3 class="lib-book-card__title" title="{{ $libro->nombre }}">{{ $libro->nombre }}</h3>
                                        <p class="lib-book-card__author">
                                            <i class="fa fa-user"></i> {{ $libro->autor }}
                                        </p>
                                        <div class="cf-book-card__meta">
                                            @if ($libro->anio_publicacion)
                                                <span class="cf-meta-pill">
                                                    <i class="fa fa-calendar-alt"></i> {{ $libro->anio_publicacion }}
                                                </span>
                                            @endif
                                            @if ($libro->numero_paginas)
                                                <span class="cf-meta-pill">
                                                    <i class="fa fa-file-alt"></i> {{ $libro->numero_paginas }} p.
                                                </span>
                                            @endif
                                            @if ($firstCat)
                                                <span class="cf-cat-pill">
                                                    <i class="fa fa-tag"></i>
                                                    {{ \Illuminate\Support\Str::limit($firstCat->nombre, 20) }}
                                                </span>
                                            @endif
                                            @if ($libro->ubicacion)
                                                <span class="cf-meta-pill">
                                                    <i class="fa fa-map-marker-alt"></i>
                                                    {{ \App\Models\LibroFisico::nombreParaUbicacion($libro->ubicacion) }}
                                                </span>
                                            @endif
                                        </div>
                                        <div class="cf-book-card__code">
                                            @if ($libro->codigo)
                                                <span><i class="fa fa-barcode"></i> {{ $libro->codigo }}</span>
                                            @else
                                                <span></span>
                                            @endif
                                            <span>
                                                {{ $disponibles }}/{{ $libro->cantidad_ejemplares }}
                                                {{ $disponibles < 1 ? 'sin disponibles' : ($libro->cantidad_ejemplares === 1 ? 'disponible' : 'disponibles') }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Paginación --}}
                    <div class="cf-pagination">
                        {{ $libros->links() }}
                    </div>

                @else
                    <div class="cf-empty">
                        <i class="fa fa-search"></i>
                        <p>No se encontraron libros con esos criterios.</p>
                        <a href="{{ route('catalogo-fisico') }}" class="lib-sidebar-anchor">
                            Ver todos los libros
                        </a>
                    </div>
                @endif

            </div>{{-- /col content --}}
        </div>{{-- /row --}}
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
        gsap.set('.cf-hero-cat',       { y: 14 });
        gsap.set('.lib-hero__meta',    { y: 16 });
        gsap.set('.lib-hero__content .mt-3', { opacity: 0, y: 14 });

        gsap.timeline({ defaults: { ease: 'power3.out' } })
            .to('.lib-hero__eyebrow', { opacity: 1, y: 0, duration: .75 }, .2)
            .to('.lib-hero__title',   { opacity: 1, y: 0, duration: .85 }, .4)
            .to('.cf-hero-cat',       { opacity: 1, y: 0, duration: .6  }, .65)
            .to('.lib-hero__divider', { width: 56,  duration: .5  },       .8)
            .to('.lib-hero__meta',    { opacity: 1, y: 0, duration: .6  }, 1.0)
            .to('.lib-hero__content .mt-3', { opacity: 1, y: 0, duration: .5 }, 1.2);

        /* ── Cards reveal (solo y, sin opacity para evitar transparencias) ── */
        gsap.from('.cf-card-item', {
            y: 26, duration: .4, stagger: .045, ease: 'power2.out',
            scrollTrigger: { trigger: '#cf-grid', start: 'top 90%', once: true }
        });

        /* ── Sidebar + buscador ── */
        gsap.from('.lib-sidebar', { x: -18, opacity: 0, duration: .45, ease: 'power2.out', delay: .15 });
        gsap.from('.lib-controls', { y: -10, opacity: 0, duration: .4, ease: 'power2.out', delay: .1 });

        /* ── Búsqueda: submit al dejar de escribir (debounce 550ms) ── */
        var searchTimer;
        var mainQ = document.getElementById('cf-main-q');
        if (mainQ) {
            mainQ.addEventListener('input', function () {
                clearTimeout(searchTimer);
                searchTimer = setTimeout(function () {
                    document.getElementById('mainSearchForm').submit();
                }, 550);
            });
            if (mainQ.value) {
                mainQ.setSelectionRange(mainQ.value.length, mainQ.value.length);
            }
        }

        /* ── Resaltado de coincidencias en las tarjetas ── */
        (function () {
            var q = (document.getElementById('cf-main-q') || {}).value || '';
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
                        result += '<mark class="cf-hl">' + esc(txt.slice(start, end)) + '</mark>';
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

            document.querySelectorAll('.lib-book-card__title, .lib-book-card__author').forEach(resaltarNodo);
        })();

        /* ── FAB: scroll al sidebar, se oculta cuando el sidebar es visible ── */
        var fab     = document.getElementById('lib-sidebar-fab');
        var sidebar = document.getElementById('lib-sidebar');
        if (fab && sidebar) {
            fab.addEventListener('click', function () {
                sidebar.scrollIntoView({ behavior: 'smooth', block: 'start' });
            });
            new IntersectionObserver(function (entries) {
                var e = entries[0];
                if (e.isIntersecting) {
                    gsap.to(fab, {
                        y: 70, opacity: 0, duration: .28, ease: 'power2.in',
                        onComplete: function () { fab.style.pointerEvents = 'none'; }
                    });
                } else {
                    fab.style.pointerEvents = 'auto';
                    gsap.to(fab, { y: 0, opacity: 1, duration: .35, ease: 'power2.out' });
                }
            }, { threshold: 0.15 }).observe(sidebar);
        }
    })();
    </script>

@endsection
