@extends('layouts.principal')
@section('title', 'Actas')
<meta name="csrf-token" content="{{ csrf_token() }}"/>
<style type="text/css">

    .results tr[visible='false'],
    .no-result {
        display: none;
    }

    .results tr[visible='true'] {
        display: table-row;
    }

    .counter {
        padding: 8px;
        color: #ccc;
    }

    .dataTables_filter {
        display: none;
    }
</style>
@section('menuLateral')
    <li class="active">
        <a href=" {{ url('/modules/estadistica') }}">
            <i class="fa fa-area-chart"></i></i> <span>Estadística</span></i>
        </a>
    </li>
@endsection
@section('contenido')
    <script src="https://code.jquery.com/jquery-1.11.3.js"></script>

    <section class="content-header">
        <h1>
            Control Escolar
            <small>Estadística Cuatrimestral</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Modulos</a></li>
            <li><a href="#">Control Escolar</a></li>
            <li class="active">Estadística Cuatrimestral</li>
        </ol>
    </section>
    <section class="content">
        <div class="row">
            <div class="col-xs-12">


                <div class="box box-primary">
                    <div class="box-header">
                        <h3 class="box-title">Relación de Estadísticas Cuatrimestrales</h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="col-md-1"></div>
                        <div class="col-md-12">
                            @if(session('mensaje'))
                                <div class="callout callout-success">
                                    <p>{{session('mensaje')}}</p>
                                </div>
                            @elseif(session('error'))
                                <div class="callout callout-warning">
                                    <p>{{session('error')}}</p>
                                </div>
                            @endif
                            <div class="col-sm-5">
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="glyphicon glyphicon-search"></i></span>
                                    <input type="text" id="searchbox" class="form-control" placeholder="Buscar...">
                                </div>
                            </div>
                            <div class="col-md-offset-2 col-sm-3 pull-right">
                                <div class="input-group">
                                    <a class="btn btn-block btn-social btn-bitbucket"
                                       href="{{url('/modules/estadistica/add')}}">
                                        <i class="fa fa-plus"></i> Agregar Asignacion
                                    </a>
                                </div>
                            </div>
                            <table id="tablaGrupos" class="table table-bordered table-hover" style="font-size: 12px;">
                                <thead>
                                <tr>
                                    <th width="5">#</th>
                                    <th class="col-md-4 col-xs-5">Ciclo Escolar</th>
                                    <th class="col-md-2 col-xs-2">Hombres</th>
                                    <th class="col-md-2 col-xs-2">Mujeres</th>
                                    <th class="col-md-3 col-xs-3">Acciones</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                $i = 1;
                                ?>
                                @foreach($asignaciones as $asignacion)
                                    <tr id={{$asignacion->id}}>
                                        <th scope="row">{{$i++}}</th>
                                        <td>{{$asignacion->nombre_ciclo}}</td>
                                        <td>{{$asignacion->hombres}}</td>
                                        <td>{{$asignacion->mujeres}}</td>
                                        <td>
                                            <a class="btn btn-danger" data-toggle="tooltip" data-placement="top" title="Eliminar Estadística"
                                               onclick="setValue('{{ $asignacion->id }}',this)"><i
                                                        class="fa fa-trash fa-lg"></i></a>
                                            <a class="btn btn-info" data-toggle="tooltip" data-placement="bottom" title="Imprimir CGMAC Licenciaturas"
                                               href="{{ url('modules/estadistica/imprime/')."/".$asignacion->id."/". 1}}" target="_blank">
                                                <i class="fa fa-file-pdf-o fa-lg"></i>
                                            </a>
                                            <a class="btn btn-success" data-toggle="tooltip" data-placement="top" title="Imprimir CGMAC Posgrado"
                                               href="{{ url('modules/estadistica/imprime/')."/".$asignacion->id."/". 2 }}" target="_blank">
                                                <i class="fa fa-file-pdf-o fa-lg"></i>
                                            </a>

                                            <a class="btn btn-warning" data-toggle="tooltip" data-placement="bottom" title="Imprimir MAEP"
                                               href="{{ url('modules/estadistica/imprimemaep/')."/".$asignacion->id}}" target="_blank">
                                                <i class="fa fa-file-pdf-o fa-lg"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>

                            </table>
                        </div>
                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->
            </div>
            <!-- /.col -->
        </div>
        <!-- /.row -->
    </section>
    <!--<button type="button" class="btn btn-info btn-lg" data-toggle="modal" data-target="#myModal">Open Modal</button>-->

    <!-- Modal -->
    <div class="modal fade" id="myModal" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Eliminar Registro Grupo</h4>
                </div>
                <div class="modal-body">
                    <p>¿Deseas Eliminar el grupo?</p>
                    <input type="text" name="idgp" id="idgp" hidden>
                    <input type="text" name="idciclo" id="idciclo" hidden>
                    <input type="text" name="idcarrera" id="idcarrera" hidden>
                    <input type="text" name="mod" id="mod" hidden>
                    <input type="number" name="row" id="row" hidden>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" onclick="deleteRow()">Aceptar</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                </div>
            </div>

        </div>
    </div>

@endsection
@section('js')
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        function setValue(idCiclo, row) {
            swal({
                title: '¿Está seguro que desea eliminar el registro?',
                text: "(Una vez elminado no se podrá recuperar)",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Eliminar',
                cancelButtonText: 'Cancelar',
            }).then(function () {
                $.ajax({
                    type : "DELETE",
                    url : "/modules/estadistica",
                    data : {
                        idCiclo: idCiclo,
                    },
                    dataType :  "json",
                    error : function (jqXHR, textStatus, errorThrown) {
                        swal("Error!",errorThrown,"error");
                    },
                    success : function (data, textStatus,jqXHR) {
                        if (data.success){
                            swal("Correcto!",data.success,"success");
                            location.reload();
                        }else {
                            swal("Ups!",data.error,"errror");
                        }
                    }
                });
            });

        }
        $(function () {


            $("#example2").DataTable();
            var dataTable = $('#tablaGrupos').dataTable({
                "paging": true,
                "lengthChange": false,
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "language": {
                    "emptyTable": "Datos no encotrados en la tabla",
                    "info": "Mostrando _START_ a _END_ de _TOTAL_ entradas",
                    "infoEmpty": "Mostrando 0 a 0 de 0 entradas",
                    "infoFiltered": "(filtered from _MAX_ total entries)",
                    "infoPostFix": "",
                    "thousands": ",",
                    "lengthMenu": "Show _MENU_ entries",
                    "loadingRecords": "Cargando...",
                    "processing": "Procesando...",
                    "search": "Buscar:",
                    "zeroRecords": "No hay resultados",
                    "paginate": {
                        "first": "Primer",
                        "last": "Último",
                        "next": "Siguiente",
                        "previous": "Anterior"
                    }
                }
            });
            $("#searchbox").keyup(function () {
                dataTable.fnFilter(this.value);
            });
            $("#tablaGrupos_filter").addClass("pull-right");
            $("#tablaGrupos_paginate").addClass("pull-right");
            $('[data-toggle="tooltip"]').tooltip();
        });
    </script>
@endsection