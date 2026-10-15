<div>
    <footer class="puklla-footer">
        <div class="container">
            <div class="row puklla-footer__top gy-4">
                <div class="col-lg-4">
                    <a href="{{ route('index') }}" class="puklla-footer__brand">
                        <img src="{{ asset('img/Asociacion-logo-blanco.png') }}" alt="Logo EESP Pukllasunchis"
                            loading="lazy" decoding="async" width="190" height="76">
                    </a>
                    <p class="puklla-footer__tagline">
                        Formando educadores con identidad, interculturalidad y compromiso social.
                    </p>
                    <ul class="puklla-footer__contact">
                        <li>
                            <i class="fa fa-map-marker-alt" aria-hidden="true"></i>
                            <span>Calle Siete Diablitos 222, San Blas, Cusco.</span>
                        </li>
                        <li>
                            <i class="fa fa-phone" aria-hidden="true"></i>
                            <span>
                                <a class="wasa" href="https://wa.me/51984529158" target="_blank" rel="noopener">+51 984 529 158</a>
                                <span aria-hidden="true"> · </span>
                                <a class="wasa" href="https://wa.me/51969572566" target="_blank" rel="noopener">+51 969 572 566</a>
                            </span>
                        </li>
                        <li>
                            <i class="fa-solid fa-envelope" aria-hidden="true"></i>
                            <a href="mailto:eespp@pukllasunchis.org">eespp@pukllasunchis.org</a>
                        </li>
                    </ul>
                </div>

                <div class="col-6 col-lg-3">
                    <h3 class="puklla-footer__heading">Explorar</h3>
                    <ul class="puklla-footer__links">
                        <li><a href="{{ route('index') }}">Inicio</a></li>
                        <li><a href="{{ route('libros') }}">Biblioteca virtual</a></li>
                        <li><a href="{{ route('catalogo-fisico') }}">Biblioteca física</a></li>
                        <li><a href="{{ route('videos') }}">Videos</a></li>
                        <li><a href="{{ route('cancionesPuklla') }}">Canciones</a></li>
                    </ul>
                </div>

                <div class="col-6 col-lg-2">
                    <h3 class="puklla-footer__heading">Institución</h3>
                    <ul class="puklla-footer__links puklla-footer__links--static">
                        <li>EESP Pukllasunchis</li>
                        <li>Información Institucional</li>
                        <li>Repositorio</li>
                        <li>Bolsa de trabajo</li>
                        <li>Radio en la escuela</li>
                    </ul>
                </div>

                <div class="col-lg-3">
                    <h3 class="puklla-footer__heading">Síguenos</h3>
                    <ul class="puklla-footer__social">
                        <li>
                            <a href="https://www.facebook.com/eesp.pukllasunchis" target="_blank" rel="noopener"
                                aria-label="Facebook">
                                <i class="fa-brands fa-facebook-f" aria-hidden="true"></i>
                            </a>
                        </li>
                        <li>
                            <a href="https://www.instagram.com/eesp.pukllasunchis/" target="_blank" rel="noopener"
                                aria-label="Instagram">
                                <i class="fa-brands fa-instagram" aria-hidden="true"></i>
                            </a>
                        </li>
                        <li>
                            <a href="https://www.tiktok.com/@eesp.pukllasunchis?is_from_webapp=1&sender_device=pc"
                                target="_blank" rel="noopener" aria-label="TikTok">
                                <i class="fa-brands fa-tiktok" aria-hidden="true"></i>
                            </a>
                        </li>
                        <li>
                            <a href="https://www.youtube.com/@eesp.pukllasunchis" target="_blank" rel="noopener"
                                aria-label="YouTube">
                                <i class="fa-brands fa-youtube" aria-hidden="true"></i>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="puklla-footer__bottom">
                <p>EESP Pukllasunchis &copy; {{ date('Y') }} · Todos los derechos reservados</p>
                <p>
                    Hecho por
                    <a href="https://www.facebook.com/DjmWebMaster" target="_blank" rel="nofollow noopener">DJM2</a>
                </p>
            </div>
        </div>
    </footer>

    <script>
        (function () {
            if (typeof gsap === 'undefined') return;
            var footer = document.querySelector('.puklla-footer');
            if (!footer) return;

            var reveal = function () {
                gsap.from(footer.querySelectorAll('.puklla-footer__top > div'), {
                    y: 22, duration: .5, stagger: .08, ease: 'power2.out'
                });
                gsap.from(footer.querySelector('.puklla-footer__bottom'), {
                    y: 14, duration: .45, delay: .3, ease: 'power2.out'
                });
            };

            if ('IntersectionObserver' in window) {
                var io = new IntersectionObserver(function (entries, obs) {
                    if (entries[0].isIntersecting) { reveal(); obs.disconnect(); }
                }, { threshold: .15 });
                io.observe(footer);
            } else {
                reveal();
            }
        })();
    </script>
</div>
