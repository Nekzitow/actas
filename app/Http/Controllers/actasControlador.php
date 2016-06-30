<?php

namespace App\Http\Controllers;

use App\Asignacion_acta;
use App\Carreras;
use App\ciclos;
use App\Clases\Alumnos;
use App\Clases\ImprimeActa;
use App\Grupos_acta;
use App\Http\Requests;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

//use Fpdf;

class actasControlador extends Controller
{
	/**
	 * @return
	 */
	public function inicio(Request $request)
	{
		//if (Auth::check()) {
			$asignacion = Asignacion_acta::join('control.grupos_acta', 'asignacion_acta.id_grupos_acta', '=', 'grupos_acta.id')
				->join('control.ciclos', 'asignacion_acta.id_ciclos', '=', 'ciclos.id')
				->join('control.carreras', 'carreras.id', '=', 'id_carrera')
				->select('asignacion_acta.*', 'grupos_acta.nombre', 'ciclos.nombre_ciclo', 'carreras.nombre AS nombrec')
				->where([['id_grupos_acta', $request->idGrupo], ['ciclos.id', $request->idCiclo], ['carreras.id', $request->idCarrera],
					['modalidad', $request->modalidad . ""]])->get();
			return view('controlViews.actas', ['asignacion' => $asignacion]);
		//} else
		//	return redirect()->action('HomeController@index');
	}

	/**
	 * @return view vista menu principal de actas
	 */
	public function menu()
	{
		//if (Auth::check()) {
			$results = DB::select('select * from users where id = :id', ['id' => 1]);
			$gruposEsco = Grupos_acta::distinct()->join('control.asignacion_acta', 'asignacion_acta.id_grupos_acta', '=', 'grupos_acta.id')
				->join('control.ciclos', 'asignacion_acta.id_ciclos', '=', 'ciclos.id')
				->join('control.carreras', 'carreras.id', '=', 'id_carrera')
				->select('grupos_acta.id', 'grupos_acta.nombre', 'asignacion_acta.id_ciclos', 'ciclos.nombre_ciclo', 'asignacion_acta.id_carrera', 'carreras.nombre AS nombrec', 'modalidad')->where('modalidad', 'ESCOLARIZADO')->get();
			$gruposSabados = Grupos_acta::distinct()->join('control.asignacion_acta', 'asignacion_acta.id_grupos_acta', '=', 'grupos_acta.id')
				->join('control.ciclos', 'asignacion_acta.id_ciclos', '=', 'ciclos.id')
				->join('control.carreras', 'carreras.id', '=', 'id_carrera')
				->select('grupos_acta.id', 'grupos_acta.nombre', 'asignacion_acta.id_ciclos', 'ciclos.nombre_ciclo', 'asignacion_acta.id_carrera', 'carreras.nombre AS nombrec', 'modalidad')->where('modalidad', 'SABADOS')->get();
			$gruposDomingos = Grupos_acta::distinct()->join('control.asignacion_acta', 'asignacion_acta.id_grupos_acta', '=', 'grupos_acta.id')
				->join('control.ciclos', 'asignacion_acta.id_ciclos', '=', 'ciclos.id')
				->join('control.carreras', 'carreras.id', '=', 'id_carrera')
				->select('grupos_acta.id', 'grupos_acta.nombre', 'asignacion_acta.id_ciclos', 'ciclos.nombre_ciclo', 'asignacion_acta.id_carrera', 'carreras.nombre AS nombrec', 'modalidad')->where('modalidad', 'DOMINGOS')->get();
			return view('controlViews.MenuActas', ['grupos' => $gruposEsco, 'gruposS' => $gruposSabados, 'gruposD' => $gruposDomingos]);
			// Si está autenticado lo mandamos a la raíz donde estara el mensaje de bienvenida.

		//} else {
		//	return redirect()->action('HomeController@index');
		//}


	}

	public function agregarActa()
	{
		//if (Auth::check()) {
			$carreras = Carreras::where('id_campus', 1)->get();
			$grupos = Grupos_acta::where('id_campus', 1)->get();
			$ciclos = ciclos::all();
			//$carreras = DB::select('select * from carreras');
			return view('controlViews.agregarActa', ['carreras' => $carreras,
				'grupos' => $grupos,
				'ciclos' => $ciclos]);
		//} else
			//return redirect()->action('HomeController@index');
	}

	public function guardarActa(Request $request)
	{
		//if (Auth::check()) {
			//Validamos los campos del formulario
			$validacion = \Validator::make($request->all(), [
				'clavedse' => 'required',
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
				$dse = $request->clavedse;
				$mod = $request->esco;
				$file = $request->file('archivo');
				//verificamos el tipo de archivo que subio el usuario
				$extension = $file->getClientOriginalExtension();
				if ($extension == "CSV" || $extension == "csv") {
					$imprimeActas = new ImprimeActa($carrera, $ciclo, $grupo, $turno, $file, $mod, $dse);
					$imprimeActas->leerCSV();
					return redirect()->action('actasControlador@menu');

				} else {
					return redirect()->back()->withErrors([$file->getClientOriginalName() . " : Archivo no soportado"]);
				}
			}
		//} else {
		//	return redirect()->action('HomeController@index');
		//}


		//$post = $request->all();

	}

	public function exportpdf(Request $request)
	{
		return ImprimeActa::imprimePDF($request->idAsignacion);
	}

	public function delete(Request $request)
	{
		$asignacion = Asignacion_acta::join('control.grupos_acta', 'asignacion_acta.id_grupos_acta', '=', 'grupos_acta.id')
			->join('control.ciclos', 'asignacion_acta.id_ciclos', '=', 'ciclos.id')
			->join('control.carreras', 'carreras.id', '=', 'id_carrera')
			->select('asignacion_acta.*', 'grupos_acta.nombre', 'ciclos.nombre_ciclo', 'carreras.nombre AS nombrec')
			->where([['id_grupos_acta', $request->idg], ['ciclos.id', $request->idciclo], ['carreras.id', $request->idcarrera],
				['modalidad', $request->mod . ""]])->get();

		foreach ($asignacion as $asig) {
			Alumnos::deleteAsignacionAlumno($asig->id);
			//$result =  Alumno_Calificacion::where('id_asignacion_acta',$asig->id)->delete();
		}
		$result = Asignacion_acta::where([['id_grupos_acta', $request->idg], ['id_ciclos', $request->idciclo], ['id_carrera', $request->idcarrera],
			['modalidad', $request->mod . ""]])->delete();

	}

	public function checkAuth()
	{
		if (!Auth::check()) {
			// Si está autenticado lo mandamos a la raíz donde estara el mensaje de bienvenida.
			return redirect()->action('HomeController@index');
		}
	}
}
