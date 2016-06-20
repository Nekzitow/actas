<?php
namespace App\Clases;


use App\Alumno_Calificacion;
use App\Asignacion_acta;
use App\Campus;
use App\Carreras;
use App\Docente;
use App\Grupos_acta;
use App\Http\Requests;
use App\Materias_acta;
use Mockery\CountValidator\Exception;

class ImprimeActa
{
	//variables
	private $carrera;
	private $nomCarrera;
	private $ciclo;
	private $grupo;
	private $nomGrupo;
	private $turno;
	private $file;
	private $esco;
	private $dse;
	private $idDocente;
	private $nombreMateria;

	function __construct($carrera, $ciclo, $grupo, $turno, $file, $esco, $dse)
	{
		$this->carrera = $carrera;
		$this->ciclo = $ciclo;
		$this->grupo = $grupo;
		$this->turno = $turno;
		$this->file = $file;
		$this->esco = $esco;
		$this->dse = $dse;
		$this->nomCarrera = Carreras::where('id', $this->carrera)->get();
		$this->nomGrupo = Grupos_acta::where('id', $this->grupo)->get();
	}

	public function leerCSV()
	{
		try {
			$fp = fopen($this->file, "r");
			$nombreMateria = "";
			$nombreDocente = "";
			$json = array();
			$array = array();
			$alumns = array();
			$cambio = 0;
			$nombreDocente = '';
			while (($data = fgetcsv($fp, 1000, ",")) !== FALSE) { // Mientras hay líneas que leer...
				if ($data[4] == 'Titular :') {
					if (count(str_split($data[5])) > 2) {

					} else {

						array_push($array, 'No asignado');
					}

				}

				if (count(str_split($data[5])) > 2) {
					if ($data[4] == 'Clase :') {
						if (count($array) > 0) {
							array_push($array, $alumns);
							array_push($json, $array);
							$array = array();
							$alumns = array();
							$cambio = 1;
						} else {
							$cambio = 0;
						}
						$nombreMateria = $data[5];
						array_push($array, $nombreMateria);
					} else {

						$nombreDocente = $data[5];
						array_push($array, $nombreDocente);
					}
				}
				if (count(str_split($data[1])) > 2 && $data[3] != 'Nombre del Alumno(a)' && $data[1] != 'Matricula') {
					array_push($alumns, array($data[1], $data[6]+0));
				}

			}
			if ($cambio == 1) {
				array_push($array, $alumns);
				array_push($json, $array);
			}
			foreach ($this->nomCarrera as $key) {
				$nombreC = $key->nombre;
			}
			foreach ($this->nomGrupo as $key) {
				$nombreG = $key->nombre;
			}
			//return view('controlViews.actas');
			//$this->imprimePDF;
			$asignaciones = array();
			for ($i = 0; $i < count($json); $i++) {
				array_push($asignaciones, $this->imprimePreacta($json[$i], $nombreC, $nombreG));

			}
			fclose($fp);

		} catch (Exception $e) {
			return redirect()->back()->withErrors(["error", "archivo no soportado"]);
		}

	}


	public function imprimePreacta($arrayDetalle, $nombreC, $nombreG)
	{
		try {
			$this->nombreMateria = utf8_encode($arrayDetalle[0]);
			$this->idDocente = Docente::where('nombre', 'like', utf8_encode($arrayDetalle[1]) . '%')->value('id');
			$alumnos = $arrayDetalle[2];
			$idAsignacion = $this->guardaActa();
			$this->guardarAlumnos($alumnos, $idAsignacion);
			return $idAsignacion;
		} catch (Exception $e) {
			return redirect()->back()->withErrors(["error", $e->getMessage()]);
		}
	}

