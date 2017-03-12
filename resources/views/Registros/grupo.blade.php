<!-- Modal -->
<div class="modal fade" id="modalGrupo" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Agregar nuevo grupo</h4>
            </div>
            <div class="modal-body">
                <form class="form-horizontal">
                    <input type="hidden" name="_token" id="_token" value="{{ csrf_token() }}"/>
                    <div class="form-group">
                        <label class="control-label col-sm-3">Agregar Grupo</label>
                        <div class="col-sm-8">
                            <select class="select2" id="grupos">
                                @foreach($grupos as $grupo)
                                    <option value="{{$grupo->id}}">{{$grupo->nombre}}</option>
                                @endforeach
                            </select>
                        </div>
                        <input type="hidden" id="cicloEscolar" value="{{$idCiclo}}">
                        <input type="hidden" id="idCarrera" value="{{$idCarrera}}">
                        <input type="hidden" id="modalidad" value="{{$modalidad}}">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" onclick="agregarNuevoGrupo()">Aceptar</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
            </div>
        </div>

    </div>
</div>

<div class="modal fade" id="alumnosGrupo" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Agregar nuevo grupo</h4>
            </div>
            <div class="modal-body">
                <form class="form-horizontal">
                    <input type="hidden" name="_token2" id="_token2" value="{{ csrf_token() }}"/>
                    <div class="form-group">
                        <label class="control-label col-sm-3" for="gruposAlumnos">Agregar Alumnos</label>
                        <div class="col-sm-8">
                            <select class="select2" id="gruposAlumnos">
                                <option disabled selected>Favor de seleccionar un alumno</option>
                            </select>
                        </div>
                        <input type="hidden" id="cicloEscolarNuevo" value="{{$idCiclo}}">
                        <input type="hidden" id="idGrupoNuevo">
                        <input type="hidden" id="idModalidadNuevo">
                        <input type="hidden" id="idCarreraNuevo" value="{{$idCarrera}}">
                        <input type="hidden" id="modalidadNuevo" value="{{$modalidad}}">
                    </div>
                </form>
                <div class="col-lg-offset-3 col-lg-5">
                    <button type="button" class="btn btn-primary pull-right" onclick="moverAlumno()">Aceptar</button>
                </div>
                <table id="tablaAlumnos" class="table table-bordered table-hover" style="font-size: 12px;">
                    <thead>
                    <tr>
                        <th class="col-md-1 col-xs-1">Matricula</th>
                        <th class="col-md-4 col-xs-4">Nombre</th>
                    </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
            </div>
        </div>

    </div>
</div>