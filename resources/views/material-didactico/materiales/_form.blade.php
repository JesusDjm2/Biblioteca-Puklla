<div class="bv-form-card" id="bvCardMaterial" style="max-width:760px;">
    <div class="bv-form-section-title">
        <i class="fa fa-cube mr-1"></i>Datos del material
    </div>

    <div class="form-row">
        <div class="form-group col-md-8">
            <label class="bv-form-label" for="nombre">
                Nombre <span class="text-danger">*</span>
            </label>
            <input type="text" name="nombre" id="nombre"
                class="form-control form-control-sm @error('nombre') is-invalid @enderror"
                value="{{ old('nombre', $material->nombre) }}" required maxlength="255"
                placeholder="Ej: Base diez, Regletas de Cuisenaire, Tangram…" autofocus>
            @error('nombre')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-group col-md-4">
            <label class="bv-form-label" for="categoria_material_didactico_id">
                Categoría <span class="text-danger">*</span>
            </label>
            <select name="categoria_material_didactico_id" id="categoria_material_didactico_id" required
                class="form-control form-control-sm @error('categoria_material_didactico_id') is-invalid @enderror">
                <option value="">Selecciona…</option>
                @foreach ($categorias as $cat)
                    <option value="{{ $cat->id }}"
                        @selected((string) old('categoria_material_didactico_id', $material->categoria_material_didactico_id) === (string) $cat->id)>
                        {{ $cat->nombre }}{{ $cat->codigo ? ' ('.$cat->codigo.')' : '' }}
                    </option>
                @endforeach
            </select>
            @error('categoria_material_didactico_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="form-row">
        <div class="form-group col-md-6">
            <label class="bv-form-label" for="codigo">
                Código
                @unless ($material->exists)
                    <small class="text-muted font-weight-normal">(opcional)</small>
                @endunless
            </label>
            <input type="text" name="codigo" id="codigo"
                class="form-control form-control-sm @error('codigo') is-invalid @enderror"
                value="{{ old('codigo', $material->codigo) }}" maxlength="50"
                placeholder="Ej: MAT-001">
            @unless ($material->exists)
                <small class="form-text text-muted">Si lo dejas vacío se genera automáticamente con el código de la categoría.</small>
            @endunless
            @error('codigo')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-group col-md-3">
            <label class="bv-form-label" for="cantidad">
                Cantidad <span class="text-danger">*</span>
            </label>
            <input type="number" name="cantidad" id="cantidad" min="0" max="100000" step="1" required
                class="form-control form-control-sm @error('cantidad') is-invalid @enderror"
                value="{{ old('cantidad', $material->cantidad) }}">
            @error('cantidad')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="form-group mb-0">
        <label class="bv-form-label" for="descripcion">
            Descripción <small class="text-muted font-weight-normal">(opcional)</small>
        </label>
        <textarea name="descripcion" id="descripcion" rows="4"
            class="form-control form-control-sm @error('descripcion') is-invalid @enderror"
            maxlength="2000"
            placeholder="Contenido, piezas que incluye, estado, uso sugerido…">{{ old('descripcion', $material->descripcion) }}</textarea>
        <div class="bv-char-counter" id="descCounter">0 / 2000</div>
        @error('descripcion')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

@push('scripts')
<script>
    (function () {
        window.bvPageAnimated = true;
        gsap.from('#bvCardMaterial', { opacity: 0, y: 22, duration: 0.4, ease: 'power2.out' });

        var el = document.getElementById('descripcion');
        var counter = document.getElementById('descCounter');
        if (!el || !counter) return;
        function update() {
            var len = el.value.length;
            counter.textContent = len + ' / 2000';
            counter.className = 'bv-char-counter' +
                (len >= 2000 ? ' at-limit' : len >= 1700 ? ' near-limit' : '');
        }
        el.addEventListener('input', update);
        update();
    })();
</script>
@endpush
