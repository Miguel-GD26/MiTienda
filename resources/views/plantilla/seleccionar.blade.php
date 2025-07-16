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
                        
                        <div class="mt-4 d-flex justify-content-end gap-2" x-show="empresaSeleccionada">
                            <button class="btn btn-outline-secondary" @click="copiarEnlace(empresaSeleccionada)">
                                <i class="fa-regular fa-copy me-1"></i> Copiar Enlace
                            </button>
                            <a :href="generarUrl(empresaSeleccionada)" target="_blank" class="btn btn-primary">
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
    const tiendaUrlBase = "{{ url('/tienda') }}";

    function generarUrl(slug) {
        return `${tiendaUrlBase}/${slug}`;
    }

    function copiarEnlace(slug) {
        const url = generarUrl(slug);
        navigator.clipboard.writeText(url).then(() => {
            alert('¡Enlace de la tienda copiado al portapapeles!');
        }).catch(err => {
            console.error('Error al copiar: ', err);
            alert('No se pudo copiar el enlace.');
        });
    }
    </script>