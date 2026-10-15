<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión · Biblioteca Virtual Pukllasunchis</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=EB+Garamond:ital,wght@0,400..800;1,400..800&family=Lato:ital,wght@0,300;0,400;0,700;0,900;1,400&display=swap"
        rel="stylesheet">
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('img/Icono-Puklla.png') }}">
    <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('img/Icono-Puklla.png') }}">
    <style>
        :root {
            --pk-accent: #cb9042;
            --pk-accent-hover: #dba04d;
            --pk-text: #f6f1ea;
            --pk-text-muted: rgba(246, 241, 234, 0.62);
            --pk-card-bg: rgba(20, 16, 12, 0.62);
            --pk-card-border: rgba(255, 255, 255, 0.12);
            --pk-input-bg: rgba(255, 255, 255, 0.07);
            --pk-input-border: rgba(255, 255, 255, 0.18);
            --pk-ease: cubic-bezier(0.4, 0, 0.2, 1);
        }

        * { box-sizing: border-box; }

        html, body {
            height: 100%;
            margin: 0;
            font-family: "Lato", system-ui, -apple-system, sans-serif;
        }

        .pk-login {
            position: relative;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            padding: 2rem 1.25rem;
        }

        .pk-login__bg {
            position: fixed;
            inset: 0;
            z-index: -2;
            overflow: hidden;
        }

        .pk-login__bg img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transform: scale(1.02);
            will-change: transform;
        }

        .pk-login__overlay {
            position: absolute;
            inset: 0;
            background:
                radial-gradient(circle at 50% 20%, rgba(20, 16, 12, 0.35), rgba(10, 8, 6, 0.82) 72%),
                linear-gradient(180deg, rgba(10, 8, 6, 0.55), rgba(10, 8, 6, 0.78));
        }

        .pk-login__content {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 400px;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }

        .pk-login__logo {
            width: 132px;
            max-width: 55vw;
            margin-bottom: 1.1rem;
        }

        .pk-login__title {
            font-family: "EB Garamond", serif;
            font-weight: 600;
            font-size: 1.9rem;
            color: var(--pk-text);
            margin: 0 0 0.35rem;
        }

        .pk-login__subtitle {
            font-size: 0.85rem;
            letter-spacing: 0.03em;
            color: var(--pk-text-muted);
            margin: 0 0 1.75rem;
        }

        .pk-login__card {
            width: 100%;
            background: var(--pk-card-bg);
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            border: 1px solid var(--pk-card-border);
            border-radius: 16px;
            padding: 1.85rem 1.75rem;
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.4);
        }

        .pk-login__alert {
            display: flex;
            align-items: center;
            gap: 0.55rem;
            background: rgba(220, 53, 69, 0.18);
            border: 1px solid rgba(220, 53, 69, 0.4);
            color: #ffb3ba;
            border-radius: 8px;
            padding: 0.6rem 0.85rem;
            font-size: 0.82rem;
            margin-bottom: 1.2rem;
            text-align: left;
        }

        .pk-field {
            text-align: left;
            margin-bottom: 1.15rem;
        }

        .pk-field label {
            display: block;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.07em;
            color: var(--pk-text-muted);
            margin-bottom: 0.4rem;
        }

        .pk-input {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            background: var(--pk-input-bg);
            border: 1px solid var(--pk-input-border);
            border-radius: 10px;
            padding: 0 0.85rem;
            transition: border-color 0.2s var(--pk-ease), box-shadow 0.2s var(--pk-ease);
        }

        .pk-input:focus-within {
            border-color: var(--pk-accent);
            box-shadow: 0 0 0 3px rgba(203, 144, 66, 0.22);
        }

        .pk-input i.fa {
            color: var(--pk-text-muted);
            font-size: 0.9rem;
            flex-shrink: 0;
        }

        .pk-input input {
            flex: 1;
            min-width: 0;
            background: transparent;
            border: 0;
            outline: none;
            color: var(--pk-text);
            font-size: 0.92rem;
            padding: 0.7rem 0;
        }

        .pk-input input::placeholder { color: rgba(246, 241, 234, 0.35); }

        .pk-input__toggle {
            background: transparent;
            border: 0;
            color: var(--pk-text-muted);
            cursor: pointer;
            padding: 0.3rem;
            flex-shrink: 0;
        }

        .pk-input__toggle:hover { color: var(--pk-accent); }

        .pk-field__error {
            display: block;
            margin-top: 0.4rem;
            font-size: 0.76rem;
            color: #ff9aa3;
        }

        .pk-login__row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.75rem;
            margin-bottom: 1.4rem;
            flex-wrap: wrap;
        }

        .pk-checkbox {
            display: flex;
            align-items: center;
            gap: 0.45rem;
            font-size: 0.8rem;
            color: var(--pk-text-muted);
            cursor: pointer;
            user-select: none;
        }

        .pk-checkbox input { accent-color: var(--pk-accent); }

        .pk-login__forgot {
            font-size: 0.8rem;
            color: var(--pk-accent);
            text-decoration: none;
        }

        .pk-login__forgot:hover { color: var(--pk-accent-hover); text-decoration: underline; }

        .pk-login__submit {
            width: 100%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            background: var(--pk-accent);
            color: #201a14;
            border: 0;
            border-radius: 999px;
            padding: 0.75rem 1rem;
            font-size: 0.92rem;
            font-weight: 700;
            cursor: pointer;
            transition: background 0.18s var(--pk-ease), transform 0.18s var(--pk-ease);
        }

        .pk-login__submit:hover { background: var(--pk-accent-hover); transform: translateY(-1px); }
        .pk-login__submit:active { transform: translateY(0); }

        .pk-login__back {
            margin-top: 1.5rem;
            font-size: 0.82rem;
            color: var(--pk-text-muted);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
        }

        .pk-login__back:hover { color: var(--pk-text); }

        @media (max-width: 420px) {
            .pk-login__card { padding: 1.5rem 1.25rem; border-radius: 14px; }
            .pk-login__title { font-size: 1.6rem; }
        }

        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after { animation-duration: 0.001ms !important; transition-duration: 0.001ms !important; }
        }
    </style>
