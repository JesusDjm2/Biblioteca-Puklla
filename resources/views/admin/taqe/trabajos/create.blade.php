@extends('layouts.app')
@section('titulo', "Registrar nuevo Trabajo — Taq'e")
@section('contenido')
    @include('partials.taqe-crud-header', ['active' => 'trabajos'])
    @include('partials.admin-breadcrumb', [
        'items' => [
            ['label' => 'Panel', 'url' => route('home')],
            ['label' => "Taq'e Trabajos", 'url' => route('admin.taqe.trabajos.index')],
            ['label' => 'Nuevo trabajo', 'url' => null],
        ],
    ])

    <div class="d-flex flex-wrap justify-content-between align-items-start mb-3">
        <div>
            <h1 class="bv-crud-page-title mb-0">Nuevo trabajo de estudiante</h1>
            <p class="bv-crud-meta">Sube investigaciones, materiales didácticos, grabaciones en runasimi o videos de estudiantes.</p>
        </div>
        <a href="{{ route('admin.taqe.trabajos.index') }}" class="btn btn-outline-secondary btn-sm mt-2">
            <i class="fa fa-arrow-left"></i> Volver
        </a>
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

    <form action="{{ route('admin.taqe.trabajos.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="bv-form-card mb-4">
            <p class="bv-form-section-title"><i class="fa fa-info-circle"></i> Información básica</p>
            <div class="form-row">
                <div class="form-group col-lg-8">
                    <label for="titulo" class="bv-form-label">Título del trabajo <span class="text-danger">*</span></label>
                    <input type="text" name="titulo" id="titulo" class="form-control" value="{{ old('titulo') }}" required placeholder="Ej: Wankay de despedida en la fiesta de San Juan Bautista">
                </div>
                <div class="form-group col-lg-4">
                    <label for="categoria_id" class="bv-form-label">Categoría <span class="text-danger">*</span></label>
                    <select name="categoria_id" id="categoria_id" class="custom-select" required>
                        <option value="">Seleccione una categoría…</option>
                        @foreach ($categorias as $c)
                            <option value="{{ $c->id }}" @selected(old('categoria_id') == $c->id)>{{ $c->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-lg-6">
                    <label for="autores" class="bv-form-label">Autor(es) / Estudiante(s) <span class="text-danger">*</span></label>
                    <input type="text" name="autores" id="autores" class="form-control" value="{{ old('autores') }}" required placeholder="Ej: Juan Quispe Huamán, María Pérez">
                </div>
                <div class="form-group col-lg-3">
                    <label for="año_periodo" class="bv-form-label">Año o Periodo</label>
                    <input type="text" name="año_periodo" id="año_periodo" class="form-control" value="{{ old('año_periodo', date('Y')) }}" placeholder="Ej: 2024 o 2024-II">
                </div>
                <div class="form-group col-lg-3">
                    <label for="ciclo_carrera" class="bv-form-label">Especialidad / Ciclo</label>
                    <input type="text" name="ciclo_carrera" id="ciclo_carrera" class="form-control" value="{{ old('ciclo_carrera') }}" placeholder="Ej: EIB Primaria · Ciclo VII">
                </div>
                <div class="form-group col-lg-6">
                    <label for="formato_principal" class="bv-form-label">Formato principal <span class="text-danger">*</span></label>
                    <select name="formato_principal" id="formato_principal" class="custom-select" required>
                        @foreach ($formatos as $key => $f)
                            <option value="{{ $key }}" @selected(old('formato_principal', 'pdf') === $key)>{{ $f['nombre'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-lg-6">
                    <label for="idioma" class="bv-form-label">Lengua / Idioma <span class="text-danger">*</span></label>
                    <select name="idioma" id="idioma" class="custom-select" required>
                        @foreach ($idiomas as $k => $i)
                            <option value="{{ $k }}" @selected(old('idioma', 'castellano') === $k)>{{ $i['nombre'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-12">
                    <label for="descripcion" class="bv-form-label">Descripción o Resumen</label>
                    <textarea name="descripcion" id="descripcion" rows="4" class="form-control" placeholder="Describe brevemente de qué trata este trabajo, metodología, o contexto comunal…">{{ old('descripcion') }}</textarea>
                </div>
            </div>
        </div>

        <div class="bv-form-card mb-4">
            <p class="bv-form-section-title"><i class="fa fa-paperclip"></i> Recursos y Archivos Multimedia</p>
            <div class="form-row">
                <div class="form-group col-lg-6">
                    <label for="archivo_pdf" class="bv-form-label">Documento PDF (opcional, máx. 50MB)</label>
                    <div class="custom-file">
                        <input type="file" class="custom-file-input" id="archivo_pdf" name="archivo_pdf" accept="application/pdf">
                        <label class="custom-file-label" for="archivo_pdf" data-browse="Elegir">Seleccionar archivo PDF…</label>
                    </div>
                    <small class="form-text text-muted">Acepta archivos PDF con tesis, cartillas o informes.</small>
                </div>
                <div class="form-group col-lg-6">
                    <label for="archivo_audio" class="bv-form-label">Archivo de Audio (opcional, MP3, WAV, OGG)</label>
                    <div class="custom-file">
                        <input type="file" class="custom-file-input" id="archivo_audio" name="archivo_audio" accept="audio/*">
                        <label class="custom-file-label" for="archivo_audio" data-browse="Elegir">Seleccionar archivo de audio…</label>
                    </div>
                    <small class="form-text text-muted">Relatos, entrevistas comunitarias o cantos recopilados.</small>
                </div>
                <div class="form-group col-lg-6">
                    <label for="url_video" class="bv-form-label">Enlace de Video (YouTube o Google Drive)</label>
                    <input type="url" name="url_video" id="url_video" class="form-control" value="{{ old('url_video') }}" placeholder="https://www.youtube.com/watch?v=... o https://drive.google.com/file/d/.../view">
                    <small class="form-text text-muted">Se mostrará con reproductor integrado en la página del trabajo.</small>
                </div>
                <div class="form-group col-lg-6">
                    <label for="portada" class="bv-form-label">Imagen de Carátula / Portada (JPG, PNG, WebP)</label>
                    <div class="custom-file">
                        <input type="file" class="custom-file-input" id="portada" name="portada" accept="image/*">
                        <label class="custom-file-label" for="portada" data-browse="Elegir">Seleccionar carátula…</label>
                    </div>
                    <small class="form-text text-muted">Si se omite, se usará la imagen por defecto del repositorio.</small>
                </div>
            </div>
        </div>

        <div class="bv-form-card mb-4">
            <p class="bv-form-section-title"><i class="fa fa-sliders"></i> Opciones de publicación</p>
            <div class="form-row align-items-center">
                <div class="form-group col-md-6">
                    <label for="estado" class="bv-form-label">Estado</label>
                    <select name="estado" id="estado" class="custom-select">
                        <option value="publicado" @selected(old('estado', 'publicado') === 'publicado')>Publicado (Visible a todos)</option>
                        <option value="borrador" @selected(old('estado') === 'borrador')>Borrador (Oculto)</option>
                    </select>
                </div>
                <div class="form-group col-md-6 pt-md-4">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" id="destacado" name="destacado" value="1" @checked(old('destacado'))>
                        <label class="custom-control-label" for="destacado">Destacar este trabajo en la página principal</label>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2 mb-4">
            <a href="{{ route('admin.taqe.trabajos.index') }}" class="btn btn-secondary mr-2">Cancelar</a>
            <button type="submit" class="btn btn-primary"><i class="fa fa-save mr-1"></i> Guardar trabajo</button>
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
