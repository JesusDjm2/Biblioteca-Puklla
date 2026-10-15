(function () {
    'use strict';

    var THEME_KEY = 'puklla-admin-theme';
    var root = document.documentElement;
    var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    /* ---- Tema claro / oscuro ---- */
    var themeToggle = document.getElementById('bvThemeToggle');
    if (themeToggle) {
        themeToggle.addEventListener('click', function () {
            var next = root.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
            root.setAttribute('data-theme', next);
            try { localStorage.setItem(THEME_KEY, next); } catch (e) {}
        });
    }

    /* ---- Año en el pie del sidebar ---- */
    var yearEl = document.getElementById('bvSidebarYear');
    if (yearEl) { yearEl.textContent = new Date().getFullYear(); }

    /* ---- Sidebar móvil: apertura/cierre con backdrop ---- */
    var sidebar = document.getElementById('sidebar');
    var sidebarToggle = document.getElementById('bvSidebarToggle');
    var backdrop = document.getElementById('bvSidebarBackdrop');

    function isMobile() {
        return window.matchMedia('(max-width: 991.98px)').matches;
    }

    function closeSidebarMobile() {
        if (!sidebar) return;
        sidebar.classList.remove('active');
        if (backdrop) backdrop.classList.remove('is-visible');
        document.body.classList.remove('bv-no-scroll');
        if (sidebarToggle) sidebarToggle.setAttribute('aria-expanded', 'false');
    }

    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', function () {
            sidebar.classList.toggle('active');
            if (isMobile()) {
                var shown = sidebar.classList.contains('active');
                if (backdrop) backdrop.classList.toggle('is-visible', shown);
                document.body.classList.toggle('bv-no-scroll', shown);
                sidebarToggle.setAttribute('aria-expanded', shown ? 'true' : 'false');
            }
        });
    }

    if (backdrop) {
        backdrop.addEventListener('click', closeSidebarMobile);
    }

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && isMobile()) closeSidebarMobile();
    });

    /* ---- Confirmación propia (reemplaza confirm()/alert() nativos y SweetAlert) ----
       Uso: bvConfirm({ titulo, mensaje, tono: 'accent'|'danger'|'warning', icono: 'fa-trash', textoConfirmar, textoCancelar })
            .then(function (ok) { if (ok) { ... } });
       Cada llamada crea su propio backdrop (se destruye al cerrar), así que
       pueden coexistir varias instancias sin pisarse. */
    window.bvConfirm = function (opts) {
        opts = opts || {};
        return new Promise(function (resolve) {
            var backdrop = document.createElement('div');
            backdrop.className = 'bv-confirm-backdrop';
            backdrop.innerHTML =
                '<div class="bv-confirm-modal" role="alertdialog" aria-modal="true">' +
                    '<div class="bv-confirm-icon"><i class="fa"></i></div>' +
                    '<div class="bv-confirm-title"></div>' +
                    '<div class="bv-confirm-message"></div>' +
                    '<div class="bv-confirm-actions">' +
                        '<button type="button" class="bv-confirm-btn bv-confirm-btn--cancel"></button>' +
                        '<button type="button" class="bv-confirm-btn bv-confirm-btn--confirm"></button>' +
                    '</div>' +
                '</div>';
            document.body.appendChild(backdrop);

            var modal = backdrop.querySelector('.bv-confirm-modal');
            var icon = backdrop.querySelector('.bv-confirm-icon i');
            var title = backdrop.querySelector('.bv-confirm-title');
            var message = backdrop.querySelector('.bv-confirm-message');
            var btnCancel = backdrop.querySelector('.bv-confirm-btn--cancel');
            var btnConfirm = backdrop.querySelector('.bv-confirm-btn--confirm');

            modal.classList.add('is-' + (opts.tono || 'accent'));
            icon.className = 'fa ' + (opts.icono || 'fa-question-circle');
            title.textContent = opts.titulo || '¿Estás seguro?';
            message.textContent = opts.mensaje || '';
            btnCancel.textContent = opts.textoCancelar || 'Cancelar';
            btnConfirm.textContent = opts.textoConfirmar || 'Confirmar';

            var closed = false;
            function close(result) {
                if (closed) return;
                closed = true;
                backdrop.classList.remove('is-open');
                document.body.classList.remove('bv-no-scroll');
                document.removeEventListener('keydown', onKeydown);
                setTimeout(function () {
                    if (backdrop.parentNode) backdrop.parentNode.removeChild(backdrop);
                }, 200);
                resolve(result);
            }
            function onKeydown(e) {
                if (e.key === 'Escape') close(false);
            }

            btnCancel.addEventListener('click', function () { close(false); });
            btnConfirm.addEventListener('click', function () { close(true); });
            backdrop.addEventListener('mousedown', function (e) {
                if (e.target === backdrop) close(false);
            });
            document.addEventListener('keydown', onKeydown);

            document.body.classList.add('bv-no-scroll');
            requestAnimationFrame(function () {
                backdrop.classList.add('is-open');
                btnConfirm.focus();
            });
        });
    };

    /* ---- Cerrar sesión: confirmación antes de salir ---- */
    var logoutLink = document.getElementById('bvLogoutLink');
    var logoutForm = document.getElementById('logout-form');
    if (logoutLink && logoutForm) {
        logoutLink.addEventListener('click', function (e) {
            e.preventDefault();
            window.bvConfirm({
                titulo: 'Cerrar sesión',
                mensaje: '¿Seguro que deseas salir del sistema?',
                tono: 'danger',
                icono: 'fa-sign-out',
                textoConfirmar: 'Salir',
            }).then(function (ok) {
                if (ok) logoutForm.submit();
            });
        });
    }

    /* ---- Modal de confirmación: entrada animada ---- */
    document.addEventListener('shown.bs.modal', function (e) {
        if (reduceMotion || typeof gsap === 'undefined') return;
        var content = e.target.querySelector('.modal-content');
        if (!content) return;
        gsap.fromTo(content,
            { opacity: 0, scale: 0.94, y: 8 },
            { opacity: 1, scale: 1, y: 0, duration: 0.28, ease: 'power2.out' });
    });

    /* ---- Entrada de página (GSAP) ----
       Se omite si la propia vista ya declaró su propia animación de entrada
       (window.bvPageAnimated = true), para no re-animar los mismos elementos. */
    if (!reduceMotion && typeof gsap !== 'undefined' && !window.bvPageAnimated) {
        var tl = gsap.timeline({ defaults: { ease: 'power2.out' } });

        if (document.querySelector('.bv-crud-page-title')) {
            tl.from('.bv-crud-page-title, .bv-crud-meta', { y: 12, opacity: 0, duration: 0.45, stagger: 0.06 });
        }
        if (document.querySelector('.bv-crud-toolbar')) {
            tl.from('.bv-crud-toolbar', { y: 10, opacity: 0, duration: 0.4 }, '-=0.2');
        }

        var rows = gsap.utils.toArray('.bv-crud-table-wrap tbody tr');
        if (rows.length) {
            tl.from(rows, { opacity: 0, y: 8, duration: 0.35, stagger: 0.03 }, '-=0.15');
        }

        var cards = gsap.utils.toArray('.bv-form-card');
        if (cards.length) {
            // Primera tarjeta: entra junto con el resto del encabezado (ya está a la vista)
            var firstCardGroups = cards[0].querySelectorAll('.form-group');
            if (firstCardGroups.length) {
                tl.from(firstCardGroups, { opacity: 0, y: 10, duration: 0.35, stagger: 0.04 }, '-=0.15');
            } else {
                tl.from(cards[0], { opacity: 0, y: 10, duration: 0.35 }, '-=0.15');
            }

            // Tarjetas siguientes (formularios largos): aparecen al hacer scroll
            if (typeof ScrollTrigger !== 'undefined' && cards.length > 1) {
                cards.slice(1).forEach(function (card) {
                    gsap.from(card, {
                        opacity: 0,
                        y: 24,
                        duration: 0.5,
                        ease: 'power2.out',
                        scrollTrigger: { trigger: card, start: 'top 88%' },
                    });
                });
            }
        }
    }
})();
