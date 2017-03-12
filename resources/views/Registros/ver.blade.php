@extends('layouts.principal')
@section("title","Ver grupos")
@section("css")
    <meta name="csrf-token" content="{{ csrf_token() }}"/>
    <!-- Select2 -->
    <link rel="stylesheet" href="{{ asset('components/plugins/select2/select2.min.css') }}">
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
@endsection
@section("menuLateral")
    <li class="active">
        <a href=" {{ url('/modules/registro') }}">
            <i class="fa fa-files-o"></i></i> <span>Generar Registro</span></i>
        </a>
    </li>
@endsection
@section("contenido")
    <section class="content-header">
        <h1>
            Control Escolar
            <small>Registro Escolaridad</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Modulos</a></li>
            <li><a href="#">Control Escolar</a></li>
            <li class="active">Registro Escolaridad</li>
        </ol>
    </section>
    <section class="content">
        <div class="row">
            <div class="col-xs-12">


                <div class="box box-primary">
                    <div class="box-header">
                        <h3 class="box-title">{{$carrera->nombre}}</h3>
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
                            <div class="col-md-offset-1 col-sm-3 ">
                                <div class="input-group pull-right">
                                    <a class="btn btn-block btn-social btn-bitbucket"
                                       onclick="verGrupo()">
                                        <i class="fa fa-file-pdf-o"></i> Agregar Grupo
                                    </a>
                                </div>
                            </div>
                            <div class="col-sm-3 pull-right">
                                <div class="input-group">
                                    <a class="btn btn-block btn-social btn-openid"
                                       href="{{url('/modules/registro/recursamiento')}}">
                                        <i class="fa fa-plus"></i> Recursamiento
                                    </a>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <table id="tablaGrupos" class="table table-bordered table-hover" style="font-size: 12px;">
                                    <thead>
                                    <tr>
                                        <th class="col-md-1 col-xs-1">#</th>
                                        <th class="col-md-1 col-xs-1">Grupo</th>
                                        <th class="col-md-4 col-xs-5">Modalidad</th>
                                        <th class="col-md-5 col-xs-6">Acciones</th>
                                    </tr>
                                    </thead>
                                    <tbody>
									<?php
									$i = 0;
									?>
                                        @foreach($registros as $registro)
                                            <tr id="{{ $i++ }}">
                                                <td>{{ $i }}</td>
                                                <td>{{$registro->nombre}}</td>
                                                <td>{{$registro->descripcion}}</td>
                                                <td>
                                                    <a class="btn btn-success btn-flat"
                                                       href="{{url('/modules/registro/recursamiento/').
                                                       "/".$registro->id_carreras."/".$registro->id_grupos_actas."/".
                                                       $registro->id_ciclos."/".$registro->id_modalidad."/".$registro->tipo_modalidad}}">Agregar Recursamiento</a>
                                                    <a class="btn btn-info" data-toggle="tooltip" data-placement="bottom"
                                                       title="" href="{{url('/modules/registro/imprimir').
                                                       "/".$registro->id_carreras."/".$registro->id_grupos_actas."/".
                                                       $registro->id_ciclos."/".$registro->id_modalidad."/".$registro->tipo_modalidad}}"
                                                       target="_blank" data-original-title="Imprimir Registro Escolaridad">
                                                        <i class="fa fa-file-pdf-o fa-lg"></i>
                                                    </a>
                                                    <a class="btn btn-primary"
                                                       onclick="showModalAlumno('{{ $registro->id_modalidad }}',
                                                               '{{$registro->id_grupos_actas}}','{{$registro->id_carreras}}',
                                                               '{{$registro->tipo_modalidad}}','{{$registro->id_ciclos}}')" >
                                                        <i class="fa fa-eye fa-lg"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>

                                </table>
                            </div>

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
    @include("Registros.grupo")
