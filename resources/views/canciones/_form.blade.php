@php
    $plataforma = old('plataforma', $cancion->plataforma ?? 'youtube');
    $opciones = [
        'youtube' => ['YouTube', 'fa-youtube-play', 'Enlace del video o su identificador (ej. https://youtu.be/dQw4w9WgXcQ).'],
        'spotify' => ['Spotify', 'fa-spotify', 'Enlace de la pista o su identificador (ej. https://open.spotify.com/track/…).'],
        'drive' => ['Google Drive', 'fa-google', 'Enlace del archivo de audio en Drive (debe estar compartido públicamente).'],
    ];
@endphp
<div class="bv-form-card mb-4">
    <div class="form-row">
        <div class="form-group col-md-6">
            <label for="nombre" class="bv-form-label">Nombre de la canción <span class="text-danger">*</span></label>
            <input type="text" name="nombre" id="nombre" maxlength="255" required
                class="form-control @error('nombre') is-invalid @enderror" value="{{ old('nombre', $cancion->nombre) }}">
            @error('nombre')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="form-group col-md-6">
            <label for="autor" class="bv-form-label">Autor / compositor <span class="text-danger">*</span></label>
            <input type="text" name="autor" id="autor" maxlength="255" required
                class="form-control @error('autor') is-invalid @enderror" value="{{ old('autor', $cancion->autor) }}">
            @error('autor')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>

    <div class="form-group">
        <label for="categoria_id" class="bv-form-label">Categoría <span class="text-danger">*</span></label>
        <select name="categoria_id" id="categoria_id" required class="custom-select @error('categoria_id') is-invalid @enderror">
            <option value="">Seleccionar categoría…</option>
            @foreach ($categorias as $c)
                <option value="{{ $c->id }}" @selected((string) old('categoria_id', $cancion->categoria_id) === (string) $c->id)>{{ $c->nombre }}</option>
            @endforeach
        </select>
        @error('categoria_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="form-group mb-2">
        <span class="bv-form-label d-block">¿Dónde está publicada? <span class="text-danger">*</span></span>
        <div class="btn-group btn-group-toggle btn-group-sm" data-toggle="buttons" role="group">
            @foreach ($opciones as $clave => $op)
                <label class="btn btn-outline-secondary {{ $plataforma === $clave ? 'active' : '' }}">
                    <input type="radio" name="plataforma" value="{{ $clave }}" autocomplete="off" @checked($plataforma === $clave)>
                    <i class="fa {{ $op[1] }} mr-1"></i>{{ $op[0] }}
                </label>
            @endforeach
        </div>
    </div>

    @foreach ($opciones as $clave => $op)
        <div class="form-group mb-0 js-plataforma" data-plataforma="{{ $clave }}" @if ($plataforma !== $clave) hidden @endif>
            <label for="{{ $clave }}" class="bv-form-label">{{ $op[0] }}</label>
            <input type="text" name="{{ $clave }}" id="{{ $clave }}" maxlength="255"
                class="form-control @error($clave) is-invalid @enderror" value="{{ old($clave, $cancion->{$clave}) }}"
                placeholder="Pega aquí el enlace">
            <small class="form-text text-muted">{{ $op[2] }}</small>
            @error($clave)<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    @endforeach
</div>

<script>
    (function () {
        // Bootstrap 4 (data-toggle="buttons") dispara "change" en el radio elegido.
        function mostrar(valor) {
            document.querySelectorAll('.js-plataforma').forEach(function (g) {
                g.hidden = g.dataset.plataforma !== valor;
            });
        }
        document.querySelectorAll('input[name="plataforma"]').forEach(function (radio) {
            radio.addEventListener('change', function () { mostrar(radio.value); });
            radio.closest('label').addEventListener('click', function () { mostrar(radio.value); });
        });
    })();
</script>
