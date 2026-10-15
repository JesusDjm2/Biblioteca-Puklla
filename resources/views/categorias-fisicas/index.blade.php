@extends('layouts.app')
@section('titulo', 'Categorías físicas')
@section('contenido')
    @include('partials.biblioteca-fisica-crud-header', [
        'active' => 'categorias',
        'stats' => [['valor' => $categorias->total(), 'singular' => 'categoría', 'plural' => 'categorías', 'icono' => 'fa-folder']],
        'botones' => [['route' => route('biblioteca-fisica.categorias.create'), 'label' => 'Nueva categoría']],
    ])

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show bv-alert" role="alert">
            <i class="fa fa-check-circle mr-2"></i>{{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Cerrar">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show bv-alert" role="alert">
            <i class="fa fa-exclamation-circle mr-2"></i>{{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Cerrar">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif
    
    <form method="get" action="{{ route('biblioteca-fisica.categorias.index') }}" class="bv-crud-toolbar">
        <div class="form-row align-items-end">
            <div class="form-group col-md-8 mb-2 mb-md-0">
                <label for="cf-q" class="small text-muted mb-1 d-block">
                    <i class="fa fa-search mr-1"></i>Buscar
                </label>
                <input type="text" id="cf-q" name="q" class="form-control form-control-sm bv-toolbar-control"
                    value="{{ request('q') }}" placeholder="Nombre o texto en la descripción…"
                    autocomplete="off">
            </div>
            <div class="form-group col-md-4 mb-0">
                <label class="small mb-1 d-block invisible" aria-hidden="true">Acciones</label>
                <div class="btn-group btn-group-sm bv-toolbar-btn-group w-100">
                    <button type="submit" class="btn btn-sm btn-info">
                        <i class="fa fa-filter mr-1"></i>Filtrar
                    </button>
                    <a href="{{ route('biblioteca-fisica.categorias.index') }}"
                        class="btn btn-sm btn-outline-secondary">Limpiar</a>
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
                    <th style="width:6rem;" class="text-center">Libros</th>
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
                            <span class="badge badge-info badge-pill">
                                {{ $categoria->libros_fisicos_count }}
                            </span>
                        </td>
                        <td class="bv-crud-actions text-center text-nowrap align-middle">
                            @if ($categoria->libros_fisicos_count > 0)
                                <a href="{{ route('biblioteca-fisica.libros.index', ['categoria_fisica_id' => $categoria->id]) }}"
                                    class="btn btn-sm btn-outline-info" title="Ver libros de esta categoría">
                                    <i class="fa fa-book"></i>
                                </a>
                            @else
                                <span class="btn btn-sm btn-outline-secondary disabled" style="cursor:default;" title="Sin libros">
                                    <i class="fa fa-book"></i>
                                </span>
                            @endif
                            <a href="{{ route('biblioteca-fisica.categorias.edit', $categoria) }}"
                                class="btn btn-sm btn-outline-warning" title="Editar">
                                <i class="fa fa-edit"></i>
                            </a>
                            <button type="button" class="btn btn-sm btn-outline-danger btn-delete-cat"
                                data-url="{{ route('biblioteca-fisica.categorias.destroy', $categoria) }}"
                                data-nombre="{{ $categoria->nombre }}" title="Eliminar">
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="p-0 border-0">
                            <div class="bv-empty-state">
                                <i class="fa fa-folder-open bv-empty-icon"></i>
                                <p>No hay categorías físicas aún.</p>
                                <a href="{{ route('biblioteca-fisica.categorias.create') }}"
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

    <form id="deleteFormCat" method="POST" class="d-none">
        @csrf
        @method('DELETE')
    </form>

    <script>
        document.addEventListener('click', function (e) {
            const btn = e.target.closest('.btn-delete-cat');
            if (!btn) return;
            window.bvConfirm({
                titulo: 'Eliminar categoría',
                mensaje: '¿Eliminar la categoría "' + btn.dataset.nombre + '"? Esta acción no se puede deshacer.',
                tono: 'danger',
                icono: 'fa-trash',
                textoConfirmar: 'Eliminar',
            }).then(function (ok) {
                if (!ok) return;
                var f = document.getElementById('deleteFormCat');
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
