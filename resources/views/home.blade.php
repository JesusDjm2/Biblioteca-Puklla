@extends('layouts.app')
@section('titulo', 'Inicio')

@push('styles')
<style>
    .dash { --dash-borde: var(--bv-border, #ebe5dc); --dash-texto: var(--bv-text, #2a2520); --dash-suave: var(--bv-text-muted, #8a7d70); --dash-fondo: var(--bv-surface, #fff); --dash-mezcla: var(--bv-surface, #fff); }

    .dash-cabecera { display: flex; flex-wrap: wrap; align-items: flex-end; justify-content: space-between; gap: .5rem; margin-bottom: 1.25rem; }
    .dash-cabecera h4 { font-weight: 700; color: var(--dash-texto); margin: 0; letter-spacing: -.01em; }
    .dash-cabecera p { color: var(--dash-suave); font-size: .85rem; margin: .15rem 0 0; }
    .dash-fecha { font-size: .78rem; color: var(--dash-suave); border: 1px solid var(--dash-borde); border-radius: 999px; padding: .25rem .75rem; background: var(--dash-fondo); }

    .dash-grupo { font-size: .68rem; font-weight: 700; letter-spacing: .09em; text-transform: uppercase; color: var(--dash-suave);
        display: flex; align-items: center; gap: .6rem; margin: 1.1rem 0 .65rem; }
    .dash-grupo::after { content: ''; flex: 1; height: 1px; background: var(--dash-borde); }

    .dash-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(290px, 1fr)); gap: .85rem; }

    .dash-card {
        --acento: #6c757d;
        position: relative; display: flex; flex-direction: column; gap: .7rem;
        padding: .95rem 1rem .9rem; background: var(--dash-fondo); color: var(--dash-texto);
        border: 1px solid var(--dash-borde); border-radius: 12px; overflow: hidden;
        text-decoration: none !important;
        transition: border-color .2s ease, box-shadow .2s ease, transform .2s ease;
    }
    .dash-card::before {
        content: ''; position: absolute; inset: 0 auto 0 0; width: 3px; background: var(--acento);
        transform: scaleY(0); transform-origin: top; transition: transform .25s ease;
    }
    .dash-card:hover, .dash-card:focus-visible {
        color: var(--dash-texto); border-color: color-mix(in srgb, var(--acento) 35%, var(--dash-borde));
        box-shadow: var(--bv-shadow-lg, 0 6px 18px rgba(42, 37, 32, .07)); transform: translateY(-2px); outline: none;
    }
    .dash-card:hover::before, .dash-card:focus-visible::before { transform: scaleY(1); }

    .dash-card__cab { display: flex; align-items: center; gap: .7rem; min-width: 0; }
    .dash-card__icono {
        flex: 0 0 38px; height: 38px; border-radius: 10px; display: flex; align-items: center; justify-content: center;
        font-size: 1.05rem; color: var(--acento); background: color-mix(in srgb, var(--acento) 14%, var(--dash-mezcla));
    }
    .dash-card__titulos { min-width: 0; flex: 1; }
    .dash-card__titulo { font-size: .93rem; font-weight: 700; margin: 0; line-height: 1.25; }
    .dash-card__desc { font-size: .75rem; color: var(--dash-suave); margin: .1rem 0 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .dash-card__ir { color: var(--dash-suave); opacity: .55; font-size: .85rem; transition: transform .2s ease, color .2s ease; }
    .dash-card:hover .dash-card__ir { color: var(--acento); opacity: 1; transform: translateX(3px); }

    .dash-card__cuerpo { display: flex; align-items: flex-end; justify-content: space-between; gap: .75rem; }
    .dash-kpi { line-height: 1; white-space: nowrap; }
    .dash-kpi strong { display: block; font-size: 1.55rem; font-weight: 800; letter-spacing: -.02em; font-variant-numeric: tabular-nums; }
    .dash-kpi span { font-size: .7rem; color: var(--dash-suave); font-weight: 600; }

    .dash-chips { display: flex; flex-wrap: wrap; justify-content: flex-end; gap: .3rem; }
    .dash-chip {
        --tono: #6c757d;
        display: inline-flex; align-items: center; gap: .3rem; font-size: .7rem; font-weight: 600; line-height: 1;
        padding: .28rem .5rem; border-radius: 6px; white-space: nowrap;
        color: color-mix(in srgb, var(--tono) 85%, var(--dash-texto)); background: color-mix(in srgb, var(--tono) 13%, var(--dash-mezcla));
    }
    .dash-chip i { font-size: .68rem; opacity: .85; }
    .dash-chip--alerta { --tono: #c0392b; }
    .dash-chip--pendiente { --tono: #6c757d; }
    .dash-chip--info { --tono: #138496; }
    .dash-chip--ok { --tono: #1e7e34; }
    .dash-chip--neutro { --tono: #8c6d3f; }

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
