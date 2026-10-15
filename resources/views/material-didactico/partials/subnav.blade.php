@php
    $subactive = $subactive ?? (request()->routeIs('biblioteca-fisica.material-categorias.*') ? 'categorias' : 'materiales');
@endphp

<div class="bv-subnav-bar mb-3">
    <div class="bv-subnav-items">
        <a href="{{ route('biblioteca-fisica.materiales.index') }}"
            class="bv-subnav-item {{ $subactive === 'materiales' ? 'is-active' : '' }}">
            <i class="fa fa-cubes mr-1"></i>Inventario de materiales
        </a>
        <a href="{{ route('biblioteca-fisica.material-categorias.index') }}"
            class="bv-subnav-item {{ $subactive === 'categorias' ? 'is-active' : '' }}">
            <i class="fa fa-tags mr-1"></i>Categorías
        </a>
    </div>

    <div class="bv-subnav-actions d-none d-md-flex align-items-center">
        @if ($subactive === 'materiales')
            <a href="{{ route('biblioteca-fisica.material-categorias.index') }}" class="small text-muted font-weight-500">
                <i class="fa fa-cog mr-1"></i>Gestionar categorías
            </a>
        @else
            <a href="{{ route('biblioteca-fisica.materiales.index') }}" class="small text-muted font-weight-500">
                <i class="fa fa-arrow-left mr-1"></i>Volver al inventario
            </a>
        @endif
    </div>
</div>
