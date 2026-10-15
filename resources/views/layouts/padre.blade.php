<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Puklla Virtual @yield('titulo')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="{{ asset('css/estilos.css') }}?v={{ @filemtime(public_path('css/estilos.css')) }}">
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('img/Icono-Puklla.png') }}">
    <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('img/Icono-Puklla.png') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"
        media="print" onload="this.media='all'">
    <noscript>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    </noscript>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=EB+Garamond:ital,wght@0,400..800;1,400..800&family=Lato:ital,wght@0,300;0,400;0,700;0,900;1,400&display=swap"
        rel="stylesheet">
    @yield('estilos')
</head>

<body style="background: #f2f2f2">

    {{-- Loader de página --}}
    <div id="puklla-loader" role="status" aria-label="Cargando">
        <div class="puklla-loader__box">
            <div class="puklla-loader__track"><div class="puklla-loader__bar"></div></div>
            <p class="puklla-loader__label">Biblioteca Pukllasunchis</p>
        </div>
    </div>
    <script>
    window.addEventListener('load', function () {
        var l = document.getElementById('puklla-loader');
        if (!l) return;
        setTimeout(function () {
            l.classList.add('out');
            setTimeout(function () { l.remove(); }, 500);
        }, 180);
    });
    </script>

    @livewire('menu')

    <main class="main-content">
        @yield('contenido')
    </main>

    @livewire('foot')
    <div id="fb-root"></div>
    <script async defer crossorigin="anonymous"
        src="https://connect.facebook.net/es_LA/sdk.js#xfbml=1&version=v22.0&appId=366341694202025"></script>
    {{-- Sitio público: sin jQuery; Bootstrap 5 no lo requiere --}}
    <script defer src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>

    @include('partials.libro-preview-modal')

    {{-- Botón Subir al Top --}}
    <button id="btn-top" class="btn-top" aria-label="Subir al inicio" title="Volver arriba">
        <i class="fas fa-chevron-up"></i>
    </button>

    <style>
        .btn-top {
            position: fixed;
            bottom: 30px;
            right: 30px;
            z-index: 9999;
            width: 48px;
            height: 48px;
            border-radius: 50%;
            border: none;
            background: linear-gradient(135deg, #cb9042 0%, #a06e2e 100%);
            color: #fff;
            font-size: 1.2rem;
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(203,144,66,.35);
            opacity: 0;
            visibility: hidden;
            transform: translateY(20px) scale(0.9);
            transition: opacity .3s ease, transform .3s ease, visibility .3s ease, box-shadow .3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .btn-top.is-visible {
            opacity: 1;
            visibility: visible;
            transform: translateY(0) scale(1);
        }
        .btn-top:hover {
            background: linear-gradient(135deg, #dba04d 0%, #b87d34 100%);
            box-shadow: 0 6px 25px rgba(203,144,66,.5);
            transform: translateY(-3px) scale(1.05);
        }
        .btn-top:active {
            transform: translateY(1px) scale(0.95);
        }
        .btn-top i {
            transition: transform .3s ease;
        }
        .btn-top:hover i {
            transform: translateY(-2px);
        }

        @media (max-width: 575.98px) {
            .btn-top {
                bottom: 20px;
                right: 20px;
                width: 42px;
                height: 42px;
                font-size: 1rem;
            }
        }
    </style>

    <script>
    (function() {
        var btn = document.getElementById('btn-top');
        if (!btn) return;

        var scrollThreshold = 400;
        var isVisible = false;

        function checkScroll() {
            var scrollY = window.scrollY || window.pageYOffset;
            if (scrollY > scrollThreshold && !isVisible) {
                isVisible = true;
                btn.classList.add('is-visible');
            } else if (scrollY <= scrollThreshold && isVisible) {
                isVisible = false;
                btn.classList.remove('is-visible');
            }
        }

        // Throttle para rendimiento
        var ticking = false;
        window.addEventListener('scroll', function() {
            if (!ticking) {
                window.requestAnimationFrame(function() {
                    checkScroll();
                    ticking = false;
                });
                ticking = true;
            }
        }, { passive: true });

        // Scroll suave al inicio
        btn.addEventListener('click', function() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });

        // Verificar estado inicial
        checkScroll();
    })();
    </script>

</body>

</html>
