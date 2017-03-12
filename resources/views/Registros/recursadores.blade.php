@extends("layouts.principal")
@section("title","recursadores")
@section("css")
    <!-- Select2 -->
    <meta name="csrf-token" content="{{ csrf_token() }}"/>
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
@section("contenido")
    <section class="content-header">
        <h1>
            Registro de Escolaridad
            <small>{{$carrera->nombre}} {{$grupo->grado}}° {{$grupo->grupo}}</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Modulos</a></li>
            <li><a href="#">Control Escolar</a></li>
            <li class="active">Registro Escolaridad</li>
        </ol>
    </section>
    <!-- Main content -->
    <section class="content">
        <div class="col-sm-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Agregar Alumno Registro</h3>
                </div>
                <!-- /.box-header -->
                <!-- form start -->
                @if(session('mensaje'))
                    <div class="callout callout-success">
                        <p>{{session('mensaje')}}</p>
                    </div>
                @elseif(session('error'))
                    <div class="callout callout-warning">
                        <p>{{session('error')}}</p>
                    </div>
                @endif

                <input type="hidden" name="_token" value="{{ csrf_token() }}"/>
                <div class="box-body">
                    <div role="form" class="form-horizontal">
                        <div class="form-group">
                            <label class="control-label col-sm-2" for="alumno">Alumno</label>
                            <div class="col-sm-5">
                                <select class="form-control select2" name="alumno" id="alumno">
                                    @foreach($alumnos as $alumno)
                                        <option value="{{$alumno->matricula}}">{{ $alumno->nombre}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-sm-2" for="registro">Materia</label>
                            <div class="col-sm-5">
                                <select class="select2 form-control" name="registro" id="registro">
                                    @foreach($registros as $registro)
                                        <option value="{{$registro->id}}">{{$registro->materia}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-9 col-lg-offset-1">
                        <table id="tablaGrupos" class="table table-bordered table-hover" style="font-size: 12px;">
                            <thead>
                            <tr>
                                <th class="col-md-1 col-xs-1">Matricula</th>
                                <th class="col-md-4 col-xs-4">Nombre</th>
                                <th class="col-md-4 col-xs-4">Materia</th>
                                <th class="col-md-1 col-xs-1">CF</th>
                                <th class="col-md-1 col-xs-1">EX</th>
                                <th class="col-md-1 col-xs-1"></th>
                            </tr>
                            </thead>
                            <tbody>
                                @foreach($recursamientos as $recursamiento)
                                    <tr>
                                        <td scope="row">{{ $recursamiento->matricula }}</td>
                                        <td>{{ $recursamiento->nombre }}</td>
                                        <td>{{ $recursamiento->materia }}</td>
                                        <td>{{ $recursamiento->cf }}</td>
                                        <td>{{ $recursamiento->ts }}</td>
                                        <td><a class="btn btn-danger" onclick="eliminarAlumno({{ $recursamiento->id }})">eliminar</a></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="box-footer">
                    <div class="col-sm-7">
                        <button type="submit" class="btn btn-primary pull-right" onclick="showCalificacion()">Generar
                            Asignación
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </section>
    <!--Modal de alumno-->
    <div class="modal fade" id="myModal" role="dialog">
        <div class="modal-dialog modal-lg">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title" id="nombreAlumno"></h4>
                </div>
                <div class="modal-body">
                    <div class="col-sm-12"><h4 id="nombreMateria"></h4></div>
                    <div class="col-sm-12">
                        <div class="form-inline">
                            <input type="text" name="idAlumno" id="idAlumno" hidden>
                            <input type="text" name="nomAlumno" id="nomAlumno" hidden>
                            <input type="text" name="nomMateria" id="nomMateria" hidden>
                            <input type="text" name="idRegistro" id="idRegistro" hidden>
                            <div class="form-group">
                                <label>Calificación Final</label>
                                <input class="form-control" type="number" name="pc" id="pc" value="0"/>
                            </div>
                            <div class="form-group">
                                <label>Calificación Extraordinario</label>
                                <input class="form-control" type="number" name="extra" id="extra" value="0"/>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" onclick="GuardarAlumno()">Aceptar</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                </div>
            </div>

        </div>
    </div>
@endsection
@section("js")
    <script src="{{asset('components/plugins/select2/select2.full.min.js')}}"></script>
    <script>
        function showCalificacion() {
            //obtenemos los id seleccionados
            var idAlumno = $("#alumno").val();
            var idRegistro = $("#registro").val();
            $("#idAlumno").val(idAlumno);
            $("#idRegistro").val(idRegistro);
            //texto de los options
            var nombreAlumno = $('#alumno option:selected').text();
            var registroNombre = $('#registro option:selected').text();
            $("#nombreAlumno").empty();
            $("#nombreMateria").empty();
            $("#nombreAlumno").append(nombreAlumno);
            $("#nombreMateria").append(registroNombre);
            $("#nomMateria").val(registroNombre);
            $("#nomAlumno").val(nombreAlumno);
            $("#myModal").modal();
        }

        function GuardarAlumno() {
            //obtenemos todos los datos
            var idAlumno = $("#idAlumno").val();
            var idRegistro = $("#idRegistro").val();
            var dataTable = $('#tablaGrupos').DataTable();
            var registroNombre = $("#nomMateria").val();
            var nombreAlumno = $("#nomAlumno").val();
            var pc = $("#pc").val();
            var extra = $("#extra").val();
            //hacemos el ajax
            swal({
                title: '¿Guardar la calificación?',
                text: "",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Guardar',
                cancelButtonText: 'Cancelar',
            }).then(function () {
                $.ajax({
                    type: "POST",
                    url: "/modules/registro/recursamiento",
                    data: {
                        matricula: idAlumno,
                        idAsignacionMateria: idRegistro,
                        pc: pc,
                        ex: extra,
                    },
                    dataType: "json",
                    error: function (jqXHR, textStatus, errorThrown) {
                        swal("Error!", errorThrown, "error");
                    },
                    success: function (data, textStatus, jqXHR) {
                        if (data["success"]) {
                            swal("Correcto!", data["success"], "success");
                            dataTable.row.add([
                                idAlumno,
                                nombreAlumno,
                                registroNombre,
                                pc + '',
                                extra + '',
                                '<a class="btn btn-danger" onclick="eliminarAlumno('+data["id"]+')">eliminar</a>',
                            ]).draw(false);
                            $("#myModal").modal("hide");
                        } else if (data["error"]) {
                            swal("Atención!", data["error"], "warning");
                        } else {
                            var text = "";
                            $.each(data, function (key, value) {
                                text += "<li>" + value + "</li>";
                            });
                            swal("!", data[0], "success");
                            swal({
                                title: 'Atención!',
                                html: text,
                                type: 'warning',
                            });
                        }

                    }
                });
            });

        }

        function eliminarAlumno(idAsignacion) {
            swal({
                title: '¿Deseas elminiar el registro?',
                text: "Una vez eliminado, no se podrá recuperar",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Eliminar',
                cancelButtonText: 'Cancelar',
            }).then(function () {
                $.ajax({
                    type: "DELETE",
                    url: "/modules/registro/recursamiento",
                    data: {
                        idAsignacion: idAsignacion,
                    },
                    dataType: "json",
                    error: function (jqXHR, textStatus, errorThrown) {
                        swal("Error!", errorThrown, "error");
                    },
                    success: function (data, textStatus, jqXHR) {
                        if (data["success"]) {
                            swal("Correcto!", data["success"], "success");
                            location.reload(true);
                        } else if (data["error"]) {
                            swal("Atención!", data["error"], "warning");
                        }
                    }
                });
            });
        }
        $(function () {
            $(".select2").select2();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

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