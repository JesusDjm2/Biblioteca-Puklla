@extends('layouts.app')
@section('titulo', 'Material didáctico')
@section('contenido')
    @include('partials.biblioteca-fisica-crud-header', [
        'active' => 'materiales',
        'stats' => [['valor' => $materiales->total(), 'singular' => 'material', 'plural' => 'materiales', 'icono' => 'fa-cubes']],
        'botones' => [
            [
                'route' => route('biblioteca-fisica.materiales.create', array_filter(['categoria_id' => request('categoria_id')])),
                'label' => 'Nuevo material',
                'icono' => 'fa-plus',
                'clase' => 'btn-info',
            ],
            [
                'route' => route('biblioteca-fisica.material-categorias.index'),
                'label' => 'Categorías',
                'icono' => 'fa-tags',
                'clase' => 'btn-outline-secondary',
            ],
        ],
    ])

    @include('material-didactico.partials.subnav', ['subactive' => 'materiales'])
    @include('material-didactico.partials.alertas')

    <form method="get" action="{{ route('biblioteca-fisica.materiales.index') }}" class="bv-crud-toolbar">
        <div class="form-row align-items-end">
            <div class="form-group col-md-5 mb-2 mb-md-0">
                <label for="md-q" class="small text-muted mb-1 d-block">
                    <i class="fa fa-search mr-1"></i>Buscar
                </label>
                <input type="text" id="md-q" name="q" class="form-control form-control-sm bv-toolbar-control"
                    value="{{ request('q') }}" placeholder="Nombre, código o descripción…" autocomplete="off">
            </div>
            <div class="form-group col-md-3 mb-2 mb-md-0">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <label for="md-cat" class="small text-muted mb-0">
                        <i class="fa fa-folder mr-1"></i>Categoría
                    </label>
                    <a href="{{ route('biblioteca-fisica.material-categorias.index') }}" class="small text-info text-decoration-none" title="Administrar categorías">
                        <i class="fa fa-cog"></i>
                    </a>
                </div>
                <select id="md-cat" name="categoria_id" class="form-control form-control-sm bv-toolbar-control">
                    <option value="">Todas</option>
                    @foreach ($categorias as $cat)
                        <option value="{{ $cat->id }}" @selected((string) request('categoria_id') === (string) $cat->id)>
                            {{ $cat->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="form-group col-md-4 mb-0">
                <label class="small mb-1 d-block invisible" aria-hidden="true">Acciones</label>
                <div class="btn-group btn-group-sm bv-toolbar-btn-group w-100">
                    <button type="submit" class="btn btn-info">
                        <i class="fa fa-filter mr-1"></i>Filtrar
                    </button>
                    <a href="{{ route('biblioteca-fisica.materiales.index') }}"
                        class="btn btn-outline-secondary">Limpiar</a>
                </div>
            </div>
        </div>
    </form>

    @if ($materiales->total() > 0)
        <p class="bv-crud-meta mb-2">
            Mostrando {{ $materiales->firstItem() }}–{{ $materiales->lastItem() }} de {{ $materiales->total() }} resultado(s)
        </p>
    @endif

    <div class="bv-crud-table-wrap table-responsive">
        <table class="table table-hover table-sm">
            <thead class="thead-light">
                <tr>
                    <th style="width:3rem;">#</th>
                    <th style="width:8rem;">Código</th>
                    <th>Nombre</th>
                    <th>Categoría</th>
                    <th>Descripción</th>
                    <th style="width:6rem;" class="text-center">Cantidad</th>
                    <th style="width:6rem;" class="text-center">Prestadas</th>
                    <th style="width:6rem;" class="text-center">Disponibles</th>
                    <th style="width:10rem;" class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($materiales as $material)
                    <tr class="bv-table-row">
                        <td class="text-muted small align-middle">
                            {{ $materiales->firstItem() + $loop->index }}
                        </td>
                        <td class="align-middle"><code class="small">{{ $material->codigo }}</code></td>
                        <td class="font-weight-bold align-middle">{{ $material->nombre }}</td>
                        <td class="small align-middle">{{ $material->categoria?->nombre ?? '—' }}</td>
                        <td class="small text-muted align-middle">
                            {{ \Illuminate\Support\Str::limit($material->descripcion ?? '—', 90) }}
                        </td>
                        <td class="text-center align-middle">
                            <span class="badge badge-info badge-pill">{{ $material->cantidad }}</span>
                        </td>
                        <td class="text-center align-middle">
                            <span class="badge {{ $material->unidades_prestadas > 0 ? 'badge-warning' : 'badge-light' }} badge-pill">
                                {{ $material->unidades_prestadas }}
                            </span>
                        </td>
                        <td class="text-center align-middle">
                            <span class="badge {{ $material->unidades_disponibles > 0 ? 'badge-success' : 'badge-secondary' }} badge-pill">
                                {{ $material->unidades_disponibles }}
                            </span>
                        </td>
                        <td class="bv-crud-actions text-center text-nowrap align-middle">
                            <a href="{{ route('biblioteca-fisica.prestamos.index', ['material_didactico_id' => $material->id]) }}"
                                class="btn btn-sm btn-outline-info" title="Ver préstamos de este material">
                                <i class="fa fa-hand-holding"></i>
                            </a>
                            <a href="{{ route('biblioteca-fisica.materiales.edit', $material) }}"
                                class="btn btn-sm btn-outline-warning" title="Editar">
                                <i class="fa fa-edit"></i>
                            </a>
                            <button type="button" class="btn btn-sm btn-outline-danger btn-delete-material"
                                data-url="{{ route('biblioteca-fisica.materiales.destroy', $material) }}"
                                data-nombre="{{ $material->nombre }}" title="Eliminar">
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="p-0 border-0">
                            <div class="bv-empty-state">
                                <i class="fa fa-cubes bv-empty-icon"></i>
                                <p>No hay materiales didácticos registrados.</p>
                                <a href="{{ route('biblioteca-fisica.materiales.create', array_filter(['categoria_id' => request('categoria_id')])) }}"
                                    class="btn btn-sm btn-info">
                                    <i class="fa fa-plus mr-1"></i>Registrar material
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="bv-crud-pagination">{{ $materiales->links() }}</div>

    <form id="deleteFormMaterial" method="POST" class="d-none">
        @csrf
        @method('DELETE')
    </form>

    <script>
        document.addEventListener('click', function (e) {
            const btn = e.target.closest('.btn-delete-material');
            if (!btn) return;
            window.bvConfirm({
                titulo: 'Eliminar material',
                mensaje: '¿Eliminar el material "' + btn.dataset.nombre + '"? Esta acción no se puede deshacer.',
                tono: 'danger',
                icono: 'fa-trash',
                textoConfirmar: 'Eliminar',
            }).then(function (ok) {
                if (!ok) return;
                var f = document.getElementById('deleteFormMaterial');
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
