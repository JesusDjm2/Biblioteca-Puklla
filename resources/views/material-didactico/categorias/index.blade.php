@extends('layouts.app')
@section('titulo', 'Categorías de material didáctico')
@section('contenido')
    @include('partials.biblioteca-fisica-crud-header', [
        'active' => 'material-categorias',
        'stats' => [['valor' => $categorias->total(), 'singular' => 'categoría', 'plural' => 'categorías', 'icono' => 'fa-folder']],
        'botones' => [
            [
                'route' => route('biblioteca-fisica.material-categorias.create'),
                'label' => 'Nueva categoría',
                'icono' => 'fa-plus',
                'clase' => 'btn-info',
            ],
            [
                'route' => route('biblioteca-fisica.materiales.index'),
                'label' => 'Ver materiales',
                'icono' => 'fa-cubes',
                'clase' => 'btn-outline-secondary',
            ],
        ],
    ])

    @include('material-didactico.partials.subnav', ['subactive' => 'categorias'])
    @include('material-didactico.partials.alertas')

    <form method="get" action="{{ route('biblioteca-fisica.material-categorias.index') }}" class="bv-crud-toolbar">
        <div class="form-row align-items-end">
            <div class="form-group col-md-8 mb-2 mb-md-0">
                <label for="mc-q" class="small text-muted mb-1 d-block">
                    <i class="fa fa-search mr-1"></i>Buscar
                </label>
                <input type="text" id="mc-q" name="q" class="form-control form-control-sm bv-toolbar-control"
                    value="{{ request('q') }}" placeholder="Nombre, código o texto en la descripción…"
                    autocomplete="off">
            </div>
            <div class="form-group col-md-4 mb-0">
                <label class="small mb-1 d-block invisible" aria-hidden="true">Acciones</label>
                <div class="btn-group btn-group-sm bv-toolbar-btn-group w-100">
                    <button type="submit" class="btn btn-info">
                        <i class="fa fa-filter mr-1"></i>Filtrar
                    </button>
                    <a href="{{ route('biblioteca-fisica.material-categorias.index') }}"
                        class="btn btn-outline-secondary">Limpiar</a>
                </div>
            </div>
        </div>
    </form>

    @if ($categorias->total() > 0)
        <p class="bv-crud-meta mb-2">
            Mostrando {{ $categorias->firstItem() }}–{{ $categorias->lastItem() }} de {{ $categorias->total() }} resultado(s)
        </p>
    @endif

    <div class="bv-crud-table-wrap table-responsive">
        <table class="table table-hover table-sm">
            <thead class="thead-light">
                <tr>
                    <th style="width:3rem;">#</th>
                    <th>Nombre</th>
                    <th style="width:7rem;">Código</th>
                    <th>Descripción</th>
                    <th style="width:7rem;" class="text-center">Materiales</th>
                    <th style="width:7rem;" class="text-center">Unidades</th>
                    <th style="width:10rem;" class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($categorias as $categoria)
                    <tr class="bv-table-row">
                        <td class="text-muted small align-middle">
                            {{ $categorias->firstItem() + $loop->index }}
                        </td>
                        <td class="font-weight-bold align-middle">{{ $categoria->nombre }}</td>
                        <td class="align-middle">
                            @if ($categoria->codigo)
                                <code class="small">{{ $categoria->codigo }}</code>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td class="small text-muted align-middle">
                            {{ \Illuminate\Support\Str::limit($categoria->descripcion ?? '—', 100) }}
                        </td>
                        <td class="text-center align-middle">
                            <span class="badge badge-info badge-pill">{{ $categoria->materiales_count }}</span>
                        </td>
                        <td class="text-center align-middle">
                            <span class="badge badge-secondary badge-pill">{{ (int) $categoria->materiales_sum_cantidad }}</span>
                        </td>
                        <td class="bv-crud-actions text-center text-nowrap align-middle">
                            <a href="{{ route('biblioteca-fisica.materiales.index', ['categoria_id' => $categoria->id]) }}"
                                class="btn btn-sm btn-outline-info" title="Ver materiales de esta categoría">
                                <i class="fa fa-cubes"></i>
                            </a>
                            <a href="{{ route('biblioteca-fisica.material-categorias.edit', $categoria) }}"
                                class="btn btn-sm btn-outline-warning" title="Editar">
                                <i class="fa fa-edit"></i>
                            </a>
                            <button type="button" class="btn btn-sm btn-outline-danger btn-delete-matcat"
                                data-url="{{ route('biblioteca-fisica.material-categorias.destroy', $categoria) }}"
                                data-nombre="{{ $categoria->nombre }}" title="Eliminar">
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="p-0 border-0">
                            <div class="bv-empty-state">
                                <i class="fa fa-folder-open bv-empty-icon"></i>
                                <p>No hay categorías de material didáctico aún.</p>
                                <a href="{{ route('biblioteca-fisica.material-categorias.create') }}"
                                    class="btn btn-sm btn-info">
                                    <i class="fa fa-plus mr-1"></i>Crear la primera categoría
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="bv-crud-pagination">{{ $categorias->links() }}</div>

    <form id="deleteFormMatCat" method="POST" class="d-none">
        @csrf
        @method('DELETE')
    </form>

    <script>
        document.addEventListener('click', function (e) {
            const btn = e.target.closest('.btn-delete-matcat');
            if (!btn) return;
            window.bvConfirm({
                titulo: 'Eliminar categoría',
                mensaje: '¿Eliminar la categoría "' + btn.dataset.nombre + '"? Esta acción no se puede deshacer.',
                tono: 'danger',
                icono: 'fa-trash',
                textoConfirmar: 'Eliminar',
            }).then(function (ok) {
                if (!ok) return;
                var f = document.getElementById('deleteFormMatCat');
                f.action = btn.dataset.url;
                f.submit();
            });
        });
    </script>
@endsection

@push('scripts')
<script>
    (function () {
        window.bvPageAnimated = true;
        var rows = document.querySelectorAll('.bv-table-row');
        if (rows.length) {
            gsap.from(rows, { opacity: 0, y: 12, duration: 0.32, stagger: 0.05, ease: 'power2.out' });
        }
        gsap.from('.bv-crud-toolbar', { opacity: 0, y: -8, duration: 0.3, delay: 0.05, ease: 'power2.out' });
        gsap.from('.bv-crud-nav-actions', { opacity: 0, x: -10, duration: 0.3, delay: 0.1, ease: 'power2.out' });
    })();
</script>
@endpush
