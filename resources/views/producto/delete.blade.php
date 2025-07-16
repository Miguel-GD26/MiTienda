<div class="modal fade" id="modal-delete-{{$producto->id}}">
    <div class="modal-dialog">
        <form action="{{ route('productos.destroy', $producto->id) }}" method="post">
            @method('DELETE')
            @csrf
            <div class="modal-content bg-danger">
                <div class="modal-header">
                    <h4 class="modal-title">Eliminar Producto</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>¿Realmente desea eliminar el producto <strong>{{$producto->name}}</strong>?</p>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-outline-light" data-bs-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-outline-light">Eliminar</button>
                </div>
            </div>
        </form>
    </div>
</div>