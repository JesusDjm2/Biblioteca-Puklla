@extends('layouts.app')
@section('titulo', 'Libros físicos')
@section('contenido')
    @include('partials.biblioteca-fisica-crud-header', ['active' => 'libros'])

    <div class="d-flex flex-wrap justify-content-between align-items-start mb-3">
        <div>
            <h1 class="bv-crud-page-title mb-1">Libros físicos</h1>
            <div class="bv-stat-chips">
                <span class="bv-stat-chip">
                    <i class="fa fa-book"></i>
                    {{ $libros->total() }} {{ $libros->total() === 1 ? 'libro' : 'libros' }}
                </span>
            </div>
        </div>
        <a href="{{ route('biblioteca-fisica.libros.create') }}" class="btn btn-info btn-sm mt-2">
            <i class="fa fa-plus mr-1"></i>Registrar libro
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

    <form method="get" action="{{ route('biblioteca-fisica.libros.index') }}" class="bv-crud-toolbar">
        <div class="form-row align-items-end">
            <div class="form-group col-md-5 mb-2 mb-md-0">
                <label for="fis-q" class="small text-muted mb-1 d-block">
                    <i class="fa fa-search mr-1"></i>Buscar
                </label>
                <input type="text" id="fis-q" name="q" class="form-control form-control-sm"
                    value="{{ request('q') }}" placeholder="Nombre, autor o código…" autocomplete="off">
            </div>
            <div class="form-group col-md-4 mb-2 mb-md-0">
                <label for="fis-cat" class="small text-muted mb-1 d-block">
                    <i class="fa fa-tag mr-1"></i>Categoría física
                </label>
                <select id="fis-cat" name="categoria_fisica_id" class="custom-select custom-select-sm">
                    <option value="">Todas las categorías</option>
                    @foreach ($categoriasFiltro as $cat)
                        <option value="{{ $cat->id }}"
                            @selected((string) request('categoria_fisica_id') === (string) $cat->id)>
                            {{ $cat->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="form-group col-md-3 mb-0">
                <div class="btn-group btn-group-sm w-100">
                    <button type="submit" class="btn btn-info">
                        <i class="fa fa-filter mr-1"></i>Filtrar
                    </button>
                    <a href="{{ route('biblioteca-fisica.libros.index') }}" class="btn btn-outline-secondary">
                        Limpiar
                    </a>
                </div>
            </div>
        </div>
    </form>

    @if ($libros->total() > 0)
        <p class="bv-crud-meta mb-2">
            Mostrando {{ $libros->firstItem() }}–{{ $libros->lastItem() }} de {{ $libros->total() }} resultado(s)
        </p>
    @endif

    <div class="bv-crud-table-wrap table-responsive">
        <table class="table table-hover table-sm">
            <thead class="thead-light">
                <tr>
                    <th style="width:3rem;">#</th>
                    <th>Nombre</th>
                    <th>Autor</th>
                    <th style="width:4.5rem;">Año</th>
                    <th style="width:7rem;">Código</th>
                    <th>Categorías</th>
                    <th style="width:8rem;" class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody id="librosTable">
                @forelse ($libros as $libro)
                    <tr class="libro-fis-item bv-table-row">
                        <td class="text-muted small align-middle">
                            {{ $libros->firstItem() + $loop->index }}
                        </td>
                        <td class="font-weight-bold align-middle">{{ $libro->nombre }}</td>
                        <td class="align-middle">{{ $libro->autor }}</td>
                        <td class="text-muted align-middle">{{ $libro->anio_publicacion }}</td>
                        <td class="align-middle"><code class="small">{{ $libro->codigo }}</code></td>
                        <td class="align-middle">
                            @foreach ($libro->categoriasFisicas as $cat)
                                <span class="badge badge-info mr-1 mb-1" style="font-weight:500;">
                                    {{ $cat->nombre }}
                                </span>
                            @endforeach
                        </td>
                        <td class="bv-crud-actions text-center text-nowrap align-middle">
                            <a href="{{ route('biblioteca-fisica.libros.edit', $libro) }}"
                                class="btn btn-sm btn-outline-warning" title="Editar">
                                <i class="fa fa-edit"></i>
                            </a>
                            <button type="button" class="btn btn-sm btn-outline-danger btn-delete-libro"
                                data-url="{{ route('biblioteca-fisica.libros.destroy', $libro) }}"
                                data-nombre="{{ $libro->nombre }}" title="Eliminar">
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="p-0 border-0">
                            <div class="bv-empty-state">
                                <i class="fa fa-book bv-empty-icon"></i>
                                <p>No se encontraron libros físicos con esos criterios.</p>
                                @if (request('q') || request('categoria_fisica_id'))
                                    <a href="{{ route('biblioteca-fisica.libros.index') }}"
                                        class="btn btn-sm btn-outline-secondary">
                                        <i class="fa fa-times mr-1"></i>Limpiar filtros
                                    </a>
                                @else
                                    <a href="{{ route('biblioteca-fisica.libros.create') }}"
                                        class="btn btn-sm btn-info">
                                        <i class="fa fa-plus mr-1"></i>Registrar el primero
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="bv-crud-pagination">{{ $libros->links() }}</div>

    {{-- Modal de confirmación --}}
    <div class="modal fade" id="deleteModalLibro" tabindex="-1" role="dialog">
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
                    <p class="mb-1">¿Eliminar el libro <strong id="libroNombreFis" class="text-info"></strong>?</p>
                    <small class="text-muted">Esta acción no se puede deshacer.</small>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Cancelar</button>
                    <form id="deleteFormLibro" method="POST" class="d-inline">
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
            const btn = e.target.closest('.btn-delete-libro');
            if (!btn) return;
            document.getElementById('deleteFormLibro').action = btn.getAttribute('data-url');
            document.getElementById('libroNombreFis').textContent = btn.getAttribute('data-nombre');
            $('#deleteModalLibro').modal('show');
        });
    </script>
@endsection

@push('scripts')
<script>
    (function () {
        var rows = document.querySelectorAll('.bv-table-row');
        if (rows.length) {
            gsap.from(rows, { opacity: 0, y: 12, duration: 0.32, stagger: 0.04, ease: 'power2.out' });
        }
        gsap.from('.bv-crud-toolbar', { opacity: 0, y: -8, duration: 0.3, delay: 0.05, ease: 'power2.out' });
        gsap.from('.bv-stat-chips',   { opacity: 0, x: -10, duration: 0.3, delay: 0.1, ease: 'power2.out' });
    })();
</script>
@endpush
