{{-- Colecciones de una comunidad y, debajo, sus subcomunidades (recursivo). --}}
<ul class="cg-card__lista">
    @forelse ($comunidad->colecciones as $coleccion)
        @php $n = $coleccion->items_count ?? $coleccion->items()->count(); @endphp
        <li class="cg-fila">
            <a class="cg-fila__nombre" href="{{ route('admin.repositorio.items.index', ['coleccion_id' => $coleccion->id]) }}" title="Ver los documentos de «{{ $coleccion->nombre }}»">
                <span class="cg-card__item"><i class="fa fa-folder-open"></i> {{ $coleccion->nombre }}</span>
                <span class="cg-fila__meta">
                    {{ $n }} {{ $n === 1 ? 'documento' : 'documentos' }}
                    @if ($coleccion->borradores_count) · {{ $coleccion->borradores_count }} sin publicar @endif
                    @if ($coleccion->programa) · {{ $coleccion->programa }} @endif
                    @if ($coleccion->tipo_documento) · {{ $coleccion->tipo_documento }} @endif
                </span>
            </a>
            <span class="cg-fila__acciones">
                <a href="{{ route('admin.repositorio.items.create', ['coleccion_id' => $coleccion->id, 'volver' => 'estructura']) }}"
                    class="cg-fila__btn cg-fila__btn--crear" title="Catalogar un documento en esta colección"><i class="fa fa-plus"></i></a>
                <a href="{{ route('admin.repositorio.estructura.colecciones.edit', $coleccion) }}" class="cg-fila__btn" title="Editar colección"><i class="fa fa-pencil"></i></a>
                <button type="button" class="cg-fila__btn btn-delete" title="Eliminar colección"
                    data-url="{{ route('admin.repositorio.estructura.colecciones.destroy', $coleccion) }}"
                    data-nombre="{{ $coleccion->nombre }}" data-titulo="Eliminar colección" data-total="{{ $n }}" data-entidad="documento(s)">
                    <i class="fa fa-trash"></i>
                </button>
            </span>
        </li>
    @empty
        @if ($comunidad->subcomunidades->isEmpty())
            <li class="cg-card__vacia">Sin colecciones todavía.</li>
        @endif
    @endforelse
</ul>

@foreach ($comunidad->subcomunidades as $sub)
    <div class="cg-sub" style="margin-left: {{ $nivel * .75 }}rem;">
        <div class="cg-sub__cab">
            <i class="fa fa-folder-o"></i> {{ $sub->nombre }}
            <span class="cg-fila__acciones">
                <a href="{{ route('admin.repositorio.estructura.colecciones.create', ['comunidad_id' => $sub->id]) }}" class="cg-fila__btn cg-fila__btn--crear" title="Nueva colección en esta subcomunidad"><i class="fa fa-plus mr-1"></i>Colección</a>
                <a href="{{ route('admin.repositorio.estructura.comunidades.edit', $sub) }}" class="cg-fila__btn" title="Editar subcomunidad"><i class="fa fa-pencil"></i></a>
                <button type="button" class="cg-fila__btn btn-delete" title="Eliminar subcomunidad"
                    data-url="{{ route('admin.repositorio.estructura.comunidades.destroy', $sub) }}"
                    data-nombre="{{ $sub->nombre }}" data-titulo="Eliminar subcomunidad"
                    data-total="{{ $sub->colecciones->count() + $sub->subcomunidades->count() }}" data-entidad="colección(es) o subcomunidad(es)">
                    <i class="fa fa-trash"></i>
                </button>
            </span>
        </div>
        @include('admin.repositorio.estructura._comunidad', ['comunidad' => $sub, 'nivel' => $nivel + 1])
    </div>
@endforeach
