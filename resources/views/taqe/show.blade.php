@extends('layouts.padre')
@section('titulo', "- {$trabajo->titulo}")

@section('estilos')
    <style>
        .taqe-detail-header {
            background: linear-gradient(135deg, #2a221b 0%, #17120e 100%);
            color: #fff;
            padding: 3rem 0 2.5rem;
            border-bottom: 3px solid #c78e41;
        }

        .taqe-badge-lg {
            padding: 0.35rem 0.85rem;
            border-radius: 6px;
            font-size: 0.82rem;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
        }

        .taqe-detail-title {
            font-family: "Lato", system-ui, -apple-system, sans-serif;
            font-size: clamp(1.4rem, 3.2vw, 2.2rem);
            font-weight: 700;
            color: #fff;
            margin: 0.75rem 0 0.5rem;
            line-height: 1.25;
        }

        .taqe-detail-authors {
            font-size: 1.05rem;
            color: #d8a562;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.5rem;
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

    {{-- ═══ HEADER DEL DETALLE ═══ --}}
    <div class="taqe-detail-header">
        <div class="container">
            {{-- Breadcrumb --}}
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb bg-transparent p-0 mb-3" style="font-size: 0.82rem;">
                    <li class="breadcrumb-item"><a href="{{ route('index') }}" class="text-white-50">Inicio</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('taqe.index') }}" class="text-white-50">Taq'e</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('taqe.index', ['categoria_id' => $trabajo->categoria_id]) }}" class="text-white-50">{{ $trabajo->categoria->nombre ?? 'General' }}</a></li>
                    <li class="breadcrumb-item active text-white" aria-current="page">{{ Str::limit($trabajo->titulo, 35) }}</li>
                </ol>
            </nav>

            <div class="d-flex flex-wrap gap-2 mb-2">
                <span class="taqe-badge-lg" style="background: {{ $fmt['color'] }}; color: #fff;">
                    <i class="fa {{ $fmt['icono'] }}"></i> {{ $fmt['nombre'] }}
                </span>
                <span class="taqe-badge-lg" style="background: rgba(255,255,255,0.15); border: 1px solid rgba(255,255,255,0.3); color: #fff;">
                    <i class="fa {{ $idm['icono'] }}"></i> {{ $idm['nombre'] }}
                </span>
                @if ($trabajo->año_periodo)
                    <span class="taqe-badge-lg" style="background: rgba(255,255,255,0.1); color: #f5eedf;">
                        <i class="fa fa-calendar"></i> {{ $trabajo->año_periodo }}
                    </span>
                @endif
            </div>

            <h1 class="taqe-detail-title">{{ $trabajo->titulo }}</h1>

            <div class="taqe-detail-authors">
                <i class="fa fa-user-circle"></i>
                <span>Investigador(es): {{ $trabajo->autores }}</span>
            </div>
            @if ($trabajo->ciclo_carrera)
                <p class="text-white-50 small mb-0 mt-1">
                    <i class="fa fa-graduation-cap mr-1"></i>{{ $trabajo->ciclo_carrera }}
                </p>
            @endif
        </div>
    </div>

    {{-- ═══ BODY DEL DETALLE ═══ --}}
    <div class="container py-4">
        <div class="row">
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

                {{-- VISOR DE DOCUMENTO PDF (SI TIENE PDF) --}}
                @if ($trabajo->pdf_url)
                    <div class="taqe-card-box">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h3 class="taqe-section-title mb-0 border-0 p-0">
                                <i class="fa fa-file-pdf-o text-danger"></i> Documento de investigación
                            </h3>
                            <a href="{{ route('taqe.descargar', $trabajo->slug) }}" class="btn btn-sm btn-danger font-weight-bold" target="_blank">
                                <i class="fa fa-download mr-1"></i> Descargar PDF
                            </a>
                        </div>
                        <iframe src="{{ $trabajo->pdf_url }}" class="taqe-pdf-frame"></iframe>
                    </div>
                @endif

                {{-- RESUMEN / DESCRIPCIÓN DEL TRABAJO --}}
                <div class="taqe-card-box">
                    <h3 class="taqe-section-title">
                        <i class="fa fa-align-left text-warning"></i> Resumen de la investigación
                    </h3>
                    <div style="font-size: 0.98rem; line-height: 1.7; color: #3d352e;">
                        @if ($trabajo->descripcion)
                            {!! nl2br(e($trabajo->descripcion)) !!}
                        @else
                            <p class="text-muted italic">Este trabajo no cuenta con una descripción detallada registrada.</p>
                        @endif
                    </div>
                </div>

                {{-- TRABAJOS RELACIONADOS --}}
                @if ($relacionados->isNotEmpty())
                    <div class="mt-4">
                        <h4 class="font-weight-bold mb-3" style="color: #2a2520;">
                            <i class="fa fa-book text-muted mr-1"></i> Otros trabajos en "{{ $trabajo->categoria->nombre }}"
                        </h4>
                        <div class="row">
                            @foreach ($relacionados as $rel)
                                <div class="col-md-4 mb-3">
                                    <div class="card h-100 border-0 shadow-sm rounded-lg overflow-hidden">
                                        <img src="{{ $rel->portada_url }}" class="card-img-top" style="height: 120px; object-fit: cover;" alt="{{ $rel->titulo }}">
                                        <div class="card-body p-3 d-flex flex-column">
                                            <span class="badge badge-light border mb-1 align-self-start">{{ $rel->formato_info['nombre'] }}</span>
                                            <h6 class="font-weight-bold mb-1">
                                                <a href="{{ route('taqe.show', $rel->slug) }}" class="text-dark text-decoration-none">
                                                    {{ Str::limit($rel->titulo, 45) }}
                                                </a>
                                            </h6>
                                            <p class="small text-muted mb-0 mt-auto">{{ $rel->autores }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

            </div>

            {{-- Columna Lateral: Ficha Técnica e Información --}}
            <div class="col-lg-4">
                <div class="taqe-card-box">
                    <h3 class="taqe-section-title">
                        <i class="fa fa-info-circle text-primary"></i> Ficha del Trabajo
                    </h3>

                    <div class="taqe-meta-item">
                        <span class="taqe-meta-label">Categoría / Eje:</span>
                        <span class="taqe-meta-val">{{ $trabajo->categoria->nombre ?? 'General' }}</span>
                    </div>

                    <div class="taqe-meta-item">
                        <span class="taqe-meta-label">Formato:</span>
                        <span class="taqe-meta-val">
                            <i class="fa {{ $fmt['icono'] }}"></i> {{ $fmt['nombre'] }}
                        </span>
                    </div>

                    <div class="taqe-meta-item">
                        <span class="taqe-meta-label">Idioma:</span>
                        <span class="taqe-meta-val">{{ $idm['nombre'] }}</span>
                    </div>

                    @if ($trabajo->año_periodo)
                        <div class="taqe-meta-item">
                            <span class="taqe-meta-label">Año / Periodo:</span>
                            <span class="taqe-meta-val">{{ $trabajo->año_periodo }}</span>
                        </div>
                    @endif

                    @if ($trabajo->ciclo_carrera)
                        <div class="taqe-meta-item">
                            <span class="taqe-meta-label">Ciclo / Formación:</span>
                            <span class="taqe-meta-val">{{ $trabajo->ciclo_carrera }}</span>
                        </div>
                    @endif

                    <div class="taqe-meta-item">
                        <span class="taqe-meta-label">Visualizaciones:</span>
                        <span class="taqe-meta-val">{{ number_format($trabajo->vistas) }}</span>
                    </div>

                    @if ($trabajo->descargas > 0)
                        <div class="taqe-meta-item">
                            <span class="taqe-meta-label">Descargas de PDF:</span>
                            <span class="taqe-meta-val">{{ number_format($trabajo->descargas) }}</span>
                        </div>
                    @endif

                    @if ($trabajo->archivo_pdf)
                        <div class="mt-3 pt-3 border-top">
                            <a href="{{ route('taqe.descargar', $trabajo->slug) }}"
                               class="btn btn-danger btn-block font-weight-bold" target="_blank">
                                <i class="fa fa-download mr-1"></i> Descargar Documento (PDF)
                            </a>
                        </div>
                    @endif

                    <div class="mt-3">
                        <a href="{{ route('taqe.index') }}" class="btn btn-outline-secondary btn-block">
                            <i class="fa fa-arrow-left mr-1"></i> Volver al Taq'e
                        </a>
                    </div>
                </div>

                {{-- Banner Institucional Pukllasunchis --}}
                <div class="card border-0 text-white rounded-lg p-3" style="background: linear-gradient(135deg, #8c581e 0%, #5c3a13 100%);">
                    <h6 class="font-weight-bold mb-1"><i class="fa fa-graduation-cap mr-1"></i> EESP Pukllasunchis</h6>
                    <p class="small mb-0 text-white-50">
                        Formación docente inicial con identidad cultural, bilingüismo y valoración de los saberes comunitarios.
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection
