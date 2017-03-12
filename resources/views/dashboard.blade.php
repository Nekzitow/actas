@extends('layouts.menu')
@section('css')
    <style type="text/css">
        .pointer {cursor: pointer}
    </style>
@endsection
@section('title',"Menu principal")
@section('contenido')
    <div class="content-wrapper">
        <section class="content-header">
            <h1>
                Menú Principal
                <small>Sistema Integral Universidad Maya</small>
            </h1>
            <ol class="breadcrumb">
                <li><a href="{{ url("/") }}" class="active"><i class="fa fa-dashboard"></i> Home</a></li>
            </ol>
        </section>
        <section class="content">
            <div class="row">
                <div class="col-lg-3 col-xs-6 pointer" onclick="window.location = '{{url("modules/actas")}}'">
                    <!-- small box -->
                    <div class="small-box bg-aqua">
                        <div class="inner">
                            <h3>Actas</h3>
                            <p><strong>Finales</strong></p>
                        </div>
                        <div class="icon">
                            <i class="fa fa-files-o"></i>
                        </div>
                        <a href="{{ url("modules/actas/") }}" class="small-box-footer">
                            Acceder <i class="fa fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
                <div class="col-lg-3 col-xs-6 pointer" onclick="window.location = '{{url("modules/estadistica")}}'">
                    <!-- small box -->
                    <div class="small-box bg-aqua">
                        <div class="inner">
                            <h3>Estadística</h3>
                            <p><strong>Cuatrimestral</strong></p>
                        </div>
                        <div class="icon">
                            <i class="fa fa-area-chart"></i>
                        </div>
                        <a href="{{ url("modules/estadistica/") }}" class="small-box-footer">
                            Acceder <i class="fa fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
                <div class="col-lg-3 col-xs-6 pointer" onclick="window.location = '{{url("modules/registro")}}'" >
                    <!-- small box -->
                    <div class="small-box bg-aqua">
                        <div class="inner">
                            <h3>Registros </h3>
                            <p><strong>De Escolaridad</strong></p>
                        </div>
                        <div class="icon">
                            <i class="fa fa-area-chart"></i>
                        </div>
                        <a href="{{ url("modules/registro/") }}" class="small-box-footer">
                            Acceder <i class="fa fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection