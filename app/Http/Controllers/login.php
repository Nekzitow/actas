<?php

namespace App\Http\Controllers;
use Auth;
use Illuminate\Http\Request;
use Validator;

use App\Http\Requests;

class login extends Controller
{
	function aut(Request $request){
		$validacion = Validator::make($request->all(), [
			'email' => 'required|email|max:255',
			'password' => 'required|min:6',
		]);
		if ($validacion->fails()) {
			return redirect()->back()->withErrors($validacion->errors());
		} else {
			$valid = Auth::attempt(['email' => $request->email, 'password' => $request->password],true);
			if ($valid){
				$user = Auth::user();
				return redirect("/");
			}else{
					return redirect()->back()->withErrors(['email' => 'email incorrecto', 'password' => 'contraseña incorrecto']);
			}
		}
	}
}
