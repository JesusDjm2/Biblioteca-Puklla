@extends('layouts.padre')
@section('titulo', '- Biblioteca virtual')
@section('contenido')
    <div class="fixed-image-container">
        <img src="{{ asset('img/FIlmadora-Puklla-Virtual-Fondo.webp') }}" alt="Fondo Pukllasunchis" loading="lazy" decoding="async">
        <div class="overlay"></div>
        <div class="centered-content">
            <h2>Videos</h2>
            <h1>ESPP Pukllasunchis</h1>
        </div>
    </div>
    <div class="container mt-4">
        <div class="row mt-4">
            <div class="col-lg-4 mt-4">
                <div class="custom-sidebar">
                    <h4 class="custom-sidebar-title">Secciones:</h4>
                    <ul class="custom-sidebar-menu">
                        @foreach ($categorias as $seccion)
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
                        @endforeach
                    </ul>
                </div>
            </div>
            <div class="col-lg-8">
                <div class="row">
                    <h2 class="titulo2Puklla mb-5">Últimas publicaciones</h2>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text btn btn-sm btn-primary" id="basic-addon1">Buscar</span>
                        </div>
                        <input type="text" id="search-input" placeholder="" class="form-control form-control-sm"
                            aria-label="Buscar videos por nombre" aria-describedby="basic-addon1" autocomplete="off">
                    </div>

                    <div id="videos-container" class="row">
                        @foreach ($videos as $video)
                            <div class="col-lg-6 p-3 video-item" data-nombre="{{ strtolower($video->nombre) }}">
                                <div class="cardVideos">
                                    @if ($video->youtube)
                                        <div class="ratio ratio-16x9 video-thumb-wrapper">
                                            <div class="video-thumbnail" data-youtube="{{ $video->youtube }}" role="button" tabindex="0" aria-label="Reproducir video: {{ $video->nombre }}">
                                                <img 
                                                    src="https://img.youtube.com/vi/{{ $video->youtube }}/hqdefault.jpg" 
                                                    alt="{{ $video->nombre }}"
                                                    loading="lazy"
                                                    decoding="async"
                                                    class="img-fluid"
                                                    width="480"
                                                    height="360"
                                                    onerror="this.style.display='none'; this.nextElementSibling.style.display='flex'; this.closest('.video-thumbnail').style.background='#333';"
                                                >
                                                <div class="play-overlay">
                                                    <i class="fa-brands fa-youtube"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <h6>{{ $video->nombre }}</h6>
                                        @if($video->descripcion)
                                            <p class="video-desc">{{ $video->descripcion }}</p>
                                        @endif
                                    @elseif ($video->drive)
                                        <div class="ratio ratio-16x9">
                                            <div class="video-thumbnail" data-drive="{{ $video->drive }}" role="button" tabindex="0" aria-label="Reproducir video: {{ $video->nombre }}">
                                                <img 
                                                    src="https://drive.google.com/thumbnail?id={{ $video->drive }}"
                                                    alt="{{ $video->nombre }}"
                                                    loading="lazy"
                                                    decoding="async"
                                                    class="img-fluid"
                                                    onerror="this.style.display='none'; this.nextElementSibling.style.display='flex'; this.closest('.video-thumbnail').style.background='#333';"
                                                >
                                                <div class="play-overlay">
                                                    <i class="fa-brands fa-google-drive"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <h6>{{ $video->nombre }}</h6>
                                        @if($video->descripcion)
                                            <p class="video-desc">{{ $video->descripcion }}</p>
                                        @endif
                                    @endif
                                </div>
                            </div>
                    @endforeach
                    </div>

                    <div id="no-results" class="text-center py-5" style="display:none;">
                        <i class="fa fa-search fa-3x text-muted mb-3"></i>
                        <p class="text-muted h5">No se encontraron videos con ese nombre.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @section('estilos')
    <style>
        .video-thumb-wrapper {
            position: relative;
            background: #2a2a2a;
        }
        
        .video-thumbnail {
            position: relative;
            width: 100%;
            height: 100%;
            cursor: pointer;
            overflow: hidden;
            border-radius: 3px;
        }
        
        .video-thumbnail img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
            transition: transform 0.4s ease;
        }
        
        .video-thumbnail:hover img {
            transform: scale(1.05);
        }
        
        .play-overlay {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 3.5rem;
            color: #ff0000;
            text-shadow: 0 2px 8px rgba(0,0,0,0.6);
            transition: transform 0.3s ease, opacity 0.3s ease;
            opacity: 0.9;
            pointer-events: none;
        }
        
        .video-thumbnail:hover .play-overlay {
            transform: translate(-50%, -50%) scale(1.1);
            opacity: 1;
        }
        
        .video-thumbnail[data-drive] .play-overlay {
            color: #4285F4;
        }
        
        .video-iframe {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border: 0;
            opacity: 0;
            animation: fadeInIframe 0.5s ease forwards;
        }
        
        @keyframes fadeInIframe {
            to { opacity: 1; }
        }
        
        .cardVideos h6 {
            padding-left: 0;
            padding-top: 0.9em;
            margin-bottom: 0.3em;
            font-size: 0.95rem;
        }
        
        .cardVideos .video-desc {
            font-size: 0.85rem;
            opacity: 0.85;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            padding-top: 0.2em;
        }
        
        .custom-sidebar {
            position: sticky;
            top: 6em;
        }
        
        .custom-sidebar-menu {
            max-height: 60vh;
            overflow-y: auto;
            scrollbar-width: thin;
            scrollbar-color: #c88e41 transparent;
        }
        
        .custom-sidebar-menu::-webkit-scrollbar {
            width: 4px;
        }
        
        .custom-sidebar-menu::-webkit-scrollbar-thumb {
            background: #c88e41;
            border-radius: 2px;
        }
        
        @media (max-width: 991.98px) {
            .custom-sidebar {
                position: relative;
                top: 0;
                margin-bottom: 1.5rem;
            }
            
            .custom-sidebar-menu {
                max-height: none;
                overflow-y: visible;
            }
            
            .play-overlay {
                font-size: 2.5rem;
            }
            
            .fixed-image-container {
                height: 40vh;
            }
            
            .fixed-image-container .centered-content h1 {
                font-size: 36px;
            }
            
            .fixed-image-container .centered-content h2 {
                font-size: 20px;
            }
        }
        
        @media (max-width: 575.98px) {
            .fixed-image-container {
                height: 35vh;
            }
            
            .fixed-image-container .centered-content h1 {
                font-size: 28px;
            }
            
            .fixed-image-container .centered-content h2 {
                font-size: 18px;
            }
            
            .play-overlay {
                font-size: 2rem;
            }
            
            .cardVideos {
                padding: 8px;
            }
        }
        
        .video-item {
            transition: opacity 0.3s ease, transform 0.3s ease;
        }
        
        .video-item.hidden {
            opacity: 0;
            transform: scale(0.95);
            pointer-events: none;
            position: absolute;
            visibility: hidden;
        }
    </style>
    @endsection

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('search-input');
            const videoItems = document.querySelectorAll('.video-item');
            const noResults = document.getElementById('no-results');

            // Normalizar string (quitar tildes)
            function normalizeString(str) {
                return str.normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLowerCase();
            }

            // Animación de placeholder
            const texts = [
                'Buscar videos institucionales...',
                'Método Montessori de educación en adolescentes...',
                'El docente como educador...'
            ];
            let textIndex = 0;
            let charIndex = 0;
            let isDeleting = false;
            let timeout;

            function typeEffect() {
                const currentText = texts[textIndex];
                if (!isDeleting) {
                    searchInput.placeholder = currentText.substring(0, charIndex + 1);
                    charIndex++;
                    if (charIndex === currentText.length) {
                        isDeleting = true;
                        timeout = setTimeout(typeEffect, 2500);
                        return;
                    }
                    timeout = setTimeout(typeEffect, 80);
                } else {
                    searchInput.placeholder = currentText.substring(0, charIndex - 1);
                    charIndex--;
                    if (charIndex === 0) {
                        isDeleting = false;
                        textIndex = (textIndex + 1) % texts.length;
                        timeout = setTimeout(typeEffect, 500);
                        return;
                    }
                    timeout = setTimeout(typeEffect, 40);
                }
            }
            timeout = setTimeout(typeEffect, 500);

            // Filtro en tiempo real
            searchInput.addEventListener('input', function() {
                const searchTerm = normalizeString(this.value.trim());
                let visibleCount = 0;

                videoItems.forEach(function(item) {
                    const nombre = item.getAttribute('data-nombre') || '';
                    const matches = searchTerm === '' || normalizeString(nombre).includes(searchTerm);
                    
                    if (matches) {
                        item.classList.remove('hidden');
                        item.style.position = '';
                        item.style.visibility = '';
                        visibleCount++;
                    } else {
                        item.classList.add('hidden');
                        item.style.position = 'absolute';
                        item.style.visibility = 'hidden';
                    }
                });

                if (noResults) {
                    noResults.style.display = (searchTerm !== '' && visibleCount === 0) ? 'block' : 'none';
                }
            });

            // Click en thumbnail para cargar iframe
            document.querySelectorAll('.video-thumbnail').forEach(function(thumb) {
                thumb.addEventListener('click', function() {
                    const youtubeId = this.getAttribute('data-youtube');
                    const driveId = this.getAttribute('data-drive');
                    
                    if (youtubeId) {
                        const iframe = document.createElement('iframe');
                        iframe.className = 'video-iframe';
                        iframe.src = 'https://www.youtube.com/embed/' + youtubeId + '?autoplay=1&rel=0';
                        iframe.title = 'YouTube video';
                        iframe.allow = 'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture';
                        iframe.allowFullscreen = true;
                        iframe.loading = 'lazy';
                        
                        this.innerHTML = '';
                        this.style.background = '#000';
                        this.appendChild(iframe);
                    } else if (driveId) {
                        const iframe = document.createElement('iframe');
                        iframe.className = 'video-iframe';
                        iframe.src = 'https://drive.google.com/file/d/' + driveId + '/preview';
                        iframe.title = 'Google Drive video';
                        iframe.allow = 'autoplay';
                        iframe.allowFullscreen = true;
                        
                        this.innerHTML = '';
                        this.style.background = '#000';
                        this.appendChild(iframe);
                    }
                });
                
                thumb.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter' || e.key === ' ') {
                        e.preventDefault();
                        this.click();
                    }
                });
            });
        });
    </script>

@endsection
