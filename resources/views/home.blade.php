@extends('layouts.app')
@section('titulo', 'Inicio')

@push('styles')
<style>
    .dash-card {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border-radius: 12px;
        border: 1px solid rgba(0, 0, 0, 0.08);
        background: #fff;
    }
    .dash-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.08) !important;
    }
    .dash-icon-circle {
        width: 52px;
        height: 52px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1.4rem;
        margin-bottom: 1.25rem;
    }
    .icon-virtual { background: rgba(23, 162, 184, 0.12); color: #17a2b8; }
    .icon-fisica { background: rgba(108, 117, 125, 0.12); color: #6c757d; }
    .icon-prestamos { background: rgba(111, 66, 193, 0.12); color: #6f42c1; }
    .icon-videos { background: rgba(220, 53, 69, 0.12); color: #dc3545; }
    .icon-canciones { background: rgba(40, 167, 69, 0.12); color: #28a745; }
    .icon-usuarios { background: rgba(0, 123, 255, 0.12); color: #007bff; }
    .icon-sisicha { background: rgba(253, 126, 20, 0.12); color: #fd7e14; }

    .stat-badge {
        font-size: 0.775rem;
        font-weight: 500;
        padding: 0.3rem 0.65rem;
        border-radius: 50px;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        margin-right: 0.25rem;
        margin-bottom: 0.35rem;
    }
    .badge-secciones { background: #e8f7fa; color: #138496; }
    .badge-categorias { background: #fff3cd; color: #856404; }
    .badge-registros { background: #d4edda; color: #155724; }
    .badge-atrasados { background: #f8d7da; color: #a71d2a; }
</style>
@endpush

@section('contenido')
    @php
        $user = auth()->user();
        $isAdmin = $user->hasRole('admin');
        $isBiblioteca = $user->hasRole('biblioteca');
        $isVideos = $user->hasRole('videos');
        $isAudios = $user->hasRole('audios');
        $isSisicha = $user->hasRole('sisicha');
    @endphp

    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-lg-12">
                <h4 class="font-weight-bold text-dark mb-1">
                    @if ($isAdmin)
                        Administrador General
                    @else
                        Panel de Administración
                    @endif
                </h4>
                <p class="text-muted small">Bienvenido, {{ $user->name }}. Gestiona los módulos de Puklla Virtual.</p>
            </div>
        </div>

        <div class="row">
            {{-- MÓDULO: BIBLIOTECA VIRTUAL --}}
            @if ($isAdmin || $isBiblioteca)
                <div class="col-md-6 col-lg-4 mb-4">
                    <a href="{{ route('libros.index') }}" class="text-decoration-none text-dark d-block h-100">
                        <div class="card h-100 shadow-sm border-0 dash-card">
                            <div class="card-body d-flex flex-column justify-content-between p-4">
                                <div>
                                    <div class="dash-icon-circle icon-virtual">
                                        <i class="fa fa-book"></i>
                                    </div>
                                    <h5 class="font-weight-bold text-dark mb-2">Biblioteca Virtual</h5>
                                    <p class="small text-muted mb-4">Gestiona textos, autores, enlaces de Drive, filtros por sección y categoría.</p>
                                </div>
                                <div class="mt-2">
                                    <span class="stat-badge badge-secciones" title="Secciones virtuales">
                                        <i class="fa fa-layer-group"></i> {{ $stats['secciones'] }} secc.
                                    </span>
                                    <span class="stat-badge badge-categorias" title="Categorías virtuales">
                                        <i class="fa fa-folder"></i> {{ $stats['categorias'] }} cat.
                                    </span>
                                    <span class="stat-badge badge-registros" title="Libros virtuales">
                                        <i class="fa fa-file-alt"></i> {{ $stats['libros'] }} libros
                                    </span>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            @endif

            {{-- MÓDULO: BIBLIOTECA FÍSICA --}}
            @if ($isAdmin || $isBiblioteca)
                <div class="col-md-6 col-lg-4 mb-4">
                    <a href="{{ route('biblioteca-fisica.categorias.index') }}" class="text-decoration-none text-dark d-block h-100">
                        <div class="card h-100 shadow-sm border-0 dash-card">
                            <div class="card-body d-flex flex-column justify-content-between p-4">
                                <div>
                                    <div class="dash-icon-circle icon-fisica">
                                        <i class="fa fa-archive"></i>
                                    </div>
                                    <h5 class="font-weight-bold text-dark mb-2">Biblioteca Física</h5>
                                    <p class="small text-muted mb-4">Administra categorías de libros físicos, ejemplares, autores y códigos físicos.</p>
                                </div>
                                <div class="mt-2">
                                    <span class="stat-badge badge-categorias" title="Categorías físicas">
                                        <i class="fa fa-folder"></i> {{ $stats['categorias_fisicas'] }} cat.
                                    </span>
                                    <span class="stat-badge badge-registros" title="Ejemplares físicos">
                                        <i class="fa fa-file-signature"></i> {{ $stats['libros_fisicos'] }} textos
                                    </span>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            @endif

            {{-- MÓDULO: PRÉSTAMOS --}}
            @if ($isAdmin || $isBiblioteca)
                <div class="col-md-6 col-lg-4 mb-4">
                    <a href="{{ route('biblioteca-fisica.prestamos.index') }}" class="text-decoration-none text-dark d-block h-100">
                        <div class="card h-100 shadow-sm border-0 dash-card">
                            <div class="card-body d-flex flex-column justify-content-between p-4">
                                <div>
                                    <div class="dash-icon-circle icon-prestamos">
                                        <i class="fa fa-hand-holding"></i>
                                    </div>
                                    <h5 class="font-weight-bold text-dark mb-2">Préstamos</h5>
                                    <p class="small text-muted mb-4">Registra y controla los préstamos de libros físicos: entregas, devoluciones y atrasos.</p>
                                </div>
                                <div class="mt-2">
                                    <span class="stat-badge badge-registros" title="Préstamos registrados">
                                        <i class="fa fa-list"></i> {{ $stats['prestamos_total'] }} registros
                                    </span>
                                    <span class="stat-badge badge-secciones" title="Préstamos activos">
                                        <i class="fa fa-hand-holding"></i> {{ $stats['prestamos_activos'] }} activos
                                    </span>
                                    @if ($stats['prestamos_atrasados'] > 0)
                                        <span class="stat-badge badge-atrasados" title="Préstamos atrasados">
                                            <i class="fa fa-exclamation-triangle"></i> {{ $stats['prestamos_atrasados'] }} atrasados
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            @endif

            {{-- MÓDULO: VIDEOS --}}
            @if ($isAdmin || $isVideos)
                <div class="col-md-6 col-lg-4 mb-4">
                    <a href="{{ route('videos.index') }}" class="text-decoration-none text-dark d-block h-100">
                        <div class="card h-100 shadow-sm border-0 dash-card">
                            <div class="card-body d-flex flex-column justify-content-between p-4">
                                <div>
                                    <div class="dash-icon-circle icon-videos">
                                        <i class="fa fa-video"></i>
                                    </div>
                                    <h5 class="font-weight-bold text-dark mb-2">Videos (YouTube/Drive)</h5>
                                    <p class="small text-muted mb-4">Gestiona el catálogo de videos de YouTube, reproducciones y almacenamiento de Drive.</p>
                                </div>
                                <div class="mt-2">
                                    <span class="stat-badge badge-categorias" title="Categorías de video">
                                        <i class="fa fa-tags"></i> {{ $stats['video_categorias'] }} cat.
                                    </span>
                                    <span class="stat-badge badge-registros" title="Total de videos">
                                        <i class="fa fa-play"></i> {{ $stats['videos'] }} videos
                                    </span>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            @endif

            {{-- MÓDULO: CANCIONES --}}
            @if ($isAdmin || $isAudios)
                <div class="col-md-6 col-lg-4 mb-4">
                    <a href="{{ route('canciones.index') }}" class="text-decoration-none text-dark d-block h-100">
                        <div class="card h-100 shadow-sm border-0 dash-card">
                            <div class="card-body d-flex flex-column justify-content-between p-4">
                                <div>
                                    <div class="dash-icon-circle icon-canciones">
                                        <i class="fa fa-music"></i>
                                    </div>
                                    <h5 class="font-weight-bold text-dark mb-2">Canciones & Audios</h5>
                                    <p class="small text-muted mb-4">Administra canciones, audios, cantautores, enlaces de Spotify, YouTube o Drive.</p>
                                </div>
                                <div class="mt-2">
                                    <span class="stat-badge badge-categorias" title="Categorías de música">
                                        <i class="fa fa-compact-disc"></i> {{ $stats['canciones_categorias'] }} cat.
                                    </span>
                                    <span class="stat-badge badge-registros" title="Total canciones">
                                        <i class="fa fa-volume-up"></i> {{ $stats['canciones'] }} temas
                                    </span>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            @endif

            {{-- MÓDULO: SISICHAKUNA --}}
            @if ($isAdmin || $isSisicha)
                <div class="col-md-6 col-lg-4 mb-4">
                    <a href="{{ route('adminSisicha') }}" class="text-decoration-none text-dark d-block h-100">
                        <div class="card h-100 shadow-sm border-0 dash-card">
                            <div class="card-body d-flex flex-column justify-content-between p-4">
                                <div>
                                    <div class="dash-icon-circle icon-sisicha">
                                        <i class="fa fa-bug"></i>
                                    </div>
                                    <h5 class="font-weight-bold text-dark mb-2">Sisichakunay</h5>
                                    <p class="small text-muted mb-4">Accede al módulo de administración del programa Sisichakunay.</p>
                                </div>
                                <div class="mt-2">
                                    <span class="stat-badge badge-registros">
                                        <i class="fa fa-external-link-alt"></i> Administrar
                                    </span>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            @endif

            {{-- MÓDULO: USUARIOS --}}
            @if ($isAdmin)
                <div class="col-md-6 col-lg-4 mb-4">
                    <a href="{{ route('admin.index') }}" class="text-decoration-none text-dark d-block h-100">
                        <div class="card h-100 shadow-sm border-0 dash-card">
                            <div class="card-body d-flex flex-column justify-content-between p-4">
                                <div>
                                    <div class="dash-icon-circle icon-usuarios">
                                        <i class="fa fa-users"></i>
                                    </div>
                                    <h5 class="font-weight-bold text-dark mb-2">Usuarios & Roles</h5>
                                    <p class="small text-muted mb-4">Administra cuentas de usuario, roles de acceso, contraseñas y permisos del panel.</p>
                                </div>
                                <div class="mt-2">
                                    <span class="stat-badge badge-secciones">
                                        <i class="fa fa-user-shield"></i> Gestionar usuarios
                                    </span>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            @endif
        </div>
    </div>
@endsection

