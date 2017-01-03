<?php
/**
 * Created by PhpStorm.
 * User: OSORIO
 * Date: 21/12/2016
 * Time: 12:23 PM
 */

namespace App\Clases;
use App\Clases\Carrera;
use App\Estadistica;
use App\Grupos_acta;
use Illuminate\Database\QueryException;
use DB;


class Estadisticas {
	private $id;
	private $hombres;
	private $mujeres;
	private $created;
	private $updated;
	private $idAsignacion;


	public function __construct() {
		$this->id = 0;
		$this->hombres = 0;
		$this->mujeres = 0;
		$this->updated = date("Y-m-d");
		$this->created = date("Y-m-d");
		$this->idAsignacion = 0;
	}

	public static function withData($id, $hombres, $mujeres, $idAsignacion, $created, $updated) {
		$instance = new self();
		$instance->id = $id;
		$instance->hombres = $hombres;
		$instance->mujeres = $mujeres;
		$instance->idAsignacion = $idAsignacion;
		$instance->created = $created;
		$instance->updated = $updated;
		return $instance;
	}

	/**
	 * @return int
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * @param int $id
	 */
	public function setId($id) {
		$this->id = $id;
	}

	/**
	 * @return int
	 */
	public function getHombres() {
		return $this->hombres;
	}

	/**
	 * @param int $hombres
	 */
	public function setHombres($hombres) {
		$this->hombres = $hombres;
	}

	/**
	 * @return int
	 */
	public function getMujeres() {
		return $this->mujeres;
	}

	/**
	 * @param int $mujeres
	 */
	public function setMujeres($mujeres) {
		$this->mujeres = $mujeres;
	}

	/**
	 * @return false|string
	 */
	public function getCreated() {
		return $this->created;
	}

	/**
	 * @param false|string $created
	 */
	public function setCreated($created) {
		$this->created = $created;
	}

	/**
	 * @return false|string
	 */
	public function getUpdated() {
		return $this->updated;
	}

	/**
	 * @param false|string $updated
	 */
	public function setUpdated($updated) {
		$this->updated = $updated;
	}

	/**
	 * @return int
	 */
	public function getIdAsignacion() {
		return $this->idAsignacion;
	}

	/**
	 * @param int $idAsignacion
	 */
	public function setIdAsignacion($idAsignacion) {
		$this->idAsignacion = $idAsignacion;
	}