@endsection
@section("js")
    <script src="{{asset('components/plugins/select2/select2.full.min.js')}}"></script>
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        function verGrupo(){
            $("#modalGrupo").modal();
        }
        
        function agregarNuevoGrupo() {
            var idGrupo = $("#grupos").val();
            var idCarrera = $("#idCarrera").val();
            var idCiclo = $("#cicloEscolar").val();
            var modalidad = $("#modalidad").val();
            var _token = $("#_token").val();
            //hacemos el ajax
            swal({
                title: '¿Está seguro que desea agregar el nuevo grupo?',
                text: "",
                type: 'info',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Agregar',
                cancelButtonText: 'Cancelar',
            }).then(function () {
                $.ajax({
                    type : "POST",
                    url : "/modules/registro/grupo",
                    data : {
                        idGrupo: idGrupo,
                        idCarrera: idCarrera,
                        idCiclo: idCiclo,
                        modalidad: modalidad,
                        _token: _token,
                    },
                    dataType :  "json",
                    error : function (jqXHR, textStatus, errorThrown) {
                        swal("Error!",errorThrown,"error");
                    },
                    success : function (data, textStatus,jqXHR) {

                        if (data.success){
                            //swal("Correcto!",data.success,"success");
                            $("#modalGrupo").modal("hide");
                            $("#alumnosGrupo").modal();
                            $("#gruposAlumnos").empty();
                            $("#idGrupoNuevo").val(data.idGrupo);
                            $("#idModalidadNuevo").val(data.idModalidad);
                            $.each(data.arrayAlumnos, function (key, value) {
                                $.each(value, function (key, value) {
                                    $("#gruposAlumnos").append("<option value='"+value.matricula+"'>"+value.nombre+"</option>");

                                });
                            });
                        }else {
                            swal("Ups!",data.error,"errror");
                        }
                    }
                });
            });
        }

        function showModalAlumno(idModalidad,idGrupo,idCarrera,modalidad,idCiclo){
            $("#gruposAlumnos").empty();
            var dataTable = $('#tablaAlumnos').DataTable();
            dataTable.clear().draw();
            $.ajax({
                type : "GET",
                url : "/modules/registro/alumno",
                data : {
                    idGrupo: idGrupo,
                    idCarrera: idCarrera,
                    idCiclo: idCiclo,
                    modalidad: modalidad,
                    idModalidad: idModalidad

                },
                dataType :  "json",
                error : function (jqXHR, textStatus, errorThrown) {
                    swal("Error!",errorThrown,"error");
                },
                success : function (data, textStatus,jqXHR) {

                    if (data.success){
                        $("#alumnosGrupo").modal();
                        $("#gruposAlumnos").empty();
                        $("#idGrupoNuevo").val(idGrupo);
                        $("#idModalidadNuevo").val(idModalidad);
                        $.each(data.arrayAlumnos, function (key, value) {
                            $.each(value, function (key, value) {
                                $("#gruposAlumnos").append("<option value='"+value.matricula+"'>"+value.nombre+"</option>");
                            });
                        });
                        $.each(data.lista, function (key, value) {
                            dataTable.row.add([
                                value.matricula,
                                value.nombre,
                            ]).draw(false);
                        });
                    }else {
                        swal("Ups!",data.error,"errror");
                    }
                }
            });
        }
        function moverAlumno(){
            var idGrupo = $("#idGrupoNuevo").val();
            var idCarrera = $("#idCarreraNuevo").val();
            var idCiclo = $("#cicloEscolarNuevo").val();
            var modalidad = $("#modalidadNuevo").val();
            var idModalidad = $("#idModalidadNuevo").val();
            var matricula = $("#gruposAlumnos").val();
            var _token = $("#_token2").val();
            var dataTable = $('#tablaAlumnos').DataTable();
            $.ajax({
                type : "POST",
                url : "/modules/registro/alumno",
                data : {
                    idGrupo: idGrupo,
                    idCarrera: idCarrera,
                    idCiclo: idCiclo,
                    modalidad: modalidad,
                    idModalidad: idModalidad,
                    matricula: matricula,
                    _token: _token
                },
                dataType :  "json",
                error : function (jqXHR, textStatus, errorThrown) {
                    swal("Error!",errorThrown,"error");
                },
                success : function (data, textStatus,jqXHR) {
                    if (data.success){
                        var nombreAlumno = $('#gruposAlumnos option:selected').text();
                        swal("Correcto!", data["success"], "success");
                        dataTable.row.add([
                            matricula,
                            nombreAlumno,
                        ]).draw(false);

                    }else {
                        swal("Ups!",data.error,"errror");
                    }
                }
            });
        }
        $(function () {
            $(".select2").select2();
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

            var dataTable2 = $('#tablaAlumnos').dataTable({
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
        });
    </script>
@endsection