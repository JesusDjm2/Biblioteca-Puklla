@extends('layouts.padre')
@section('titulo', "- {$trabajo->titulo}")

@section('estilos')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrollTrigger.min.js"></script>
    <style>
        .taqe-badge-lg {
            padding: 0.35rem 0.85rem;
            border-radius: 6px;
            font-size: 0.82rem;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
        }

        .taqe-card-box {
            background: #fff;
            border-radius: 12px;
            border: 1px solid rgba(200, 142, 65, 0.18);
            box-shadow: 0 2px 12px rgba(42, 37, 32, 0.06);
            padding: 1.5rem;
            margin-bottom: 1.75rem;
        }

        .taqe-section-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: #2a2520;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid #f0e9df;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .taqe-media-embed {
            position: relative;
            width: 100%;
            aspect-ratio: 16 / 9;
            background: #000;
            border-radius: 10px;
            overflow: hidden;
            margin-bottom: 1.25rem;
        }

        .taqe-media-embed iframe {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            border: 0;
        }

        .taqe-player-box {
            background: #fdfaf5;
            border: 1px solid #e3dbd1;
            border-radius: 10px;
            padding: 1.25rem;
            margin-bottom: 1.25rem;
        }

        .taqe-player-box audio {
            width: 100%;
        }

        .taqe-meta-item {
            display: flex;
            justify-content: space-between;
            padding: 0.6rem 0;
            border-bottom: 1px dashed #e8e1d5;
            font-size: 0.88rem;
        }

        .taqe-meta-item:last-child {
            border-bottom: none;
        }

        .taqe-meta-label {
            color: #7c6f62;
            font-weight: 600;
        }

        .taqe-meta-val {
            color: #2a2520;
            font-weight: 600;
            text-align: right;
        }

        .taqe-pdf-frame {
            width: 100%;
            height: 600px;
            border: 1px solid #d5cbbf;
            border-radius: 8px;
        }
    </style>
@endsection

