<?php
/**
 * Created by PhpStorm.
 * User: OSORIO
 * Date: 21/12/2016
 * Time: 12:05 PM
 */

namespace App\Clases;


use App\AsignacionGrupos;
use Illuminate\Database\QueryException;
use DB;

class AsignacionGrupo {
	private $id;
	private $nomenclatura;
	private $idGrupos;
	private $idCiclos;
	private $idCarreras;
	private $idModalidad;
	private $created;
	private $updated;

	public function __construct() {
		$this->id = 0;
		$this->nomenclatura = "";
		$this->idGrupos = 0;
		$this->idCiclos = 0;
		$this->idCarreras = 0;
		$this->idModalidad = 0;
		$this->created = date('Y-m-d');
		$this->updated = date('Y-m-d');
	}

	public static function withData($id, $nomenclatura, $idGrupos, $idCarreras, $idModalidad, $created, $updated){
		$instance = new self();
		$instance->id = $id;
		$instance->nomenclatura = $nomenclatura;
		$instance->idGrupos = $idGrupos;
		$instance->idCarreras = $idCarreras;
		$instance->idModalidad = $idModalidad;
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
	 * @return string
	 */
	public function getNomenclatura() {
		return $this->nomenclatura;
	}

	/**
	 * @param string $nomenclatura
	 */
	public function setNomenclatura($nomenclatura) {
		$this->nomenclatura = $nomenclatura;
	}

	/**
	 * @return int
	 */
	public function getIdGrupos() {
		return $this->idGrupos;
	}

	/**
	 * @param int $idGrupos
	 */
	public function setIdGrupos($idGrupos) {
		$this->idGrupos = $idGrupos;
	}

	/**
	 * @return int
	 */
	public function getIdCiclos() {
		return $this->idCiclos;
	}

	/**
	 * @param int $idCiclos
	 */
	public function setIdCiclos($idCiclos) {
		$this->idCiclos = $idCiclos;
	}

	/**
	 * @return int
	 */
	public function getIdCarreras() {
		return $this->idCarreras;
	}

	/**
	 * @param int $idCarreras
	 */
	public function setIdCarreras($idCarreras) {
		$this->idCarreras = $idCarreras;
	}

	/**
	 * @return int
	 */
	public function getIdModalidad() {
		return $this->idModalidad;
	}

	/**
	 * @param int $idModalidad
	 */
	public function setIdModalidad($idModalidad) {
		$this->idModalidad = $idModalidad;
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

	public function getAsignacion(){
		try {
			$asignacion = AsignacionGrupos::join('ciclos','ciclos.id','=','id_ciclos')
			->join('estadistica','id_asignacion_grupos','=','asignacion_grupos.id')
				->select('ciclos.id AS id','nombre_ciclo',DB::raw('SUM(hombres) AS hombres'),DB::raw('SUM(mujeres) AS mujeres'))
				->groupBy('nombre_ciclo','ciclos.id')->get();
			/**$asignacion = AsignacionGrupos::join('estadistica','id_asignacion_grupos','=','asignacion_grupos.id')
				->get();**/
			return $asignacion;
		} catch (QueryException $e) {
			return ["error"=>"Error al obtener la lista de asignaciones: ".$e->getMessage()];
		}
	}

	public function insert($ciclo,$carrera,$idmodalidad,$idgrupo,$nomenclatura){
		try{
			$asignacion = new AsignacionGrupos();
			$asignacion->nomenclatura = $nomenclatura;
			$asignacion->id_grupos_acta = $idgrupo;
			$asignacion->id_ciclos = $ciclo;
			$asignacion->id_carreras = $carrera;
			$asignacion->id_modalidad = $idmodalidad;
			$asignacion->save();

			return ["success"=>$asignacion->id];
		}catch (QueryException $e){
			return ["error"=>"Error al guardar la asignacion".$e->getMessage()];
		}

	}

	public function delete($ciclo){
		try{
			$asignacion = AsignacionGrupos::where("id_ciclos",$ciclo)->delete();
			return ["success"=>"Se elminó la estadística correctamente"];
		}catch (QueryException $e){
			return ["error"=>"Error al eliminar la asignación estadística: ".$e->getMessage()];
		}
	}

}