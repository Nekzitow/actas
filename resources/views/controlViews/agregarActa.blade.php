@extends('layouts.principal')
@section('title', 'Actas')
@section('menuLateral')
    <li class="active">
        <a href="/modules/actas/agregar/acta">
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
            <li><a href="#">Control Escolar</a></li>
            <li class="active">Actas Finales</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">

        <div class="col-sm-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Generar Acta</h3>
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
                      action="{{action('actasControlador@guardarActa')}}" enctype="multipart/form-data">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}"/>
                    <div class="box-body">
                        <div class="form-group">
                            <label for="carrera" class="col-sm-2 control-label">Carrera</label>
                            <div class="col-sm-5">
                                <select class="form-control input-sm" id="idCarrera" name="idCarrera">
                                    <!--<option>Elige una carrera</option>-->
                                    <?php foreach ($carreras as $carrera) {
                                        echo "<option value=" . $carrera->id . ">" . $carrera->nombre . "</option>";
                                    }?>
                                </select>
                            </div>
                        </div>
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
                            <label for="grupo" class="col-sm-2 control-label">Grupo</label>
                            <div class="col-sm-5">
                                <select class="form-control input-sm" id="idGrupo" name="idGrupo">
                                    <!--<option>Elige un grupo</option>-->
                                    <?php foreach ($grupos as $grupo) {
                                        echo "<option value=" . $grupo->id . ">" . $grupo->nombre . "</option>";
                                    }?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="turno" class="col-sm-2 control-label">Turno</label>
                            <div class="col-sm-5">
                                <select class="form-control input-sm" id="turno" name="turno">
                                    <option value="1">Matutino</option>
                                    <option value="2">Vespertino</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="turno" class="col-sm-2 control-label">Modalidad</label>
                            <div class="col-sm-5">
                                <select class="form-control input-sm" id="esco" name="esco">
                                    <option value="ESCOLARIZADO">Escolarizado</option>
                                    <option value="SABADOS">Sabados</option>
                                    <option value="DOMINGOS">Domingos</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="turno" class="col-sm-2 control-label">Clave D.S.E</label>
                            <div class="col-sm-5">
                                <input type="text" id="clavedse" name="clavedse" class="form-control input-sm">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="archivo" class="col-sm-2 control-label">Subir Archivo</label>
                            <div class="col-sm-5">
                                <input type="file" id="archivo" name="archivo">
                                <p class="help-block">Favor de subir el archivo en formato CSV.</p>
                            </div>
                        </div>
                    </div>


                    <div class="box-footer">
                        <div class="col-sm-7">
                            <button type="submit" class="btn btn-primary pull-right">Generar Acta</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

    </section>
    <!-- /.content -->
@endsection
  