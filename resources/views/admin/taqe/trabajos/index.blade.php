@extends('layouts.app')
@section('titulo', "Taq'e — Trabajos de Estudiantes")
@section('contenido')
    @include('partials.taqe-crud-header', [
        'active' => 'trabajos',
        'stats' => [['valor' => $trabajos->total(), 'singular' => 'trabajo', 'plural' => 'trabajos', 'icono' => 'fa-graduation-cap']],
        'botones' => [
            ['route' => route('admin.taqe.trabajos.create'), 'label' => 'Nuevo trabajo', 'icono' => 'fa-plus'],
        ],
    ])

    <p class="bv-crud-meta mb-3">Gestiona las investigaciones, proyectos y recursos elaborados por los estudiantes del IESP Pukllasunchis.</p>

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

    <form method="get" action="{{ route('admin.taqe.trabajos.index') }}" class="bv-crud-toolbar">
        <div class="form-row align-items-end">
            <div class="form-group col-md-4 col-lg-3 mb-2 mb-md-0">
                <label for="filtro-q" class="small text-muted mb-1 d-block">Buscar</label>
                <input type="text" id="filtro-q" name="q" class="form-control form-control-sm bv-toolbar-control"
                    value="{{ request('q') }}" placeholder="Título, autor o palabra clave…">
            </div>
            <div class="form-group col-md-3 col-lg-3 mb-2 mb-md-0">
                <label for="filtro-cat" class="small text-muted mb-1 d-block">Categoría</label>
                <select id="filtro-cat" name="categoria_id" class="custom-select custom-select-sm bv-toolbar-control">
                    <option value="">Todas las categorías</option>
                    @foreach ($categorias as $c)
                        <option value="{{ $c->id }}" @selected((string) request('categoria_id') === (string) $c->id)>
                            {{ $c->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="form-group col-md-3 col-lg-2 mb-2 mb-md-0">
                <label for="filtro-formato" class="small text-muted mb-1 d-block">Formato</label>
                <select id="filtro-formato" name="formato" class="custom-select custom-select-sm bv-toolbar-control">
                    <option value="">Todos los formatos</option>
                    @foreach ($formatos as $key => $f)
                        <option value="{{ $key }}" @selected(request('formato') === $key)>{{ $f['nombre'] }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group col-md-2 col-lg-2 mb-2 mb-md-0">
                <label for="filtro-idioma" class="small text-muted mb-1 d-block">Idioma</label>
                <select id="filtro-idioma" name="idioma" class="custom-select custom-select-sm bv-toolbar-control">
                    <option value="">Todos</option>
                    @foreach ($idiomas as $k => $i)
                        <option value="{{ $k }}" @selected(request('idioma') === $k)>{{ $i['nombre'] }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group col-md-12 col-lg-2 mb-0">
                <label class="small mb-1 d-block invisible" aria-hidden="true">Acciones</label>
                <div class="btn-group btn-group-sm bv-toolbar-btn-group w-100">
                    <button type="submit" class="btn btn-sm btn-info">Filtrar</button>
                    <a href="{{ route('admin.taqe.trabajos.index') }}" class="btn btn-sm btn-outline-secondary">Limpiar</a>
                </div>
            </div>
        </div>
    </form>

    <p class="bv-crud-meta">
        @if ($trabajos->total() > 0)
            Mostrando {{ $trabajos->firstItem() }}–{{ $trabajos->lastItem() }} de {{ $trabajos->total() }} registros
        @else
            Sin resultados con los filtros actuales.
        @endif
    </p>

    <div class="bv-crud-table-wrap table-responsive">
        <table class="table table-hover table-sm">
            <thead class="thead-light">
                <tr>
                    <th style="width:3rem;">#</th>
                    <th style="width:4.5rem;">Portada</th>
                    <th>Título y Autores</th>
                    <th>Categoría</th>
                    <th style="width:9rem;">Formato</th>
                    <th style="width:6rem;">Año</th>
                    <th style="width:5rem;">Estado</th>
                    <th style="width:7.5rem;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($trabajos as $trabajo)
                    <tr>
                        <td>{{ $trabajos->firstItem() + $loop->index }}</td>
                        <td>
                            <img src="{{ $trabajo->portada_url }}" alt="{{ $trabajo->titulo }}"
                                style="width:48px; height:36px; object-fit:cover; border-radius:4px; border:1px solid #ddd;">
                        </td>
                        <td>
                            <div class="bv-libro-identidad">
                                <div class="bv-cell-title">
                                    <a href="{{ route('taqe.show', $trabajo->slug) }}" target="_blank" style="color:inherit; text-decoration:none;">
                                        {{ $trabajo->titulo }}
                                    </a>
                                </div>
                                <div class="bv-cell-sub">
                                    <i class="fa fa-user"></i> {{ $trabajo->autores }}
                                    @if($trabajo->ciclo_carrera)
                                        <span class="text-muted">· {{ $trabajo->ciclo_carrera }}</span>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>
                            @if ($trabajo->categoria)
                                <span class="badge" style="background:rgba(200,142,65,0.15); color:var(--puklla-accent, #c88e41); font-weight:600; padding:4px 8px;">
                                    <i class="fa {{ $trabajo->categoria->icono }}"></i> {{ $trabajo->categoria->nombre }}
                                </span>
                            @else
                                <span class="text-muted small"><em>Sin categoría</em></span>
                            @endif
                        </td>
                        <td>
                            <span class="badge" style="background:#eef2f6; color:#334155; font-weight:600; padding:4px 8px;">
                                <i class="{{ $trabajo->icono }}"></i> {{ $trabajo->formato_label }}
                            </span>
                        </td>
                        <td class="small">{{ $trabajo->anio ?? '—' }}</td>
                        <td>
                            @if ($trabajo->estado === 'publicado')
                                <span class="badge badge-success">Publicado</span>
                            @else
                                <span class="badge badge-secondary">Borrador</span>
                            @endif
                        </td>
                        <td class="bv-crud-actions text-nowrap">
                            <div class="btn-group btn-group-sm" role="group">
                                <a href="{{ route('taqe.show', $trabajo->slug) }}" target="_blank" class="btn btn-outline-info" title="Ver en público">
                                    <i class="fa fa-external-link"></i>
                                </a>
                                <a href="{{ route('admin.taqe.trabajos.edit', $trabajo->id) }}" class="btn btn-warning" title="Editar">
                                    <i class="fa fa-edit"></i>
                                </a>
                                <button type="button" class="btn btn-danger btn-delete"
                                    data-url="{{ route('admin.taqe.trabajos.destroy', $trabajo->id) }}"
                                    data-nombre="{{ $trabajo->titulo }}" title="Eliminar">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="bv-crud-empty">No hay trabajos que coincidan con la búsqueda.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="bv-crud-pagination">{{ $trabajos->links() }}</div>

    <form id="deleteForm" method="POST" class="d-none">
        @csrf
        @method('DELETE')
    </form>

    <script>
        document.addEventListener('click', function(e) {
            const btn = e.target.closest('.btn-delete');
            if (!btn) return;
            window.bvConfirm({
                titulo: 'Eliminar trabajo de investigación',
                mensaje: '¿Eliminar "' + btn.dataset.nombre + '"? Se borrarán sus archivos adjuntos de audio o PDF.',
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
