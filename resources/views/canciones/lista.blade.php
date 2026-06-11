@extends('layouts.padre')

@section('titulo', '- Lista de canciones Pukllasunchis')

@section('contenido')
    <div class="fixed-image-container">
        <img src="{{ asset('img/fondo/Fondo-musica-andina-Puklla.webp') }}" alt="Fondo Pukllasunchis" loading="lazy">
        <div class="overlay"></div>
        <div class="centered-content">
            <h2>CANCIONES</h2>
            <h1>ESPP Pukllasunchis</h1>
        </div>
    </div>
    <div class="container mt-4">
        <div class="row mt-4">
            <div class="col-lg-4 mt-4">
                <div class="custom-sidebar">
                    <h4 class="custom-sidebar-title">Categorías:</h4>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text btn btn-sm btn-primary" id="basic-addon1">Buscar</span>
                        </div>
                        <input type="text" id="search-input" placeholder="" class="form-control form-control-sm"
                            aria-label="Buscar audios por nombre o autor..." aria-describedby="basic-addon1">
                    </div>
                    <ul class="custom-sidebar-menu">
                        @foreach ($categorias as $seccion)
                            <li class="custom-sidebar-menu-item">
                                <a href="{{ route('videoscat.show', $seccion->url) }}" class="custom-sidebar-link">
                                    {{ $seccion->nombre }} <i class="fa fa-angle-right"></i>
                                </a>
                            </li>
                        @endforeach

                        {{-- @foreach ($categorias as $seccion)
                            <li class="custom-sidebar-menu-item">
                                <a href="{{ route('videoscat.show', $seccion->url) }}" class="custom-sidebar-link">                                   
                                    <span>
                                        {{ $seccion->nombre }}
                                        <span class="video-count">
                                            ({{ $seccion->videos_count }}
                                            {{ $seccion->videos_count === 1 ? 'video' : 'videos' }})
                                        </span>
                                    </span>
                                    <i class="fa fa-angle-right"></i>
                                </a>
                            </li>
                        @endforeach --}}
                    </ul>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="row">
                    <h2 class="titulo2Puklla mb-5">Últimas publicaciones</h2>
                    @foreach ($canciones as $cancion)
                        @if ($cancion->youtube)
                            <div class="col-lg-6 mb-4">
                                <div class="cardVideos">
                                    <div class="ratio ratio-16x9">
                                        <iframe loading="lazy" src="https://www.youtube.com/embed/{{ $cancion->youtube }}"
                                            title="YouTube video" allowfullscreen frameborder="0"></iframe>
                                    </div>                                    
                                </div>
                            </div>
                        @elseif($cancion->drive)
                            <div class="col-lg-6 mb-4">
                                <div class="card" style="border-radius: 12px;">
                                    <div class="card-body" style="background: #a76a18;border-radius: 12px;">
                                        <div class="row">
                                            <div class="col-7">
                                                <h6 class="text-white">{{ $cancion->nombre }}</h6>
                                                <p class="card-text mb-2 text-white">{{ $cancion->autor }}</p>
                                            </div>
                                            <div class="col-5 d-flex align-items-center justify-content-center">
                                                <img src="{{ asset('img/min/logo-drive.png') }}" width="50px">
                                            </div>
                                        </div>
                                        <div class="drive d-flex justify-content-center align-items-center"
                                            style="height: 135px; background:url('{{ asset('img/min/FONDO-DRIVE.webp') }}'); background-size: cover;">
                                            <iframe src="https://drive.google.com/file/d/{{ $cancion->drive }}/preview"
                                                loading="lazy" allowfullscreen width="100%" height="60"></iframe>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @elseif($cancion->spotify)
                            <div class="col-lg-6 mb-4">
                                <iframe style="border-radius:12px"
                                    src="https://open.spotify.com/embed/track/{{ $cancion->spotify }}" width="100%"
                                    height="302" frameBorder="0"
                                    allow="autoplay; clipboard-write; encrypted-media; fullscreen; picture-in-picture"
                                    loading="lazy">
                                </iframe>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    <script>
        //Tiperar texto en buscador:
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('search-input');
            const texts = [
                'Canciones de Pukllasunchis...',
                'Metodo montessori de educación en adolescentes...',
                'El docente como educador...'
            ];
            let textIndex = 0;
            let charIndex = 0;

            function typeText() {
                if (charIndex < texts[textIndex].length) {
                    searchInput.placeholder += texts[textIndex].charAt(charIndex);
                    charIndex++;
                    setTimeout(typeText, 100); // Velocidad de tipeo
                } else {
                    setTimeout(eraseText, 2000); // Tiempo antes de borrar el texto
                }
            }

            function eraseText() {
                if (charIndex > 0) {
                    searchInput.placeholder = searchInput.placeholder.slice(0, -1);
                    charIndex--;
                    setTimeout(eraseText, 50); // Velocidad de borrado
                } else {
                    textIndex = (textIndex + 1) % texts.length;
                    setTimeout(typeText, 500); // Tiempo antes de comenzar a tipear nuevamente
                }
            }

            setTimeout(typeText, 500); // Tiempo antes de comenzar la animación
        });
    </script>
@endsection
