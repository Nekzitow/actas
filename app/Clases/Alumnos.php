<?php 
	namespace App\Clases;
	use App\Alumno_Calificacion;
	use App\Alumnos_acta;
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
	}

?>