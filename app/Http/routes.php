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
});
