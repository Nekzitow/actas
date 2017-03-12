<?php
/**
 * Created by PhpStorm.
 * User: OSORIO
 * Date: 07/02/2017
 * Time: 09:56 AM
 */

namespace App\Clases;


use App\AlumnoRegistro;
use App\RegistroActa;
use App\Regularizacion;
use DB;
use Illuminate\Database\QueryException;

class Registros {


	/**
	 * @param $file
	 * @param $carrera
	 * @param $idCiclo
	 * @param $rvoe
	 * @return $this|array
	 */
	public static function leerArchivo($file, $carrera, $idCiclo, $rvoe, $turno, $modalidadtipo) {
		try {
			$fp = fopen($file, "r");
			$response = [];
			$materia = "";
			$grupos = "";
			$arregloGP = null;
			$con = 0;
			$concentrado = [];
			$arrayGrupos = [];
			$arregloAlumnos = [];
			while (($data = fgetcsv($fp, 1000, ",")) !== FALSE) { // Mientras hay líneas que leer...
				if ($data[4] == "Clase :") {
					if ($materia != "") {
						array_push($arrayGrupos, ["alumnos" => $arregloAlumnos]);
						array_push($concentrado, $arrayGrupos);
						$arrayGrupos = [];
						$arregloAlumnos = [];
					}
					//obtenemos la materia
					$materia = $data[5];
					array_push($arrayGrupos, ["materia" => $materia]);
					//echo $materia."<br>";
				}

				if ($data[9] != "") {
					$grupos = $data[9];
					$arregloGP = str_split($grupos);
					$grado = substr($data[9], -3, 1);
					$grupo = substr($data[9], -2, 1);
					$idGrupo = Estadisticas::getIdGrupo($grado, $grupo);
					//obtenemos el grado y el grupo de arreglo para obtener el id del grupo
					if (is_numeric($arregloGP[1])) {
						$idGrupo = $idGrupo[0]["id"];
						array_push($arrayGrupos, ["grupo" => $idGrupo]);
					} elseif (is_numeric($arregloGP[2])) {
						$idGrupo = $idGrupo[0]["id"];
						array_push($arrayGrupos, ["grupo" => $idGrupo]);
					} elseif (is_numeric($arregloGP[3])) {
						$grado = substr($data[9], -2, 1);
						$grupo = substr($data[9], -3, 1);
						$idGrupo = Estadisticas::getIdGrupo($grado, $grupo);
						$idGrupo = $idGrupo[0]["id"];
						array_push($arrayGrupos, ["grupo" => $idGrupo]);

					}

				}


				//Aqui obtenemos todos los datos del alumno
				$matricula = $data[1];

				if (intval($matricula)) {
					$cf = $data[6];
					$ex = $data[7];
					$ts = $data[8];
					$alumnos = Alumnos::getAlumno($matricula);
					$calificacion = ["matricula" => $alumnos->matricula, "cf" => $cf, "ex" => $ex, "ts" => $ts];
					array_push($arregloAlumnos, $calificacion);
				}
				//Ahora vamos con las calificaciones de los alumnos
			}
			echo "<br>";
			array_push($arrayGrupos, ["alumnos" => $arregloAlumnos]);
			array_push($concentrado, $arrayGrupos);
			//empezamos guardando los datos
			foreach ($concentrado as $concen) {
				//Guardamos el acta de registro y devolvemos un repuesta
				$registros = self::guardarRegistro($concen[0]["materia"], $concen[1]["grupo"], $carrera, $turno, $idCiclo, $rvoe, $modalidadtipo);
				if (isset($registros["success"])) {
					//obtenemos el id y hacemos el guardado de calificaciones para los alumnos
					$arrayAlumnos = $concen[2]["alumnos"];
					$idRegistro = $registros["success"];
					$response = self::recorrerAlumno($arrayAlumnos, $idRegistro);
				} else {
					$response = $registros;
					break;
				}

			}
			fclose($fp);
			return $response;
		} catch (Exception $e) {
			return redirect()->back()->withErrors(["error", "archivo no soportado"]);
		}
	}


