{{-- Recibe la variable $permiso_a_eliminar del @include --}}
<div class="modal fade" id="modal-delete-permission-{{$permiso_a_eliminar->id}}">
    <div class="modal-dialog">
        <form action="{{route('permisos.destroy', $permiso_a_eliminar->id)}}" method="POST">
            @method('DELETE')
            @csrf
            <div class="modal-content bg-danger text-white">
                <div class="modal-header">
                    <h4 class="modal-title">Eliminar Permiso</h4>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>¿Realmente desea eliminar el permiso "<strong>{{$permiso_a_eliminar->name}}</strong>"?</p>
                    <p class="small">Esta acción no se puede deshacer.</p>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-outline-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-outline-light">Sí, Eliminar</button>
                </div>
            </div>
        </form>
    </div>
</div>