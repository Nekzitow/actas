@extends('layouts.principal')
@section('title','Recursamientos')
@section('css')
    <!-- Select2 -->
    <link rel="stylesheet" href="{{ asset('components/plugins/select2/select2.min.css') }}">
@endsection
@section('menuLateral')
    <li class="active">
        <a href=" {{ url('/modules/registro') }}">
            <i class="fa fa-area-chart"></i> <span>Registro</span>
        </a>
    </li>
@endsection
@section('contenido')
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
                    <h3 class="box-title">Generar Reincorporaciones</h3>
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
                      action="{{action('RegistrosController@saveReincorporacion')}}" enctype="multipart/form-data">
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
                        <div class="form-group {{ $errors->has('archivor') ? ' has-error' : '' }}">
                            <label for="archivor" class="col-sm-2 control-label">Subir Archivo</label>
                            <div class="col-sm-5">
                                <input type="file" id="archivor" name="archivor" class="filestyle"
                                       data-buttonText="Elegir archivo" data-buttonName="btn-info"
                                       data-size="sm">
                                @if($errors->has('archivor'))
                                    <p class="help-block">
                                        <strong>{{ $errors->first('archivor') }}</strong>
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
@section('js')
    <script type="text/javascript"
            src="{{ asset('components/plugins/bootstrap-filestyle-1.2.1/bootstrap-filestyle.min.js')}}"></script>
    <script src="{{asset('components/plugins/select2/select2.full.min.js')}}"></script>
    <script>
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