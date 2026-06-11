@extends('layouts.padre')
@section('titulo', '- Página de Inicio')
@section('contenido')
    <div class="fixed-image-container">
        <img src="{{ asset('img/biblioteca-muestra.jpg') }}" alt="Imagen Fija">
        <div class="overlay"></div>
        <div class="centered-content">
            <h2>EESP Pukllasunchis</h2>
            <h1>Resultados de la busqueda: <br>"{{ $query }}"</h1>
            @if (!$hasResults)
                <p>
                    Sin resultados.
                </p>
            @else
                @php
                    $resultados = [];

                    if ($librosCount > 0) {
                        $resultados[] = $librosCount . ' ' . ($librosCount === 1 ? 'Texto' : 'Textos');
                    }

                    if ($videosCount > 0) {
                        $resultados[] = $videosCount . ' ' . ($videosCount === 1 ? 'Video' : 'Videos');
                    }

                    if ($cancionesCount > 0) {
                        $resultados[] = $cancionesCount . ' ' . ($cancionesCount === 1 ? 'Canción' : 'Canciones');
                    }
                @endphp

                Se encontraron:
                {{ implode(', ', $resultados) }}
                </p>
            @endif
        </div>
    </div>
    <div class="container mt-4 contenedorContenido">
        @if (!$hasResults)
            <div class="row">
                <div class="col-lg-12">
                    <p>No se encontraron resultados para <strong class="text-success">"{{ $query }}"</strong>. Por
                        favor, intenta con otra búsqueda.</p>
                    <form action="{{ route('busqueda') }}" method="GET" class="search-form">
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" name="query"
                                placeholder="Escriba una nueva consulta..." aria-label="Recipient's username"
                                aria-describedby="basic-addon2">
                            <div class="input-group-append">
                                <button class="btn btn-outline-secondary" type="submit">Buscar</button>
                            </div>
                        </div>
                    </form>
                    
                </div>
            </div>
        @endif
        <div class="row mb-2">
            @if ($videos->isNotEmpty())
                <div class="col-lg-12">
                    <h3>Resultados en Videos</h3>
                </div>
                @foreach ($videos as $video)
                    <div class="col-lg-4 p-3 mb-2">
                        @if ($video->youtube)
                            <div class="cardVideos">
                                <div class="video-thumbnail" style="position: relative; width: 100%; height: 220px;">
                                    <img loading="lazy" src="https://img.youtube.com/vi/{{ $video->youtube }}/hqdefault.jpg"
                                        alt="Video Thumbnail" style="width: 100%; height: 100%;">
                                    <a href="https://www.youtube.com/watch?v={{ $video->youtube }}" target="_blank"
                                        style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);">
                                        <i class="fa-brands fa-youtube" style="color: red; font-size:60px"></i>
                                    </a>
                                </div>
                                <h6>{{ $video->nombre }}</h6>
                                <p>{{ $video->descripcion }}</p>
                            </div>
                        @elseif($video->drive)
                            <div class="cardVideos">
                                <iframe width="100%" height="220" loading="lazy"
                                    src="https://drive.google.com/file/d/{{ $video->drive }}/preview"
                                    class="embed-responsive-item" allowfullscreen></iframe>
                                <h6>{{ $video->nombre }}</h6>
                                <p>{{ $video->descripcion }}</p>
                            </div>
                        @endif
                    </div>
                @endforeach
            @endif
            @if ($libros->isNotEmpty())
                <div class="col-lg-12 mb-3">
                    <h3>Resultados en libros:</h3>
                </div>
                @foreach ($libros as $libro)
                    <div class="col-lg-3 mb-4 cardLibros" data-nombre="{{ $libro->nombre }}"
                        data-autor="{{ $libro->autor }}">
                        <div class="card">
                            <div class="card-body">
                                <h5 class='text-center'>{{ $libro->nombre }}</h5>
                                <p class="text-center"><i style="font-size: 12px" class="fa fa-user"></i>
                                    {{ $libro->autor }}</p>
                                @php
                                    $idDocumento = $libro->identificador;
                                    $urlVistaPrevia = "https://drive.google.com/thumbnail?id={$idDocumento}&sz=w1000";
                                @endphp
                                <div class="contenedorLibro">
                                    <div class="contenedorImagen">
                                        <img class="vistaPrevia" src="{{ $urlVistaPrevia }}" alt="Vista previa del PDF">
                                        <div class="overlay">
                                            <a href="https://drive.google.com/file/d/{{ $libro->identificador }}/preview"
                                                target="_blank">
                                                <button class="btnAbrirPDF">Abrir PDF</button>
                                            </a>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                @endforeach
            @endif
            @if ($canciones->isNotEmpty())
                <div class="col-lg-12 mb-2">
                    <h3>Resultados de busqueda para Canciones:</h3>
                </div>
                @foreach ($canciones as $cancion)
                    @if ($cancion->youtube)
                        <div class="col-lg-4 mb-4">
                            <div class="card">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-7">
                                            <h5 class="card-title">{{ $cancion->nombre }}</h5>
                                            <p class="card-text mb-2">{{ $cancion->autor }}</p>
                                        </div>
                                        <div class="col-5 d-flex align-items-center justify-content-center">
                                            <img src="{{ asset('img/min/Youtube-Logo.webp') }}" loading="lazy"
                                                width="110px">
                                        </div>
                                    </div>
                                    <div class="video-thumbnail" style="position: relative; width: 100%; height: 220px;">
                                        <img loading="lazy"
                                            src="https://img.youtube.com/vi/{{ $cancion->youtube }}/hqdefault.jpg"
                                            alt="Video Thumbnail" style="width: 100%; height: 100%;">
                                        <a href="https://www.youtube.com/watch?v={{ $cancion->youtube }}" target="_blank"
                                            style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);">
                                            <i class="fa-brands fa-youtube" style="color: red; font-size:60px"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @elseif($cancion->drive)
                        <div class="col-lg-4 mb-4">
                            <div class="card">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-7">
                                            <h5 class="card-title">{{ $cancion->nombre }}</h5>
                                            <p class="card-text mb-2">{{ $cancion->autor }}</p>
                                        </div>
                                        <div class="col-5 d-flex align-items-center justify-content-center">
                                            <img src="{{ asset('img/min/google-drive-min.webp') }}" width="88px"
                                                alt="">
                                        </div>
                                    </div>
                                    <div class="drive d-flex justify-content-center align-items-center"
                                        style="height: 135px; background:url('{{ asset('img/min/FONDO-DRIVE.webp') }}'); background-size: cover;">
                                        <iframe src="https://drive.google.com/file/d/{{ $cancion->drive }}/preview"
                                            loading="lazy" allowfullscreen width="100%" height="80"></iframe>
                                    </div>

                                </div>
                            </div>
                        </div>
                    @elseif($cancion->spotify)
                        <div class="col-lg-4 mb-4">
                            <iframe style="border-radius:12px"
                                src="https://open.spotify.com/embed/track/{{ $cancion->spotify }}" width="100%"
                                height="302" frameBorder="0"
                                allow="autoplay; clipboard-write; encrypted-media; fullscreen; picture-in-picture"
                                loading="lazy">
                            </iframe>
                        </div>
                    @endif
                @endforeach
            @endif
        </div>
    </div>
@endsection
