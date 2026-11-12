@extends('layouts.app')
@section('titulo', 'Inicio')

@push('styles')
<style>
    .dash { --dash-borde: var(--bv-border, #ebe5dc); --dash-texto: var(--bv-text, #1e293b); --dash-suave: var(--bv-text-muted, #64748b); --dash-fondo: var(--bv-surface, #fff); --dash-mezcla: var(--bv-surface, #fff); }

    .dash-cabecera { display: flex; flex-wrap: wrap; align-items: flex-end; justify-content: space-between; gap: .5rem; margin-bottom: 1.25rem; }
    .dash-cabecera h4 { font-weight: 700; color: var(--dash-texto); margin: 0; letter-spacing: -.01em; }
    .dash-cabecera p { color: var(--dash-suave); font-size: .85rem; margin: .15rem 0 0; }
    .dash-fecha { font-size: .78rem; color: var(--dash-suave); border: 1px solid var(--dash-borde); border-radius: 999px; padding: .25rem .75rem; background: var(--dash-fondo); }

    .dash-grupo { font-size: .72rem; font-weight: 700; letter-spacing: .08em; text-transform: uppercase; color: var(--dash-suave);
        display: flex; align-items: center; gap: .6rem; margin: 1.2rem 0 .7rem; }
    .dash-grupo::after { content: ''; flex: 1; height: 1px; background: var(--dash-borde); }

    .dash-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(290px, 1fr)); gap: .85rem; }

    .dash-card {
        --acento: #6c757d;
        position: relative; display: flex; flex-direction: column; gap: .7rem;
        padding: .95rem 1rem .9rem; background: var(--dash-fondo); color: var(--dash-texto);
        border: 1px solid var(--dash-borde); border-radius: 12px; overflow: hidden;
        text-decoration: none !important;
        transition: border-color .2s ease, box-shadow .2s ease, transform .2s ease, background-color .2s ease;
    }
    .dash-card::before {
        content: ''; position: absolute; inset: 0 auto 0 0; width: 3px; background: var(--acento);
        transform: scaleY(0); transform-origin: top; transition: transform .25s ease;
    }
    .dash-card:hover, .dash-card:focus-visible {
        color: var(--dash-texto); border-color: color-mix(in srgb, var(--acento) 45%, var(--dash-borde));
        box-shadow: var(--bv-shadow-lg, 0 6px 18px rgba(0, 0, 0, .15)); transform: translateY(-2px); outline: none;
    }
    .dash-card:hover::before, .dash-card:focus-visible::before { transform: scaleY(1); }

    .dash-card__cab { display: flex; align-items: center; gap: .7rem; min-width: 0; }
    .dash-card__icono {
        flex: 0 0 38px; height: 38px; border-radius: 10px; display: flex; align-items: center; justify-content: center;
        font-size: 1.05rem; color: var(--acento); background: color-mix(in srgb, var(--acento) 16%, var(--dash-mezcla));
    }
    .dash-card__titulos { min-width: 0; flex: 1; }
    .dash-card__titulo { font-size: .93rem; font-weight: 700; margin: 0; line-height: 1.25; color: var(--dash-texto); }
    .dash-card__desc { font-size: .75rem; color: var(--dash-suave); margin: .1rem 0 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .dash-card__ir { color: var(--dash-suave); opacity: .55; font-size: .85rem; transition: transform .2s ease, color .2s ease; }
    .dash-card:hover .dash-card__ir { color: var(--acento); opacity: 1; transform: translateX(3px); }

    .dash-card__cuerpo { display: flex; align-items: flex-end; justify-content: space-between; gap: .75rem; }
    .dash-kpi { line-height: 1; white-space: nowrap; }
    .dash-kpi strong { display: block; font-size: 1.55rem; font-weight: 800; letter-spacing: -.02em; font-variant-numeric: tabular-nums; color: var(--dash-texto); }
    .dash-kpi span { font-size: .7rem; color: var(--dash-suave); font-weight: 600; }

    .dash-chips { display: flex; flex-wrap: wrap; justify-content: flex-end; gap: .35rem; }
    .dash-chip {
        --tono: #6c757d;
        display: inline-flex; align-items: center; gap: .35rem; font-size: .7rem; font-weight: 600; line-height: 1;
        padding: .28rem .55rem; border-radius: 6px; white-space: nowrap;
        color: color-mix(in srgb, var(--tono) 85%, var(--dash-texto)); background: color-mix(in srgb, var(--tono) 12%, var(--dash-mezcla));
        border: 1px solid transparent;
    }
    .dash-chip i { font-size: .68rem; opacity: .85; }
    .dash-chip--alerta { --tono: #dc2626; }
    .dash-chip--pendiente { --tono: #64748b; }
    .dash-chip--info { --tono: #0284c7; }
    .dash-chip--ok { --tono: #16a34a; }
    .dash-chip--neutro { --tono: #d97706; }

    :root[data-theme="dark"] .dash {
        --dash-borde: rgba(255, 255, 255, 0.08);
        --dash-texto: #f1f5f9;
        --dash-suave: #94a3b8;
        --dash-fondo: #111827;
        --dash-mezcla: #111827;
    }
    :root[data-theme="dark"] .dash-grupo {
        color: #94a3b8;
    }
    :root[data-theme="dark"] .dash-grupo::after {
        background: rgba(255, 255, 255, 0.1);
    }
    :root[data-theme="dark"] .dash-card {
        background: #111827;
        border-color: rgba(255, 255, 255, 0.08);
    }
    :root[data-theme="dark"] .dash-card:hover {
        background: #182234;
        border-color: rgba(255, 255, 255, 0.18);
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.5);
    }
    :root[data-theme="dark"] .dash-card__icono {
        background: color-mix(in srgb, var(--acento) 22%, #111827);
    }
    :root[data-theme="dark"] .dash-chip {
        background: rgba(255, 255, 255, 0.05);
        color: #e2e8f0;
        border-color: rgba(255, 255, 255, 0.08);
    }
    :root[data-theme="dark"] .dash-chip i {
        color: var(--tono);
    }
    :root[data-theme="dark"] .dash-chip--alerta {
        background: rgba(239, 68, 68, 0.15);
        color: #fca5a5;
        border-color: rgba(239, 68, 68, 0.3);
    }
    :root[data-theme="dark"] .dash-chip--info {
        background: rgba(56, 189, 248, 0.15);
        color: #7dd3fc;
        border-color: rgba(56, 189, 248, 0.3);
    }
    :root[data-theme="dark"] .dash-chip--ok {
        background: rgba(74, 222, 128, 0.15);
        color: #86efac;
        border-color: rgba(74, 222, 128, 0.3);
    }
    :root[data-theme="dark"] .dash-chip--pendiente {
        background: rgba(148, 163, 184, 0.12);
        color: #cbd5e1;
        border-color: rgba(148, 163, 184, 0.2);
    }
    :root[data-theme="dark"] .dash-chip--neutro {
        background: rgba(245, 158, 11, 0.15);
        color: #fde68a;
        border-color: rgba(245, 158, 11, 0.3);
    }

    @media (max-width: 575.98px) {
        .dash-grid { grid-template-columns: 1fr; }
        .dash-card__desc { white-space: normal; }
    }
</style>
@endpush

@section('contenido')
    @php
        $user = auth()->user();
        $isAdmin = $user->hasRole('admin');
        $isBiblioteca = $user->hasRole('biblioteca');
        $esPersonalBiblioteca = $isAdmin || $isBiblioteca;
        $n = fn ($v) => number_format((int) $v);

        // Cada módulo: grupo, permisos, destino, apariencia, cifra principal y chips secundarios.
        $modulos = [
            [
                'grupo' => 'Biblioteca', 'visible' => $esPersonalBiblioteca,
                'url' => route('libros.index'), 'icono' => 'fa-book', 'color' => '#17a2b8',
                'titulo' => 'Biblioteca Virtual', 'desc' => 'Textos digitales por sección y categoría',
                'kpi' => [$stats['libros'], 'libros'],
                'chips' => [
                    ['fa-clone', $n($stats['secciones']).' secciones', 'info'],
                    ['fa-folder', $n($stats['categorias']).' categorías', 'neutro'],
                ],
            ],
            [
                'grupo' => 'Biblioteca', 'visible' => $esPersonalBiblioteca,
                'url' => route('biblioteca-fisica.libros.index'), 'icono' => 'fa-archive', 'color' => '#6c757d',
                'titulo' => 'Biblioteca Física', 'desc' => 'Ejemplares, códigos y ubicación por local',
                'kpi' => [$stats['libros_fisicos'], 'textos'],
                'chips' => array_merge(
                    $stats['libros_por_sede']->map(fn ($s) => ['fa-map-marker', $n($s['total']).' '.$s['corto'], null, $s['color']])->values()->all(),
                    $stats['libros_sin_ubicacion'] > 0 ? [['fa-question-circle', $n($stats['libros_sin_ubicacion']).' sin ubicación', 'pendiente']] : []
                ),
            ],
            [
                'grupo' => 'Biblioteca', 'visible' => $esPersonalBiblioteca,
                'url' => route('biblioteca-fisica.prestamos.index'), 'icono' => 'fa-handshake-o', 'color' => '#6f42c1',
                'titulo' => 'Préstamos', 'desc' => 'Entregas, devoluciones y atrasos',
                'kpi' => [$stats['prestamos_activos'], 'activos'],
                'chips' => array_filter([
                    ['fa-list', $n($stats['prestamos_total']).' registros', 'neutro'],
                    $stats['prestamos_materiales'] > 0 ? ['fa-puzzle-piece', $n($stats['prestamos_materiales']).' de material', 'info'] : null,
                    $stats['prestamos_atrasados'] > 0 ? ['fa-exclamation-triangle', $n($stats['prestamos_atrasados']).' atrasados', 'alerta'] : null,
                ]),
            ],
            [
                'grupo' => 'Biblioteca', 'visible' => $esPersonalBiblioteca,
                'url' => route('biblioteca-fisica.materiales.index'), 'icono' => 'fa-puzzle-piece', 'color' => '#139a72',
                'titulo' => 'Material Didáctico', 'desc' => 'Materiales por área disponibles para préstamo',
                'kpi' => [$stats['materiales'], 'materiales'],
                'chips' => [
                    ['fa-folder', $n($stats['material_categorias']).' categorías', 'neutro'],
                    ['fa-th', $n($stats['material_unidades']).' unidades', 'info'],
                ],
            ],
            [
                'grupo' => 'Investigación y saberes', 'visible' => $esPersonalBiblioteca,
                'url' => route('admin.repositorio.estructura.index'), 'icono' => 'fa-university', 'color' => '#5a1ac2',
                'titulo' => 'Repositorio Institucional', 'desc' => 'Trabajos de investigación y tesis FID / PPD',
                'kpi' => [$stats['repo_publicados'], 'publicados'],
                'chips' => array_filter([
                    ['fa-sitemap', $n($stats['repo_colecciones']).' colecciones', 'neutro'],
                    $stats['repo_sin_pdf'] > 0 ? ['fa-exclamation-triangle', $n($stats['repo_sin_pdf']).' sin PDF', 'alerta', null, 'Documentos que aún no tienen su PDF'] : null,
                    $stats['repo_borradores'] > 0 ? ['fa-pencil', $n($stats['repo_borradores']).' borradores', 'pendiente'] : null,
                    $stats['repo_descargas'] > 0 ? ['fa-download', $n($stats['repo_descargas']).' descargas', 'ok'] : null,
                ]),
            ],
            [
                'grupo' => 'Investigación y saberes', 'visible' => $esPersonalBiblioteca,
                'url' => route('admin.taqe.categorias.index'), 'icono' => 'fa-calendar-check-o', 'color' => '#b07a32',
                'titulo' => "Taq'e", 'desc' => 'Actividades y eventos de saberes',
                'kpi' => [$stats['taqe_actividades'], 'actividades'],
                'chips' => array_filter([
                    $stats['taqe_participantes'] > 0 ? ['fa-users', $n($stats['taqe_participantes']).' participantes', 'info'] : null,
                    $stats['taqe_sin_participantes'] > 0 ? ['fa-pencil', $n($stats['taqe_sin_participantes']).' por completar', 'alerta', null, 'Eventos ya realizados sin participantes registrados'] : null,
                    $stats['taqe_borradores'] > 0 ? ['fa-eye-slash', $n($stats['taqe_borradores']).' borradores', 'pendiente'] : null,
                ]),
            ],
            [
                'grupo' => 'Multimedia', 'visible' => $isAdmin || $isBiblioteca || $user->hasRole('videos'),
                'url' => route('videoscat.index'), 'icono' => 'fa-video-camera', 'color' => '#dc3545',
                'titulo' => 'Videos', 'desc' => 'YouTube, Google Drive y subidas directas',
                'kpi' => [$stats['videos'], 'videos'],
                'chips' => [['fa-tags', $n($stats['video_categorias']).' categorías', 'neutro']],
            ],
            [
                'grupo' => 'Multimedia', 'visible' => $isAdmin || $user->hasRole('audios'),
                'url' => route('cancionescat.index'), 'icono' => 'fa-music', 'color' => '#28a745',
                'titulo' => 'Canciones y audios', 'desc' => 'Temas, cantautores y enlaces de audio',
                'kpi' => [$stats['canciones'], 'temas'],
                'chips' => [['fa-headphones', $n($stats['canciones_categorias']).' categorías', 'neutro']],
            ],
            [
                'grupo' => 'Sistema', 'visible' => $isAdmin || $user->hasRole('sisicha'),
                'url' => route('adminSisicha'), 'icono' => 'fa-bug', 'color' => '#fd7e14',
                'titulo' => 'Sisichakunay', 'desc' => 'Administración del programa Sisichakunay',
                'kpi' => null,
                'chips' => [['fa-external-link', 'Abrir módulo', 'neutro']],
            ],
            [
                'grupo' => 'Sistema', 'visible' => $isAdmin,
                'url' => route('admin.index'), 'icono' => 'fa-users', 'color' => '#007bff',
                'titulo' => 'Usuarios y roles', 'desc' => 'Cuentas, roles de acceso y permisos',
                'kpi' => null,
                'chips' => [['fa-shield', 'Gestionar accesos', 'neutro']],
            ],
        ];

        $grupos = collect($modulos)->where('visible', true)->groupBy('grupo');
    @endphp

    <div class="container-fluid dash">
        <div class="dash-cabecera">
            <div>
                <h4>{{ $isAdmin ? 'Administrador general' : 'Panel de administración' }}</h4>
                <p>Bienvenido, {{ $user->name }}. Gestiona los módulos de Puklla Virtual.</p>
            </div>
            <span class="dash-fecha"><i class="fa fa-calendar-o mr-1"></i>{{ now()->locale('es')->translatedFormat('l j \d\e F') }}</span>
        </div>

        @foreach ($grupos as $grupo => $lista)
            <div class="dash-grupo">{{ $grupo }}</div>
            <div class="dash-grid">
                @foreach ($lista as $m)
                    <a href="{{ $m['url'] }}" class="dash-card" style="--acento: {{ $m['color'] }};">
                        <div class="dash-card__cab">
                            <span class="dash-card__icono"><i class="fa {{ $m['icono'] }}"></i></span>
                            <div class="dash-card__titulos">
                                <h5 class="dash-card__titulo">{{ $m['titulo'] }}</h5>
                                <p class="dash-card__desc" title="{{ $m['desc'] }}">{{ $m['desc'] }}</p>
                            </div>
                            <i class="fa fa-chevron-right dash-card__ir" aria-hidden="true"></i>
                        </div>
                        <div class="dash-card__cuerpo">
                            @if ($m['kpi'])
                                <div class="dash-kpi">
                                    <strong>{{ $n($m['kpi'][0]) }}</strong>
                                    <span>{{ $m['kpi'][1] }}</span>
                                </div>
                            @else
                                <span></span>
                            @endif
                            <div class="dash-chips">
                                @foreach ($m['chips'] as $chip)
                                    @php [$icono, $texto, $tono, $color, $ayuda] = $chip + [null, null, null, null, null]; @endphp
                                    <span class="dash-chip {{ $tono ? 'dash-chip--'.$tono : '' }}"
                                          @if ($color) style="--tono: {{ $color }};" @endif
                                          @if ($ayuda) title="{{ $ayuda }}" @endif>
                                        <i class="fa {{ $icono }}"></i>{{ $texto }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        @endforeach
    </div>
@endsection