	public static function imprimePDF($idAsignacion)
	{

		//obtenemos todos los datos de Asigancion
		$asignacion = Asignacion_acta::join('control.grupos_acta', 'asignacion_acta.id_grupos_acta', '=', 'grupos_acta.id')
			->join('control.ciclos', 'asignacion_acta.id_ciclos', '=', 'ciclos.id')
			->join('control.carreras', 'carreras.id', '=', 'id_carrera')
			->select('asignacion_acta.*', 'grupos_acta.nombre', 'ciclos.nombre_ciclo', 'carreras.nombre AS nombrec', 'rvoe', 'carreras.id AS idCarrera')
			->where('asignacion_acta.id', $idAsignacion)->get();
		$claveCct = Campus::join('control.carreras', 'campus.id', '=', 'carreras.id_campus')
			->join('control.asignacion_acta', 'carreras.id', '=', 'id_carrera')
			->select('campus.*')
			->where('asignacion_acta.id', $idAsignacion)->get();
		$docente = Docente::where("id", $asignacion[0]->id_docente)->get();
		if ($docente->isEmpty()) {

			return redirect()->back()->withErrors(['NO SE ENCOTRO EL DOCENTE ASIGNADO PARA LA MATERIA: ' . $asignacion[0]->materia]);
		}
		$turno = ["MATUTINO", 'VESPERTINO'];
		$fpdi = new \fpdi\FPDI('P', 'mm', 'A4');
		$link = "components/pdf/acta.pdf";
		$pageCount = $fpdi->setSourceFile($link);
		$tplIdx = $fpdi->importPage(1, '/MediaBox');
		$size = $fpdi->getTemplateSize($tplIdx);
		$fpdi->addPage();
		$fpdi->useTemplate($tplIdx, 0, 0);
		$fpdi->setFont('Arial', '', 11);
		$fpdi->SetTextColor(4, 4, 4);
		$fpdi->setXY(10, 20);
		$fpdi->Cell(0, 10, "CLAVE: " . utf8_decode($claveCct[0]->clave), 0, 0, 'C');
		//nombre de la carrera
		$fpdi->setXY(10, 33);
		$fpdi->Cell(0, 10, utf8_decode($asignacion[0]->nombrec), 0, 0, 'C');
		$fpdi->setXY(10, 37);
		$fpdi->Cell(0, 10, "Acuerdo " . $asignacion[0]->rvoe, 0, 0, 'C');

		//numero de acuerdo

		$fpdi->setXY(110, 46.5);
		//nombre del grupo
		$fpdi->write(15, $asignacion[0]->nombre);
		$fpdi->setXY(155, 46.5);
		//turno
		$fpdi->write(15, $turno[$asignacion[0]->turno - 1]);
		$fpdi->setFont('Arial', '', 8);
		$fpdi->setXY(43, 53);
		//materia
		$fpdi->write(15, utf8_decode($asignacion[0]->materia));
		$fpdi->setXY(155, 53);
		//clave materia
		$cvl = ImprimeActa::buscarClaveMateria($asignacion[0]->idCarrera, $asignacion[0]->materia, $asignacion[0]->modalidad);
		$fpdi->write(15, $cvl);
		$fpdi->setFont('Arial', '', 11);
		$fpdi->setXY(43, 58);
		$fpdi->write(15, $asignacion[0]->clave_dse);
		$fpdi->setFont('Arial', '', 7);
		$fpdi->setXY(53, 63);
		$fpdi->write(15, $asignacion[0]->modalidad);
		$fpdi->setFont('Arial', '', 9);
		$fpdi->setXY(133, 63);
		$fpdi->write(15, $asignacion[0]->nombre_ciclo);
		//obtenemos los alumnos
		$alumnos = Alumno_Calificacion::join('control.asignacion_acta', 'id_asignacion_acta', '=', 'asignacion_acta.id')
			->join('control.alumnos_acta', 'alumnos_acta.matricula', '=', 'alumno_calificacion.matricula_alumnos_acta')
			->select('alumnos_acta.matricula', 'alumnos_acta.nombre', 'alumno_calificacion.calificacion')
			->where('asignacion_acta.id', $idAsignacion)->get();
		//nuevo arreglos para generar los numeros de las calificaciones
		$arrayCalif = ["CERO", "UNO", "DOS", "TRES", "CUATRO", "CINCO", "SEIS", "SIETE", "OCHO", "NUEVE", "DIEZ"];
		$fpdi->setFont('Arial', '', 9);
		$i = 1;
		$y = 84;
		//recorremos el arreglo de alumnos obtenidos
		foreach ($alumnos as $key) {
			if ($i > 26 && $i < 28) {
				$fpdi->setXY(15, 250);
				$fpdi->write(15, "LIC. IVON ESPINOSA SANTOS");
				$fpdi->setXY(120, 250);
				$fpdi->write(15, "LIC. " . utf8_decode($docente[0]->nombre . ""));
				$fpdi->addPage();
				$fpdi->useTemplate($tplIdx, 0, 0);
				$fpdi->setFont('Arial', '', 11);
				$fpdi->SetTextColor(4, 4, 4);
				$fpdi->setXY(10, 20);
				$fpdi->Cell(0, 10, "CLAVE: " . utf8_decode($claveCct[0]->clave), 0, 0, 'C');
				//nombre de la carrera
				$fpdi->setXY(10, 33);
				$fpdi->Cell(0, 10, utf8_decode($asignacion[0]->nombrec), 0, 0, 'C');
				$fpdi->setXY(10, 37);
				$fpdi->Cell(0, 10, "Acuerdo " . $asignacion[0]->rvoe, 0, 0, 'C');

				//numero de acuerdo

				$fpdi->setXY(110, 46.5);
				//nombre del grupo
				$fpdi->write(15, $asignacion[0]->nombre);
				$fpdi->setXY(155, 46.5);
				//turno
				$fpdi->write(15, $turno[$asignacion[0]->turno - 1]);
				$fpdi->setFont('Arial', '', 8);
				$fpdi->setXY(43, 53);
				//materia
				$fpdi->write(15, utf8_decode($asignacion[0]->materia));
				$fpdi->setXY(155, 53);
				$fpdi->write(15, $cvl);
				$fpdi->setFont('Arial', '', 11);
				$fpdi->setXY(43, 58);
				$fpdi->write(15, $asignacion[0]->clave_dse);
				$fpdi->setFont('Arial', '', 7);
				$fpdi->setXY(53, 63);
				$fpdi->write(15, $asignacion[0]->modalidad);
				$fpdi->setFont('Arial', '', 9);
				$fpdi->setXY(133, 63);
				$fpdi->write(15, $asignacion[0]->nombre_ciclo);

				$y = 84;
			}

			$fpdi->setXY(29, $y);
			$fpdi->write(15, $i++);
			$fpdi->setXY(43, $y);
			$fpdi->write(15, utf8_decode($key->nombre));
			$fpdi->setXY(140, $y);
			$fpdi->write(15, $key->calificacion);
			$fpdi->setXY(163, $y);
			$fpdi->write(15, $arrayCalif[round($key->calificacion, 2)]);
			if ($i > 5 && $i < 7) {
				$y += 6;
			} elseif ($i > 8 && $i < 10) {
				$y += 6;
			} elseif ($i > 9 && $i < 11) {
				$y += 6;
			} elseif ($i > 14 && $i < 16) {
				$y += 6;
			} elseif ($i > 16 && $i < 18) {
				$y += 6;
			} elseif (($i > 18 && $i < 20) || ($i > 20 && $i < 22) || ($i > 30 && $i < 32) || ($i > 35 && $i < 37)) {
				$y += 6.5;
			} else {
				$y += 5;
			}
		}

		$fpdi->setXY(15, 250);
		$fpdi->write(15, "LIC. IVON ESPINOSA SANTOS");
		$fpdi->setXY(120, 250);
		$fpdi->write(15, "LIC. " . utf8_decode($docente[0]->nombre));
		$fpdi->Output();
		exit;
	}

