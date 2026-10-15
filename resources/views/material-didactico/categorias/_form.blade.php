<div class="bv-form-card" id="bvCardMatCat" style="max-width:680px;">
    <div class="bv-form-section-title">
        <i class="fa fa-tag mr-1"></i>Datos de la categoría
    </div>

    <div class="form-row">
        <div class="form-group col-md-8">
            <label class="bv-form-label" for="nombre">
                Nombre <span class="text-danger">*</span>
            </label>
            <input type="text" name="nombre" id="nombre"
                class="form-control form-control-sm @error('nombre') is-invalid @enderror"
                value="{{ old('nombre', $categoria->nombre) }}" required maxlength="120"
                placeholder="Ej: Matemática, Comunicación, Artes…" autofocus>
            <div class="bv-char-counter" id="nombreCounter">0 / 120</div>
            @error('nombre')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-group col-md-4">
            <label class="bv-form-label" for="codigo">
                Código <small class="text-muted font-weight-normal">(opcional)</small>
            </label>
            <input type="text" name="codigo" id="codigo"
                class="form-control form-control-sm @error('codigo') is-invalid @enderror"
                value="{{ old('codigo', $categoria->codigo) }}" maxlength="50"
                placeholder="Ej: MAT">
            <small class="form-text text-muted">Se usa como prefijo del código de los materiales.</small>
            @error('codigo')
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
            maxlength="500"
            placeholder="Describe brevemente qué materiales abarca esta categoría…">{{ old('descripcion', $categoria->descripcion) }}</textarea>
        <div class="bv-char-counter" id="descCounter">0 / 500</div>
        @error('descripcion')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

@push('scripts')
<script>
    (function () {
        window.bvPageAnimated = true;
        gsap.from('#bvCardMatCat', { opacity: 0, y: 22, duration: 0.4, ease: 'power2.out' });

        function attachCounter(inputId, counterId, max) {
            var el = document.getElementById(inputId);
            var counter = document.getElementById(counterId);
            if (!el || !counter) return;
            function update() {
                var len = el.value.length;
                counter.textContent = len + ' / ' + max;
                counter.className = 'bv-char-counter' +
                    (len >= max ? ' at-limit' : len >= max * 0.85 ? ' near-limit' : '');
            }
            el.addEventListener('input', update);
            update();
        }

        attachCounter('nombre',      'nombreCounter', 120);
        attachCounter('descripcion', 'descCounter',   500);
    })();
</script>
@endpush
