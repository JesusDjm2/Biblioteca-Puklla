@extends('layouts.padre')
@section('titulo', '- Página de Inicio')
@section('contenido')
    <div class="fixed-image-container">
        <img src="{{ asset('img/biblioteca-muestra.webp') }}" alt="Logo Sisipedia" fetchpriority="high" decoding="async">
        <div class="overlay"></div>
        <div class="centered-content">
            <h2>Biblioteca Virtual </h2>
            <h1>Asociación Pukllasunchis</h1>
        </div>
    </div>
    <div class="container mt-4 contenedorContenido">
        <div class="row cardIndex">
            <div class="col-lg-4">
                <div class="contenedor">
                    <img src="{{ asset('img/min/libros-Pukllasunchis-min.webp') }}" alt="Libros Puklla" class="img-fluid"
                        loading="lazy" decoding="async" width="640" height="360">
                    <h2 class="position-absolute text-center text-white">Biblioteca</h2>
                    <a href="{{ route('libros') }}">Ver lista</a>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="contenedor">
                    <img src="{{ asset('img/min/formacion-continua-pukllasunchis-01.webp') }}" alt="Libros Puklla"
                        class="img-fluid" loading="lazy" decoding="async" width="640" height="360">
                    <h2 class="position-absolute text-center text-white">Videos Institucionales</h2>
                    <a href="{{ route('videos') }}">Ver lista</a>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="contenedor">
                    <img src="{{ asset('img/min/radio-Pukllasunchis.webp') }}" alt="Libros Puklla" class="img-fluid"
                        loading="lazy" decoding="async" width="640" height="360">
                    <h2 class="position-absolute text-center text-white">Sisichakunaq</h2>
                    <a href="">Ver lista</a>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="contenedor">
                    <img src="{{ asset('img/min/canciones-Pukllasunchis.webp') }}" alt="Libros Puklla" class="img-fluid"
                        loading="lazy" decoding="async" width="640" height="360">
                    <h2 class="position-absolute text-center text-white">Canciones</h2>
                    <a href="">Ver lista</a>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="contenedor">
                    <img src="{{ asset('img/min/libros-Pukllasunchis-min.webp') }}" alt="Libros Puklla" class="img-fluid"
                        loading="lazy" decoding="async" width="640" height="360">
                    <h2 class="position-absolute text-center text-white">Varios</h2>
                    <a href="">Ver lista</a>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="contenedor">
                    <img src="{{ asset('img/min/videos-otros-Pukllasunchis.webp') }}" alt="Libros Puklla" class="img-fluid"
                        loading="lazy" decoding="async" width="640" height="360">
                    <h2 class="position-absolute text-center text-white">Videos otros</h2>
                    <a href="">Ver lista</a>
                </div>
            </div>
        </div>
        <div class="row mb-4 mb-2 mt-5">
            <div class="col-lg-12">
                <h2 class="titulosDos">
                    Últimas publicaciones Sisichakunay <a href="https://www.facebook.com/Sisichakunaq" class="verMas"
                        target="_blank">→ Ver en FaceBook <i class="fab fa-facebook" style="font-size: 15px"></i></a>
                </h2>
            </div>
            <div class="col-lg-6">
                <iframe width="100%" height="400" loading="lazy" src="https://www.youtube.com/embed/NtcKGRUgnrs"
                    title="Pukllasunchis | Sisichakunaq Pukllaynin | Radio para niñas y niños andinos" frameborder="0"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                    referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
            </div>
            <div class="col-lg-6">
                <iframe width="100%" height="400" loading="lazy" src="https://www.youtube.com/embed/7wdm2XdChZI"
                    title="Pukllasunchis | Kawsay" frameborder="0"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                    referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
            </div>

        </div>
        <div class="row">
            <div class="col-lg-12 mt-4 mb-2">
                <h2 class="titulosDos">Últimas publicaciones Sisichakunay <a target="_blank"
                        href="https://open.spotify.com/show/21uqGdVqtet2iYx3XkhAVh" class="verMas">→ Ver en Spotify <i
                            class="fab fa-spotify" style="font-size: 15px"></i> </a></h2>
            </div>
            <div class="col-lg-3 p-3">
                <iframe style="border-radius:12px"
                    src="https://open.spotify.com/embed/episode/2jdkuFtfvjoEDqPryvBMAK?utm_source=generator&theme=0"
                    width="100%" height="352" frameBorder="0" allowfullscreen=""
                    allow="autoplay; clipboard-write; encrypted-media; fullscreen; picture-in-picture"
                    loading="lazy"></iframe>
            </div>
            <div class="col-lg-3 p-3">
                <iframe style="border-radius:12px"
                    src="https://open.spotify.com/embed/episode/59RAn2Ev1KkZy0dUBTJfKw?utm_source=generator" width="100%"
                    height="352" frameBorder="0" allowfullscreen=""
                    allow="autoplay; clipboard-write; encrypted-media; fullscreen; picture-in-picture"
                    loading="lazy"></iframe>
            </div>
            <div class="col-lg-3 p-3">
                <iframe style="border-radius:12px"
                    src="https://open.spotify.com/embed/episode/5IJQXlHveuE3iAP0dNvRoW?utm_source=generator&theme=0"
                    width="100%" height="352" frameBorder="0" allowfullscreen=""
                    allow="autoplay; clipboard-write; encrypted-media; fullscreen; picture-in-picture"
                    loading="lazy"></iframe>
            </div>
            <div class="col-lg-3 p-3">
                <iframe style="border-radius:12px"
                    src="https://open.spotify.com/embed/episode/6qdHD2sbWtUcDtpzZWh6Yb?utm_source=generator"
                    width="100%" height="352" frameBorder="0" allowfullscreen=""
                    allow="autoplay; clipboard-write; encrypted-media; fullscreen; picture-in-picture"
                    loading="lazy"></iframe>
            </div>
        </div>
        <div class="row mt-5">
            <div class="col-lg-12">
                <h2 class="mt-3 mb-4 titulosDos">Últimas publicaciones de textos <a href="{{ route('libros') }}"
                        class="verMas">→ Ver todos los textos</a></h2>
            </div>
            <div class="normalSize">
                <div class="row">
                    @foreach ($libros as $libro)
                        <div class="col-lg-3 mb-4 cardLibros" data-nombre="{{ $libro->nombre }}"
                            data-autor="{{ $libro->autor }}">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class='text-center'>{{ $libro->nombre }}</h5>
                                    <p class="text-center"><i style="font-size: 12px" class="fa fa-user"></i>
                                        {{ $libro->autor }}
                                    </p>
                                    @php
                                        $idDocumento = $libro->identificador;
                                        $urlVistaPrevia = "https://drive.google.com/thumbnail?id={$idDocumento}&sz=w600";
                                    @endphp
                                    <div class="contenedorLibro">
                                        <div class="contenedorImagen">
                                            <img class="vistaPrevia" src="{{ $urlVistaPrevia }}"
                                                alt="Vista previa del PDF" loading="lazy" decoding="async" width="300"
                                                height="424">
                                            <div class="overlay">
                                                <a href="https://drive.google.com/file/d/{{ $libro->identificador }}/view"
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
                </div>
            </div>
            <div class="responsive">
                <div class="row">
                    @foreach ($libros->take(5) as $libro)
                        <div class="col-lg-3 mb-4 cardLibros p-3" data-nombre="{{ $libro->nombre }}"
                            data-autor="{{ $libro->autor }}">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class='text-center'>{{ $libro->nombre }}</h5>
                                    <p class="text-center"><i style="font-size: 12px" class="fa fa-user"></i>
                                        {{ $libro->autor }}
                                    </p>
                                    @php
                                        $idDocumento = $libro->identificador;
                                        $urlVistaPrevia = "https://drive.google.com/thumbnail?id={$idDocumento}&sz=w600";
                                    @endphp
                                    <div class="contenedorLibro">
                                        <div class="contenedorImagen">
                                            <img class="vistaPrevia" src="{{ $urlVistaPrevia }}"
                                                alt="Vista previa del PDF" loading="lazy" decoding="async" width="300"
                                                height="424">
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
                </div>
            </div>
        </div>
        <div class="normalSize">
            <div class="row mt-4">
                <div class="col-lg-12">
                    <h2 class="titulosDos">
                        Últimos videos
                    </h2>
                </div>
                <!------Video en Drive--------->
                @foreach ($videos as $video)
                    <div class="col-lg-4 p-3">
                        <div class="cardVideos">
                            <div class="ratio ratio-16x9">
                                @if ($video->youtube)
                                    <iframe loading="lazy" src="https://www.youtube.com/embed/{{ $video->youtube }}"
                                        title="YouTube video" allowfullscreen frameborder="0"></iframe>
                                @elseif ($video->drive)
                                    <iframe loading="lazy"
                                        src="https://drive.google.com/file/d/{{ $video->drive }}/preview" allowfullscreen
                                        frameborder="0"></iframe>
                                @endif
                            </div>
                            <h6>{{ $video->nombre }}
                                <small class="float-end">
                                    <a href="{{ route('videoscat.show', $video->categoria->url) }}"
                                        style="text-decoration: none; color:rgb(79 172 255) !important">
                                        {{ $video->categoria->nombre }} <i class="fa fa-link fa-sm"></i>
                                    </a>
                                </small>
                            </h6>
                            <p>{{ $video->descripcion }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        <div class="responsive">
            <div class="row mt-4">
                <div class="col-lg-12">
                    <h2 class="titulosDos">
                        Últimos videos
                    </h2>
                </div>
                <!------Video en Drive--------->
                @foreach ($videos->take(4) as $video)
                    <div class="col-lg-4 p-3">
                        @if ($video->youtube)
                            <h4>{{ $video->nombre }}</h4>
                            <div class="video-thumbnail" style="position: relative; width: 100%; height: 261px;">
                                <img loading="lazy" src="https://img.youtube.com/vi/{{ $video->youtube }}/hqdefault.jpg"
                                    alt="Video Thumbnail" style="width: 100%; height: 100%;">
                                <a href="https://www.youtube.com/watch?v={{ $video->youtube }}" target="_blank"
                                    style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);">
                                    <i class="fa-brands fa-youtube" style="color: red; font-size:60px"></i>
                                </a>
                            </div>
                        @elseif($video->drive)
                            <h4>{{ $video->nombre }}</h4>
                            <iframe width="100%" height="261" loading="lazy"
                                src="https://drive.google.com/file/d/{{ $video->drive }}/preview"
                                class="embed-responsive-item" allowfullscreen></iframe>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
        {{-- <div class="container mt-4">
            <div class="row">
                <div class="col-lg-4">
                    <h2 style="font-size:20px" class="mt-3 mb-4 titulosDos">Publicaciones EESP Pukllasunchis<a
                            href="https://www.facebook.com/eesp.pukllasunchis" target="_blank" class="verMas">→ Ver Fan
                            page </a></h2>
                    <div class="facebook" style="width: 100%;">
                        <div class="fb-page" data-href="https://www.facebook.com/eesp.pukllasunchis" data-tabs="timeline"
                            data-width="" data-height="" data-small-header="false" data-adapt-container-width="true"
                            data-hide-cover="false" data-show-facepile="true">
                            <blockquote cite="https://www.facebook.com/eesp.pukllasunchis" class="fb-xfbml-parse-ignore">
                                <a href="https://www.facebook.com/eesp.pukllasunchis">EESP Pukllasunchis</a>
                            </blockquote>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <h2 style="font-size:20px" class="mt-3 mb-4 titulosDos">Publicaciones Colegio Pukllasunchis<a
                            href="https://www.facebook.com/colegio.pukllasunchis" target="_blank" class="verMas">→ Ver
                            Fan page </a></h2>
                    <div class="facebook" style="width: 100%;">
                        <div class="fb-page" data-href="https://www.facebook.com/colegio.pukllasunchis"
                            data-tabs="timeline" data-width="" data-height="" data-small-header="false"
                            data-adapt-container-width="true" data-hide-cover="false" data-show-facepile="true">
                            <blockquote cite="https://www.facebook.com/colegio.pukllasunchis"
                                class="fb-xfbml-parse-ignore"><a
                                    href="https://www.facebook.com/colegio.pukllasunchis">Colegio Pukllasunchis</a>
                            </blockquote>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <h2 style="font-size:20px" class="mt-3 mb-4 titulosDos">Publicaciones Centro Cultural<a
                            href="https://www.facebook.com/CentroCulturalPukllasunchis" target="_blank" class="verMas">→
                            Ver
                            Fan page </a></h2>
                    <div class="facebook" style="width: 100%;">
                        <div class="fb-page" data-href="https://www.facebook.com/CentroCulturalPukllasunchis"
                            data-tabs="timeline" data-width="" data-height="" data-small-header="false"
                            data-adapt-container-width="true" data-hide-cover="false" data-show-facepile="true">
                            <blockquote cite="https://www.facebook.com/CentroCulturalPukllasunchis"
                                class="fb-xfbml-parse-ignore"><a
                                    href="https://www.facebook.com/CentroCulturalPukllasunchis">Centro Cultural
                                    Pukllasunchis</a></blockquote>
                        </div>
                    </div>
                </div>
            </div>
        </div> --}}
    </div>
@endsection
