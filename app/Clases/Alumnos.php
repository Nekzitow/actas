<?php
	namespace App\Clases;
	use App\Alumno_Calificacion;
	use App\Alumnos_acta;
	use Illuminate\Database\QueryException;

	/**
	*
	*/
	class Alumnos {

		function __construct($argument) {
			# code...
		}

		/**
		*Se eliminan todos las asignaciones de alumnos con calificación
		*@return boolean
		*/
		public static function deleteAsignacionAlumno($asignacion) {
			try {
				$result =  Alumno_Calificacion::where('id_asignacion_acta',$asignacion)->delete();
				return true;
			} catch (Exception $e) {
				return $e->getMessage();
			}
		}

		public static function getAlumno($matricula){
			try {
				$alumnos = Alumnos_acta::find($matricula);
				return $alumnos;
			} catch (QueryException $exception) {
				return ["error"=>$exception->getMessage()] ;
			}

		}

		public static function getAlumnos($idCarrera){
			try {
				$alumnos = Alumnos_acta::where("id_carreras", $idCarrera)->get();
				return $alumnos;
			} catch (QueryException $exception) {
				return ["error"=>$exception->getMessage()] ;
			}
		}


	}

?>