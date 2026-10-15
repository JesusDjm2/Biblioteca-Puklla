<!doctype html>
<html lang="en">

<head>
    <script>
        (function () {
            try {
                var t = localStorage.getItem('puklla-admin-theme') || 'light';
                document.documentElement.setAttribute('data-theme', t);
            } catch (e) {}
        })();
    </script>
    <title>@yield('titulo') Biblioteca EESP Pukllasunchis</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700,800,900" rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="{{ asset('admin/css/style.css') }}">
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('/img/logoiesp.ico') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('img/logoiesp.ico') }}">
    <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('img/Icono-Puklla.png') }}">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="{{ asset('css/biblioteca-crud.css') }}?v={{ @filemtime(public_path('css/biblioteca-crud.css')) }}">
    @stack('styles')
</head>

<body>
    <div class="wrapper d-flex align-items-stretch">
        @livewire('menu-admin')
        <div id="bvSidebarBackdrop"></div>
        <div id="content" class="bg-light">
            {{-- <header class="bv-topbar">
                <button type="button" id="bvSidebarToggle" class="bv-topbar__icon-btn" aria-label="Abrir menú"
                    aria-controls="sidebar" aria-expanded="false">
                    <i class="fa fa-bars"></i>
                </button>
                <span class="bv-topbar__title">@yield('titulo')</span>
                <div class="bv-topbar__spacer"></div>
            </header> --}}
            <div class="container-fluid bv-content-pad">
                @yield('contenido')
            </div>
        </div>
    </div>

    @include('partials.libro-preview-modal')

    <script src="{{ asset('admin/js/jquery.min.js') }}" defer></script>
    <script src="{{ asset('admin/js/popper.js') }}" defer></script>
    <script src="{{ asset('admin/js/bootstrap.min.js') }}" defer></script>
    <script src="{{ asset('admin/js/main.js') }}" defer></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js" defer></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrollTrigger.min.js"></script>
    <script src="{{ asset('js/admin-panel.js') }}?v={{ @filemtime(public_path('js/admin-panel.js')) }}" defer></script>
    @stack('scripts')
</body>

</html>