@section('contenido')
    @php
        $fmt = $trabajo->formato_info;
        $idm = $trabajo->idioma_info;
    @endphp

    {{-- ===== HERO ===== --}}
    <div class="lib-hero">
        <img src="{{ $trabajo->portada_url }}" alt="{{ $trabajo->titulo }}" class="lib-hero__bg" loading="eager">
        <div class="lib-hero__overlay"></div>
        <div class="lib-hero__content">
            <p class="lib-hero__eyebrow">ESPP Pukllasunchis · {{ $trabajo->categoria->nombre ?? 'Taq\'e' }}</p>
            <h1 class="lib-hero__title lib-hero__title--cat">{{ $trabajo->titulo }}</h1>
            <div class="lib-hero__divider"></div>
            <div class="lib-hero__meta">
                <span><i class="fa fa-user"></i> {{ $trabajo->autores }}</span>
                <span><i class="fa {{ $fmt['icono'] }}"></i> {{ $fmt['nombre'] }}</span>
                @if ($trabajo->año_periodo)
                    <span><i class="fa fa-calendar"></i> {{ $trabajo->año_periodo }}</span>
                @endif
            </div>
        </div>
    </div>

    {{-- ═══ BODY DEL DETALLE ═══ --}}
    <div class="container py-4 py-lg-5">

        {{-- Breadcrumb --}}
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb bg-white px-3 py-2 rounded border" style="font-size: 0.85rem;">
                <li class="breadcrumb-item"><a href="{{ route('index') }}">Inicio</a></li>
                <li class="breadcrumb-item"><a href="{{ route('taqe.index') }}">Taq'e</a></li>
                @if ($trabajo->categoria)
                    <li class="breadcrumb-item"><a href="{{ route('taqe.index', ['categoria_id' => $trabajo->categoria_id]) }}">{{ $trabajo->categoria->nombre }}</a></li>
                @endif
                <li class="breadcrumb-item active" aria-current="page">{{ Str::limit($trabajo->titulo, 40) }}</li>
            </ol>
        </nav>

        <div class="row g-4">
            {{-- Columna Principal: Recursos Multimedia & Descripción --}}
            <div class="col-lg-8">
                
                {{-- REPRODUCTOR DE VIDEO (SI TIENE VIDEO) --}}
                @if ($trabajo->video_embed_url)
                    <div class="taqe-card-box p-0 overflow-hidden">
                        <div class="taqe-media-embed mb-0">
                            <iframe src="{{ $trabajo->video_embed_url }}" allowfullscreen
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"></iframe>
                        </div>
                    </div>
                @endif

                {{-- REPRODUCTOR DE AUDIO (SI TIENE AUDIO) --}}
                @if ($trabajo->audio_url)
                    <div class="taqe-card-box">
                        <h3 class="taqe-section-title">
                            <i class="fa fa-headphones text-info"></i> Escuchar relato / testimonio sonoro
                        </h3>
                        <div class="taqe-player-box">
                            <audio controls class="w-100">
                                <source src="{{ $trabajo->audio_url }}" type="audio/mpeg">
                                Tu navegador no soporta el reproductor de audio.
                            </audio>
                        </div>
                    </div>
                @endif

                {{-- DESCRIPCIÓN / RESUMEN --}}
                <div class="taqe-card-box">
                    <h3 class="taqe-section-title">
                        <i class="fa fa-align-left text-warning"></i> Resumen del trabajo
                    </h3>
                    @if ($trabajo->descripcion)
                        <div style="font-size: 0.95rem; line-height: 1.7; color: #423b33;">
                            {!! nl2br(e($trabajo->descripcion)) !!}
                        </div>
                    @else
                        <p class="text-muted mb-0"><em>No se ha registrado una descripción detallada para este trabajo.</em></p>
                    @endif
                </div>

                {{-- VISOR DE PDF EMBEBIDO O ACCIÓN DE DESCARGA --}}
                @if ($trabajo->archivo_pdf)
                    <div class="taqe-card-box">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h3 class="taqe-section-title mb-0 border-0 pb-0">
                                <i class="fa fa-file-pdf text-danger"></i> Documento de investigación
                            </h3>
                            <a href="{{ route('taqe.descargar', $trabajo->slug) }}" class="btn btn-sm btn-outline-danger" target="_blank">
                                <i class="fa fa-download mr-1"></i> Descargar PDF
                            </a>
                        </div>
                        <iframe src="{{ $trabajo->pdf_url }}#toolbar=1" class="taqe-pdf-frame"></iframe>
                    </div>
                @endif
            </div>

            {{-- Columna Lateral: Ficha Técnica & Trabajos Relacionados --}}
            <div class="col-lg-4">
                
                {{-- Ficha Técnica --}}
                <div class="taqe-card-box">
                    <h3 class="taqe-section-title">
                        <i class="fa fa-info-circle text-primary"></i> Ficha del trabajo
                    </h3>

                    <div class="taqe-meta-item">
                        <span class="taqe-meta-label">Categoría</span>
                        <span class="taqe-meta-val">
                            <a href="{{ route('taqe.index', ['categoria_id' => $trabajo->categoria_id]) }}">
                                {{ $trabajo->categoria->nombre ?? 'General' }}
                            </a>
                        </span>
                    </div>

                    <div class="taqe-meta-item">
                        <span class="taqe-meta-label">Formato</span>
                        <span class="taqe-meta-val">
                            <span class="badge" style="background: {{ $fmt['color'] }}; color: #fff;">
                                <i class="fa {{ $fmt['icono'] }}"></i> {{ $fmt['nombre'] }}
                            </span>
                        </span>
                    </div>

                    <div class="taqe-meta-item">
                        <span class="taqe-meta-label">Lengua / Idioma</span>
                        <span class="taqe-meta-val">{{ $idm['nombre'] }}</span>
                    </div>

                    @if ($trabajo->año_periodo)
                        <div class="taqe-meta-item">
                            <span class="taqe-meta-label">Año / Periodo</span>
                            <span class="taqe-meta-val">{{ $trabajo->año_periodo }}</span>
                        </div>
                    @endif

                    @if ($trabajo->ciclo_carrera)
                        <div class="taqe-meta-item">
                            <span class="taqe-meta-label">Ciclo / Especialidad</span>
                            <span class="taqe-meta-val">{{ $trabajo->ciclo_carrera }}</span>
                        </div>
                    @endif

                    <div class="taqe-meta-item">
                        <span class="taqe-meta-label">Vistas</span>
                        <span class="taqe-meta-val"><i class="fa fa-eye mr-1 text-muted"></i>{{ number_format($trabajo->vistas) }}</span>
                    </div>

                    <div class="taqe-meta-item">
                        <span class="taqe-meta-label">Descargas</span>
                        <span class="taqe-meta-val"><i class="fa fa-download mr-1 text-muted"></i>{{ number_format($trabajo->descargas) }}</span>
                    </div>

                    {{-- Acciones principales --}}
                    <div class="mt-4 pt-2">
                        @if ($trabajo->archivo_pdf)
                            <a href="{{ route('taqe.descargar', $trabajo->slug) }}" class="btn btn-danger btn-block w-100 mb-2">
                                <i class="fa fa-download mr-1"></i> Descargar Documento (PDF)
                            </a>
                        @endif

                        @if ($trabajo->audio_url)
                            <a href="{{ $trabajo->audio_url }}" download class="btn btn-outline-info btn-block w-100 mb-2">
                                <i class="fa fa-volume-up mr-1"></i> Descargar Audio
                            </a>
                        @endif

                        <a href="{{ route('taqe.index') }}" class="btn btn-outline-secondary btn-block w-100">
                            <i class="fa fa-arrow-left mr-1"></i> Volver al repositorio
                        </a>
                    </div>
                </div>

                {{-- Trabajos Relacionados --}}
                @if ($relacionados->isNotEmpty())
                    <div class="taqe-card-box">
                        <h3 class="taqe-section-title">
                            <i class="fa fa-bookmark text-success"></i> Trabajos relacionados
                        </h3>

                        <div class="list-group list-group-flush">
                            @foreach ($relacionados as $rel)
                                <a href="{{ route('taqe.show', $rel->slug) }}" class="list-group-item list-group-item-action px-0 border-bottom py-2">
                                    <div class="d-flex w-100 justify-content-between align-items-center">
                                        <h6 class="mb-1 text-truncate" style="max-width: 80%; font-size: 0.9rem;" title="{{ $rel->titulo }}">
                                            {{ $rel->titulo }}
                                        </h6>
                                        <small class="badge badge-light border">{{ $rel->formato_info['nombre'] }}</small>
                                    </div>
                                    <small class="text-muted"><i class="fa fa-user mr-1"></i>{{ $rel->autores }}</small>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

            </div>
        </div>
    </div>

    {{-- Script de reveal con GSAP --}}
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
    })();
    </script>
@endsection
