@extends('layouts.padre')
@section('titulo', '- Taq\'e de Saberes Estudiantiles')

@section('estilos')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrollTrigger.min.js"></script>
    <style>
        /* ═══════════════════════════════════════════════════════
           TAQ'E: REPOSITORIO DE TRABAJOS ESTUDIANTILES
           Integrado al sistema visual "lib-*" de Pukllasunchis
        ═══════════════════════════════════════════════════════ */

        .taqe-hero-overlay {
            background: linear-gradient(160deg, rgba(30, 20, 15, 0.82) 0%, rgba(15, 10, 8, 0.72) 100%);
        }

        .taqe-about-card {
            background: #fff;
            border-radius: 12px;
            padding: 1.15rem 1.4rem;
            margin-top: -1.75rem;
            margin-bottom: 2rem;
            border: 1px solid rgba(200, 142, 65, 0.22);
            box-shadow: 0 4px 18px rgba(42, 37, 32, 0.08);
            position: relative;
            z-index: 10;
        }

        .taqe-about-title {
            font-size: 0.95rem;
            font-weight: 700;
            color: #8c581e;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 0.35rem;
            display: flex;
            align-items: center;
            gap: 0.45rem;
        }

        .taqe-about-text {
            font-size: 0.92rem;
            color: #5a5047;
            line-height: 1.55;
            margin: 0;
        }

        /* ── Barra de Filtros de Formato (Pills) ── */
        .taqe-filter-bar {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .taqe-pill {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.4rem 0.85rem;
            border-radius: 999px;
            font-size: 0.82rem;
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
            font-size: 0.72rem;
            font-weight: 700;
        }

        .taqe-pill.is-active .taqe-count {
            background: rgba(255, 255, 255, 0.25);
            color: #fff;
        }

        /* ── Tarjetas de Trabajos de Estudiantes ── */
        .taqe-card {
            background: #fff;
            border-radius: 12px;
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
            background: #2a221b;
            overflow: hidden;
        }

        .taqe-card__thumb {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.35s ease;
        }

        .taqe-card:hover .taqe-card__thumb {
            transform: scale(1.04);
        }

        .taqe-badge-format {
            position: absolute;
            top: 0.65rem;
            left: 0.65rem;
            padding: 0.25rem 0.65rem;
            border-radius: 6px;
            font-size: 0.72rem;
            font-weight: 700;
            color: #fff;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.35);
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            z-index: 2;
        }

        .taqe-badge-lang {
            position: absolute;
            top: 0.65rem;
            right: 0.65rem;
            padding: 0.2rem 0.55rem;
            border-radius: 6px;
            font-size: 0.7rem;
            font-weight: 600;
            background: rgba(20, 15, 10, 0.78);
            backdrop-filter: blur(4px);
            color: #f5eedf;
            border: 1px solid rgba(255, 255, 255, 0.2);
            z-index: 2;
        }

        .taqe-card__body {
            padding: 1.1rem 1.15rem 1rem;
            display: flex;
            flex-direction: column;
            flex: 1;
            gap: 0.5rem;
        }

        .taqe-card__meta-top {
            font-size: 0.76rem;
            color: #8c7c6d;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .taqe-card__title {
            font-family: "Lato", system-ui, -apple-system, sans-serif;
            font-size: 1.02rem;
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
            font-size: 0.8rem;
            color: #8c581e;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.4rem;
        }

        .taqe-card__desc {
            font-size: 0.82rem;
            color: #6a5e52;
            line-height: 1.45;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
            margin-top: 0.2rem;
        }

        .taqe-card__footer {
            margin-top: auto;
            padding-top: 0.85rem;
            border-top: 1px solid #f0e9df;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.5rem;
        }

        .taqe-card__btn {
            font-size: 0.78rem;
            font-weight: 600;
            border-radius: 6px;
            padding: 0.35rem 0.75rem;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
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

        /* ── Mini Player inline de Audio ── */
        .taqe-audio-inline {
            width: 100%;
            height: 32px;
            margin-top: 0.25rem;
        }

        /* ── Buscador y Controles ── */
        .taqe-controls-bar {
            background: #fff;
            border-radius: 10px;
            padding: 0.85rem 1rem;
            border: 1px solid #e3dbd1;
            margin-bottom: 1.5rem;
            box-shadow: 0 2px 8px rgba(42, 37, 32, 0.04);
        }

        .taqe-search-input {
            border: 1px solid #d5cbbf;
            border-radius: 6px;
            padding: 0.45rem 0.75rem 0.45rem 2.2rem;
            font-size: 0.88rem;
            width: 100%;
            outline: none;
            transition: border-color 0.2s ease;
        }

        .taqe-search-input:focus {
            border-color: #c78e41;
            box-shadow: 0 0 0 3px rgba(199, 142, 65, 0.18);
        }

        .taqe-search-wrap {
            position: relative;
        }

        .taqe-search-wrap .fa-search {
            position: absolute;
            left: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
            color: #9c8c7c;
        }
    </style>
@endsection

@section('contenido')

    {{-- ═══ HERO ═══ --}}
    <div class="lib-hero">
        <img src="{{ asset('img/fondo/taqe-hero.jpg') }}" alt="Taq'e de Saberes Pukllasunchis"
             class="lib-hero__bg" loading="eager">
        <div class="lib-hero__overlay taqe-hero-overlay"></div>
        <div class="lib-hero__content">
            <p class="lib-hero__eyebrow">EESP Pukllasunchis · Memoria y Saberes</p>
            <h1 class="lib-hero__title">Taq'e de Saberes</h1>
            <p class="text-white-50 small mb-0">Repositorio de Investigaciones y Creaciones Estudiantiles</p>
            <div class="lib-hero__divider"></div>
            <div class="lib-hero__meta">
                <span><i class="fa fa-graduation-cap"></i> {{ number_format($totalTrabajos) }} trabajos estudiantiles</span>
                <span><i class="fa fa-folder-open"></i> {{ $categorias->count() }} ejes temáticos</span>
                <span><i class="fa fa-cubes"></i> Multimedia (PDF, Audio, Video, Cartillas)</span>
            </div>
        </div>
    </div>

    {{-- ═══ BODY ═══ --}}
    <div class="container py-3 py-lg-4">

        {{-- Tarjeta de pequeña descripción de lo que es el Taq'e solicitada por el usuario --}}
        <div class="taqe-about-card">
            <div class="taqe-about-title">
                <i class="fa fa-info-circle"></i> ¿Qué es el Taq'e?
            </div>
            <p class="taqe-about-text">
                En la tradición andina, el <strong>Taq'e</strong> es el granero sagrado donde se resguardan las mejores semillas para asegurar el futuro de la comunidad. 
                En la <strong>EESP Pukllasunchis</strong>, este espacio es el depósito vivo de las investigaciones, proyectos pedagógicos, relatos orales, cartillas y creaciones multimedia elaboradas por nuestras y nuestros estudiantes a lo largo de su formación docente bilingüe intercultural.
            </p>
        </div>

        {{-- ── Barra de Filtro Rápido por Formato ── --}}
        <div class="taqe-filter-bar">
            <a href="{{ route('taqe.index', request()->except('formato', 'page')) }}"
               class="taqe-pill {{ !request('formato') ? 'is-active' : '' }}">
                <i class="fa fa-th-large"></i> Todos los formatos
                <span class="taqe-count">{{ $totalTrabajos }}</span>
            </a>

            @foreach ($formatos as $fKey => $fVal)
                @php
                    $count = $formatoCounts[$fKey] ?? 0;
                    $url = route('taqe.index', array_merge(request()->except('page'), ['formato' => $fKey]));
                @endphp
                <a href="{{ $url }}" class="taqe-pill {{ request('formato') === $fKey ? 'is-active' : '' }}">
                    <i class="fa {{ $fVal['icono'] }}"></i> {{ $fVal['nombre'] }}
                    <span class="taqe-count">{{ $count }}</span>
                </a>
            @endforeach
        </div>

        {{-- ── Barra de Controles: Búsqueda, Categoría, Idioma, Orden ── --}}
        <form method="GET" action="{{ route('taqe.index') }}" class="taqe-controls-bar">
            @if (request('formato'))
                <input type="hidden" name="formato" value="{{ request('formato') }}">
            @endif

            <div class="form-row align-items-center">
                <div class="form-group col-lg-4 col-md-6 mb-2 mb-lg-0">
                    <div class="taqe-search-wrap">
                        <i class="fa fa-search"></i>
                        <input type="text" name="q" value="{{ request('q') }}"
                               class="taqe-search-input"
                               placeholder="Buscar por título, autor o tema…"
                               autocomplete="off">
                    </div>
                </div>

                <div class="form-group col-lg-3 col-md-6 mb-2 mb-lg-0">
                    <select name="categoria_id" class="custom-select custom-select-sm" onchange="this.form.submit()">
                        <option value="">Todas las temáticas ({{ $categorias->count() }})</option>
                        @foreach ($categorias as $cat)
                            <option value="{{ $cat->id }}" @selected((string) request('categoria_id') === (string) $cat->id)>
                                {{ $cat->nombre }} ({{ $cat->trabajos_publicados_count }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group col-lg-2 col-md-6 mb-2 mb-lg-0">
                    <select name="idioma" class="custom-select custom-select-sm" onchange="this.form.submit()">
                        <option value="">Todos los idiomas</option>
                        @foreach ($idiomas as $iKey => $iVal)
                            <option value="{{ $iKey }}" @selected(request('idioma') === $iKey)>
                                {{ $iVal['nombre'] }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group col-lg-2 col-md-4 mb-2 mb-lg-0">
                    <select name="orden" class="custom-select custom-select-sm" onchange="this.form.submit()">
                        <option value="recientes" @selected(request('orden') === 'recientes')>Más recientes</option>
                        <option value="populares" @selected(request('orden') === 'populares')>Más vistos</option>
                        <option value="titulo" @selected(request('orden') === 'titulo')>Título (A-Z)</option>
                    </select>
                </div>

                <div class="form-group col-lg-1 col-md-2 mb-0 text-right">
                    <button type="submit" class="btn btn-sm btn-info w-100" title="Aplicar filtros">
                        <i class="fa fa-filter"></i>
                    </button>
                </div>
            </div>

            @if (request()->hasAny(['q', 'categoria_id', 'idioma', 'formato', 'periodo']))
                <div class="mt-2 pt-2 border-top d-flex justify-content-between align-items-center">
                    <span class="small text-muted">
                        Filtros aplicados · Mostrando {{ $trabajos->total() }} resultado(s)
                    </span>
                    <a href="{{ route('taqe.index') }}" class="small text-danger font-weight-bold">
                        <i class="fa fa-times"></i> Limpiar filtros
                    </a>
                </div>
            @endif
        </form>

        {{-- ── Grid de Trabajos Estudiantiles ── --}}
        @if ($trabajos->isNotEmpty())
            <div class="row g-3">
                @foreach ($trabajos as $trabajo)
                    @php
                        $fmt = $trabajo->formato_info;
                        $idm = $trabajo->idioma_info;
                    @endphp
                    <div class="col-md-6 col-lg-4 mb-4">
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
                                        <i class="fa fa-tag text-muted mr-1"></i>{{ $trabajo->categoria->nombre ?? 'General' }}
                                    </span>
                                    @if ($trabajo->año_periodo)
                                        <span class="badge badge-light border">{{ $trabajo->año_periodo }}</span>
                                    @endif
                                </div>

                                <h3 class="taqe-card__title">
                                    <a href="{{ route('taqe.show', $trabajo->slug) }}">
                                        {{ $trabajo->titulo }}
                                    </a>
                                </h3>

                                <div class="taqe-card__authors">
                                    <i class="fa fa-user-circle"></i>
                                    <span>{{ $trabajo->autores }}</span>
                                </div>

                                @if ($trabajo->ciclo_carrera)
                                    <p class="small text-muted mb-0">
                                        <i class="fa fa-graduation-cap mr-1"></i>{{ $trabajo->ciclo_carrera }}
                                    </p>
                                @endif

                                @if ($trabajo->descripcion)
                                    <p class="taqe-card__desc">{{ $trabajo->descripcion }}</p>
                                @endif

                                {{-- Mini reproductor inline para audio si corresponde --}}
                                @if ($trabajo->formato_principal === 'audio' && $trabajo->audio_url)
                                    <div class="mt-2">
                                        <audio controls class="taqe-audio-inline">
                                            <source src="{{ $trabajo->audio_url }}" type="audio/mpeg">
                                            Tu navegador no soporta el reproductor de audio.
                                        </audio>
                                    </div>
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
                                               title="Descargar PDF" target="_blank">
                                                <i class="fa fa-download"></i> PDF
                                            </a>
                                        @endif

                                        <a href="{{ route('taqe.show', $trabajo->slug) }}"
                                           class="taqe-card__btn taqe-card__btn--primary">
                                            Ver detalle <i class="fa fa-arrow-right"></i>
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
                <i class="fa fa-folder-open-o text-muted" style="font-size: 3.5rem;"></i>
                <h4 class="mt-3 text-muted">No se encontraron trabajos con los filtros seleccionados</h4>
                <p class="text-muted small">Prueba modificando los criterios de búsqueda o limpiando los filtros.</p>
                <a href="{{ route('taqe.index') }}" class="btn btn-sm btn-outline-secondary mt-2">
                    <i class="fa fa-refresh mr-1"></i>Ver todos los trabajos
                </a>
            </div>
        @endif

    </div>
@endsection
