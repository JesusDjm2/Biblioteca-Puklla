@extends('layouts.padre')
@section('titulo', '- Taq\'e de Saberes Estudiantiles')

@section('estilos')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrollTrigger.min.js"></script>
    <style>
        /* ═══════════════════════════════════════════════════════
           TAQ'E: REPOSITORIO DE TRABAJOS ESTUDIANTILES
           Alineado al sistema visual "lib-*" / "vl-*" de Pukllasunchis
        ═══════════════════════════════════════════════════════ */

        /* ── Sidebar estilo videos/libros ── */
        .vl-sidebar {
            position: sticky;
            top: 6rem;
        }
        .vl-sidebar__title {
            font-family: "Lato", system-ui, -apple-system, sans-serif;
            font-size: 1.05rem;
            font-weight: 600;
            color: #2a2520;
            margin-bottom: .8rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .vl-sidebar__list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .vl-sidebar__item + .vl-sidebar__item {
            margin-top: 2px;
        }
        .vl-sidebar__link {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 8px 12px;
            border-radius: 8px;
            color: #5a4a3a;
            font-size: .84rem;
            text-decoration: none;
            transition: background .2s, color .2s;
            border-left: 3px solid transparent;
        }
        .vl-sidebar__link:hover {
            background: rgba(203,144,66,.08);
            color: #2a2520;
        }
        .vl-sidebar__link.is-active {
            background: rgba(203,144,66,.12);
            color: #a06e2e;
            border-left-color: #cb9042;
            font-weight: 600;
        }
        .vl-sidebar__count {
            font-size: .72rem;
            background: rgba(200,142,65,.1);
            color: #8a7a6a;
            padding: 1px 8px;
            border-radius: 999px;
        }
        .vl-sidebar__link.is-active .vl-sidebar__count {
            background: rgba(203,144,66,.2);
            color: #a06e2e;
        }

        @media (max-width: 991.98px) {
            .vl-sidebar {
                position: relative;
                top: 0;
                margin-bottom: 1.5rem;
            }
        }

        /* ── Barra de Filtros de Formato (Pills) ── */
        .taqe-filter-bar {
            display: flex;
            flex-wrap: wrap;
            gap: 0.45rem;
            align-items: center;
        }
        .taqe-pill {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.35rem 0.8rem;
            border-radius: 999px;
            font-size: 0.8rem;
            font-weight: 600;
            text-decoration: none;
            background: #fff;
            border: 1px solid #e0d8ce;
            color: #4a4036;
            transition: all 0.2s ease;
        }
        .taqe-pill:hover {
            background: #fdfaf5;
            border-color: #c78e41;
            color: #8c581e;
            text-decoration: none;
            transform: translateY(-1px);
        }
        .taqe-pill.is-active {
            background: #c78e41;
            border-color: #c78e41;
            color: #fff;
            box-shadow: 0 2px 8px rgba(199, 142, 65, 0.35);
        }
        .taqe-pill .taqe-count {
            background: rgba(0, 0, 0, 0.08);
            padding: 0.1rem 0.45rem;
            border-radius: 999px;
            font-size: 0.7rem;
            font-weight: 700;
        }
        .taqe-pill.is-active .taqe-count {
            background: rgba(255, 255, 255, 0.25);
            color: #fff;
        }

        /* ── Tarjetas de Trabajos ── */
        .taqe-card {
            background: #fff;
            border-radius: 10px;
            border: 1px solid rgba(200, 142, 65, 0.16);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            height: 100%;
            box-shadow: 0 2px 10px rgba(42, 37, 32, 0.06);
            transition: transform 0.25s ease, box-shadow 0.25s ease, border-color 0.25s ease;
        }
        .taqe-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 24px rgba(42, 37, 32, 0.12);
            border-color: #c78e41;
        }
        .taqe-card__thumb-wrap {
            position: relative;
            aspect-ratio: 16 / 9;
            background: #1a1612;
            overflow: hidden;
        }
        .taqe-card__thumb {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.35s ease;
        }
        .taqe-card:hover .taqe-card__thumb {
            transform: scale(1.05);
        }
        .taqe-badge-format {
            position: absolute;
            top: 0.5rem;
            left: 0.5rem;
            padding: 0.2rem 0.55rem;
            border-radius: 6px;
            font-size: 0.7rem;
            font-weight: 700;
            color: #fff;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.35);
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            z-index: 2;
        }
        .taqe-badge-lang {
            position: absolute;
            top: 0.5rem;
            right: 0.5rem;
            padding: 0.18rem 0.5rem;
            border-radius: 6px;
            font-size: 0.68rem;
            font-weight: 600;
            background: rgba(20, 15, 10, 0.8);
            backdrop-filter: blur(4px);
            color: #f5eedf;
            border: 1px solid rgba(255, 255, 255, 0.2);
            z-index: 2;
        }
        .taqe-card__body {
            padding: 12px 14px 14px;
            display: flex;
            flex-direction: column;
            flex: 1;
            gap: 6px;
        }
        .taqe-card__meta-top {
            font-size: 0.74rem;
            color: #8c7c6d;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .taqe-card__title {
            font-family: "Lato", system-ui, -apple-system, sans-serif;
            font-size: 0.95rem;
            font-weight: 700;
            color: #2a2520;
            margin: 0;
            line-height: 1.35;
        }
        .taqe-card__title a {
            color: inherit;
            text-decoration: none;
            transition: color 0.18s ease;
        }
        .taqe-card__title a:hover {
            color: #c78e41;
        }
        .taqe-card__authors {
            font-size: 0.78rem;
            color: #8c581e;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.35rem;
            margin: 0;
        }
        .taqe-card__desc {
            font-size: 0.78rem;
            color: #7a6e62;
            line-height: 1.4;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            margin: 0;
        }
        .taqe-card__footer {
            margin-top: auto;
            padding-top: 8px;
            border-top: 1px solid #f0e9df;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.5rem;
        }
        .taqe-card__btn {
            font-size: 0.76rem;
            font-weight: 600;
            border-radius: 6px;
            padding: 0.3rem 0.65rem;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            transition: all 0.2s ease;
        }
        .taqe-card__btn--primary {
            background: #c78e41;
            color: #fff;
            border: 1px solid #c78e41;
        }
        .taqe-card__btn--primary:hover {
            background: #ad772e;
            color: #fff;
            text-decoration: none;
        }
        .taqe-card__btn--outline {
            background: transparent;
            color: #6a5e52;
            border: 1px solid #d5cbbf;
        }
        .taqe-card__btn--outline:hover {
            background: #fdfaf5;
            color: #2a2520;
            border-color: #c78e41;
            text-decoration: none;
        }
    </style>
