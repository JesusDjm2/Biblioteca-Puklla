@extends('layouts.app')
@section('titulo', 'Importación Masiva de Videos')
@section('contenido')
    @include('partials.admin-breadcrumb', [
        'items' => [
            ['label' => 'Panel', 'url' => route('home')],
            ['label' => 'Videos', 'url' => route('videos.index')],
            ['label' => 'Importación Masiva', 'url' => null],
        ],
    ])
    <div class="row">
        <div class="col-md-12">
            <h3 class="text-info">Importación Masiva de Videos (Batch)
                <a href="{{ route('videos.index') }}" class="btn btn-danger btn-sm float-right">Volver</a>
            </h3>
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif
        </div>
        <div style="border-bottom: 1px dashed rgb(74, 113, 138); width: 100%;" class="mt-2"></div>
    </div>

    <div class="row mt-4">
        <!-- Formulario de Carga -->
        <div class="col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h5 class="card-title text-info font-weight-bold">Subir archivo de datos</h5>
                    <p class="text-muted small">Selecciona un archivo JSON, CSV o TXT con los videos organizados por categoría.</p>

                    <form action="{{ route('videos.import.process') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group my-4">
                            <label for="archivo" class="font-weight-bold">Archivo (JSON, CSV, TXT):</label>
                            <div class="custom-file">
                                <input type="file" name="archivo" id="archivo" class="custom-file-input" accept=".json,.csv,.txt" required>
                                <label class="custom-file-label" for="archivo" data-browse="Buscar">Elegir archivo...</label>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block rounded-pill">
                            <i class="fa fa-upload mr-2"></i> Procesar Importación
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Instrucciones y Plantillas -->
        <div class="col-md-6">
            <div class="card shadow-sm border-0 bg-light">
                <div class="card-body">
                    <h5 class="card-title text-dark font-weight-bold">Instrucciones de Formato</h5>

                    <ul class="nav nav-tabs" id="formatTabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active py-1" id="csv-tab" data-toggle="tab" href="#csvFormat" role="tab">CSV / Excel</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link py-1" id="json-tab" data-toggle="tab" href="#jsonFormat" role="tab">JSON</a>
                        </li>
                    </ul>

                    <div class="tab-content mt-3" id="formatTabsContent">
                        <!-- CSV -->
                        <div class="tab-pane fade show active" id="csvFormat" role="tabpanel">
                            <p class="small text-muted">El archivo CSV puede usar coma (<code>,</code>) o punto y coma (<code>;</code>) como delimitador. Debe incluir una cabecera con nombres similares a:</p>
                            <pre class="bg-dark text-white p-2 rounded small" style="font-size: 0.75rem;">categoria;nombre;descripcion;youtube;drive
Minkarikuy;Sesión 1 - Introducción;Video introductorio;M8-hpUElhQY;
Minkarikuy;Sesión 2 - Desarrollo;;https://youtu.be/abcdefghijk;</pre>
                            <small class="text-info d-block mt-2">
                                <i class="fa fa-info-circle"></i> <strong>Extractor de YouTube:</strong> Puedes pegar el ID del video o la URL completa (watch?v=, youtu.be/, embed/); el sistema extrae el ID automáticamente.
                            </small>
                        </div>

                        <!-- JSON -->
                        <div class="tab-pane fade" id="jsonFormat" role="tabpanel">
                            <p class="small text-muted">Estructura esperada del JSON:</p>
                            <pre class="bg-dark text-white p-2 rounded small" style="font-size: 0.75rem;">[
  {
    "categoria": "Minkarikuy",
    "nombre": "Sesión 1 - Introducción",
    "descripcion": "Video introductorio",
    "youtube": "M8-hpUElhQY",
    "drive": ""
  }
]</pre>
                            <small class="text-info d-block mt-2">
                                <i class="fa fa-info-circle"></i> Usa <strong>youtube</strong> o <strong>drive</strong> (al menos uno de los dos) para identificar el origen del video.
                            </small>
                        </div>
                    </div>

                    <hr>
                    <h6 class="font-weight-bold text-muted small">Notas importantes:</h6>
                    <ul class="text-muted small pl-3 mb-0">
                        <li>Si la <strong>Categoría</strong> no existe en el sistema actual, se creará de forma automática generando una URL amigable (slug).</li>
                        <li>Si el video ya existe (según el ID de YouTube o de Drive), se actualizarán su nombre, descripción y categoría.</li>
                        <li>El sistema limpia y extrae automáticamente el ID del video si se ingresa una URL completa de YouTube o Drive.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Script para actualizar el nombre del archivo seleccionado en el input de Bootstrap -->
    <script>
        document.addEventListener('change', function(e) {
            if (e.target.classList.contains('custom-file-input')) {
                const fileName = e.target.files[0] ? e.target.files[0].name : "Elegir archivo...";
                const label = e.target.nextElementSibling;
                label.innerText = fileName;
            }
        });
    </script>
@endsection
