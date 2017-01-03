<?php

namespace App\Http\Controllers;

use App\Clases\AsignacionGrupo;
use App\Clases\Estadisticas;
use App\Clases\ImprimeEstadistica;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\AsignacionGrupos;
use App\Grupos_acta;
use App\Carreras;
use App\ciclos;
use App\Modalidad;

class EstadisticaController extends Controller
{
    //
	public function inicio(){
		$asignacion = new AsignacionGrupo();
		return view("Estadistica.inicio",["asignaciones"=>$asignacion->getAsignacion()]);
	}

	public function addEstatistica(){
		$carreras = Carreras::where('id_campus', 1)->get();
		$grupos = Grupos_acta::where('id_campus', 1)->get();
		$ciclos = ciclos::all();

		return view("Estadistica.form",['carreras' => $carreras,
			'grupos' => $grupos,
			'ciclos' => $ciclos]);
	}

	public function save(Request $request){
		$validacion = \Validator::make($request->all(), [
			'archivo' => 'required'
		]);

		//Vaciamos los datos en un arreglo
		if ($validacion->fails()) {
			return redirect()->back()->withErrors($validacion->errors());
		} else {
			//solicitamos todo los campos de formulario
			$carrera = $request->idCarrera;
			$ciclo = $request->idCiclo;
			$grupo = $request->idGrupo;
			$turno = $request->turno;
			$file = $request->file('archivo');
			//verificamos el tipo de archivo que subio el usuario
			$extension = $file->getClientOriginalExtension();
			if ($extension == "CSV" || $extension == "csv") {
				$response = Estadisticas::leerArchivo($file,$carrera,$ciclo,$turno);
				if (isset($response['error']))
					return redirect()->action('EstadisticaController@inicio')->with('error', $response['error']);
				else
					return redirect()->action('EstadisticaController@inicio')->with('mensaje', $response['success']);
			} else {
				return redirect()->back()->withErrors([$file->getClientOriginalName() . " : Archivo no soportado"]);
			}
		}
	}

	public function drop(Request $request){
		$id = $request->idCiclo;
		$asignacion = new AsignacionGrupo();
		$response = $asignacion->delete($id);
		return $response;
	}

	public function imprime(Request $request){
		$idCiclo = $request->idCiclo;
		$tipo = $request->tipo;
		$imprimeesta = new ImprimeEstadistica();
		$imprimeesta->imprime($idCiclo,$tipo);
	}

	public function imprimemaep(Request $request){
		$idCiclo = $request->idCiclo;
		$imprimeesta = new ImprimeEstadistica();
		$imprimeesta->imprimeMaep($idCiclo);
	}
}
