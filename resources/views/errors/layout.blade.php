<!doctype html>
<html lang="es">

<head>
    <script>
        (function () {
            try {
                var t = localStorage.getItem('puklla-admin-theme') || 'light';
                document.documentElement.setAttribute('data-theme', t);
            } catch (e) {}
        })();
    </script>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>@yield('titulo', 'Error') · Biblioteca EESP Pukllasunchis</title>
    <link href="https://fonts.googleapis.com/css?family=Poppins:400,500,600,700,800" rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('/img/logoiesp.ico') }}">
    <link rel="stylesheet" href="{{ asset('css/biblioteca-crud.css') }}?v={{ @filemtime(public_path('css/biblioteca-crud.css')) }}">
    <style>
        html, body {
            height: 100%;
        }
        body {
            margin: 0;
            font-family: 'Poppins', sans-serif;
            background: var(--bv-bg);
            color: var(--bv-text);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
        }
        .bv-error-card {
            width: 100%;
            max-width: 460px;
            background: var(--bv-surface);
            border: 1px solid var(--bv-border);
            border-radius: 16px;
            box-shadow: var(--bv-shadow-lg);
            padding: 2.5rem 2.25rem;
            text-align: center;
        }
        .bv-error-icon {
            width: 76px;
            height: 76px;
            margin: 0 auto 1.25rem;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            background: var(--tone-soft, var(--bv-accent-soft));
            color: var(--tone, var(--bv-accent));
        }
        .bv-error-code {
            font-size: 0.8rem;
            font-weight: 700;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: var(--tone, var(--bv-accent));
            margin-bottom: 0.4rem;
        }
        .bv-error-title {
            font-size: 1.3rem;
            font-weight: 700;
            margin-bottom: 0.6rem;
            color: var(--bv-text);
        }
        .bv-error-message {
            font-size: 0.9rem;
            color: var(--bv-text-muted);
            line-height: 1.6;
            margin-bottom: 1.75rem;
        }
        .bv-error-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 0.6rem;
            justify-content: center;
        }
        .bv-error-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            padding: 0.6rem 1.15rem;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.875rem;
            border: 1px solid transparent;
            cursor: pointer;
            text-decoration: none;
            transition: background 0.15s, border-color 0.15s, color 0.15s, transform 0.1s, filter 0.15s;
        }
        .bv-error-btn:active { transform: scale(0.97); }
        .bv-error-btn:hover { text-decoration: none; }
        .bv-error-btn--primary {
            background: var(--tone, var(--bv-accent));
            color: #fff;
        }
        .bv-error-btn--primary:hover { filter: brightness(0.93); color: #fff; }
        .bv-error-btn--ghost {
            background: var(--bv-surface-2);
            color: var(--bv-text);
            border-color: var(--bv-border);
        }
        .bv-error-btn--ghost:hover { background: var(--bv-border); color: var(--bv-text); }
        .bv-error-footer {
            margin-top: 1.75rem;
            font-size: 0.72rem;
            color: var(--bv-text-muted);
            opacity: 0.75;
        }
        .bv-error-card.is-danger { --tone: var(--bv-danger); --tone-soft: var(--bv-danger-soft); }
        .bv-error-card.is-warning { --tone: #b06a1a; --tone-soft: rgba(203, 144, 66, 0.16); }
        :root[data-theme="dark"] .bv-error-card.is-warning { --tone: #e0a355; }
    </style>
    @yield('estilos')
</head>

<body>
    <div class="bv-error-card @yield('tono')">
        <div class="bv-error-icon"><i class="fa @yield('icono', 'fa-exclamation-circle')"></i></div>
        <div class="bv-error-code">Error @yield('codigo')</div>
        <h1 class="bv-error-title">@yield('titulo', 'Ha ocurrido un error')</h1>
        <p class="bv-error-message">@yield('mensaje')</p>
        <div class="bv-error-actions">
            @yield('acciones')
        </div>
        <p class="bv-error-footer">Biblioteca EESP Pukllasunchis</p>
    </div>
</body>

</html>
