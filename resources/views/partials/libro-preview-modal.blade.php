{{-- Modal reutilizable: vista previa de libros virtuales (Google Drive) antes de abrir el PDF completo --}}
<div id="libroPreviewModal" class="lpm-overlay" onclick="if(event.target===this){cerrarVistaPreviaLibro();}">
    <div class="lpm-box" role="dialog" aria-modal="true" aria-labelledby="libroPreviewTitulo">
        <div class="lpm-head">
            <h3 id="libroPreviewTitulo" class="lpm-title">Vista previa</h3>
            <button type="button" class="lpm-close" onclick="cerrarVistaPreviaLibro()" aria-label="Cerrar">&times;</button>
        </div>
        <div class="lpm-body">
            <iframe id="libroPreviewFrame" src="" loading="lazy" allow="autoplay"></iframe>
        </div>
        <div class="lpm-foot">
            <a id="libroPreviewFullLink" href="#" target="_blank" rel="noopener" class="lpm-full-btn">
                <i class="fab fa-google-drive"></i> Abrir PDF completo
            </a>
        </div>
    </div>
</div>

<style>
    .lpm-overlay {
        position: fixed;
        inset: 0;
        z-index: 10500;
        background: rgba(20, 16, 12, .72);
        display: none;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }
    .lpm-overlay.is-open { display: flex; }
    .lpm-box {
        background: #fff;
        border-radius: 12px;
        width: 100%;
        max-width: 720px;
        max-height: 90vh;
        display: flex;
        flex-direction: column;
        overflow: hidden;
        box-shadow: 0 15px 50px rgba(0,0,0,.35);
    }
    .lpm-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        padding: 12px 16px;
        border-bottom: 1px solid rgba(200,142,65,.2);
    }
    .lpm-title {
        margin: 0;
        font-size: .95rem;
        font-weight: 700;
        color: #2a2520;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    .lpm-close {
        border: 0;
        background: transparent;
        font-size: 1.6rem;
        line-height: 1;
        color: #8a7d6e;
        cursor: pointer;
        padding: 0 4px;
        flex-shrink: 0;
    }
    .lpm-close:hover { color: #2a2520; }
    .lpm-body {
        flex: 1;
        min-height: 60vh;
        background: #f0ebe4;
    }
    .lpm-body iframe {
        width: 100%;
        height: 100%;
        min-height: 60vh;
        border: 0;
        display: block;
    }
    .lpm-foot {
        padding: 10px 16px;
        border-top: 1px solid rgba(200,142,65,.2);
        text-align: right;
    }
    .lpm-full-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: var(--puklla-accent, #cb9042);
        color: #fff;
        padding: 8px 16px;
        border-radius: 999px;
        font-size: .8rem;
        font-weight: 700;
        text-decoration: none;
    }
    .lpm-full-btn:hover { background: #af7d39; color: #fff; text-decoration: none; }
</style>

<script>
    function abrirVistaPreviaLibro(identificador, nombre) {
        var modal = document.getElementById('libroPreviewModal');
        if (!modal || !identificador) return;
        document.getElementById('libroPreviewFrame').src = 'https://drive.google.com/file/d/' + encodeURIComponent(identificador) + '/preview';
        document.getElementById('libroPreviewTitulo').textContent = nombre || 'Vista previa';
        document.getElementById('libroPreviewFullLink').href = 'https://drive.google.com/file/d/' + encodeURIComponent(identificador) + '/view';
        modal.classList.add('is-open');
        document.body.style.overflow = 'hidden';
    }

    function cerrarVistaPreviaLibro() {
        var modal = document.getElementById('libroPreviewModal');
        if (!modal) return;
        modal.classList.remove('is-open');
        document.body.style.overflow = '';
        document.getElementById('libroPreviewFrame').src = '';
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') cerrarVistaPreviaLibro();
    });
</script>