	/**
	 * Guardar calificacion registro alumno
	 */
	public static function recorrerAlumno($arrayAlumnno, $idRegistro) {
		try {
			$response = [];
			foreach ($arrayAlumnno as $alumno) {
				$cf = $alumno["cf"];
				if ($alumno["ex"] != "") {
					$cf = $alumno["ex"];
				}
				//guardamos las primeras calificaciones del alumnos
				$idCalificacion = self::saveAlumno($cf, $alumno["matricula"], $idRegistro, 1);
				if (isset($idCalificacion["success"])) {
					//si el alumno tiene un titulo de insuficencia se guardara en regularización
					if ($alumno["ts"] != "") {
						$ts = $alumno["ts"];
						$respuestas = self::saveRegularizacion($idCalificacion["success"], $ts);
						if (isset($respuestas["error"])) {
							$response = $respuestas;
							break;
						} else {
							$response = $respuestas;
						}
					} else {
						$response = ["success" => "Se guardó correctamente"];
					}
				} else {
					$response = $idCalificacion["error"];
					break;
				}
				//echo $alumno["matricula"] . " ----- " . $alumno["cf"] . " ----- " . $alumno["ex"] . " ----- " . $alumno["ts"] . "<br>";

			}
			return $response;
		} catch (QueryException $exception) {
			return ["error" => $exception->getMessage()];
		}
	}

	/**
	 * Guarda las calificaciones del alumno para registro escolar
	 * @param $calificacion
	 * @param $matricula
	 * @param $idRegistro
	 * @return array
	 */
	public static function saveAlumno($calificacion, $matricula, $idRegistro, $tipo) {
		try {
			$alumno = new AlumnoRegistro();
			$alumno->calificacion = $calificacion;
			$alumno->matricula = $matricula;
			$alumno->id_registro_acta = $idRegistro;
			$alumno->tipo = $tipo;
			$alumno->save();
			return ["success" => $alumno->id];
		} catch (QueryException $exception) {
			return ["error" => "Error al guardar al alumno" . $exception->getMessage()];
		}
	}

	public static function saveRegularizacion($idRegistroAlumno, $calificacion) {
		try {
			$regular = new Regularizacion();
			$regular->calificacion = $calificacion;
			$regular->id_alumno_registro = $idRegistroAlumno;
			$regular->save();
			return ["success" => "Se guardó correctamente"];
		} catch (QueryException $exception) {
			return ["error" => "Error al guardar las regularizacion del alumno: " . $exception->getMessage()];
		}
	}

	/**
	 * Guardar registro de escolaridad
	 * @return AlumnoRegistro
	 */
	public static function guardarRegistro($materia, $idGrupo, $idCarrera, $turno,
										   $ciclo, $idModalidad, $tipo_modalidad) {
		try {
			$registro = new RegistroActa();
			$registro->turno = $turno;
			$registro->id_grupos_actas = $idGrupo;
			$registro->id_ciclos = $ciclo;
			$registro->materia = $materia;
			$registro->id_carreras = $idCarrera;
			$registro->id_modalidad = $idModalidad;
			$registro->tipo_modalidad = $tipo_modalidad;
			$registro->save();
			return ["success" => $registro->id];
		} catch (QueryException $exception) {
			return ["error" => "Error al guardar el registro" . $exception->getMessage()];
		}
	}

	public static function getList() {
		try {
			$registros = RegistroActa::join('carreras', 'carreras.id', '=', 'registro_acta.id_carreras')
				->join('ciclos', 'ciclos.id', '=', 'registro_acta.id_ciclos')
				->select('carreras.nombre', 'ciclos.nombre_ciclo', 'tipo_modalidad', 'id_carreras', 'id_ciclos')
				->groupBy('carreras.nombre', 'ciclos.nombre_ciclo', 'tipo_modalidad', 'id_carreras', 'id_ciclos')
				->orderBy('nombre')->get();
			return $registros;
		} catch (QueryException $e) {
			return ["error" => "Error al obtener la lista de registros, " . $e->getMessage()];
		}
	}

	public static function getListaGrupo($idCarrera, $idCiclos, $modalidad) {
		try {
			$registros = RegistroActa::join("grupos_acta", "grupos_acta.id", "=", "id_grupos_actas")
				->join("modalidad", "modalidad.id", "=", "id_modalidad")
				->where([["id_carreras", $idCarrera], ["id_ciclos", $idCiclos], ["tipo_modalidad", $modalidad]])
				->select("id_grupos_actas", "grupos_acta.nombre", "id_carreras", "id_ciclos", "id_modalidad", "modalidad.descripcion", "tipo_modalidad")
				->groupBy("id_grupos_actas", "grupos_acta.nombre", "id_carreras", "id_ciclos", "id_modalidad", "modalidad.descripcion", "tipo_modalidad")
				->get();
			return $registros;
		} catch (QueryException $exception) {
			return ["error" => "Error al obtener la lista" . $exception->getMessage()];
		}
	}

