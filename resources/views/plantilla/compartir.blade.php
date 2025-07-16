{{-- ======================================================= --}}
{{--         HTML DEL MODAL PARA COMPARTIR ENLACE            --}}
{{-- ======================================================= --}}
@auth
<div class="modal fade" id="shareLinkModal" tabindex="-1" aria-labelledby="shareLinkModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="shareLinkModalLabel"><i class="bi bi-share-fill me-2"></i> ¡Comparte tu Tienda!</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p>Este es el enlace público a tu tienda. Cópialo y compártelo con tus clientes.</p>
        <div class="input-group">
            
            {{-- ======================================================= --}}
            {{-- LÓGICA CORREGIDA AQUÍ --}}
            {{-- ======================================================= --}}
            @if(auth()->user()->empresa)
                {{-- Si el usuario TIENE una empresa, genera la URL --}}
                <input type="text" class="form-control" value="{{ route('tienda.public.index', ['empresa' => auth()->user()->empresa]) }}" id="storeLinkInput" readonly>
                <button class="btn btn-primary" type="button" id="copyLinkBtn">
                    <i class="bi bi-clipboard me-1"></i> Copiar
                </button>
            @else
                {{-- Si el usuario NO tiene una empresa (es super_admin o cliente), muestra un mensaje --}}
                <input type="text" class="form-control" value="No tienes una tienda asignada." id="storeLinkInput" readonly disabled>
            @endif
            {{-- ======================================================= --}}

        </div>
      </div>
    </div>
  </div>
</div>
@endauth
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const copyButton = document.getElementById('copyLinkBtn');
    const linkInput = document.getElementById('storeLinkInput');

    if (copyButton) {
        copyButton.addEventListener('click', function() {
            navigator.clipboard.writeText(linkInput.value).then(function() {
                const originalText = copyButton.innerHTML;
                copyButton.innerHTML = '<i class="bi bi-check-lg me-1"></i> ¡Copiado!';
                setTimeout(function() {
                    copyButton.innerHTML = originalText;
                }, 2000);
            }).catch(function(err) {
                console.error('Error al intentar copiar el enlace: ', err);
                alert('Error al copiar. Por favor, selecciona el texto manualmente.');
            });
        });
    }
});
</script>
@endpush