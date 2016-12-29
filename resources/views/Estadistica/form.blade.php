@extends('layouts.principal')
@section('title', 'Actas')
@section('menuLateral')
    <li class="active">
        <a href="/modules/actas/agregar/acta">
            <i class="fa fa-files-o"></i></i> <span>Generar Asignacion</span></i>
        </a>
    </li>
@endsection
@section('contenido')
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

    <!-- Main content -->
    <section class="content">

        <div class="col-sm-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Generar Asignacion</h3>
                </div>
                <!-- /.box-header -->
                <!-- form start -->
                @if (count($errors) > 0)
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <form role="form" method="POST" class="form-horizontal"
                      action="{{action('EstadisticaController@save')}}" enctype="multipart/form-data">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}"/>
                    <div class="box-body">
                        <div class="form-group">
                            <label for="ciclo" class="col-sm-2 control-label">Ciclo</label>
                            <div class="col-sm-5">
                                <select class="form-control input-sm" id="idCiclo" name="idCiclo" >
                                    <!--<option>Elige un ciclo</option>-->
                                    <?php foreach ($ciclos as $ciclo) {
                                        echo "<option value=" . $ciclo->id . ">" . $ciclo->nombre_ciclo . "</option>";
                                    }?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="archivo" class="col-sm-2 control-label">Subir Archivo</label>
                            <div class="col-sm-5">
                                <input type="file" id="archivo" name="archivo" class="filestyle"
                                       data-buttonText="Elegir archivo" data-buttonName="btn-warning"
                                       data-size="sm">
                                <p>Favor de subir el archivo en formato CSV.</p>
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
    <!-- /.content -->
@endsection
@section("js")
    <!-- bootstrap filestyle -->
    <script type="text/javascript"
            src="{{ asset('components/plugins/bootstrap-filestyle-1.2.1/bootstrap-filestyle.min.js')}}"></script>
    <script>

        $(function () {
            //Initialize Select2 Elements
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            //Date picker



            $(":file").filestyle({buttonText: "Elegir Archivo", size: "sm"});

        });
    </script>
@endsection