	public static function leerArchivo($file,$carrera,$ciclo,$turno){
		try {
			$fp = fopen($file, "r");
			$response = [];
			while (($data = fgetcsv($fp, 1000, ",")) !== FALSE) { // Mientras hay líneas que leer...
				if ($data[1] != ""){
					$arrayData = str_split($data[1]);
					$grado = substr($data[1],-3,1);
					$grupo = substr($data[1],-2,1);
					$modalida = substr($data[1],-1);
					$nomenclatura = "";
					$idModalidad = 0;
					$grupos = self::getIdGrupo($grado,$grupo);
					if(count($grupos)>0){
						//echo $grupos[0]['nombre'];
					}
					if (is_numeric($arrayData[1])){
						$nomenclatura = substr($data[1],0,1);
						$carrera = Carrera::getCarrerasN($nomenclatura,1);
						$idModalidad = self::getIdModalidad($modalida,$nomenclatura);
						//echo $carrera[0]['nombre']."---".$grado."---".$grupo."---".$modalida."-----".$idModalidad."<br>";
						if (count($carrera)>0){
							$response = self::insert($ciclo,$carrera[0]['id'],$data[3],$data[8],
								$idModalidad,$grupos[0]['id'],$data[1]);
						}
					}elseif (is_numeric($arrayData[2])){
						$nomenclatura = substr($data[1],0,2);
						$carrera = Carrera::getCarrerasN($nomenclatura,1);
						$idModalidad = self::getIdModalidad($modalida,$nomenclatura);
						if (count($carrera)>0){
							//echo $carrera[0]['nombre']."---".$grado."---".$grupo."---".$modalida."-----".$idModalidad."<br>";
							$response = self::insert($ciclo,$carrera[0]['id'],$data[3],$data[8],
								$idModalidad,$grupos[0]['id'],$data[1]);
						}

					}elseif(is_numeric($arrayData[3])){
						$nomenclatura = substr($data[1],0,3);
						if ($nomenclatura == 'ISC'){
							$nomenclatura = 'IS';
						}
						$carrera = Carrera::getCarrerasN($nomenclatura,1);
						$idModalidad = self::getIdModalidad($modalida,$nomenclatura);
						if (count($carrera)>0){
							//echo $carrera[0]['id'];
							//echo $grupos[0]['id']."<br>";
							$grado = substr($data[1],-2,1);
							$grupo = substr($data[1],-3,1);
							$grupos = self::getIdGrupo($grado,$grupo);
							//echo $carrera[0]['nombre']."---".$grado."---".$grupo."---".$modalida."-----".$idModalidad."<br>";
							$response = self::insert($ciclo,$carrera[0]['id'],$data[3],$data[8],
								$idModalidad,$grupos[0]['id'],$data[1]);
						}

					}
					/*($arrayData); $i++){
						if ($arrayData[])
					}*/
				}elseif($data[2] != "" && ($data[0] == "" && $data[1] == "")){
					//echo $data[2]."   ".$data[5]."    ".$data[10]."<br>";
					$arrayData = str_split($data[2]);
					$grado = substr($data[2],-3,1);
					$grupo = substr($data[2],-2,1);
					$modalida = substr($data[2],-1);
					$nomenclatura = "";
					$idModalidad = 0;
					$grupos = self::getIdGrupo($grado,$grupo);
					if(count($grupos)>0){
						//echo $grupos[0]['nombre'];
					}
					if (is_numeric($arrayData[1])){
						$nomenclatura = substr($data[2],0,1);
						$carrera = Carrera::getCarrerasN($nomenclatura,1);
						$idModalidad = self::getIdModalidad($modalida,$nomenclatura);
						//echo $carrera[0]['nombre']."---".$grado."---".$grupo."---".$modalida."-----".$idModalidad."<br>";
						if (count($carrera)>0){
							$response = self::insert($ciclo,$carrera[0]['id'],$data[5],$data[10],
								$idModalidad,$grupos[0]['id'],$data[2]);
						}
					}elseif (is_numeric($arrayData[2])){
						$nomenclatura = substr($data[2],0,2);
						$carrera = Carrera::getCarrerasN($nomenclatura,1);
						$idModalidad = self::getIdModalidad($modalida,$nomenclatura);
						if (count($carrera)>0){
							//echo $carrera[0]['nombre']."---".$grado."---".$grupo."---".$modalida."-----".$idModalidad."<br>";
							$response = self::insert($ciclo,$carrera[0]['id'],$data[5],$data[10],
								$idModalidad,$grupos[0]['id'],$data[2]);
						}

					}elseif(is_numeric($arrayData[3])){
						$nomenclatura = substr($data[2],0,3);
						if ($nomenclatura == 'ISC'){
							$nomenclatura = 'IS';
						}
						$carrera = Carrera::getCarrerasN($nomenclatura,1);
						$idModalidad = self::getIdModalidad($modalida,$nomenclatura);
						if (count($carrera)>0){
							//echo $carrera[0]['id'];
							//echo $grupos[0]['id']."<br>";
							$grado = substr($data[2],-2,1);
							$grupo = substr($data[2],-3,1);
							$grupos = self::getIdGrupo($grado,$grupo);
							//echo $carrera[0]['nombre']."---".$grado."---".$grupo."---".$modalida."-----".$idModalidad."<br>";
							$response = self::insert($ciclo,$carrera[0]['id'],$data[5],$data[10],
								$idModalidad,$grupos[0]['id'],$data[2]);
						}

					}
				}

			}
			fclose($fp);
			return $response;
		} catch (Exception $e) {
			return redirect()->back()->withErrors(["error", "archivo no soportado"]);
		}
	}

