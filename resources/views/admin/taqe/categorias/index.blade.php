@extends('layouts.app')
@section('titulo', "Taq'e — Categorías")
@section('contenido')
    @include('partials.taqe-crud-header', [
        'active' => 'categorias',
        'stats' => [
            ['valor' => $categorias->count(), 'singular' => 'categoría', 'plural' => 'categorías', 'icono' => 'fa-folder'],
            ['valor' => $categorias->sum('trabajos_count'), 'singular' => 'actividad', 'plural' => 'actividades', 'icono' => 'fa-calendar-check-o'],
        ],
        'botones' => [
            ['route' => route('admin.taqe.categorias.create'), 'label' => 'Nueva categoría', 'icono' => 'fa-folder', 'clase' => 'btn-outline-secondary'],
            ['route' => route('admin.taqe.trabajos.create', ['volver' => 'categorias']), 'label' => 'Nueva actividad', 'icono' => 'fa-plus'],
        ],
    ])

    <p class="bv-crud-meta mb-3">Elige una categoría para registrar una actividad directamente en ella, o revisa sus actividades recientes.</p>

    @if (session('success'))
        <div class="alert alert-success bv-alert alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger bv-alert alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
        </div>
    @endif

    @if ($categorias->isEmpty())
        <div class="bv-form-card text-center text-muted">
            Aún no hay categorías. <a href="{{ route('admin.taqe.categorias.create') }}">Crea la primera</a> para empezar a registrar actividades.
        </div>
    @endif

    <div class="cg-grid">
        @foreach ($categorias as $cat)
            @php
                $color = $cat->color ?: '#c78e41';
                // El panel usa Font Awesome 4.7: equivalentes para íconos de versiones más nuevas
                $icono = ['fa-book-reader' => 'fa-graduation-cap', 'fa-book-open' => 'fa-book', 'fa-hand-holding' => 'fa-handshake-o'][$cat->icono] ?? ($cat->icono ?: 'fa-folder');
            @endphp
            <article class="cg-card {{ $cat->activo ? '' : 'is-inactiva' }}" style="--cg: {{ $color }};">
                <header class="cg-card__cab">
                    <span class="cg-card__icono"><i class="fa {{ $icono }}"></i></span>
                    <div class="cg-card__titulos">
                        <h2 class="cg-card__nombre">{{ $cat->nombre }}</h2>
                        @if ($cat->descripcion)
                            <p class="cg-card__desc" title="{{ $cat->descripcion }}">{{ $cat->descripcion }}</p>
                        @endif
                    </div>
                    <div class="dropdown">
                        <button class="cg-card__menu" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="Más opciones">
                            <i class="fa fa-ellipsis-v"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-right">
                            <a class="dropdown-item" href="{{ route('admin.taqe.categorias.edit', $cat->id) }}"><i class="fa fa-edit fa-fw"></i> Editar categoría</a>
                            <a class="dropdown-item" href="{{ route('taqe.index', ['categoria_id' => $cat->id]) }}" target="_blank"><i class="fa fa-external-link fa-fw"></i> Ver en la web</a>
                            <div class="dropdown-divider"></div>
                            <button type="button" class="dropdown-item text-danger btn-delete"
                                data-url="{{ route('admin.taqe.categorias.destroy', $cat->id) }}"
                                data-nombre="{{ $cat->nombre }}" data-total="{{ $cat->trabajos_count }}">
                                <i class="fa fa-trash fa-fw"></i> Eliminar categoría
                            </button>
                        </div>
                    </div>
                </header>

                <div class="cg-card__cifras">
                    <span><strong>{{ $cat->trabajos_count }}</strong> {{ $cat->trabajos_count === 1 ? 'actividad' : 'actividades' }}</span>
                    @if ($cat->borradores_count)
                        <span class="cg-chip cg-chip--gris"><i class="fa fa-eye-slash"></i> {{ $cat->borradores_count }} borrador{{ $cat->borradores_count === 1 ? '' : 'es' }}</span>
                    @endif
                    @unless ($cat->activo)
                        <span class="cg-chip cg-chip--gris">Inactiva</span>
                    @endunless
                </div>

                <ul class="cg-card__lista">
                    @forelse ($cat->trabajos->take(3) as $act)
                        <li>
                            <a href="{{ route('admin.taqe.trabajos.edit', ['trabajo' => $act->id, 'volver' => 'categorias']) }}" title="Editar «{{ $act->titulo }}»">
                                <span class="cg-card__item">{{ $act->titulo }}</span>
                                <span class="cg-card__fecha">
                                    @if ($act->estado === 'borrador')<i class="fa fa-eye-slash" title="Borrador"></i>@endif
                                    {{ $act->fecha?->format('d/m/Y') ?? 'Sin fecha' }}
                                </span>
                            </a>
                        </li>
                    @empty
                        <li class="cg-card__vacia">Todavía no hay actividades en esta categoría.</li>
                    @endforelse
                </ul>

                <footer class="cg-card__pie">
                    <a href="{{ route('admin.taqe.trabajos.create', ['categoria_id' => $cat->id, 'volver' => 'categorias']) }}" class="btn btn-sm cg-btn-crear">
                        <i class="fa fa-plus"></i> Nueva actividad
                    </a>
                    <a href="{{ route('admin.taqe.trabajos.index', ['categoria_id' => $cat->id]) }}" class="btn btn-sm btn-outline-secondary {{ $cat->trabajos_count ? '' : 'disabled' }}">
                        <i class="fa fa-list"></i> Ver {{ $cat->trabajos_count > 3 ? 'las '.$cat->trabajos_count : 'actividades' }}
                    </a>
                </footer>
            </article>
        @endforeach
    </div>

    <form id="deleteForm" method="POST" class="d-none">
        @csrf
        @method('DELETE')
    </form>


    <script>
        document.addEventListener('click', function (e) {
            const btn = e.target.closest('.btn-delete');
            if (!btn) return;
            const total = parseInt(btn.dataset.total, 10) || 0;
            if (total > 0) {
                window.bvConfirm({
                    titulo: 'No se puede eliminar',
                    mensaje: 'La categoría "' + btn.dataset.nombre + '" tiene ' + total + ' actividad(es). Muévelas a otra categoría antes de eliminarla.',
                    tono: 'warning', icono: 'fa-info-circle', textoConfirmar: 'Entendido',
                });
                return;
            }
            window.bvConfirm({
                titulo: "Eliminar categoría Taq'e",
                mensaje: '¿Eliminar la categoría "' + btn.dataset.nombre + '"?',
                tono: 'danger', icono: 'fa-trash', textoConfirmar: 'Eliminar',
            }).then(function (ok) {
                if (!ok) return;
                var f = document.getElementById('deleteForm');
                f.action = btn.dataset.url;
                f.submit();
            });
        });
    </script>
@endsection
