<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Regularizacion extends Model
{
    //
	protected $table = "regularizacion";
	protected $fillable = [
		'calificacion',
	];
}
