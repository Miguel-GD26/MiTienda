<div class="modal fade" id="modal-toggle-{{$reg->id}}" tabindex="-1" aria-labelledby="modalToggleLabel-{{$reg->id}}" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content {{ $reg->activo ? 'bg-warning text-dark' : 'bg-success text-white' }}">
            <form action="{{ route('usuarios.toggle', $reg->id) }}" method="post">
                @csrf
                @method('PATCH')
                <div class="modal-header">
                    <h5 class="modal-title" id="modalToggleLabel-{{$reg->id}}">
                        {{ $reg->activo ? 'Desactivar Registro' : 'Activar Registro' }}
                    </h5>
                    <button type="button" class="btn-close {{ $reg->activo ? '' : 'btn-close-white' }}" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    ¿Estás seguro de que deseas {{ $reg->activo ? 'desactivar' : 'activar' }} el registro de {{ $reg->name }}?
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-outline-light" data-bs-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-light">{{ $reg->activo ? 'Sí, Desactivar' : 'Sí, Activar' }}</button>
                </div>
            </form>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>