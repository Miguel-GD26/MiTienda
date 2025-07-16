@extends('plantilla.app')
@section('contenido')
<main class="app-main">
        <!--begin::App Content Header-->
        <div class="app-content-header">
          <!--begin::Container-->
          <div class="container-fluid">
            <!--begin::Row-->
            
            <!--end::Row-->
          </div>
          <!--end::Container-->
        </div>
        <!--end::App Content Header-->
        <!--begin::App Content-->
        <div class="app-content">
          <!--begin::Container-->
          <div class="container-fluid">
            <!--begin::Row-->
            <div class="row">
              <div class="col-md-12">
                <div class="card - body">
                
                    
                  <div class="card-header"><h3 class="card-title">CATEGORIAS</h3></div>
                  <!-- /.card-header -->
                  <div class="card-body">
                    <form action="{{route('categorias.store')}}" method="post">
                        @csrf 
                        <div class="row">
                            <div class="col-lg-6 form-group">
                              <label for="">Nombre</label>
                              <input type="text" class="form-control" name="nombre">
                  
                            </div>
                            <div class="col-lg-6 form-group">
                              <label for="">Descripcion</label>
                              <input type="text" class="form-control" name="descripcion">
                  
                            </div>
                            <div class="col-lg-12 form-group">
                            <br>  
                            <a href="{{route('categorias.index')}}" class="btn btn-default"> Regresar </a>
                              
                              <button class="btn btn-primary" type="submit">Guardar </button>
                  
                            </div>
                          </div>
                  <!-- /.card-body -->
                  <div class="card-footer clearfix">
                    <ul class="pagination pagination-sm m-0 float-end">
                      <li class="page-item"><a class="page-link" href="#">&laquo;</a></li>
                      <li class="page-item"><a class="page-link" href="#">1</a></li>
                      <li class="page-item"><a class="page-link" href="#">2</a></li>
                      <li class="page-item"><a class="page-link" href="#">3</a></li>
                      <li class="page-item"><a class="page-link" href="#">&raquo;</a></li>
                    </ul>
                </div>
                </div>
                <!-- /.card -->
                <!-- /.card -->
              </div>
              <!-- /.col -->
              
              <!-- /.col -->
            </div>
            <!--end::Row-->
          </div>
          <!--end::Container-->
        </div>
        <!--end::App Content-->
      </main>

@endsection