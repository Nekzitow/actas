<?php
/**
 * Created by PhpStorm.
 * User: OSORIO
 * Date: 26/12/2016
 * Time: 08:07 AM
 */

namespace App\Clases;


use App\Carreras;
use Illuminate\Database\QueryException;

class Carrera {
	private $id;
	private $nombre;
	private $rvoe;
	private $idCampus;
	private $nomenclatura;


	public function __construct() {
		$this->id = 0;
		$this->nombre = "";
		$this->rvoe = "";
		$this->idCampus =0 ;
		$this->nomenclatura = "";
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
	public function getNombre() {
		return $this->nombre;
	}

	/**
	 * @param string $nombre
	 */
	public function setNombre($nombre) {
		$this->nombre = $nombre;
	}

	/**
	 * @return string
	 */
	public function getRvoe() {
		return $this->rvoe;
	}

	/**
	 * @param string $rvoe
	 */
	public function setRvoe($rvoe) {
		$this->rvoe = $rvoe;
	}

	/**
	 * @return int
	 */
	public function getIdCampus() {
		return $this->idCampus;
	}

	/**
	 * @param int $idCampus
	 */
	public function setIdCampus($idCampus) {
		$this->idCampus = $idCampus;
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

	public static function getCarrerasN($nomenclatura, $idCampus){
		try{
			$lista = Carreras::where([["id_campus",$idCampus],["nomenclatura",$nomenclatura]])->get();
			return $lista;
		}catch (QueryException $e){
			return ["error"=>"Error al obtener las carresas: ".$e->getMessage()];
		}
	}

	public static function getCarrerasNLike($nomenclatura, $idCampus){
		try{
			$lista = Carreras::where([["id_campus",$idCampus],["nomenclatura","LIKE",$nomenclatura."%"]])->get();
			return $lista;
		}catch (QueryException $e){
			return ["error"=>"Error al obtener las carresas: ".$e->getMessage()];
		}
	}
}