	public static function insert($ciclo,$carrera,$hombres,$mujeres,$idmodalidad,$idgrupo,$nomenclatura){
		try {
			$asignacion = new AsignacionGrupo();
			$respuesta = $asignacion->insert($ciclo,$carrera,$idmodalidad,$idgrupo,$nomenclatura);
			if (isset($respuesta["success"])){
				$estadistica = new Estadistica();
				$estadistica->hombres = $hombres;
				$estadistica->mujeres = $mujeres;
				$estadistica->id_asignacion_grupos = $respuesta["success"];
				$estadistica->save();
				return ["success"=>"Ser guardo la estadística con éxito"];
			}else{
				return $respuesta;
			}
		} catch (QueryException $e) {
			return ["Error al guardar las estadisticas"];
		}
	}
	public static function getIdModalidad($modalidad,$nomenclatura){
		$idModalidad = 0;
		switch ($modalidad) {
			case "E":
				if (self::isMixto($nomenclatura)){
					$idModalidad = 2;
				}else{
					$idModalidad = 1;
				}
				break;
			case "S":
				if (self::isPosgrado($nomenclatura)){
					$idModalidad = 2;
				}else{
					$idModalidad = 3;
				}
				break;
			case "D":
				$idModalidad = 4;
				break;
			default:
				$idModalidad = 2;
				break;
		}

		return $idModalidad;
	}
	public static function isMixto($nomenclatura){
		$respuesta = false;
		switch ($nomenclatura) {
			case 'IM':
				$respuesta = true;
				break;
			case 'EF':
				$respuesta = true;
				break;
			case 'TS':
				$respuesta = true;
				break;
			case 'IC':
				$respuesta = true;
				break;
			case 'IS':
				$respuesta = true;
				break;
			case 'ISC':
				$respuesta = true;
				break;
			case 'MC':
				$respuesta = true;
				break;
			case 'MF':
				$respuesta = true;
				break;
			case 'ME':
				$respuesta = true;
				break;
			case 'DE':
				$respuesta = true;
				break;
		}
		return $respuesta;
	}

	public static function isPosgrado($nomenclatura){
		$respuesta = false;
		switch ($nomenclatura) {
			case 'MC':
				$respuesta = true;
				break;
			case 'MF':
				$respuesta = true;
				break;
			case 'ME':
				$respuesta = true;
				break;
			case 'DE':
				$respuesta = true;
				break;
		}
		return $respuesta;
	}

	public static function getIdGrupo($grado,$grupo){
		try {
			if ($grupo == "S"){
				$grupo = "A";
			}
			$grupo = Grupos_acta::where("nombre",$grado." ".$grupo)->get();
			return $grupo;
		} catch (QueryException $e) {
			return ["error"=>"Error al obtener el grupo"];
		}
	}

	public static function getEstadisticasL($idCiclo,$campus){
		try{
			$estadisticas = Estadistica::join("asignacion_grupos AS gpa","gpa.id","id_asignacion_grupos")
				->join("grupos_acta as gp","gp.id","=","gpa.id_grupos_acta")
				->join("carreras as c","c.id","=","gpa.id_carreras")
				->join("modalidad as m","m.id","=","gpa.id_modalidad")
				->select('c.id AS idCarrera','c.nombre','gp.grado',DB::raw('COUNT(gp.grado) AS grupos'),
					DB::raw('SUM(hombres) AS hombres'),DB::raw('SUM(mujeres) AS mujeres'),
					"m.descripcion")
				->where([["id_ciclos",$idCiclo],["gp.id_campus",$campus],["c.tipo",1]])
				->groupBy('descripcion','c.nombre','gp.grado','idCarrera')
				->orderBy('c.nombre')
				->orderBy("m.descripcion","DESC")
				->orderBy('gp.grado')->get();
			return $estadisticas;
		}catch (QueryException $e){
			return ["error"=>"Error al obtener la lista de estadísticas: ".$e->getMessage()];
		}
	}

	public static function getEstadisticasP($idCiclo,$campus){
		try{
			$estadisticas = Estadistica::join("asignacion_grupos AS gpa","gpa.id","id_asignacion_grupos")
				->join("grupos_acta as gp","gp.id","=","gpa.id_grupos_acta")
				->join("carreras as c","c.id","=","gpa.id_carreras")
				->join("modalidad as m","m.id","=","gpa.id_modalidad")
				->select('c.id AS idCarrera','c.nombre','gp.grado',DB::raw('COUNT(gp.grado) AS grupos'),
					DB::raw('SUM(hombres) AS hombres'),DB::raw('SUM(mujeres) AS mujeres'),
					"m.descripcion")
				->where([["id_ciclos",$idCiclo],["gp.id_campus",$campus],["c.tipo",2]])
				->groupBy('descripcion','c.nombre','gp.grado','idCarrera')
				->orderBy('c.nombre')
				->orderBy("m.descripcion","DESC")
				->orderBy('gp.grado')->get();
			return $estadisticas;
		}catch (QueryException $e){
			return ["error"=>"Error al obtener la lista de estadísticas: ".$e->getMessage()];
		}
	}


}