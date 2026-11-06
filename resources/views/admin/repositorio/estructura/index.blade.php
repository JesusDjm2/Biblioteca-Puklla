@extends('layouts.app')
@section('titulo', 'Repositorio — Comunidades y colecciones')
@section('contenido')
    @php
        // Totales de una comunidad contando sus subcomunidades (cualquier profundidad).
        $totales = function ($comunidad) use (&$totales) {
            $t = [
                'colecciones' => $comunidad->colecciones->count(),
                'documentos' => $comunidad->colecciones->sum(fn ($c) => $c->items_count ?? $c->items()->count()),
                'borradores' => $comunidad->colecciones->sum('borradores_count'),
            ];
            foreach ($comunidad->subcomunidades as $sub) {
                foreach ($totales($sub) as $k => $v) {
                    $t[$k] += $v;
                }
            }

            return $t;
        };
        $totalColecciones = $comunidades->sum(fn ($c) => $totales($c)['colecciones']);
    @endphp

    @include('partials.repo-crud-header', [
        'active' => 'estructura',
        'stats' => [
            ['valor' => $totalColecciones, 'singular' => 'colección', 'plural' => 'colecciones', 'icono' => 'fa-folder-open'],
            ['valor' => $totalItems, 'singular' => 'documento', 'plural' => 'documentos', 'icono' => 'fa-file-pdf-o'],
        ],
        'botones' => [
            ['route' => route('admin.repositorio.estructura.comunidades.create'), 'label' => 'Nueva comunidad', 'icono' => 'fa-university', 'clase' => 'btn-outline-secondary'],
            ['route' => route('admin.repositorio.items.create', ['volver' => 'estructura']), 'label' => 'Nuevo documento', 'icono' => 'fa-plus'],
        ],
    ])

    <p class="bv-crud-meta mb-3">
        Jerarquía: <strong>Comunidad → Subcomunidad → Colección → Documento</strong>.
        Usa <i class="fa fa-plus"></i> en una colección para catalogar un documento directamente en ella.
    </p>

    @if ($comunidades->isEmpty())
        <div class="bv-form-card text-center text-muted">
            Aún no hay comunidades. <a href="{{ route('admin.repositorio.estructura.comunidades.create') }}">Crea la primera</a> para empezar a organizar el repositorio.
        </div>
    @endif

    @php $paleta = ['#5a1ac2', '#c78e41', '#1f8a70', '#3f6ad8', '#dc3545', '#16a2b8', '#d35400', '#6c757d']; @endphp

    <div class="cg-grid">
        @foreach ($comunidades as $comunidad)
            @php $t = $totales($comunidad); @endphp
            <article class="cg-card" style="--cg: {{ $paleta[$loop->index % count($paleta)] }};">
                <header class="cg-card__cab">
                    <span class="cg-card__icono"><i class="fa fa-university"></i></span>
                    <div class="cg-card__titulos">
                        <h2 class="cg-card__nombre">{{ $comunidad->nombre }}</h2>
                        @if ($comunidad->descripcion)
                            <p class="cg-card__desc" title="{{ $comunidad->descripcion }}">{{ $comunidad->descripcion }}</p>
                        @endif
                    </div>
                    <div class="dropdown">
                        <button class="cg-card__menu" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="Más opciones">
                            <i class="fa fa-ellipsis-v"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-right">
                            <a class="dropdown-item" href="{{ route('admin.repositorio.estructura.comunidades.edit', $comunidad) }}"><i class="fa fa-edit fa-fw"></i> Editar comunidad</a>
                            <a class="dropdown-item" href="{{ route('admin.repositorio.estructura.comunidades.create', ['parent_id' => $comunidad->id]) }}"><i class="fa fa-folder-o fa-fw"></i> Nueva subcomunidad</a>
                            <a class="dropdown-item" href="{{ route('repositorio.index', ['comunidad' => $comunidad->slug]) }}" target="_blank"><i class="fa fa-external-link fa-fw"></i> Ver en la web</a>
                            <div class="dropdown-divider"></div>
                            <button type="button" class="dropdown-item text-danger btn-delete"
                                data-url="{{ route('admin.repositorio.estructura.comunidades.destroy', $comunidad) }}"
                                data-nombre="{{ $comunidad->nombre }}" data-titulo="Eliminar comunidad"
                                data-total="{{ $comunidad->colecciones->count() + $comunidad->subcomunidades->count() }}"
                                data-entidad="colección(es) o subcomunidad(es)">
                                <i class="fa fa-trash fa-fw"></i> Eliminar comunidad
                            </button>
                        </div>
                    </div>
                </header>

                <div class="cg-card__cifras">
                    <span><strong>{{ $t['documentos'] }}</strong> {{ $t['documentos'] === 1 ? 'documento' : 'documentos' }}</span>
                    <span class="cg-chip cg-chip--gris"><i class="fa fa-folder-open"></i> {{ $t['colecciones'] }} {{ $t['colecciones'] === 1 ? 'colección' : 'colecciones' }}</span>
                    @if ($t['borradores'])
                        <span class="cg-chip cg-chip--gris"><i class="fa fa-eye-slash"></i> {{ $t['borradores'] }} sin publicar</span>
                    @endif
                </div>

                <div style="flex: 1;">
                    @include('admin.repositorio.estructura._comunidad', ['comunidad' => $comunidad, 'nivel' => 0])
                </div>

                <footer class="cg-card__pie">
                    <a href="{{ route('admin.repositorio.estructura.colecciones.create', ['comunidad_id' => $comunidad->id]) }}" class="btn btn-sm cg-btn-crear">
                        <i class="fa fa-plus"></i> Nueva colección
                    </a>
                    <a href="{{ route('admin.repositorio.estructura.comunidades.create', ['parent_id' => $comunidad->id]) }}" class="btn btn-sm btn-outline-secondary">
                        <i class="fa fa-folder-o"></i> Subcomunidad
                    </a>
                </footer>
            </article>
        @endforeach
    </div>

    <form id="deleteForm" method="POST" class="d-none">
        @csrf
        @method('DELETE')
    </form>

    @include('partials.cg-scripts', ['entidad' => 'documento(s)', 'titulo' => 'Eliminar'])
@endsection
