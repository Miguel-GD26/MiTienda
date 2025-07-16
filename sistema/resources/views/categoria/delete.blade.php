<div class="modal fade" id="modal-delete-{{$reg->id}}">
    <div class="modal-dialog">
        <form action="{{route('categorias.destroy', $reg->id)}}" method="post">
            @method('DELETE')
            @csrf
        <div class="modal-content bg-danger">
            <div class="modal-header">
                <h4 class="modal-title">Eliminar registro</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Realmente desea eliminar el registro {{$reg->nombre}}?</p>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-outline-light" data-bs-dismiss="modal">Cerrar</button>
                <button type="submit" class="btn btn-outline-light">Eliminar</button>
            </div>
        </div>
        <!-- /.modal-content -->
        </form>
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->