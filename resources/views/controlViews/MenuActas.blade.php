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
        <a href=" {{ url('/modules/actas/agregar/acta') }}">
            <i class="fa fa-files-o"></i></i> <span>Generar Acta</span></i>
        </a>
    </li>
@endsection
@section('contenido')
    <script src="https://code.jquery.com/jquery-1.11.3.js"></script>

    <section class="content-header">
        <h1>
            Control Escolar
            <small>Actas Finales</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Modulos</a></li>
            <li><a href="#">Control Escolar</a></li>
            <li class="active">Actas Finales</li>
        </ol>
    </section>
    <section class="content">
        <div class="row">
            <div class="col-xs-12">


                <div class="box box-primary">
                    <div class="box-header">
                        <h3 class="box-title">Actas Grupales</h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="col-md-1"></div>
                        <div class="col-md-12">
                            <div class="col-sm-5">
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="glyphicon glyphicon-search"></i></span>
                                    <input type="text" id="searchbox" class="form-control" placeholder="Buscar...">
                                </div>
                            </div>
                            <table id="tablaGrupos" class="table table-bordered table-hover" style="font-size: 12px;">
                                <thead>
                                <tr>
                                    <th width="5">#</th>
                                    <th class="col-md-1 col-xs-1">Grupo</th>
                                    <th class="col-md-4 col-xs-4">Carrera</th>
                                    <th class="col-md-3 col-xs-2">Modalidad</th>
                                    <th class="col-md-2 col-xs-2">Ciclo</th>
                                    <th class="col-md-3 col-xs-3">Acciones</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                $i = 1;
                                ?>
                                @foreach ($grupos as $grupo)
                                    <tr id={{ $grupo->id}}>
                                        <th scope='row'>{{ $i++ }}</th>
                                        <td>{{ $grupo->nombre }}</td>
                                        <td>{{ $grupo->nombrec }}</td>
                                        <td>{{ $grupo->modalidad }}</td>
                                        <td>{{ $grupo->nombre_ciclo }}</td>
                                        <td>
                                            <a class="btn btn-danger"
                                               onclick="setValue({{ $grupo->id }},{{ $grupo->id_ciclos }},{{ $grupo->id_carrera }},'{{ $grupo->modalidad }}',this)">
                                                <i class="fa fa-trash fa-lg"></i>
                                            </a>
                                            <a href="{{url("/")}}/modules/actas/grupo/{{ $grupo->id }}/{{ $grupo->id_ciclos }}/{{ $grupo->id_carrera }}/{{$grupo->modalidad}}"
                                               class="btn btn-primary" title="Ver Actas">
                                                <i class="fa fa-arrow-right fa-lg"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                                @foreach ($gruposS as $grupo)
                                    <tr id={{ $grupo->id}}>
                                        <th scope='row'>{{ $i++ }}</th>
                                        <td>{{ $grupo->nombre }}</td>
                                        <td>{{ $grupo->nombrec }}</td>
                                        <td>{{ $grupo->modalidad }}</td>
                                        <td>{{ $grupo->nombre_ciclo }}</td>
                                        <td>
                                            <a class="btn btn-danger"
                                               onclick="setValue({{ $grupo->id }},{{ $grupo->id_ciclos }},{{ $grupo->id_carrera }},'{{ $grupo->modalidad }}',this)">
                                                <i class="fa fa-trash fa-lg"></i>
                                            </a>
                                            <a href="{{url("/")}}/modules/actas/grupo/{{ $grupo->id }}/{{ $grupo->id_ciclos }}/{{ $grupo->id_carrera }}/{{$grupo->modalidad}}"
                                               class="btn btn-primary" title="Ver Actas">
                                                <i class="fa fa-arrow-right fa-lg"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                                @foreach ($gruposD as $grupo)
                                    <tr id={{ $grupo->id}}>
                                        <th scope='row'>{{ $i++ }}</th>
                                        <td>{{ $grupo->nombre }}</td>
                                        <td>{{ $grupo->nombrec }}</td>
                                        <td>{{ $grupo->modalidad }}</td>
                                        <td>{{ $grupo->nombre_ciclo }}</td>
                                        <td>
                                            <a class="btn btn-danger"
                                               onclick="setValue({{ $grupo->id }},{{ $grupo->id_ciclos }},{{ $grupo->id_carrera }},'{{ $grupo->modalidad }}',this)">
                                                <i class="fa fa-trash fa-lg"></i>
                                            </a>
                                            <a href="{{url("/")}}/modules/actas/grupo/{{ $grupo->id }}/{{ $grupo->id_ciclos }}/{{ $grupo->id_carrera }}/{{$grupo->modalidad}}"
                                               class="btn btn-primary" title="Ver Actas">
                                                <i class="fa fa-arrow-right fa-lg"></i>
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
        function setValue(idG, idC, idCarrera, modalidad, row) {
            $("#myModal").modal();
            $("#idgp").val(idG);
            $("#idciclo").val(idC);
            $("#idCarrera ").val(idCarrera);
            $("#mod").val(modalidad);
            var i = row.parentNode.parentNode.rowIndex;
            $("#row").val(i);
        }
        function deleteRow() {

            var idg = $("#idgp").val();
            var idciclo = $("#idciclo").val();
            var idcarrera = $("#idCarrera ").val();
            var mod = $("#mod").val();
            var i = $("#row").val();
            document.getElementById("tablaGrupos").deleteRow(i);
            /**/
            var rpost = $.post("actas/delete", {
                idg: idg,
                idciclo: idciclo,
                idcarrera: idcarrera,
                mod: mod,
            });
            rpost.success(function (result) {
                alert("Grupo eliminado");

            });
            rpost.error(function (result, status, ss) {
                alert("Error" + result.responseText);
            });
            rpost.complete(function () {
                //alert("ajax complete");
            });
            $("#myModal").modal("hide");
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
        });
    </script>
@endsection