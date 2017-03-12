<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

/*Route::get('/', function () {
    return view('welcome');
});*/

Route::auth();

Route::get('/', 'HomeController@index');
Route::group(['middleware' => 'auth'], function () {
    /*Rutas para el modulo de control escolar*/
    Route::get('modules/actas/grupo/{idGrupo}/{idCiclo}/{idCarrera}/{modalidad}', 'actasControlador@inicio');
    Route::get('modules/actas/', 'actasControlador@menu');
    Route::get('modules/actas/grupo/pdf/{idAsignacion}', 'actasControlador@exportpdf');
    Route::get('modules/actas/agregar/acta', 'actasControlador@agregarActa');
//Route::get('modules/actas/imprime/acta', 'actasControlador@exportpdf');
    Route::post('modules/actas/agregar/guardarActa', 'actasControlador@guardarActa');
    Route::post('modules/actas/delete', 'actasControlador@delete');


	/**
	 * Estadistica cuatrimestral
	 */

	Route::get("modules/estadistica","EstadisticaController@inicio");
	Route::get("modules/estadistica/add","EstadisticaController@addEstatistica");
	Route::post("modules/estadistica/save","EstadisticaController@save");
	Route::delete("modules/estadistica","EstadisticaController@drop");
	Route::get("modules/estadistica/imprime/{idCiclo}/{tipo}","EstadisticaController@imprime");
	Route::get("modules/estadistica/imprimemaep/{idCiclo}","EstadisticaController@imprimemaep");


	/**
	 * Registro de Escolaridad
	 */
	Route::get("modules/registro","RegistrosController@inicio");
	Route::get("modules/registro/add","RegistrosController@formulario");
	Route::post("modules/registro","RegistrosController@save");
	Route::get("modules/registro/rvoe","RegistrosController@rvoes");
	Route::get("modules/registro/ver/{idCiclo}/{idCarrera}/{modalidad}","RegistrosController@ver");
	Route::get("modules/registro/reincorporacion","RegistrosController@reincorporacion");
	Route::post("modules/registro/reincorporacion","RegistrosController@saveReincorporacion");
	Route::get("modules/registro/recursamiento/{idCarrera}/{idGrupo}/{idCiclo}/{idModalidad}/{tipoMod}","RegistrosController@recursamiento");
	Route::post("modules/registro/recursamiento","RegistrosController@saveAlumnoRecursador");
	Route::delete("modules/registro/recursamiento","RegistrosController@eliminarRegistroAlumno");
	Route::get("modules/registro/imprimir/{idCarrera}/{idGrupo}/{idCiclo}/{idModalidad}/{tipoMod}","RegistrosController@printRegistro");
	Route::post("modules/registro/grupo","RegistrosController@grupo");
	Route::post("modules/registro/alumno","RegistrosController@alumno");
	Route::get("modules/registro/alumno","RegistrosController@getAlumnosSing");
});