	public static function buscarGrupo($idCarrera, $idCiclos, $modalidad, $idGrupo) {
		try {
			$registros = RegistroActa::where([["id_carreras", $idCarrera],
				["id_ciclos", $idCiclos], ["tipo_modalidad", $modalidad], ["id_grupos_actas", $idGrupo]])->first();
			return $registros;
		} catch (QueryException $exception) {
			return [];
		}
	}

	public static function leerReincorporacion($file, $idCiclo) {
		$f = fopen($file, "r");
		while (($data = fgetcsv($f, 1000, ",")) !== FALSE) { // Mientras hay líneas que leer...
			if ($data[2] != "") {
				if (is_numeric($data[2])) {
					//obtenemos bien la matricula y actualizamos el estatus de los alumnos a recursadores
					$registroAsignacion = self::findAsignaciones($idCiclo, $data[2]);
					//recorremos los datos recibidos
					foreach ($registroAsignacion as $row) {
						$repuesta = self::updateRegistroEscoAlumno($row->id);
						if (isset($repuesta["error"])) {
							return $repuesta;
							break;
						}
					}
				}
			}

		}
		return ["success" => "Se actualizo el estado de los alumnos"];
	}

	public static function updateRegistroEscoAlumno($idAsignacion) {
		try {
			$asignaciones = AlumnoRegistro::find($idAsignacion);
			$asignaciones->tipo = 2;
			$asignaciones->save();
			return ["success" => "Bien"];
		} catch (QueryException $e) {
			return ["error" => "Falló al actualizar el estado del alumno"];
		}
	}

	public static function findAsignaciones($idCiclo, $matricula) {
		try {
			$asignaciones = RegistroActa::join("alumno_registro", "id_registro_acta", "=", "registro_acta.id")
				->where([["alumno_registro.matricula", $matricula], ["id_ciclos", $idCiclo]])
				->select("alumno_registro.id")
				->get();
			return $asignaciones;
		} catch (QueryException $e) {
			return ["error" => "Error al obtener la lista de asignaciones para al alumnos, " . $e->getMessage()];
		}
	}

	/**
	 * Metodo que busca si el alumno cuenta ya con una calificación asignada para una materia
	 * @param $idRegistroActa
	 * @param $matricula
	 * @return array
	 */
	public static function findAsigacionMateria($idRegistroActa, $matricula) {
		try {
			$asignaciones = RegistroActa::join("alumno_registro", "id_registro_acta", "=", "registro_acta.id")
				->where([["alumno_registro.matricula", $matricula], ["registro_acta.id", $idRegistroActa]])
				->select("alumno_registro.id")
				->get();
			return $asignaciones;
		} catch (QueryException $e) {
			return ["error" => "Error al obtener el registro del alumno" . $e->getMessage()];
		}
	}

	public static function findRegistrosMateria($idGrupo, $idCiclo, $idCarrera, $idModalidad, $tipoModalidad) {
		try {
			$registros = RegistroActa::where([["id_grupos_actas", $idGrupo],
				["id_ciclos", $idCiclo], ["id_carreras", $idCarrera],
				["id_modalidad", $idModalidad], ["tipo_modalidad", $tipoModalidad]])->get();
			return $registros;
		} catch (QueryException $exception) {
			return ["error" => "Error al obtener las materias del registro" . $exception->getMessage()];
		}
	}

	public static function alumnosRecursamiento($idGrupo, $idCiclo, $idCarrera, $idModalidad, $tipoModalidad) {
		try {
			$registros = RegistroActa::join("alumno_registro", "id_registro_acta", "=", "registro_acta.id")
				->join("alumnos_acta", "alumnos_acta.matricula", "=", "alumno_registro.matricula")
				->leftJoin("regularizacion", "regularizacion.id_alumno_registro", "=", "alumno_registro.id")
				->select("alumno_registro.id", "alumnos_acta.nombre", "alumnos_acta.matricula",
					"registro_acta.materia", "alumno_registro.calificacion AS cf", "regularizacion.calificacion AS ts")
				->where([["id_grupos_actas", $idGrupo],
					["id_ciclos", $idCiclo], ["registro_acta.id_carreras", $idCarrera],
					["id_modalidad", $idModalidad], ["tipo_modalidad", $tipoModalidad]])->whereIn('tipo', [2, 3])->get();
			return $registros;
		} catch (QueryException $exception) {
			return ["error" => "Error al obtener las materias del registro" . $exception->getMessage()];
		}
	}

