
 @extends('layouts.principal')
 @section('title', 'Actas')
 <style type="text/css">

  .results tr[visible='false'],
  .no-result{
    display:none;
  }

  .results tr[visible='true']{
    display:table-row;
  }

  .counter{
    padding:8px; 
    color:#ccc;
  }
</style>
 @section('menuLateral')
  <li class="active">
    <a href=" {{ url('/modules/actas/agregar/acta') }}">
      <i class="fa fa-files-o"></i></i> <span>Generar Acta</span></i>
    </a>   
  </li>
@endsection
@section('contenido')


  <section class="content-header">
    <h1>
      Control Escolar
      <small>Actas Finales</small>
    </h1>
    <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="#">Modulos</a></li>
      <li > <a href="#">Control Escolar</a></li>
      <li class="active">Actas Finales</li>
    </ol>
  </section>
  <section class="content">
      <div class="row">
        <div class="col-xs-12">
         

          <div class="box box-primary">
            <div class="box-header">
              <h3 class="box-title">Actas</h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
            @if (count($errors) > 0)
                  <div class="alert alert-danger">
                      <ul>
                          @foreach ($errors->all() as $error)
                              <li>{{ $error }}</li>
                          @endforeach
                      </ul>
                  </div>
              @endif
              <table id="example1" class="table table-bordered table-hover " style="font-size: 12px;">
                <thead>
                <tr>
                  <th width="5">#</th>
                  <th class="col-md-1 col-xs-1">Grupo</th>
                  <th class="col-md-2 col-xs-2">Materia</th>
                  <th class="col-md-2 col-xs-2">Carrera</th>
                  <th class="col-md-2 col-xs-2">Ciclo</th>
                  <th class="col-md-2 col-xs-2">Fecha</th>
                  <th class="col-md-3 col-xs-3">Acciones</th>
                </tr>
                </thead>
                <tbody>
                <?php
                  $i=1;
                ?>
                  @foreach ($asignacion as $error)
                    <tr id={{ $error->id}}>
                      <th scope='row'>{{ $i++ }}</th>
                      <td>{{ $error->nombre }}</td>
                      <td>{{ $error->materia }}</td>
                      <td>{{ $error->nombrec }}</td>
                      <td>{{ $error->nombre_ciclo }}</td>
                      <td>{{ $error->created_at }}</td>
                      <td>
                       
                        <a href="../../../pdf/{{ $error->id }}" target="_blank" class="btn btn-primary" title="Imprimir">
                          <i class="fa fa-file-pdf-o fa-lg"></i>
                        </a>
                      </td>
                    </tr>
                  @endforeach
                </tbody>
               
              </table>
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->
  </section>
 

@endsection
@section('js')
<script src="https://code.jquery.com/jquery-1.11.3.js"></script>
  <!-- jQuery 2.2.0 -->
<script src="{{ asset('components/plugins/jQuery/jQuery-2.2.0.min.js')}}"></script>
<!-- Bootstrap 3.3.6 -->

<!-- DataTables -->
<script src="{{ asset('components/plugins/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{ asset('components/plugins/datatables/dataTables.bootstrap.min.js')}}"></script>

<script>
  $(function () {
    $("#example2").DataTable();
    $('#example1').DataTable({
      "paging": true,
      "lengthChange": false,
      "searching": false,
      "ordering": true,
      "info": true,
      "autoWidth": false,
      "language": {
            "emptyTable":     "Datos no encotrados en la tabla",
            "info":           "Mostrando _START_ a _END_ de _TOTAL_ entradas",
            "infoEmpty":      "Mostrando 0 a 0 de 0 entradas",
            "infoFiltered":   "(filtered from _MAX_ total entries)",
            "infoPostFix":    "",
            "thousands":      ",",
            "lengthMenu":     "Show _MENU_ entries",
            "loadingRecords": "Cargando...",
            "processing":     "Procesando...",
            "search":         "Buscar:",
            "zeroRecords":    "No matching records found",
            "paginate": {
                "first":      "Primer",
                "last":       "Último",
                "next":       "Siguiente",
                "previous":   "Anterior"
            }
        }
    });
    $( "#example1_filter" ).addClass( "pull-right" );
    $( "#example1_paginate" ).addClass( "pull-right" );
  });
</script>
@endsection