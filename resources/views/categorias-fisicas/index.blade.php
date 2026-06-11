@extends('layouts.app')
@section('titulo', 'Categorías físicas')
@section('contenido')
    @include('partials.biblioteca-fisica-crud-header', ['active' => 'categorias'])

    <div class="d-flex flex-wrap justify-content-between align-items-start mb-3">
        <div>
            <h1 class="bv-crud-page-title mb-1">Categorías físicas</h1>
            <div class="bv-stat-chips">
                <span class="bv-stat-chip">
                    <i class="fa fa-folder"></i>
                    {{ $categorias->total() }} {{ $categorias->total() === 1 ? 'categoría' : 'categorías' }}
                </span>
            </div>
        </div>
        <a href="{{ route('biblioteca-fisica.categorias.create') }}" class="btn btn-info btn-sm mt-2">
            <i class="fa fa-plus mr-1"></i>Nueva categoría
        </a>
    </div>

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
                <input type="text" id="cf-q" name="q" class="form-control form-control-sm"
                    value="{{ request('q') }}" placeholder="Nombre o texto en la descripción…"
                    autocomplete="off">
            </div>
            <div class="form-group col-md-4 mb-0">
                <div class="btn-group btn-group-sm w-100">
                    <button type="submit" class="btn btn-info">
                        <i class="fa fa-filter mr-1"></i>Filtrar
                    </button>
                    <a href="{{ route('biblioteca-fisica.categorias.index') }}"
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
                    <th>Descripción</th>
                    <th style="width:6rem;" class="text-center">Libros</th>
                    <th style="width:8rem;" class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($categorias as $categoria)
                    <tr class="bv-table-row">
                        <td class="text-muted small align-middle">
                            {{ $categorias->firstItem() + $loop->index }}
                        </td>
                        <td class="font-weight-bold align-middle">{{ $categoria->nombre }}</td>
                        <td class="small text-muted align-middle">
                            {{ \Illuminate\Support\Str::limit($categoria->descripcion ?? '—', 100) }}
                        </td>
                        <td class="text-center align-middle">
                            <span class="badge badge-info badge-pill">
                                {{ $categoria->libros_fisicos_count }}
                            </span>
                        </td>
                        <td class="bv-crud-actions text-center text-nowrap align-middle">
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
                        <td colspan="5" class="p-0 border-0">
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

    {{-- Modal de confirmación --}}
    <div class="modal fade" id="deleteModalCat" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header border-0 pb-1">
                    <h5 class="modal-title">
                        <i class="fa fa-exclamation-triangle text-danger mr-2"></i>Confirmar eliminación
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center py-3">
                    <p class="mb-1">¿Eliminar la categoría <strong id="catNombre" class="text-info"></strong>?</p>
                    <small class="text-muted">Esta acción no se puede deshacer.</small>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Cancelar</button>
                    <form id="deleteFormCat" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger">
                            <i class="fa fa-trash mr-1"></i>Eliminar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('click', function (e) {
            const btn = e.target.closest('.btn-delete-cat');
            if (!btn) return;
            document.getElementById('deleteFormCat').action = btn.getAttribute('data-url');
            document.getElementById('catNombre').textContent = btn.getAttribute('data-nombre');
            $('#deleteModalCat').modal('show');
        });
    </script>
@endsection

@push('scripts')
<script>
    (function () {
        var rows = document.querySelectorAll('.bv-table-row');
        if (rows.length) {
            gsap.from(rows, { opacity: 0, y: 12, duration: 0.32, stagger: 0.05, ease: 'power2.out' });
        }
        gsap.from('.bv-crud-toolbar', { opacity: 0, y: -8, duration: 0.3, delay: 0.05, ease: 'power2.out' });
        gsap.from('.bv-stat-chips',   { opacity: 0, x: -10, duration: 0.3, delay: 0.1, ease: 'power2.out' });
    })();
</script>
@endpush
