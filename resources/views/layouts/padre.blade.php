<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Puklla Virtual @yield('titulo')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="{{ asset('css/estilos.css') }}">
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('img/Icono-Puklla.png') }}">
    <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('img/Icono-Puklla.png') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"
        media="print" onload="this.media='all'">
    <noscript>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    </noscript>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=EB+Garamond:ital,wght@0,400..800;1,400..800&display=swap"
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

</body>

</html>
