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
/*Rutas para el modulo de control escolar*/
Route::get('modules/actas/grupo/{idGrupo}/{idCiclo}/{idCarrera}/{modalidad}', 'actasControlador@inicio');
Route::get('modules/actas/', 'actasControlador@menu');
Route::get('modules/actas/grupo/pdf/{idAsignacion}', 'actasControlador@exportpdf');
Route::get('modules/actas/agregar/acta', 'actasControlador@agregarActa');
//Route::get('modules/actas/imprime/acta', 'actasControlador@exportpdf');
Route::post('modules/actas/agregar/guardarActa', 'actasControlador@guardarActa');
Route::post('modules/actas/delete', 'actasControlador@delete');
Route::auth();

Route::get('/', 'HomeController@index');
