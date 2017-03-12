@extends("layouts.principal")
@section("title","Agregar Registro")
@section("css")
    <!-- Select2 -->
    <link rel="stylesheet" href="{{ asset('components/plugins/select2/select2.min.css') }}">
@endsection
@section("menuLateral")
    <li class="active">
        <a href=" {{ url('/modules/registro') }}">
            <i class="fa fa-area-chart"></i> <span>Registro</span>
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
    <!-- Main content -->
    <section class="content">

        <div class="col-sm-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Generar Registro</h3>
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
                @if($errors->has('archivos'))
                    <div class="callout callout-danger">
                        <p>{{$errors->first('archivos')}}</p>
                    </div>
                @endif
                <form role="form" method="POST" class="form-horizontal"
                      action="{{action('RegistrosController@save')}}" enctype="multipart/form-data">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}"/>
                    <div class="box-body">
                        <div class="form-group">
                            <label for="ciclo" class="col-sm-2 control-label">Ciclo</label>
                            <div class="col-sm-5">
                                <select class="form-control input-sm select2" id="idCiclo" name="idCiclo">
                                    <!--<option>Elige un ciclo</option>-->
									<?php foreach ($ciclos as $ciclo) {
										echo "<option value=" . $ciclo->id . ">" . $ciclo->nombre_ciclo . "</option>";
									}?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group {{ $errors->has('idCarrera') ? ' has-error' : '' }}">
                            <label for="carrera" class="col-sm-2 control-label">Carrera</label>
                            <div class="col-sm-5">
                                <select class="form-control input-sm select2" id="idCarrera" name="idCarrera" onchange="getRvoe()">
                                    <option selected disabled>Elige una carrera</option>
									<?php foreach ($carreras as $carrera) {
										echo "<option value=" . $carrera->id . ">" . $carrera->nombre . "</option>";
									}?>
                                </select>
                                @if($errors->has('idCarrera'))
                                    <p class="help-block">
                                        <strong>{{ $errors->first('idCarrera') }}</strong>
                                    </p>
                                @endif
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="turno" class="col-sm-2 control-label">Turno</label>
                            <div class="col-sm-5">
                                <select class="form-control input-sm select2" id="turno" name="turno">
                                    <option value="1">Matutino</option>
                                    <option value="2">Vespertino</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group {{ $errors->has('esco') ? ' has-error' : '' }}">
                            <label for="esco" class="col-sm-2 control-label">Modalidad</label>
                            <div class="col-sm-5">
                                <select class="form-control input-sm select2" id="esco" name="esco">
                                    <option value="ESCOLARIZADO">Escolarizado</option>
                                    <option value="SABADOS">Sabados</option>
                                    <option value="DOMINGOS">Domingos</option>
                                </select>
                                @if($errors->has('esco'))
                                    <p class="help-block">
                                        <strong>{{ $errors->first('esco') }}</strong>
                                    </p>
                                @endif
                            </div>
                        </div>
                        <div class="form-group {{ $errors->has('rvoe') ? ' has-error' : '' }}">
                            <label for="rvoe" class="col-sm-2 control-label">RVOE</label>
                            <div class="col-sm-5">
                                <select class="form-control input-sm select2" id="rvoe" name="rvoe"></select>
                                @if($errors->has('rvoe'))
                                    <p class="help-block">
                                        <strong>{{ $errors->first('rvoe') }}</strong>
                                    </p>
                                @endif
                            </div>
                        </div>
                        <div class="form-group {{ $errors->has('archivo') ? ' has-error' : '' }}">
                            <label for="archivo" class="col-sm-2 control-label">Subir Archivo</label>
                            <div class="col-sm-5">
                                <input type="file" id="archivo" name="archivo" class="filestyle"
                                       data-buttonText="Elegir archivo" data-buttonName="btn-info"
                                       data-size="sm">
                                @if($errors->has('archivo'))
                                    <p class="help-block">
                                        <strong>{{ $errors->first('archivo') }}</strong>
                                    </p>
                                @else
                                    <p>Favor de subir el archivo en formato CSV.</p>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="box-footer">
                        <div class="col-sm-7">
                            <button type="submit" class="btn btn-primary pull-right">Generar Asignación</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

    </section>
@endsection

@section("js")
    <script type="text/javascript"
            src="{{ asset('components/plugins/bootstrap-filestyle-1.2.1/bootstrap-filestyle.min.js')}}"></script>
    <script src="{{asset('components/plugins/select2/select2.full.min.js')}}"></script>
    <script>
        function getRvoe() {
            idCarrera = $("#idCarrera").val();
            $("#rvoe").empty();
            $.ajax({
                type: "GET",
                url: "/modules/registro/rvoe",
                data: {
                    idCarrera : idCarrera,
                },
                dataType: "json",
                error: function (jqXHR, textStatus, errorThrown) {
                    swal("Error",errorThrown,"error");
                },
                success: function (data, textStatus, jqXHR) {
                    var options = "<option disabled selected>Selecciona un grupo</option>";
                    $.each(data, function(idx, obj) {
                        options+="<option value="+obj.id+">"+obj.descripcion+"</option>";
                    });
                    $("#rvoe").append(options);
                }
            });
        }
        $(function () {
            $(".select2").select2();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
        });
    </script>
@endsection