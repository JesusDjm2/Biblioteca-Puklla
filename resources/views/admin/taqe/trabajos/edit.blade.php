@extends('layouts.app')
@section('titulo', "Editar Trabajo — Taq'e")
@section('contenido')
    @include('partials.taqe-crud-header', ['active' => 'trabajos'])
    @include('partials.admin-breadcrumb', [
        'items' => [
            ['label' => 'Panel', 'url' => route('home')],
            ['label' => "Taq'e Trabajos", 'url' => route('admin.taqe.trabajos.index')],
            ['label' => 'Editar trabajo', 'url' => null],
        ],
    ])

    <div class="d-flex flex-wrap justify-content-between align-items-start mb-3">
        <div>
            <h1 class="bv-crud-page-title mb-0">Editar trabajo: {{ $trabajo->titulo }}</h1>
            <p class="bv-crud-meta">Actualiza información, enlaces multimedia o reemplaza archivos adjuntos.</p>
        </div>
        <div class="mt-2">
            <a href="{{ route('taqe.show', $trabajo->slug) }}" target="_blank" class="btn btn-outline-info btn-sm mr-1">
                <i class="fa fa-external-link"></i> Ver público
            </a>
            <a href="{{ route('admin.taqe.trabajos.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fa fa-arrow-left"></i> Volver
            </a>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger bv-alert">
            <ul class="mb-0 pl-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.taqe.trabajos.update', $trabajo->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="bv-form-card mb-4">
            <p class="bv-form-section-title"><i class="fa fa-info-circle"></i> Información básica</p>
            <div class="form-row">
                <div class="form-group col-lg-8">
                    <label for="titulo" class="bv-form-label">Título del trabajo <span class="text-danger">*</span></label>
                    <input type="text" name="titulo" id="titulo" class="form-control" value="{{ old('titulo', $trabajo->titulo) }}" required>
                </div>
                <div class="form-group col-lg-4">
                    <label for="categoria_id" class="bv-form-label">Categoría <span class="text-danger">*</span></label>
                    <select name="categoria_id" id="categoria_id" class="custom-select" required>
                        <option value="">Seleccione una categoría…</option>
                        @foreach ($categorias as $c)
                            <option value="{{ $c->id }}" @selected(old('categoria_id', $trabajo->categoria_id) == $c->id)>{{ $c->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-lg-6">
                    <label for="autores" class="bv-form-label">Autor(es) / Estudiante(s) <span class="text-danger">*</span></label>
                    <input type="text" name="autores" id="autores" class="form-control" value="{{ old('autores', $trabajo->autores) }}" required>
                </div>
                <div class="form-group col-lg-3">
                    <label for="año_periodo" class="bv-form-label">Año o Periodo</label>
                    <input type="text" name="año_periodo" id="año_periodo" class="form-control" value="{{ old('año_periodo', $trabajo->año_periodo) }}">
                </div>
                <div class="form-group col-lg-3">
                    <label for="ciclo_carrera" class="bv-form-label">Especialidad / Ciclo</label>
                    <input type="text" name="ciclo_carrera" id="ciclo_carrera" class="form-control" value="{{ old('ciclo_carrera', $trabajo->ciclo_carrera) }}">
                </div>
                <div class="form-group col-lg-6">
                    <label for="formato_principal" class="bv-form-label">Formato principal <span class="text-danger">*</span></label>
                    <select name="formato_principal" id="formato_principal" class="custom-select" required>
                        @foreach ($formatos as $key => $f)
                            <option value="{{ $key }}" @selected(old('formato_principal', $trabajo->formato_principal) === $key)>{{ $f['nombre'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-lg-6">
                    <label for="idioma" class="bv-form-label">Lengua / Idioma <span class="text-danger">*</span></label>
                    <select name="idioma" id="idioma" class="custom-select" required>
                        @foreach ($idiomas as $k => $i)
                            <option value="{{ $k }}" @selected(old('idioma', $trabajo->idioma) === $k)>{{ $i['nombre'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-12">
                    <label for="descripcion" class="bv-form-label">Descripción o Resumen</label>
                    <textarea name="descripcion" id="descripcion" rows="4" class="form-control">{{ old('descripcion', $trabajo->descripcion) }}</textarea>
                </div>
            </div>
        </div>

        <div class="bv-form-card mb-4">
            <p class="bv-form-section-title"><i class="fa fa-paperclip"></i> Recursos y Archivos Multimedia</p>
            <div class="form-row">
                <div class="form-group col-lg-6">
                    <label for="archivo_pdf" class="bv-form-label">Documento PDF (opcional, máx. 50MB)</label>
                    @if ($trabajo->archivo_pdf)
                        <div class="mb-2 small text-success">
                            <i class="fa fa-file-pdf-o"></i> Archivo actual: 
                            <a href="{{ $trabajo->pdf_url }}" target="_blank">Ver documento actual</a>
                        </div>
                    @endif
                    <div class="custom-file">
                        <input type="file" class="custom-file-input" id="archivo_pdf" name="archivo_pdf" accept="application/pdf">
                        <label class="custom-file-label" for="archivo_pdf" data-browse="Elegir">
                            {{ $trabajo->archivo_pdf ? 'Reemplazar archivo PDF…' : 'Seleccionar archivo PDF…' }}
                        </label>
                    </div>
                </div>
                <div class="form-group col-lg-6">
                    <label for="archivo_audio" class="bv-form-label">Archivo de Audio (opcional, MP3, WAV, OGG)</label>
                    @if ($trabajo->archivo_audio)
                        <div class="mb-2 small text-success">
                            <i class="fa fa-volume-up"></i> Audio actual registrado
                            <audio controls style="height:28px; vertical-align:middle; margin-left:8px;">
                                <source src="{{ $trabajo->audio_url }}">
                            </audio>
                        </div>
                    @endif
                    <div class="custom-file">
                        <input type="file" class="custom-file-input" id="archivo_audio" name="archivo_audio" accept="audio/*">
                        <label class="custom-file-label" for="archivo_audio" data-browse="Elegir">
                            {{ $trabajo->archivo_audio ? 'Reemplazar audio…' : 'Seleccionar audio…' }}
                        </label>
                    </div>
                </div>
                <div class="form-group col-lg-6">
                    <label for="url_video" class="bv-form-label">Enlace de Video (YouTube o Google Drive)</label>
                    <input type="url" name="url_video" id="url_video" class="form-control" value="{{ old('url_video', $trabajo->url_video) }}">
                    @if ($trabajo->video_embed_url)
                        <small class="form-text text-muted">Embed detectado: <a href="{{ $trabajo->url_video }}" target="_blank">{{ $trabajo->url_video }}</a></small>
                    @endif
                </div>
                <div class="form-group col-lg-6">
                    <label for="portada" class="bv-form-label">Imagen de Carátula / Portada</label>
                    @if ($trabajo->portada)
                        <div class="mb-2 d-flex align-items-center gap-2">
                            <img src="{{ $trabajo->portada_url }}" alt="Portada" style="height:36px; border-radius:4px; border:1px solid #ccc; margin-right:8px;">
                            <span class="small text-muted">Carátula actual</span>
                        </div>
                    @endif
                    <div class="custom-file">
                        <input type="file" class="custom-file-input" id="portada" name="portada" accept="image/*">
                        <label class="custom-file-label" for="portada" data-browse="Elegir">
                            {{ $trabajo->portada ? 'Reemplazar carátula…' : 'Seleccionar carátula…' }}
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <div class="bv-form-card mb-4">
            <p class="bv-form-section-title"><i class="fa fa-sliders"></i> Opciones de publicación</p>
            <div class="form-row align-items-center">
                <div class="form-group col-md-6">
                    <label for="estado" class="bv-form-label">Estado</label>
                    <select name="estado" id="estado" class="custom-select">
                        <option value="publicado" @selected(old('estado', $trabajo->estado) === 'publicado')>Publicado (Visible a todos)</option>
                        <option value="borrador" @selected(old('estado', $trabajo->estado) === 'borrador')>Borrador (Oculto)</option>
                    </select>
                </div>
                <div class="form-group col-md-6 pt-md-4">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" id="destacado" name="destacado" value="1" @checked(old('destacado', $trabajo->destacado))>
                        <label class="custom-control-label" for="destacado">Destacar este trabajo en la página principal</label>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2 mb-4">
            <a href="{{ route('admin.taqe.trabajos.index') }}" class="btn btn-secondary mr-2">Cancelar</a>
            <button type="submit" class="btn btn-primary"><i class="fa fa-save mr-1"></i> Actualizar cambios</button>
        </div>
    </form>

    <script>
        document.querySelectorAll('.custom-file-input').forEach(function(input) {
            input.addEventListener('change', function(e) {
                var fileName = e.target.files[0] ? e.target.files[0].name : 'Seleccionar archivo…';
                var nextSibling = e.target.nextElementSibling;
                if (nextSibling) nextSibling.innerText = fileName;
            });
        });
    </script>
@endsection
