@extends('plantilla.app')

@section('contenido')
<div class="app-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                @livewire('profile-manager')
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
{{-- El script de formato de teléfono se queda aquí para que se cargue con la página inicial --}}
<script>
  // Opcional: Para resetear el campo de archivo después de una subida exitosa.
  window.addEventListener('profile-updated', event => {
    let fileInput = document.getElementById('empresa_logo');
    if(fileInput) {
        fileInput.value = '';
    }
  });
  
  const camposTelefono = ['empresa_telefono_whatsapp'];
  camposTelefono.forEach(id => {
    const input = document.getElementById(id);
    if (input) {
      input.addEventListener('input', () => {
        input.value = input.value.replace(/\D/g, '').slice(0, 9);
      });
      input.addEventListener('paste', e => {
        e.preventDefault();
        const texto = (e.clipboardData || window.clipboardData).getData('text');
        const limpio = texto.replace(/\D/g, '').slice(0, 9);
        document.execCommand('insertText', false, limpio);
      });
    }
  });
</script>
@endpush