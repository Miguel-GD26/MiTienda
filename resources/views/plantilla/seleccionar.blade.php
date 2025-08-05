@auth
@if(auth()->user()->hasRole('super_admin'))
    <div class="modal fade" id="selectEmpresaModal" ...>
        <div class="modal-dialog ...">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" ...><i class="fa-solid fa-building-user me-2"></i> Seleccionar Empresa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted">Por favor elige la tienda que deseas ver o cuyo enlace quieres compartir.</p>
                    
                    <div x-data="{ empresaSeleccionada: '' }">
                        <select class="form-select" x-model="empresaSeleccionada">
                            <option value="" disabled>Elige una empresa</option>
                            @foreach($empresasParaModal as $empresa)
                                <option value="{{ $empresa->slug }}">{{ $empresa->nombre }}</option>
                            @endforeach
                        </select>
                        
                        {{-- CAMBIO AQUÍ: Se ha eliminado el x-show del div. Ahora siempre es visible. --}}
                        <div class="mt-4 d-flex justify-content-end gap-2">
                            
                            {{-- CAMBIO AQUÍ: Se añade :disabled para controlar el estado del botón. --}}
                            <button class="btn btn-outline-secondary" 
                                    @click="copiarEnlace(empresaSeleccionada)" 
                                    :disabled="!empresaSeleccionada">
                                <i class="fa-regular fa-copy me-1"></i> Copiar Enlace
                            </button>
                            
                            {{-- CAMBIO AQUÍ: Se añade :class para controlar el estado del enlace. --}}
                            <a :href="empresaSeleccionada ? generarUrl(empresaSeleccionada) : '#'" 
                               target="_blank" 
                               class="btn btn-primary"
                               :class="{ 'disabled': !empresaSeleccionada }">
                                <i class="fa-solid fa-eye me-1"></i> Ver Tienda
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif
@endauth


<script>
    const tiendaUrlBase = "{{ url('') }}";

    function generarUrl(slug) {
        return `${tiendaUrlBase}/${slug}`;
    }

    function copiarEnlace(slug) {
        // Añadimos una comprobación para no hacer nada si el slug está vacío
        if (!slug) return;

        const url = generarUrl(slug);
        navigator.clipboard.writeText(url).then(() => {
            // Sería mejor usar un toast o un feedback más sutil que un alert
            alert('¡Enlace de la tienda copiado al portapapeles!');
        }).catch(err => {
            console.error('Error al copiar: ', err);
            alert('No se pudo copiar el enlace.');
        });
    }
</script>