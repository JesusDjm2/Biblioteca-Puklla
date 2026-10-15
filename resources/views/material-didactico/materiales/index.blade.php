@extends('layouts.app')
@section('titulo', 'Material didáctico')

@section('contenido')
    {{-- Encabezado unificado de Biblioteca Física (4 tabs principales limpios) --}}
    @include('partials.biblioteca-fisica-crud-header', [
        'active' => 'materiales',
        'stats' => [
            ['valor' => $materiales->total(), 'singular' => 'material', 'plural' => 'materiales', 'icono' => 'fa-cubes'],
            ['valor' => $categorias->count(), 'singular' => 'categoría', 'plural' => 'categorías', 'icono' => 'fa-tags'],
        ],
    ])

    @include('material-didactico.partials.alertas')

    {{-- SELECTOR DE PESTAÑAS INTERNAS (CAMBIO INSTANTÁNEO SIN RECARGA DE PÁGINA) --}}
    <div class="bv-tab-switch-container mb-3">
        <ul class="nav bv-tab-switch-nav" id="materialTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="bv-tab-btn active" id="tab-materiales-btn" data-toggle="tab" data-target="#pane-materiales"
                    type="button" role="tab" aria-controls="pane-materiales" aria-selected="true">
                    <i class="fa fa-cubes mr-2"></i>Inventario de Materiales
                    <span class="badge badge-pill badge-info ml-2">{{ $materiales->total() }}</span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="bv-tab-btn" id="tab-categorias-btn" data-toggle="tab" data-target="#pane-categorias"
                    type="button" role="tab" aria-controls="pane-categorias" aria-selected="false">
                    <i class="fa fa-tags mr-2"></i>Categorías
                    <span class="badge badge-pill badge-secondary ml-2">{{ $categorias->count() }}</span>
                </button>
            </li>
        </ul>

        <div class="bv-tab-actions">
            <a href="{{ route('biblioteca-fisica.materiales.create', array_filter(['categoria_id' => request('categoria_id')])) }}"
                class="btn btn-sm btn-info" id="btnCrearMaterial">
                <i class="fa fa-plus mr-1"></i>Nuevo material
            </a>
            <a href="{{ route('biblioteca-fisica.material-categorias.create') }}"
                class="btn btn-sm btn-outline-info" id="btnCrearCategoria">
                <i class="fa fa-folder-plus mr-1"></i>Nueva categoría
            </a>
        </div>
    </div>

    {{-- CONTENIDO DE LAS PESTAÑAS (EN LA MISMA VISTA) --}}
    <div class="tab-content" id="materialTabsContent">
        {{-- ======================================================== --}}
        {{-- TAB 1: INVENTARIO DE MATERIALES                          --}}
        {{-- ======================================================== --}}
        <div class="tab-pane fade show active" id="pane-materiales" role="tabpanel" aria-labelledby="tab-materiales-btn">
            <form method="get" action="{{ route('biblioteca-fisica.materiales.index') }}" class="bv-crud-toolbar" id="filterFormMateriales">
                <div class="form-row align-items-end">
                    <div class="form-group col-md-5 mb-2 mb-md-0">
                        <label for="md-q" class="small text-muted bv-toolbar-label">
                            <i class="fa fa-search mr-1"></i>Buscar
                        </label>
                        <input type="text" id="md-q" name="q" class="form-control form-control-sm bv-toolbar-control"
                            value="{{ request('q') }}" placeholder="Nombre, código o descripción…" autocomplete="off">
                    </div>
                    <div class="form-group col-md-4 mb-2 mb-md-0">
                        <div class="bv-toolbar-label-wrap justify-content-between">
                            <label for="md-cat" class="small text-muted mb-0">
                                <i class="fa fa-folder mr-1"></i>Filtrar por Categoría
                            </label>
                            <button type="button" class="btn btn-link p-0 small text-info text-decoration-none" id="btnIrACategorias" title="Ver lista de categorías">
                                Ver categorías →
                            </button>
                        </div>
                        <select id="md-cat" name="categoria_id" class="form-control form-control-sm bv-toolbar-control">
                            <option value="">Todas las categorías ({{ $categorias->count() }})</option>
                            @foreach ($categorias as $cat)
                                <option value="{{ $cat->id }}" @selected((string) request('categoria_id') === (string) $cat->id)>
                                    {{ $cat->nombre }} ({{ $cat->materiales_count }} mat.)
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-3 mb-0">
                        <label class="small bv-toolbar-label invisible" aria-hidden="true">Acciones</label>
                        <div class="btn-group btn-group-sm bv-toolbar-btn-group w-100">
                            <button type="submit" class="btn btn-sm btn-info">
                                <i class="fa fa-filter mr-1"></i>Filtrar
                            </button>
                            <a href="{{ route('biblioteca-fisica.materiales.index') }}"
                                class="btn btn-sm btn-outline-secondary">Limpiar</a>
                        </div>
                    </div>
                </div>
            </form>

            @if ($materiales->total() > 0)
                <p class="bv-crud-meta mb-2">
                    Mostrando {{ $materiales->firstItem() }}–{{ $materiales->lastItem() }} de {{ $materiales->total() }} material(es)
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
                                <td class="small align-middle">
                                    @if ($material->categoria)
                                        <span class="badge badge-light border">{{ $material->categoria->nombre }}</span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
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
        </div>

        {{-- ======================================================== --}}
        {{-- TAB 2: CATEGORÍAS DE MATERIAL (EN LA MISMA PANTALLA)    --}}
        {{-- ======================================================== --}}
        <div class="tab-pane fade" id="pane-categorias" role="tabpanel" aria-labelledby="tab-categorias-btn">
            {{-- Toolbar de búsqueda en vivo de categorías (filtra al instante en JS sin recargar) --}}
            <div class="bv-crud-toolbar">
                <div class="form-row align-items-center">
                    <div class="form-group col-md-8 mb-2 mb-md-0">
                        <label for="catSearchInput" class="small text-muted mb-1 d-block">
                            <i class="fa fa-search mr-1"></i>Buscar categoría al instante:
                        </label>
                        <div class="input-group input-group-sm">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-transparent text-muted"><i class="fa fa-filter"></i></span>
                            </div>
                            <input type="text" id="catSearchInput" class="form-control form-control-sm"
                                placeholder="Escribe nombre o código para filtrar…" autocomplete="off">
                        </div>
                    </div>
                    <div class="form-group col-md-4 mb-0 text-md-right">
                        <label class="small mb-1 d-block invisible d-none d-md-block" aria-hidden="true">Acción</label>
                        <a href="{{ route('biblioteca-fisica.material-categorias.create') }}" class="btn btn-sm btn-info w-100 w-md-auto">
                            <i class="fa fa-plus mr-1"></i>Nueva categoría
                        </a>
                    </div>
                </div>
            </div>

            <p class="bv-crud-meta mb-2" id="catMetaText">
                Total: {{ $categorias->count() }} categoría(s) registrada(s)
            </p>

            <div class="bv-crud-table-wrap table-responsive">
                <table class="table table-hover table-sm mb-0" id="tableCategorias">
                    <thead class="thead-light">
                        <tr>
                            <th style="width:3rem;">#</th>
                            <th>Nombre</th>
                            <th style="width:8rem;">Código</th>
                            <th>Descripción</th>
                            <th style="width:7rem;" class="text-center">Materiales</th>
                            <th style="width:7rem;" class="text-center">Unidades</th>
                            <th style="width:10rem;" class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="categoriasTableBody">
                        @forelse ($categorias as $categoria)
                            <tr class="bv-table-row cat-row" data-search="{{ strtolower($categoria->nombre . ' ' . $categoria->codigo . ' ' . $categoria->descripcion) }}">
                                <td class="text-muted small align-middle">{{ $loop->iteration }}</td>
                                <td class="font-weight-bold align-middle">{{ $categoria->nombre }}</td>
                                <td class="align-middle">
                                    @if ($categoria->codigo)
                                        <code class="small">{{ $categoria->codigo }}</code>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td class="small text-muted align-middle">
                                    {{ \Illuminate\Support\Str::limit($categoria->descripcion ?? '—', 95) }}
                                </td>
                                <td class="text-center align-middle">
                                    <span class="badge badge-info badge-pill">{{ $categoria->materiales_count }}</span>
                                </td>
                                <td class="text-center align-middle">
                                    <span class="badge badge-secondary badge-pill">{{ (int) $categoria->materiales_sum_cantidad }}</span>
                                </td>
                                <td class="bv-crud-actions text-center text-nowrap align-middle">
                                    <button type="button" class="btn btn-sm btn-outline-info btn-filtrar-por-cat"
                                        data-cat-id="{{ $categoria->id }}" title="Ver materiales de esta categoría en el inventario">
                                        <i class="fa fa-cubes mr-1"></i>Ver materiales
                                    </button>
                                    <a href="{{ route('biblioteca-fisica.material-categorias.edit', $categoria) }}"
                                        class="btn btn-sm btn-outline-warning" title="Editar categoría">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-outline-danger btn-delete-matcat"
                                        data-url="{{ route('biblioteca-fisica.material-categorias.destroy', $categoria) }}"
                                        data-nombre="{{ $categoria->nombre }}" title="Eliminar categoría">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr id="emptyCategoriasRow">
                                <td colspan="7" class="p-0 border-0">
                                    <div class="bv-empty-state">
                                        <i class="fa fa-tags bv-empty-icon"></i>
                                        <p>No hay categorías de material didáctico registradas.</p>
                                        <a href="{{ route('biblioteca-fisica.material-categorias.create') }}" class="btn btn-sm btn-info">
                                            <i class="fa fa-plus mr-1"></i>Crear primera categoría
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Formularios ocultos de eliminación --}}
    <form id="deleteFormMaterial" method="POST" class="d-none">
        @csrf
        @method('DELETE')
    </form>
    <form id="deleteFormMatCat" method="POST" class="d-none">
        @csrf
        @method('DELETE')
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // 1. Manejo de pestañas sin recarga con memoria de URL hash
            var tabMaterialesBtn = document.getElementById('tab-materiales-btn');
            var tabCategoriasBtn = document.getElementById('tab-categorias-btn');

            function activarTab(tabName) {
                if (tabName === 'categorias') {
                    if (typeof $(tabCategoriasBtn).tab === 'function') {
                        $(tabCategoriasBtn).tab('show');
                    } else {
                        tabCategoriasBtn.click();
                    }
                } else {
                    if (typeof $(tabMaterialesBtn).tab === 'function') {
                        $(tabMaterialesBtn).tab('show');
                    } else {
                        tabMaterialesBtn.click();
                    }
                }
            }

            // Detectar hash en la URL (#categorias o ?tab=categorias)
            var urlParams = new URLSearchParams(window.location.search);
            if (window.location.hash === '#categorias' || urlParams.get('tab') === 'categorias') {
                activarTab('categorias');
            }

            // Actualizar URL hash al cambiar de tab sin recargar la página
            $('button[data-toggle="tab"]').on('shown.bs.tab', function (e) {
                var target = $(e.target).data('target');
                var hash = target === '#pane-categorias' ? '#categorias' : '#materiales';
                if (history.replaceState) {
                    history.replaceState(null, null, hash);
                } else {
                    window.location.hash = hash;
                }
            });

            // Botón de atajo desde el filtro: "Ver categorías →"
            var btnIrACat = document.getElementById('btnIrACategorias');
            if (btnIrACat) {
                btnIrACat.addEventListener('click', function () {
                    activarTab('categorias');
                });
            }

            // Botón en la tabla de categorías: "Ver materiales"
            // Cambia al Tab 1 y selecciona la categoría en el dropdown al instante
            document.addEventListener('click', function (e) {
                var btn = e.target.closest('.btn-filtrar-por-cat');
                if (!btn) return;
                var catId = btn.dataset.catId;
                var select = document.getElementById('md-cat');
                if (select) {
                    select.value = catId;
                }
                activarTab('materiales');
                // Enviar el formulario para aplicar el filtro al inventario
                var filterForm = document.getElementById('filterFormMateriales');
                if (filterForm) filterForm.submit();
            });

            // 2. Buscador en vivo de categorías (filtrado instantáneo en DOM)
            var catSearchInput = document.getElementById('catSearchInput');
            if (catSearchInput) {
                catSearchInput.addEventListener('input', function () {
                    var term = this.value.toLowerCase().trim();
                    var rows = document.querySelectorAll('.cat-row');
                    var visibleCount = 0;
                    rows.forEach(function (r) {
                        var text = r.dataset.search || '';
                        var match = text.indexOf(term) !== -1;
                        r.style.display = match ? '' : 'none';
                        if (match) visibleCount++;
                    });
                    var meta = document.getElementById('catMetaText');
                    if (meta) {
                        meta.textContent = term
                            ? 'Mostrando ' + visibleCount + ' de ' + rows.length + ' categoría(s)'
                            : 'Total: ' + rows.length + ' categoría(s) registrada(s)';
                    }
                });
            }

            // 3. Confirmación de eliminación para materiales
            document.addEventListener('click', function (e) {
                var btn = e.target.closest('.btn-delete-material');
                if (!btn) return;
                if (typeof window.bvConfirm === 'function') {
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
                } else if (confirm('¿Eliminar el material "' + btn.dataset.nombre + '"?')) {
                    var f = document.getElementById('deleteFormMaterial');
                    f.action = btn.dataset.url;
                    f.submit();
                }
            });

            // 4. Confirmación de eliminación para categorías
            document.addEventListener('click', function (e) {
                var btn = e.target.closest('.btn-delete-matcat');
                if (!btn) return;
                if (typeof window.bvConfirm === 'function') {
                    window.bvConfirm({
                        titulo: 'Eliminar categoría',
                        mensaje: '¿Eliminar la categoría "' + btn.dataset.nombre + '"? Los materiales asociados deben ser reasignados previamente.',
                        tono: 'danger',
                        icono: 'fa-trash',
                        textoConfirmar: 'Eliminar',
                    }).then(function (ok) {
                        if (!ok) return;
                        var f = document.getElementById('deleteFormMatCat');
                        f.action = btn.dataset.url;
                        f.submit();
                    });
                } else if (confirm('¿Eliminar la categoría "' + btn.dataset.nombre + '"?')) {
                    var f = document.getElementById('deleteFormMatCat');
                    f.action = btn.dataset.url;
                    f.submit();
                }
            });
        });
    </script>
@endsection