	public static function getAlumnosRegistro($idGrupo, $idCiclo, $idCarrera, $idModalidad, $tipoModalidad) {
		try {
			$registros = RegistroActa::join("alumno_registro", "id_registro_acta", "=", "registro_acta.id")
				->join("alumnos_acta", "alumnos_acta.matricula", "=", "alumno_registro.matricula")
				->leftJoin("regularizacion", "regularizacion.id_alumno_registro", "=", "alumno_registro.id")
				->select(DB::raw('DISTINCT(alumnos_acta.matricula)'), "alumnos_acta.nombre", "alumnos_acta.sexo", "alumnos_acta.status", "clave")
				->where([["id_grupos_actas", $idGrupo],
					["id_ciclos", $idCiclo], ["registro_acta.id_carreras", $idCarrera],
					["id_modalidad", $idModalidad], ["tipo_modalidad", $tipoModalidad], ["tipo", 1]])
				->orderBy("nombre")
				->get();
			return $registros;
		} catch (QueryException $exception) {
			return ["error" => "Error al obtener las materias del registro" . $exception->getMessage()];
		}
	}

	public static function getAlumnosRegistroReg($idGrupo, $idCiclo, $idCarrera, $idModalidad, $tipoModalidad) {
		try {
			$registros = RegistroActa::join("alumno_registro", "id_registro_acta", "=", "registro_acta.id")
				->join("alumnos_acta", "alumnos_acta.matricula", "=", "alumno_registro.matricula")
				->leftJoin("regularizacion", "regularizacion.id_alumno_registro", "=", "alumno_registro.id")
				->select(DB::raw('DISTINCT(alumnos_acta.matricula)'), "alumnos_acta.nombre", "alumnos_acta.sexo", "alumnos_acta.status", "clave", "tipo")
				->where([["id_grupos_actas", $idGrupo],
					["id_ciclos", $idCiclo], ["registro_acta.id_carreras", $idCarrera],
					["id_modalidad", $idModalidad], ["tipo_modalidad", $tipoModalidad], ["tipo", "<>", 1]])
				->orderBy("nombre")
				->get();
			return $registros;
		} catch (QueryException $exception) {
			return ["error" => "Error al obtener las materias del registro" . $exception->getMessage()];
		}
	}

	public static function delete($idAsignacion) {
		try {
			$respuesta = self::deleteRegularizacion($idAsignacion);
			if (isset($respuesta["success"])) {
				$asignacion = AlumnoRegistro::find($idAsignacion);
				$asignacion->delete();
				return ["success" => "Se elminó el registro del alumno correctamente"];
			} else {
				return $respuesta;
			}
		} catch (QueryException $exception) {
			return ["error" => $exception->getMessage()];
		}
	}

	public static function deleteRegularizacion($idAsignacion) {
		try {
			$regularizacion = Regularizacion::where("id_alumno_registro", $idAsignacion)->delete();
			return ["success" => "Se elminó la regularización correctamente"];
		} catch (QueryException $e) {
			return ["error" => "Error al eliminar la asignación estadística: " . $e->getMessage()];
		}
	}


	public static function getCalificacionMateria($claveRegistro, $matricula) {
		try {
			$calificacion = RegistroActa::join("alumno_registro", "alumno_registro.id_registro_acta", "=", "registro_acta.id")
				->leftJoin("regularizacion", "regularizacion.id_alumno_registro", "=", "alumno_registro.id")
				->select("alumno_registro.calificacion AS cf", "regularizacion.calificacion AS ts")
				->where([["registro_acta.id", $claveRegistro], ["matricula", $matricula]])->first();
			return $calificacion;
		} catch (QueryException $exception) {
			return [];
		}
	}

	public static function getMateriasAlumno($matricula,$idCiclo){
		try{
			$alumno = RegistroActa::join("alumno_registro", "id_registro_acta", "=", "registro_acta.id")
				->where([["matricula",$matricula],["id_ciclos",$idCiclo]])->get();
			return $alumno;
		}catch (QueryException $exception){
			return ["error"=>"Error al obtener las materias del alumno".$exception->getMessage()];
		}
	}


	public static function updateAlumnoMateria($idRegistro, $matricula, $oldRegistro){
		try{
			$asignaciones = AlumnoRegistro::where("id_registro_acta",$oldRegistro)->where("matricula",$matricula)
				->update(['id_registro_acta' => $idRegistro]);
			return ["success"=>"Se actualizo el alumno".$oldRegistro];
		}catch (QueryException $exception){
			return ["error" => "error al actualizar al alumno"];
		}
	}
}