</head>

<body>
    <div class="pk-login">
        <div class="pk-login__bg">
            <img id="pkLoginBg" src="{{ asset('img/fondo/tramites-2026.webp') }}" alt="" fetchpriority="high"
                decoding="async">
            <div class="pk-login__overlay"></div>
        </div>

        <div class="pk-login__content">
            <img src="{{ asset('img/Logo-Pukllasunchis-blanco.png') }}" alt="Logo Pukllasunchis"
                class="pk-login__logo" id="pkLoginLogo">
            <h1 class="pk-login__title" id="pkLoginTitle">Biblioteca Virtual</h1>
            <p class="pk-login__subtitle" id="pkLoginSubtitle">ESPP Pukllasunchis · Panel administrativo</p>

            <div class="pk-login__card" id="pkLoginCard">
                @if ($errors->any())
                    <div class="pk-login__alert">
                        <i class="fa fa-circle-exclamation"></i>
                        <span>{{ $errors->first() }}</span>
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <div class="pk-field pk-anim">
                        <label for="email">{{ __('Correo electrónico') }}</label>
                        <div class="pk-input">
                            <i class="fa fa-envelope"></i>
                            <input id="email" type="email" name="email" value="{{ old('email') }}" required
                                autocomplete="email" autofocus placeholder="tucorreo@ejemplo.com">
                        </div>
                        @error('email')
                            <span class="pk-field__error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="pk-field pk-anim">
                        <label for="password">{{ __('Contraseña') }}</label>
                        <div class="pk-input">
                            <i class="fa fa-lock"></i>
                            <input id="password" type="password" name="password" required
                                autocomplete="current-password" placeholder="••••••••">
                            <button type="button" class="pk-input__toggle" onclick="togglePasswordVisibility()"
                                aria-label="Mostrar u ocultar contraseña">
                                <i class="fa fa-eye" id="icon-password"></i>
                            </button>
                        </div>
                        @error('password')
                            <span class="pk-field__error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="pk-login__row pk-anim">
                        <label class="pk-checkbox">
                            <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                            <span>{{ __('Recordarme') }}</span>
                        </label>
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="pk-login__forgot">¿Olvidaste tu
                                contraseña?</a>
                        @endif
                    </div>

                    <button type="submit" class="pk-login__submit pk-anim">
                        {{ __('Ingresar') }} <i class="fa fa-arrow-right"></i>
                    </button>
                </form>
            </div>

            <a href="{{ route('index') }}" class="pk-login__back"><i class="fa fa-arrow-left"></i> Volver al
                sitio</a>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>
    <script>
        function togglePasswordVisibility() {
            const passwordInput = document.getElementById('password');
            const icon = document.getElementById('icon-password');
            const isPassword = passwordInput.type === 'password';
            passwordInput.type = isPassword ? 'text' : 'password';
            icon.classList.toggle('fa-eye');
            icon.classList.toggle('fa-eye-slash');
        }

        (function () {
            var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
            if (reduceMotion || typeof gsap === 'undefined') return;

            gsap.to('#pkLoginBg', { scale: 1.12, duration: 22, ease: 'sine.inOut', repeat: -1, yoyo: true });

            var tl = gsap.timeline({ defaults: { ease: 'power2.out' } });
            tl.from('#pkLoginLogo', { y: -16, opacity: 0, duration: 0.55 })
                .from(['#pkLoginTitle', '#pkLoginSubtitle'], { y: 12, opacity: 0, duration: 0.45, stagger: 0.08 }, '-=0.3')
                .from('#pkLoginCard', { y: 24, opacity: 0, duration: 0.5 }, '-=0.2')
                .from('.pk-anim', { y: 10, opacity: 0, duration: 0.35, stagger: 0.08 }, '-=0.25')
                .from('.pk-login__back', { opacity: 0, duration: 0.4 }, '-=0.1');

            @if ($errors->any())
                gsap.fromTo('#pkLoginCard',
                    { x: -10 },
                    { x: 0, duration: 0.08, repeat: 5, yoyo: true, delay: 0.9, ease: 'power1.inOut' });
            @endif
        })();
    </script>
</body>

</html>