@endsection

@section('contenido')

    {{-- ===== HERO (Diseño idéntico a Biblioteca Virtual y Videos) ===== --}}
    <div class="lib-hero">
        <img src="{{ asset('img/fondo/taqe-hero.jpg') }}" alt="Taq'e de Saberes Pukllasunchis"
             class="lib-hero__bg" loading="eager">
        <div class="lib-hero__overlay"></div>
        <div class="lib-hero__content">
            <p class="lib-hero__eyebrow">ESPP Pukllasunchis</p>
            <h1 class="lib-hero__title">Taq'e</h1>
            <div class="lib-hero__divider"></div>
            <div class="lib-hero__meta">
                <span><i class="fa fa-graduation-cap"></i> {{ $totalTrabajos }} trabajos de investigación</span>
                <span><i class="fa fa-folder-open"></i> {{ $categorias->count() }} categorías</span>
            </div>
        </div>
    </div>

    {{-- FAB móvil para scroll al sidebar --}}
    <button id="lib-sidebar-fab" class="lib-sidebar-fab" aria-label="Ver categorías">
        <i class="fa fa-folder-open"></i> Categorías
    </button>

    {{-- ===== BODY (Estructura de dos columnas idéntica a videos y libros) ===== --}}
    <div class="container py-4 py-lg-5">
        <div class="row g-4">

            {{-- Sidebar: order-2 en mobile --}}
            <div class="col-lg-3 order-2 order-lg-1">

                {{-- Pequeña descripción de lo que es el Taq'e --}}
                <div class="lib-sidebar mb-3" style="border-left: 3px solid var(--puklla-accent, #c88e41);">
                    <h4 class="lib-sidebar__title" style="font-size: 0.95rem; margin-bottom: 0.4rem; color: #a06e2e;">
                        <i class="fa fa-info-circle"></i> ¿Qué es el Taq'e?
                    </h4>
                    <p style="font-size: 0.84rem; color: #6a5a4a; line-height: 1.5; margin: 0;">
                        En la tradición andina, el <em>Taq'e</em> es el depósito comunal donde se resguardan las cosechas más valiosas. En el IESP Pukllasunchis, es el espacio para conservar y compartir las investigaciones, relatos bilingües, cantos y propuestas pedagógicas elaboradas por los estudiantes.
                    </p>
                </div>

                {{-- Listado de Categorías con conteos --}}
                <aside class="vl-sidebar" id="lib-sidebar">
                    <h4 class="vl-sidebar__title">
                        <i class="fa fa-folder-open"></i> Categorías
                    </h4>
                    <ul class="vl-sidebar__list">
                        <li class="vl-sidebar__item">
                            <a href="{{ route('taqe.index') }}"
                               class="vl-sidebar__link {{ !request('categoria_id') ? 'is-active' : '' }}">
                                <span><i class="fa fa-th-large mr-1"></i> Todos los trabajos</span>
                                <span class="vl-sidebar__count">{{ $totalTrabajos }}</span>
                            </a>
                        </li>
                        @foreach ($categorias as $cat)
                            <li class="vl-sidebar__item">
                                <a href="{{ route('taqe.index', array_merge(request()->except(['page']), ['categoria_id' => $cat->id])) }}"
                                   class="vl-sidebar__link {{ (string)request('categoria_id') === (string)$cat->id ? 'is-active' : '' }}">
                                    <span>
                                        <i class="fa {{ $cat->icono ?? 'fa-folder' }} mr-1" style="color:{{ $cat->color ?? '#cb9042' }}"></i>
                                        {{ $cat->nombre }}
                                    </span>
                                    <span class="vl-sidebar__count">{{ $cat->trabajos_publicados_count }}</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </aside>
            </div>

            {{-- Content: order-1 en mobile --}}
            <div class="col-lg-9 order-1 order-lg-2">

                {{-- Encabezado de sección --}}
                <div class="lib-content-head">
                    <h2 class="lib-content-head__title">
                        @if ($catActiva)
                            {{ $catActiva->nombre }}
                        @else
                            Trabajos de Estudiantes
                        @endif
                    </h2>
                    <p class="lib-content-head__sub">
                        @if ($catActiva)
                            Mostrando los trabajos pertenecientes a esta categoría
                        @else
                            Mostrando investigaciones, relatos y recursos elaborados por los alumnos
                        @endif
                    </p>
                </div>

                {{-- Buscador y filtros superiores --}}
                <form action="{{ route('taqe.index') }}" method="GET" class="lib-controls mb-3">
                    @if (request('categoria_id'))
                        <input type="hidden" name="categoria_id" value="{{ request('categoria_id') }}">
                    @endif
                    @if (request('formato'))
                        <input type="hidden" name="formato" value="{{ request('formato') }}">
                    @endif

                    <div class="lib-search">
                        <i class="fa fa-search lib-search__icon"></i>
                        <input type="text" name="q" id="search-input" class="lib-search__input"
                            value="{{ request('q') }}" placeholder="Buscar por título, autor o tema…"
                            autocomplete="off" aria-label="Buscar trabajos">
                    </div>
                </form>

                {{-- Filtro rápido de formato (Pills) --}}
                <div class="taqe-filter-bar mb-4">
                    <a href="{{ route('taqe.index', array_merge(request()->except(['formato', 'page']))) }}"
                       class="taqe-pill {{ !request('formato') ? 'is-active' : '' }}">
                        <i class="fa fa-cubes"></i> Todos
                        <span class="taqe-count">{{ $totalTrabajos }}</span>
                    </a>
                    @foreach ($formatos as $key => $fmt)
                        <a href="{{ route('taqe.index', array_merge(request()->except('page'), ['formato' => $key])) }}"
                           class="taqe-pill {{ request('formato') === $key ? 'is-active' : '' }}">
                            <i class="fa {{ $fmt['icono'] }}"></i> {{ $fmt['nombre'] }}
                            <span class="taqe-count">{{ $formatoCounts[$key] ?? 0 }}</span>
                        </a>
                    @endforeach
                </div>

                {{-- Grid de Trabajos --}}
                @if ($trabajos->isNotEmpty())
                    <div class="row g-3" id="contenedorTrabajos">
                        @foreach ($trabajos as $trabajo)
                            @php
                                $fmt = $trabajo->formato_info;
                                $idm = $trabajo->idioma_info;
                            @endphp
                            <div class="col-12 col-md-6 col-xl-4 taqe-card-col">
                                <div class="taqe-card">
                                    <div class="taqe-card__thumb-wrap">
                                        <img src="{{ $trabajo->portada_url }}" alt="{{ $trabajo->titulo }}" class="taqe-card__thumb" loading="lazy">
                                        
                                        <span class="taqe-badge-format" style="background: {{ $fmt['color'] }};">
                                            <i class="fa {{ $fmt['icono'] }}"></i> {{ $fmt['nombre'] }}
                                        </span>

                                        <span class="taqe-badge-lang">
                                            <i class="fa {{ $idm['icono'] }}"></i> {{ $idm['nombre'] }}
                                        </span>
                                    </div>

                                    <div class="taqe-card__body">
                                        <div class="taqe-card__meta-top">
                                            <span>
                                                <i class="fa fa-folder-open text-muted mr-1"></i>{{ $trabajo->categoria->nombre ?? 'General' }}
                                            </span>
                                            @if ($trabajo->anio)
                                                <span class="badge badge-light border">{{ $trabajo->anio }}</span>
                                            @endif
                                        </div>

                                        <h3 class="taqe-card__title">
                                            <a href="{{ route('taqe.show', $trabajo->slug) }}" title="{{ $trabajo->titulo }}">
                                                {{ $trabajo->titulo }}
                                            </a>
                                        </h3>

                                        <p class="taqe-card__authors">
                                            <i class="fa fa-user"></i>
                                            <span>{{ $trabajo->autores }}</span>
                                        </p>

                                        @if ($trabajo->ciclo_carrera)
                                            <p class="small text-muted mb-0">
                                                <i class="fa fa-graduation-cap mr-1"></i>{{ $trabajo->ciclo_carrera }}
                                            </p>
                                        @endif

                                        @if ($trabajo->descripcion)
                                            <p class="taqe-card__desc">{{ $trabajo->descripcion }}</p>
                                        @endif

                                        <div class="taqe-card__footer">
                                            <div class="small text-muted">
                                                <i class="fa fa-eye mr-1"></i>{{ number_format($trabajo->vistas) }}
                                                @if ($trabajo->descargas > 0)
                                                    <span class="ml-2"><i class="fa fa-download mr-1"></i>{{ number_format($trabajo->descargas) }}</span>
                                                @endif
                                            </div>

                                            <div class="d-flex gap-1">
                                                @if ($trabajo->archivo_pdf)
                                                    <a href="{{ route('taqe.descargar', $trabajo->slug) }}"
                                                       class="taqe-card__btn taqe-card__btn--outline"
                                                       title="Descargar documento PDF" target="_blank">
                                                        <i class="fa fa-download"></i> PDF
                                                    </a>
                                                @endif

                                                <a href="{{ route('taqe.show', $trabajo->slug) }}"
                                                   class="taqe-card__btn taqe-card__btn--primary">
                                                    Ver <i class="fa fa-arrow-right"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="d-flex justify-content-center mt-4">
                        {{ $trabajos->links() }}
                    </div>
                @else
                    <div class="text-center py-5 bg-white rounded border my-4">
                        <i class="fa fa-folder-open-o text-muted" style="font-size: 3rem;"></i>
                        <h4 class="mt-3 text-muted">No se encontraron trabajos con los filtros seleccionados</h4>
                        <p class="text-muted small">Prueba modificando los criterios de búsqueda o limpiando los filtros.</p>
                        <a href="{{ route('taqe.index') }}" class="btn btn-sm btn-outline-secondary mt-2">
                            <i class="fa fa-refresh mr-1"></i>Ver todos los trabajos
                        </a>
                    </div>
                @endif

            </div>
        </div>
    </div>

    {{-- ===== SCRIPTS (Animaciones GSAP idénticas a Biblioteca y Videos) ===== --}}
    <script>
    (function () {
        if (typeof gsap === 'undefined') return;
        gsap.registerPlugin(ScrollTrigger);

        /* ---- Hero parallax ---- */
        gsap.to('.lib-hero__bg', {
            y: '20%',
            ease: 'none',
            scrollTrigger: {
                trigger: '.lib-hero',
                start: 'top top',
                end: 'bottom top',
                scrub: 1.2
            }
        });

        /* ---- Hero text reveal ---- */
        gsap.set('.lib-hero__eyebrow', { y: 24 });
        gsap.set('.lib-hero__title',   { y: 32 });
        gsap.set('.lib-hero__meta',    { y: 18 });

        gsap.timeline({ defaults: { ease: 'power3.out' } })
            .to('.lib-hero__eyebrow', { opacity: 1, y: 0, duration: .8 }, .25)
            .to('.lib-hero__title',   { opacity: 1, y: 0, duration: .9 }, .45)
            .to('.lib-hero__divider', { width: 56, duration: .55 }, .85)
            .to('.lib-hero__meta',    { opacity: 1, y: 0, duration: .6 }, 1.05);

        /* ---- FAB móvil ---- */
        const fab = document.getElementById('lib-sidebar-fab');
        const sidebar = document.getElementById('lib-sidebar');
        if (fab && sidebar) {
            fab.addEventListener('click', function () {
                sidebar.scrollIntoView({ behavior: 'smooth', block: 'start' });
            });
            new IntersectionObserver(function (entries) {
                if (entries[0].isIntersecting) {
                    gsap.to(fab, { y: 70, opacity: 0, duration: .28, ease: 'power2.in', onComplete: function () { fab.style.pointerEvents = 'none'; } });
                } else {
                    fab.style.pointerEvents = 'auto';
                    gsap.to(fab, { y: 0, opacity: 1, duration: .35, ease: 'power2.out' });
                }
            }, { threshold: 0.15 }).observe(sidebar);
        }

        /* ---- Scroll reveal para las tarjetas ---- */
        if (document.querySelectorAll('.taqe-card-col').length > 0) {
            gsap.from('.taqe-card-col', {
                y: 28,
                duration: .45,
                stagger: .05,
                ease: 'power2.out',
                scrollTrigger: {
                    trigger: '#contenedorTrabajos',
                    start: 'top 88%',
                    once: true
                }
            });
        }
    })();
    </script>
@endsection
