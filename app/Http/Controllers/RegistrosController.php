<?php

namespace App\Http\Controllers;

use App\Campus;
use App\Carreras;
use App\ciclos;
use App\Clases\Alumnos;
use App\Clases\Carrera;
use App\Clases\ImprimeRegistro;
use App\Clases\Registros;
use App\Grupos_acta;
use App\Modalidad;
use App\RegistroActa;
use App\rvoe;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class RegistrosController extends Controller {

	public function inicio() {
		$respuesta = Registros::getList();
		return view("Registros.inicio", ["registros" => $respuesta]);
	}

	public function formulario() {
		$ciclos = ciclos::all();
		$carreras = Carreras::where('id_campus', 1)->get();
		return view("Registros.form", ['ciclos' => $ciclos, 'carreras' => $carreras]);
	}

	public function save(Request $request) {
		$validacion = \Validator::make($request->all(), [
			'archivo' => 'required|file',
			'idCarrera' => 'required',
			'rvoe' => 'required',
		], $this->messages());
		if ($validacion->fails()) {
			return redirect()->back()->withErrors($validacion->errors());
		} else {
			//obtenemos los campos del formulario
			$idCiclo = $request->idCiclo;
			$carrera = $request->idCarrera;
			$rvoe = $request->rvoe;
			$archivo = $request->file("archivo");
			$turno = $request->turno;
			$modalidadtipo = $request->esco;
			$extension = $archivo->getClientOriginalExtension();
			if ($extension == "CSV" || $extension == "csv") {
				$respuesta = Registros::leerArchivo($archivo, $carrera, $idCiclo, $rvoe, $turno, $modalidadtipo);
				if (isset($respuesta['error']))
					return redirect()->action('RegistrosController@inicio')->with('error', $respuesta['error']);
				else
					return redirect()->action('RegistrosController@inicio')->with('mensaje', $respuesta['success']);
			} else {
				return redirect()->back()->withErrors(["archivos" => $archivo->getClientOriginalName() . " : Archivo no soportado"]);
			}
		}
	}

	public function rvoes(Request $request) {
		$idCarrera = $request->idCarrera;
		try {
			$rvoes = rvoe::join("modalidad", "modalidad.id", "=", "id_modalidad")
				->select("modalidad.descripcion", "modalidad.id")->where("id_carreras", $idCarrera)->get();
			return $rvoes;
		} catch (QueryException $exception) {
			return ["Error" => "Error al obtener los rvoes, " . $exception->getMessage()];
		}
	}

	public function ver(Request $request) {
		//obtenemos las variables mandadas
		$idCarrera = $request->idCarrera;
		$idCiclos = $request->idCiclo;
		$modalidad = $request->modalidad;
		//obtenemos los datos de la carrera
		$carrera = Carrera::getCarreraSingle($idCarrera);
		//aqui mandamos a llamarl el método para buscar todos los grupos asociados a los parametros datdos
		$listGroups = Registros::getListaGrupo($idCarrera, $idCiclos, $modalidad);
		$grupos = Grupos_acta::orderBy("nombre", "ASC")->get();
		return view("Registros.ver", ["registros" => $listGroups, "carrera" => $carrera,
			"grupos" => $grupos, "idCarrera" => $idCarrera, "idCiclo" => $idCiclos, "modalidad" => $modalidad]);
	}

	public function recursamiento(Request $request) {
		$carrera = Carreras::find($request->idCarrera);
		$grupo = Grupos_acta::find($request->idGrupo);
		$modalidad = Modalidad::find($request->idModalidad);
		$tipoModalidad = $request->tipoMod;
		$alumnos = Alumnos::getAlumnos($carrera->id);
		$registros = Registros::findRegistrosMateria($grupo->id, $request->idCiclo, $carrera->id, $modalidad->id, $tipoModalidad);
		$recursamientos = Registros::alumnosRecursamiento($grupo->id, $request->idCiclo, $carrera->id, $modalidad->id, $tipoModalidad);
		$parameters = ["carrera" => $carrera,
			"grupo" => $grupo,
			"modalidad" => $modalidad,
			"alumnos" => $alumnos,
			"registros" => $registros,
			"recursamientos" => $recursamientos];
		return view("Registros.recursadores", $parameters);
	}

	public function reincorporacion() {
		$ciclos = ciclos::all();
		return view("Registros.recursamiento", ['ciclos' => $ciclos]);
	}

	public function saveReincorporacion(Request $request) {
		$validacion = \Validator::make($request->all(), [
			'archivor' => 'required|file',
		], $this->messages());
		if ($validacion->fails()) {
			return redirect()->back()->withErrors($validacion->errors());
		} else {
			$idCiclo = $request->idCiclo;
			$archivo = $request->file("archivor");
			$extension = $archivo->getClientOriginalExtension();
			if ($extension == "CSV" || $extension == "csv") {
				$respuesta = Registros::leerReincorporacion($archivo, $idCiclo);
				if (isset($respuesta['error']))
					return redirect()->action('RegistrosController@inicio')->with('error', $respuesta['error']);
				else
					return redirect()->action('RegistrosController@inicio')->with('mensaje', $respuesta['success']);
			} else {
				return redirect()->back()->withErrors(["archivos" => $archivo->getClientOriginalName() . " : Archivo no soportado"]);
			}
		}
	}

	public function verRecursadores(Request $request) {
		//obtenemos las variables mandadas
		$idCarrera = $request->idCarrera;
		$idCiclos = $request->idCiclo;
		$modalidad = $request->modalidad;
		return view("");
	}

	public function saveAlumnoRecursador(Request $request) {
		//validamos los datos enviados
		$validacion = \Validator::make($request->all(), [
			'matricula' => 'required',
			'idAsignacionMateria' => 'required',
			'pc' => 'required|integer|between:1,10',
			'ex' => 'required|integer|between:0,10',
		], $this->messages());
		if ($validacion->fails()) {
			return $validacion->errors()->all();
		} else {
			$matricula = $request->matricula;
			$idAsignacion = $request->idAsignacionMateria;
			$pc = $request->pc;
			$ex = $request->ex;
			//consultamos si este alumno ya tiene una calificacion para el registro de la materia
			$respuesta = Registros::findAsigacionMateria($idAsignacion, $matricula);
			if (count($respuesta) == 0) {
				//guardamos las primeras calificaciones del alumnos
				$idCalificacion = Registros::saveAlumno($pc, $matricula, $idAsignacion, 3);
				if (isset($idCalificacion["success"])) {
					//si el alumno tiene un titulo de insuficencia se guardara en regularización
					if ($ex > 0) {
						$ts = $ex;
						$respuestas = Registros::saveRegularizacion($idCalificacion["success"], $ts);
						if (isset($respuestas["error"])) {
							$response = $respuestas;
						} else {
							$response = ["success" => $respuestas["success"], "id" => $idCalificacion["success"]];
						}
					} else {
						$response = ["success" => "Se guardó correctamente", "id" => $idCalificacion["success"]];
					}
				} else {
					$response = $idCalificacion;

				}
				return $response;
			} elseif (isset($respuesta["error"])) {
				return $respuesta;
			} elseif (isset($respuesta[0]["id"])) {
				//en el caso que todo este bien hacemos el guardado
				return ["error" => "El alumno ya cuenta con un registro para esta materia"];
			}

		}

	}

	public function eliminarRegistroAlumno(Request $request) {
		return Registros::delete($request->idAsignacion);
	}

	public function printRegistro(Request $request) {
		$imprime = new ImprimeRegistro();
		$carrera = Carreras::find($request->idCarrera);
		$campus = Campus::join("direccion", "id_campus", "=", "campus.id")
			->where("campus.id", $carrera->id_campus)
			->get();
		$cicloEscolar = ciclos::find($request->idCiclo);
		$grupo = Grupos_acta::find($request->idGrupo);
		$modalidad = Modalidad::find($request->idModalidad);
		$tipoModalidad = $request->tipoMod;
		$rvoe = rvoe::where([["id_carreras", $carrera->id], ["id_modalidad", $modalidad->id]])->first();
		$materias = Registros::findRegistrosMateria($grupo->id, $request->idCiclo, $carrera->id, $modalidad->id, $tipoModalidad);
		$registros = Registros::getAlumnosRegistro($grupo->id, $request->idCiclo, $carrera->id, $modalidad->id, $tipoModalidad);
		$registrosReg = Registros::getAlumnosRegistroReg($grupo->id, $request->idCiclo, $carrera->id, $modalidad->id, $tipoModalidad);
		//return $registrosReg;
		//return $registros;
		//$alumnos = Alumnos::getAlumnosRegistros($grupo->id, $request->idCiclo, $carrera->id, $modalidad->id, $tipoModalidad);
		/*$registros = Registros::findRegistrosMateria($grupo->id, $request->idCiclo, $carrera->id, $modalidad->id, $tipoModalidad);
		$recursamientos = Registros::alumnosRecursamiento($grupo->id, $request->idCiclo, $carrera->id, $modalidad->id, $tipoModalidad);*/
		$imprime->imprimeRegistro($carrera, $campus, $cicloEscolar, $grupo, $modalidad, $tipoModalidad, $rvoe, $materias, $registros, $registrosReg);
	}

	public function grupo(Request $request) {
		$idGrupo = $request->idGrupo;
		$idCarrera = $request->idCarrera;
		$idCiclo = $request->idCiclo;
		$modalidad = $request->modalidad;
		$registros = Registros::buscarGrupo($idCarrera, $idCiclo, $modalidad, $idGrupo);
		if (count($registros) > 0) {
			return ["error" => "Ya existe el grupo en el sistema"];
		} else {
			//hacemos el guardado del grupo
			$listGroups = Registros::getListaGrupo($idCarrera, $idCiclo, $modalidad);
			$grupo = Grupos_acta::find($idGrupo);
			$grupo2 = Grupos_acta::where("grado", $grupo->grado)->get();
			$response = [];
			$encontrado = 0;
			$idModalidad = 0;
			$idGrupoEcontrado = 0;
			$turno = 0;
			foreach ($listGroups as $lista) {
				foreach ($grupo2 as $gp) {
					if ($gp->id == $lista->id_grupos_actas) {
						$idModalidad = $lista->id_modalidad;
						$idGrupoEcontrado = $gp->id;
						$encontrado = 1;
						break;
					}
				}
				if ($encontrado > 0)
					break;
			}

			if ($encontrado == 0) {
				return ["error" => "No hay alumnos disponibles para este grupo"];
			} else {
				$materias = Registros::findRegistrosMateria($idGrupoEcontrado, $idCiclo, $idCarrera, $idModalidad, $modalidad);
				foreach ($materias as $materia) {
					$reg = new RegistroActa();
					$reg->turno = $materia->turno;
					$reg->id_grupos_actas = $idGrupo;
					$reg->id_ciclos = $idCiclo;
					$reg->materia = $materia->materia;
					$reg->id_carreras = $idCarrera;
					$reg->id_modalidad = $idModalidad;
					$reg->tipo_modalidad = $modalidad;
					$reg->save();
				}

				$arrayAlumnos = array();
				//buscamos todos los alumnos de los otros grupos similares y los enviamos
				foreach ($grupo2 as $gp) {
					//$materiasGrupo = Registros::findRegistrosMateria($gp->id, $idCiclo, $idCarrera, $idModalidad, $modalidad);
					$registros2 = Registros::getAlumnosRegistro($gp->id, $idCiclo, $idCarrera, $idModalidad, $modalidad);
					if (count($registros2) > 0)
						array_push($arrayAlumnos, $registros2);
				}
				return ["success" => "Se guardo el grupo", "idGrupo" => $idGrupo, "idCarrera" => $idCarrera,
					"idModalidad" => $idModalidad, "tipoModalidad" => $modalidad, "idCiclo" => $idCiclo, "arrayAlumnos" => $arrayAlumnos];
			}
		}

	}

	public function alumno(Request $request) {
		$validacion = \Validator::make($request->all(), [
			'matricula' => 'required',
		], $this->messages());
		if ($validacion->fails()) {
			return ["error" => "Favor de seleccionar a un alumno"];
		} else {
			$idGrupo = $request->idGrupo;
			$idCarrera = $request->idCarrera;
			$idCiclo = $request->idCiclo;
			$modalidad = $request->modalidad;
			$idModalidad = $request->idModalidad;
			$matricula = $request->matricula;

			$materiasAlumno = Registros::getMateriasAlumno($matricula, $idCiclo);
			$materias = Registros::findRegistrosMateria($idGrupo, $idCiclo, $idCarrera, $idModalidad, $modalidad);
			$respuesta = [];
			foreach ($materiasAlumno as $item) {
				foreach ($materias as $item2) {
					if ($item->materia == $item2->materia) {
						//actualizamos al alumno
						$respuesta = Registros::updateAlumnoMateria($item2->id, $matricula, $item->id_registro_acta);
						break;
					}
					if (isset($respuesta["error"])) {
						return ["error" => $respuesta["errror"]];
					}
				}
			}
			return $respuesta;
		}

	}

	public function getAlumnosSing(Request $request) {
		$idGrupo = $request->idGrupo;
		$idCarrera = $request->idCarrera;
		$idCiclo = $request->idCiclo;
		$modalidad = $request->modalidad;
		$idModalidad = $request->idModalidad;
		$grupo = Grupos_acta::find($idGrupo);
		$grupo2 = Grupos_acta::where("grado", $grupo->grado)->get();
		$arrayAlumnos = array();
		//buscamos todos los alumnos de los otros grupos similares y los enviamos
		foreach ($grupo2 as $gp) {
			//$materiasGrupo = Registros::findRegistrosMateria($gp->id, $idCiclo, $idCarrera, $idModalidad, $modalidad);
			$registros2 = Registros::getAlumnosRegistro($gp->id, $idCiclo, $idCarrera, $idModalidad, $modalidad);
			if (count($registros2) > 0)
				array_push($arrayAlumnos, $registros2);
		}

		$alumnosDelGrupo = Registros::getAlumnosRegistro($idGrupo, $idCiclo, $idCarrera, $idModalidad, $modalidad);


		return ["success" => "Se guardo el grupo","arrayAlumnos" => $arrayAlumnos,"lista"=>$alumnosDelGrupo];


	}

	public function messages() {
		return [
			'archivo.required' => 'Archivo Titulo de insuficiencia requerido',
			'archivor.required' => 'Archivo Alumnos reincorporación requerido',
			'matricula.required' => 'Matrícula del alumno requerida',
			'idAsignacionMateria.required' => 'Especifíque la materia',
			'pc.required' => 'Promedio cuatrimestral requerido',
			'ex.required' => 'Calificacion examen extraordinario requerido',
			'pc.between' => 'La calificación cuatrimestral debe estar entre 1 - 10',
			'ex.between' => 'Calificacion examen extraordinario debe estar entre 1 - 10',
		];
	}
}
