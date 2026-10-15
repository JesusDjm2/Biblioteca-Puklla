@extends('layouts.app')
@section('titulo', "Taq'e — Categorías")
@section('contenido')
    @include('partials.taqe-crud-header', [
        'active' => 'categorias',
        'stats' => [['valor' => $categorias->count(), 'singular' => 'categoría', 'plural' => 'categorías', 'icono' => 'fa-folder']],
        'botones' => [
            ['route' => route('admin.taqe.categorias.create'), 'label' => 'Nueva categoría', 'icono' => 'fa-plus'],
        ],
    ])

    <p class="bv-crud-meta mb-3">Clasifica las áreas temáticas del repositorio Taq'e (EIB, Lengua Originaria, Expresión Artística, Práctica Preprofesional, etc.).</p>

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

    <div class="bv-crud-table-wrap table-responsive">
        <table class="table table-hover table-sm">
            <thead class="thead-light">
                <tr>
                    <th style="width:3.5rem;">Orden</th>
                    <th style="width:4rem;">Ícono</th>
                    <th>Categoría</th>
                    <th>Slug</th>
                    <th style="width:7rem;">Trabajos</th>
                    <th style="width:6rem;">Estado</th>
                    <th style="width:7.5rem;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($categorias as $cat)
                    <tr>
                        <td class="font-weight-bold text-muted">{{ $cat->orden }}</td>
                        <td>
                            <span style="display:inline-flex; align-items:center; justify-content:center; width:34px; height:34px; border-radius:8px; background:{{ $cat->color ?? '#c78e41' }}22; color:{{ $cat->color ?? '#c78e41' }}; font-size:1.1rem;">
                                <i class="fa {{ $cat->icono ?? 'fa-folder' }}"></i>
                            </span>
                        </td>
                        <td>
                            <div class="font-weight-bold" style="color:#2a2520;">{{ $cat->nombre }}</div>
                            @if ($cat->descripcion)
                                <div class="small text-muted text-truncate" style="max-width:320px;">{{ $cat->descripcion }}</div>
                            @endif
                        </td>
                        <td class="small text-muted"><code>{{ $cat->slug }}</code></td>
                        <td>
                            <span class="badge badge-pill badge-light" style="border:1px solid #ddd; font-size:0.8rem;">
                                <i class="fa fa-graduation-cap"></i> {{ $cat->trabajos_count }}
                            </span>
                        </td>
                        <td>
                            @if ($cat->activo)
                                <span class="badge badge-success">Activo</span>
                            @else
                                <span class="badge badge-secondary">Inactivo</span>
                            @endif
                        </td>
                        <td class="bv-crud-actions text-nowrap">
                            <div class="btn-group btn-group-sm" role="group">
                                <a href="{{ route('admin.taqe.categorias.edit', $cat->id) }}" class="btn btn-warning" title="Editar">
                                    <i class="fa fa-edit"></i>
                                </a>
                                <button type="button" class="btn btn-danger btn-delete"
                                    data-url="{{ route('admin.taqe.categorias.destroy', $cat->id) }}"
                                    data-nombre="{{ $cat->nombre }}" title="Eliminar">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="bv-crud-empty">No hay categorías registradas aún.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <form id="deleteForm" method="POST" class="d-none">
        @csrf
        @method('DELETE')
    </form>

    <script>
        document.addEventListener('click', function(e) {
            const btn = e.target.closest('.btn-delete');
            if (!btn) return;
            window.bvConfirm({
                titulo: 'Eliminar categoría Taq\'e',
                mensaje: '¿Eliminar la categoría "' + btn.dataset.nombre + '"? Esta acción eliminará también los trabajos vinculados.',
                tono: 'danger',
                icono: 'fa-trash',
                textoConfirmar: 'Eliminar',
            }).then(function (ok) {
                if (!ok) return;
                var f = document.getElementById('deleteForm');
                f.action = btn.dataset.url;
                f.submit();
            });
        });
    </script>
@endsection
