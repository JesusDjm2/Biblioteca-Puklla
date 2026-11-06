{{--
    Scripts de las vistas de categorías en tarjetas (.cg-card).
    - Botones .btn-delete con data-url, data-nombre y data-total (elementos que contiene).
      Usa el formulario #deleteForm de la vista.
    - Buscador #cg-q que filtra las tarjetas por data-nombre.
    Parámetros: $titulo (título del diálogo), $entidad (texto para "tiene N …").
--}}
<script>
    document.addEventListener('click', function (e) {
        var btn = e.target.closest('.btn-delete');
        if (!btn) return;
        var total = parseInt(btn.dataset.total, 10) || 0;
        if (total > 0) {
            window.bvConfirm({
                titulo: 'No se puede eliminar',
                mensaje: '"' + btn.dataset.nombre + '" tiene ' + total + ' ' + (btn.dataset.entidad || @json($entidad ?? 'elemento(s)')) + '. Muévelos o elimínalos antes de borrarla.',
                tono: 'warning', icono: 'fa-info-circle', textoConfirmar: 'Entendido',
            });
            return;
        }
        window.bvConfirm({
            titulo: btn.dataset.titulo || @json($titulo ?? 'Eliminar categoría'),
            mensaje: '¿Eliminar "' + btn.dataset.nombre + '"? Esta acción no se puede deshacer.',
            tono: 'danger', icono: 'fa-trash', textoConfirmar: 'Eliminar',
        }).then(function (ok) {
            if (!ok) return;
            var f = document.getElementById('deleteForm');
            f.action = btn.dataset.url;
            f.submit();
        });
    });

    (function () {
        var q = document.getElementById('cg-q');
        if (!q) return;
        q.addEventListener('input', function () {
            var term = this.value.normalize('NFD').replace(/[̀-ͯ]/g, '').toLowerCase().trim();
            document.querySelectorAll('.cg-card[data-nombre]').forEach(function (card) {
                card.style.display = card.dataset.nombre.includes(term) ? '' : 'none';
            });
        });
    })();
</script>

@push('scripts')
    <script>
        (function () {
            if (typeof gsap === 'undefined') return;
            var cards = document.querySelectorAll('.cg-card');
            if (!cards.length) return;
            window.bvPageAnimated = true;
            gsap.from(cards, { opacity: 0, y: 14, duration: 0.35, stagger: 0.05, ease: 'power2.out', clearProps: 'transform,opacity' });
        })();
    </script>
@endpush
