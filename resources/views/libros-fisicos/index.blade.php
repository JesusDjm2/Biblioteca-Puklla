@extends('layouts.app')
@section('titulo', 'Libros físicos')
@section('contenido')
    @include('partials.biblioteca-fisica-crud-header', [
        'active' => 'libros',
        'stats' => [['valor' => $libros->total(), 'singular' => 'libro', 'plural' => 'libros', 'icono' => 'fa-book']],
        'botones' => [['route' => route('biblioteca-fisica.libros.create'), 'label' => 'Registrar libro']],
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

    <form method="get" action="{{ route('biblioteca-fisica.libros.index') }}" class="bv-crud-toolbar">
        <div class="form-row align-items-end">
            <div class="form-group col-md-4 mb-2 mb-md-0">
                <label for="fis-q" class="small text-muted mb-1 d-block">
                    <i class="fa fa-search mr-1"></i>Buscar
                </label>
                <input type="text" id="fis-q" name="q" class="form-control form-control-sm bv-toolbar-control"
                    value="{{ request('q') }}" placeholder="Nombre, autor o código…" autocomplete="off">
            </div>
            <div class="form-group col-md-3 mb-2 mb-md-0">
                <label for="fis-cat" class="small text-muted mb-1 d-block">
                    <i class="fa fa-tag mr-1"></i>Categoría física
                </label>
                <select id="fis-cat" name="categoria_fisica_id" class="custom-select custom-select-sm bv-toolbar-control">
                    <option value="">Todas las categorías</option>
                    @foreach ($categoriasFiltro as $cat)
                        <option value="{{ $cat->id }}"
                            @selected((string) request('categoria_fisica_id') === (string) $cat->id)>
                            {{ $cat->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="form-group col-md-2 mb-2 mb-md-0">
                <label for="fis-ubi" class="small text-muted mb-1 d-block">
                    <i class="fa fa-map-marker-alt mr-1"></i>Ubicación
                </label>
                <select id="fis-ubi" name="ubicacion" class="custom-select custom-select-sm bv-toolbar-control">
                    <option value="">Todas</option>
                    @foreach (\App\Models\LibroFisico::UBICACIONES as $codigoUbicacion => $nombreUbicacion)
                        <option value="{{ $codigoUbicacion }}"
                            @selected(request('ubicacion') === $codigoUbicacion)>
                            {{ $nombreUbicacion }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="form-group col-md-3 mb-0">
                <label class="small mb-1 d-block invisible" aria-hidden="true">Acciones</label>
                <div class="btn-group btn-group-sm bv-toolbar-btn-group w-100">
                    <button type="submit" class="btn btn-info">
                        <i class="fa fa-filter mr-1"></i>Filtrar
                    </button>
                    <a href="{{ route('biblioteca-fisica.libros.index') }}" class="btn btn-outline-secondary">
                        <i class="fa fa-times mr-1"></i>Limpiar
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
                    <th style="width:26%;">Libro</th>
                    <th style="width:12%;" title="Año de publicación, número de páginas y código de estantería">Detalles</th>
                    <th>Categorías</th>
                    <th style="width:9rem;">Ubicación</th>
                    <th style="width:6.5rem;" class="text-center">Disponibilidad</th>
                    <th style="width:6.5rem;" class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody id="librosTable">
                @forelse ($libros as $libro)
                    <tr class="libro-fis-item bv-table-row">
                        <td class="text-muted small align-middle">
                            {{ $libros->firstItem() + $loop->index }}
                        </td>
                        <td class="align-middle">
                            <div class="bv-cell-title">{{ $libro->nombre }}</div>
                            <div class="bv-cell-sub">
                                {{ $libro->autor }}
                                @if ($libro->editorial)
                                    <span class="bv-cell-sub-sep">·</span>{{ $libro->editorial }}
                                @endif
                            </div>
                        </td>
                        <td class="align-middle small">
                            <div class="text-muted bv-cell-ficha-linea">
                                <span title="Año de publicación">
                                    <i class="fa fa-calendar text-muted"></i> {{ $libro->anio_publicacion }}
                                </span>
                                @if ($libro->numero_paginas)
                                    <span title="Número de páginas">
                                        <i class="fa fa-file-text-o text-muted"></i> {{ number_format($libro->numero_paginas) }} pág.
                                    </span>
                                @endif
                            </div>
                            <div class="bv-cell-ficha-linea" title="Código de estantería">
                                <i class="fa fa-barcode text-muted"></i> <code class="small">{{ $libro->codigo }}</code>
                            </div>
                        </td>
                        <td class="align-middle">
                            @if ($libro->categoriaFisica)
                                <span class="badge badge-info mr-1 mb-1" style="font-weight:500;">
                                    {{ $libro->categoriaFisica->nombre }}
                                </span>
                            @endif
                            @if ($libro->tipo_material)
                                <span class="badge badge-secondary mr-1 mb-1" style="font-weight:500;">
                                    {{ \App\Models\CategoriaFisica::nombreParaTipoMaterial($libro->tipo_material) }}
                                </span>
                            @endif
                        </td>
                        <td class="align-middle">
                            @if ($libro->ubicacion)
                                <span class="badge badge-light border">
                                    <i class="fa fa-map-marker-alt mr-1"></i>{{ \App\Models\LibroFisico::nombreParaUbicacion($libro->ubicacion) }}
                                </span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td class="text-center align-middle">
                            <span class="badge {{ $libro->ejemplares_disponibles > 0 ? 'badge-success' : 'badge-danger' }}"
                                @if ($libro->cantidad_ejemplares > 1)
                                    title="Ejemplares: {{ implode(', ', $libro->etiquetas_ejemplares_lista) }}"
                                @endif>
                                {{ $libro->ejemplares_disponibles }} / {{ $libro->cantidad_ejemplares }}
                            </span>
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
                                @if (request('q') || request('categoria_fisica_id') || request('ubicacion'))
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

    <form id="deleteFormLibro" method="POST" class="d-none">
        @csrf
        @method('DELETE')
    </form>

    <script>
        document.addEventListener('click', function (e) {
            const btn = e.target.closest('.btn-delete-libro');
            if (!btn) return;
            window.bvConfirm({
                titulo: 'Eliminar libro',
                mensaje: '¿Eliminar el libro "' + btn.dataset.nombre + '"? Esta acción no se puede deshacer.',
                tono: 'danger',
                icono: 'fa-trash',
                textoConfirmar: 'Eliminar',
            }).then(function (ok) {
                if (!ok) return;
                var f = document.getElementById('deleteFormLibro');
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
            gsap.from(rows, { opacity: 0, y: 12, duration: 0.32, stagger: 0.04, ease: 'power2.out' });
        }
        gsap.from('.bv-crud-toolbar', { opacity: 0, y: -8, duration: 0.3, delay: 0.05, ease: 'power2.out' });
        gsap.from('.bv-crud-nav-actions', { opacity: 0, x: -10, duration: 0.3, delay: 0.1, ease: 'power2.out' });
    })();
</script>
@endpush