	/**
	 * @return int
	 */
	public function guardaActa()
	{
		try{
			$asignacion = new Asignacion_acta;
			$asignacion->turno = $this->turno;
			$asignacion->clave_dse = $this->dse;
			$asignacion->id_grupos_acta = $this->grupo;
			$asignacion->id_ciclos = $this->ciclo;
			$asignacion->id_docente = $this->idDocente;
			$asignacion->materia = $this->nombreMateria;
			$asignacion->modalidad = $this->esco;
			$asignacion->id_carrera = $this->carrera;
			$asignacion->save();
			$idAsignacion = Asignacion_acta::where([['id_grupos_acta', $this->grupo],
				['id_docente', $this->idDocente],
				['id_ciclos', $this->ciclo]])->max('id');
			return $idAsignacion;
		}catch (Exception $e){
			return redirect()->back()->withErrors(["error", $e->getMessage()]);
		}

	}

	/**
	 * @param $arrayAlumno
	 * @param $idAsignacion
	 */
	public function guardarAlumnos($arrayAlumno, $idAsignacion)
	{
		try {
			for ($i = 0; $i < count($arrayAlumno); $i++) {
				$alumno = new Alumno_Calificacion;
				$alumno->calificacion = $arrayAlumno[$i][1];
				$alumno->matricula_alumnos_acta = $arrayAlumno[$i][0];
				$alumno->id_asignacion_acta = $idAsignacion;
				$alumno->save();
			}
		}catch (Exception $e){
			return redirect()->back()->withErrors(["error", $e->getMessage()]);
		}


	}

	/**
	 * @param $idCarrera
	 * @param $nombreMateria
	 * @param $modalidad
	 * @return int
	 */
	public static function buscarClaveMateria($idCarrera, $nombreMateria, $modalidad)
	{
		try {

			if ($modalidad == "ESCOLARIZADO") {
				$mod = 1;
			} else {
				$mod = 2;
			}

			$idClave = Materias_acta::where([
				['nombre', 'like', $nombreMateria . '%'],
				['id_carrera', $idCarrera],
				['modalidad', $mod]
			])->value('clave_materia');
			if ($idClave == "") {
				if ($modalidad == "ESCOLARIZADO") {
					$mod = 2;
				} else {
					$mod = 1;
				}
				$idClave = Materias_acta::where([
					['nombre', 'like', $nombreMateria . '%'],
					['id_carrera', $idCarrera],
					['modalidad', $mod],
				])->value('clave_materia');
			}

			return $idClave;
		} catch (Exception $e) {
			return 0;
		}
	}
}

?>