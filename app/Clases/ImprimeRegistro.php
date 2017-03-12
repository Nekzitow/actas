<?php
/**
 * Created by PhpStorm.
 * User: OSORIO
 * Date: 28/02/2017
 * Time: 07:40 AM
 */

namespace App\Clases;

define('DIRECTOR', 'LIC. IVON ESPINOSA SANTOS');
define('VALIDACION', 'LIC. LUCIA RUIZ NARCIA');

class PDFs extends \fpdi\FPDI {

	var $angle = 0;

	function Rotate($angle, $x = -1, $y = -1) {
		if ($x == -1)
			$x = $this->x;
		if ($y == -1)
			$y = $this->y;
		if ($this->angle != 0)
			$this->_out('Q');
		$this->angle = $angle;
		if ($angle != 0) {
			$angle *= M_PI / 180;
			$c = cos($angle);
			$s = sin($angle);
			$cx = $x * $this->k;
			$cy = ($this->h - $y) * $this->k;
			$this->_out(sprintf('q %.5F %.5F %.5F %.5F %.2F %.2F cm 1 0 0 1 %.2F %.2F cm', $c, $s, -$s, $c, $cx, $cy, -$cx, -$cy));
		}
	}

	function RotatedText($x, $y, $txt, $angle) {
		//Text rotated around its origin
		$this->Rotate($angle, $x, $y);
		$this->Text($x, $y, $txt);
		$this->Rotate(0);
	}


}

class ImprimeRegistro {
	public function imprimeRegistro($carrera, $campus, $cicloEscolar, $grupo, $modalidad, $tipoModalidad, $rvoe,
									$materias, $registros, $registrosReg) {
		$fpdi = new PDFs('L', 'mm', array(215.90, 340.36));
		//$fpdi = new \fpdi\FPDI('L', 'mm', array(215.90,30.36));
		$link = "components/pdf/FORMATO REGISTRO 2017v2.pdf";
		$pageCount = $fpdi->setSourceFile($link);
		$hombres = 0;
		$hombresFin = 0;
		$mujeres = 0;
		$mujeresFin = 0;
		$pagina = 1;
		$de = 1;
		if (count($registros)>25){
			$de = 2;
		}
		//calculamos los hombres y mujeres
		foreach ($registros as $registro) {
			if ($registro->sexo == "M") {
				$hombres++;
			} else {
				$mujeres++;
			}
			if ($registro->status != 1) {
				if ($registro->sexo == "M") {
					$hombresFin++;
				} else {
					$mujeresFin++;
				}
			}
		}
		foreach ($registrosReg as $registro) {
			if ($registro->tipo == 2) {
				if ($registro->sexo == "M") {
					$hombres++;
				} else {
					$mujeres++;
				}

				if ($registro->status != 1) {
					if ($registro->sexo == "M") {
						$hombresFin++;
					} else {
						$mujeresFin++;
					}
				}
			}
		}
		//END ENCABEZADO
		self::printHeader($fpdi, $carrera, $campus, $cicloEscolar, $grupo, $modalidad, $rvoe, $materias, $hombres, $mujeres
			, $hombresFin, $mujeresFin);

		$y = 90;
		$i = 0;
		$fpdi->setFont('Arial', '', 8);
		$fpdi->setXY(291, 44);
		$fpdi->write(10, $pagina);
		$fpdi->setXY(315, 44);
		$fpdi->write(10, $de);
		$progresivo = 1;
		foreach ($registros as $registro) {
			if ($i < 25) {
				$fpdi->setXY(11, $y);
				$fpdi->Cell(10, 10, $progresivo, 0, 0, "C");
				$fpdi->setXY(30, $y);
				$fpdi->write(10, "P");
				$fpdi->setXY(35, $y);
				$fpdi->write(10, $registro->clave);
				$fpdi->setXY(57, $y);
				$fpdi->write(10, iconv('UTF-8', 'windows-1252', $registro->nombre));
				$fpdi->setXY(146, $y);
				$fpdi->write(10, $registro->sexo);
				$xMateria = 154;
				$tsMateria = 217;
				$contadorMateria = 0;
				$encontrado = 0;
				if ($registro->status == 1) {
					$reprobadas = 0;
					$encontradas = 0;
					foreach ($materias as $materiaAlumno) {
						$calificaciones = Registros::getCalificacionMateria($materiaAlumno->id, $registro->matricula);
						if ($calificaciones->cf <= 5){
							$encontradas++;
							//$reprobadas++;
						}
					}
					if($encontradas<4){
						foreach ($materias as $materiaAlumno) {
							$calificaciones = Registros::getCalificacionMateria($materiaAlumno->id, $registro->matricula);
							//$fpdi->write(10, $calificaciones->cf);
							if ($calificaciones->ts >= 5) {
								//clave materia
								$fpdi->setXY($tsMateria, $y);
								$fpdi->write(10, self::getClaveMateria($contadorMateria));
								$fpdi->setXY($tsMateria + 9, $y);
								//$fpdi->write(10, $calificaciones->ts);
								if (intval($calificaciones->ts) > 5) {
									$fpdi->write(10, $calificaciones->ts);
								} else {
									$reprobadas += 1;
									$fpdi->write(10, "NP");
								}
								$fpdi->setXY($tsMateria + 19, $y);
								$fpdi->write(10, "EX");
								$tsMateria += 33;
								$encontrado++;
							}elseif ($calificaciones->cf<=5){
								$fpdi->setXY($xMateria, $y);
								$fpdi->Cell(10, 10, 5, 0, 0, "C");
								//clave materia
								$fpdi->setXY($tsMateria, $y);
								$fpdi->write(10, self::getClaveMateria($contadorMateria));
								$fpdi->setXY($tsMateria + 9, $y);
								//$fpdi->write(10, $calificaciones->ts);
								if (intval($calificaciones->ts) > 5) {
									$fpdi->write(10, $calificaciones->ts);
								} else {
									$reprobadas += 1;
									$fpdi->write(10, "NP");
								}
								$fpdi->setXY($tsMateria + 19, $y);
								$fpdi->write(10, "EX");
								$tsMateria += 33;
								$encontrado++;
							}else{
								$fpdi->setXY($xMateria, $y);
								$fpdi->Cell(10, 10, $calificaciones->cf, 0, 0, "C");
							}
							$contadorMateria++;
							$xMateria += 8;
						}
						self::PrintSituacion($fpdi, $y, $reprobadas);
						self::imprimirFinal($fpdi, $y, $reprobadas);
						self::drawLines(3 - $encontrado, $y, $fpdi, $tsMateria);
					}else{
						$fpdi->setXY($xMateria, $y + 2.7);
						$fpdi->setFillColor(255, 255, 255);
						$fpdi->Cell(55, 3.5, iconv('UTF-8', 'windows-1252', "BAJA POR REPROBACIÓN"), 0, 1, "C", 1);
						$fpdi->setXY(323, $y);
						$fpdi->write(10, "BR");
						self::drawLines(3 - $encontrado, $y, $fpdi, $tsMateria);
						self::imprimirFinal($fpdi, $y, count($materias));
					}

				} else {
					$fpdi->setXY($xMateria, $y + 2.7);
					$fpdi->setFillColor(255, 255, 255);
					if ($registro->status == 2) {
						$fpdi->Cell(55, 3.5, iconv('UTF-8', 'windows-1252', "BAJA POR DESERCIÓN"), 0, 1, "C", 1);
						$fpdi->setXY(323, $y);
						$fpdi->write(10, "BD");
					} else {
						$fpdi->Cell(55, 3.5, iconv('UTF-8', 'windows-1252', "BAJA POR REPROBACIÓN"), 0, 1, "C", 1);
						$fpdi->setXY(323, $y);
						$fpdi->write(10, "BR");
					}
					//$fpdi->Cell(0,10,$text,0,1,'L',1); //
					self::drawLines(3 - $encontrado, $y, $fpdi, $tsMateria);
					self::imprimirFinal($fpdi, $y, count($materias));
				}

				if ($i == 8 || $i == 15) {
					$y += 3.5;
				} else {
					$y += 4;
				}


			}
			$i++;
			if ($i == 25) {
				//imprimimos la parte de atras
				if(count($registros) > 25) {
					self::printBack($fpdi, $materias, $cicloEscolar->nombre_ciclo);
					self::printHeader($fpdi, $carrera, $campus, $cicloEscolar, $grupo, $modalidad, $rvoe, $materias, $hombres, $mujeres, $hombresFin, $mujeresFin);
					$pagina++;
					$fpdi->setFont('Arial', '', 8);
					$fpdi->setXY(291, 44);
					$fpdi->write(10, $pagina);
					$fpdi->setXY(315, 44);
					$fpdi->write(10, $de);
				}
				$y = 90;
				$i = 0;
				$fpdi->setFont('Arial', '', 8);
			}
			$progresivo++;
		}
		if($progresivo<25){
			for ($h = $progresivo; $h <= 25;$h++){
				$fpdi->setXY(11, $y);
				$fpdi->Cell(10, 10, "/", 0, 0, "C");
				$fpdi->setXY(27, $y);
				$fpdi->Cell(10, 10, "/", 0, 0, "C");
				$fpdi->setXY(36, $y);
				$fpdi->Cell(20, 10, "/", 0, 0, "C");
				$fpdi->setXY(65, $y);
				$fpdi->Cell(10, 10, "/", 0, 0, "C");
				$fpdi->setXY(90, $y);
				$fpdi->Cell(10, 10, "/", 0, 0, "C");
				$fpdi->setXY(120, $y);
				$fpdi->Cell(10, 10, "/", 0, 0, "C");
				$fpdi->setXY(144, $y);
				$fpdi->Cell(10, 10, "/", 0, 0, "C");
				$tsMateria = 217;
				for($i = 0 ;$i<3;$i++){
					$fpdi->setXY($tsMateria, $y);
					$fpdi->write(10, "-");
					$fpdi->setXY($tsMateria + 10, $y);
					$fpdi->write(10, "-");
					$fpdi->setXY($tsMateria + 20, $y);
					$fpdi->write(10, "-");
					$tsMateria += 33;
				}

				$fpdi->setXY(314,$y);
				$fpdi->write(10, "-");
				$fpdi->setXY(325,$y);
				$fpdi->write(10, "-");
				if ($h == 8 || $h == 15) {
					$y += 3.5;
				} else {
					$y += 4;
				}
			}
		}
		if($progresivo>26 && $progresivo<50){
			for ($h = $progresivo; $h<= 50;$h++){
				$fpdi->setXY(11, $y);
				$fpdi->Cell(10, 10, "/", 0, 0, "C");
				$fpdi->setXY(27, $y);
				$fpdi->Cell(10, 10, "/", 0, 0, "C");
				$fpdi->setXY(36, $y);
				$fpdi->Cell(20, 10, "/", 0, 0, "C");
				$fpdi->setXY(65, $y);
				$fpdi->Cell(10, 10, "/", 0, 0, "C");
				$fpdi->setXY(90, $y);
				$fpdi->Cell(10, 10, "/", 0, 0, "C");
				$fpdi->setXY(120, $y);
				$fpdi->Cell(10, 10, "/", 0, 0, "C");
				$fpdi->setXY(144, $y);
				$fpdi->Cell(10, 10, "/", 0, 0, "C");
				$tsMateria = 217;
				for($i = 0 ;$i<3;$i++){
					$fpdi->setXY($tsMateria, $y);
					$fpdi->write(10, "-");
					$fpdi->setXY($tsMateria + 10, $y);
					$fpdi->write(10, "-");
					$fpdi->setXY($tsMateria + 20, $y);
					$fpdi->write(10, "-");
					$tsMateria += 33;
				}

				$fpdi->setXY(314,$y);
				$fpdi->write(10, "-");
				$fpdi->setXY(325,$y);
				$fpdi->write(10, "-");
				if ($h == 34 || $h == 41) {
					$y += 3.5;
				} else {
					$y += 4;
				}
			}
		}
		//imprimimos la parte de atras
		self::printBack($fpdi, $materias, $cicloEscolar->nombre_ciclo);
		$y = 56.2;
		$i = 0;
		$fpdi->setFont('Arial', '', 8);
		foreach ($registrosReg as $registro) {
			if ($i < 25) {
				$fpdi->setXY(32, $y);
				$fpdi->write(10, $registro->clave);
				$fpdi->setXY(57, $y);
				$fpdi->write(10, iconv('UTF-8', 'windows-1252', $registro->nombre));
				$fpdi->setXY(159, $y);
				$fpdi->write(10, $registro->sexo);
				$xMateria = 165;
				$tsMateria = 231;
				$contadorMateria = 0;
				$encontrado = 0;
				if ($registro->status == 1) {
					$reprobadas = 0;
					foreach ($materias as $materiaAlumno) {
						$calificacioness = Registros::getCalificacionMateria($materiaAlumno->id, $registro->matricula);
						$fpdi->setXY($xMateria, $y);
						if (isset($calificacioness->cf)) {
							$fpdi->Cell(10, 10, $calificacioness->cf, 0, 0, "C");
							//$fpdi->write(10, $calificaciones->cf);
							if ($calificacioness->ts >= 5) {
								//clave materia
								$fpdi->setXY($tsMateria, $y);
								$fpdi->write(10, self::getClaveMateria($contadorMateria));
								$fpdi->setXY($tsMateria + 6, $y);
								if (intval($calificacioness->ts) > 5) {
									$fpdi->write(10, $calificacioness->ts);
								} else {
									$reprobadas += 1;
									$fpdi->write(10, "NP");
								}
								$fpdi->setXY($tsMateria + 12, $y);
								$fpdi->write(10, "EX");
								$tsMateria += 19.5;
								$encontrado++;
							}
						}

						$contadorMateria++;
						$xMateria += 8;
					}
					self::PrintSituacion($fpdi, $y, $reprobadas);
					self::imprimirFinal($fpdi, $y, $reprobadas);
					self::drawLinesBack(3 - $encontrado, $y, $fpdi, $tsMateria);
				} else {
					$fpdi->setXY($xMateria, $y + 3.4);
					$fpdi->setFillColor(255, 255, 255);
					if ($registro->status == 2) {
						$fpdi->Cell(55, 3, iconv('UTF-8', 'windows-1252', "BAJA POR DESERCIÓN"), 0, 1, "C", 1);
						$fpdi->setXY(323, $y);
						$fpdi->write(10, "BD");
					} else {
						$fpdi->Cell(55, 3, iconv('UTF-8', 'windows-1252', "BAJA POR REPROBACIÓN"), 0, 1, "C", 1);
						$fpdi->setXY(323, $y);
						$fpdi->write(10, "BR");
					}
					self::drawLinesBack(3 - $encontrado, $y, $fpdi, $tsMateria);
					self::imprimirFinal($fpdi, $y, count($materias));
					//$fpdi->Cell(0,10,$text,0,1,'L',1); //
				}

				if ($i == 8 || $i == 15) {
					$y += 3.5;
				} else {
					$y += 4;
				}


			}
			$i++;
			if ($i == 25) {
				//imprimimos la parte de atras
				self::printBack($fpdi, $materias, $cicloEscolar->nombre_ciclo);
				if(count($registros) > 25) {
					self::printHeader($fpdi, $carrera, $campus, $cicloEscolar, $grupo, $modalidad, $rvoe, $materias, $hombres, $mujeres, $hombresFin, $mujeresFin);
				}
				$y = 90;
				$i = 0;
				$fpdi->setFont('Arial', '', 8);
			}
		}
		$fpdi->SetTitle($cicloEscolar->nombre_ciclo);
		$fpdi->Output("Registro esco" . ".pdf", "I");
	}

	public static function getClaveMateria($contador) {
		switch ($contador) {
			case 0:
				return "A";
				break;
			case 1:
				return "B";
				break;
			case 2:
				return "C";
				break;
			case 3:
				return "D";
				break;
			case 4:
				return "E";
				break;
			case 5:
				return "F";
				break;
			case 6:
				return "G";
				break;
		}
	}

	public static function printHeader($fpdi, $carrera, $campus, $cicloEscolar, $grupo, $modalidad, $rvoe, $materias,
									   $hombres, $mujeres, $hombresFin, $mujeresFin) {
		$tplIdx = $fpdi->importPage(1, '/MediaBox');
		$fpdi->addPage();
		$fpdi->useTemplate($tplIdx, 0, 0);
		//ENCABEZADO
		$fpdi->setFont('Arial', '', 8);

		$fpdi->setXY(49, 35.7);
		$fpdi->write(10, iconv('UTF-8', 'windows-1252', "CENTRO DE FORMACIÓN PROFESIONAL DE CHIAPAS MAYA"));
		$fpdi->setXY(53, 40);
		$fpdi->write(10, iconv('UTF-8', 'windows-1252', $campus[0]["localidad"]));
		$fpdi->setXY(52, 44);
		$fpdi->write(10, iconv('UTF-8', 'windows-1252', $carrera->nombre));
		$fpdi->setXY(53, 48.5);
		$fpdi->write(10, iconv('UTF-8', 'windows-1252', $rvoe->descripcion));

		$fpdi->setXY(165, 35.7);
		$fpdi->write(10, iconv('UTF-8', 'windows-1252', $campus[0]["clave"]));
		$fpdi->setXY(166, 40);
		$fpdi->write(10, $modalidad->mod);
		$fpdi->setXY(166, 44);
		$fpdi->write(10, $rvoe->rvoe);

		$fpdi->setXY(229, 35.5);
		$fpdi->write(10, $cicloEscolar->ciclo_escolar);
		$fpdi->setXY(221, 44);
		$fpdi->write(10, $cicloEscolar->nombre_ciclo);
		$fpdi->setXY(228, 40);
		$fpdi->write(10, iconv('UTF-8', 'windows-1252', $grupo->grado . "° " . $grupo->grupo));
		$fpdi->setXY(292, 20);
		$fpdi->write(10, $hombres);
		$fpdi->setXY(303, 20);
		$fpdi->write(10, $mujeres);
		$fpdi->setXY(313, 20);
		$fpdi->write(10, $mujeres + $hombres);
		$fpdi->setXY(292, 25);
		$fpdi->write(10, $hombres - $hombresFin);
		$fpdi->setXY(303, 25);
		$fpdi->write(10, $mujeres - $mujeresFin);
		$fpdi->setXY(313, 25);
		$fpdi->write(10, ($hombres - $hombresFin) + ($mujeres - $mujeresFin));
		//END ENCABEZADO
		self::printMaterias($fpdi, $materias);
	}

	public static function printBack($fpdi, $materias, $cicloEscolar) {
		$tplIdx = $fpdi->importPage(2, '/MediaBox');
		$fpdi->addPage();
		$fpdi->useTemplate($tplIdx, 0, 0);
		self::printMateriasBack($fpdi, $materias);
		$fpdi->setFont('Arial', 'B', 8);
		$fpdi->setXY(6, 185);
		$fpdi->Cell(49, 3.5, iconv('UTF-8', 'windows-1252', DIRECTOR), 0, 1, "C");
		$fpdi->setXY(60, 190);
		$fpdi->Cell(49, 3.5, iconv('UTF-8', 'windows-1252', VALIDACION), 0, 1, "C");
		$fpdi->setXY(123, 185);
		$fpdi->Cell(49, 3.5, iconv('UTF-8', 'windows-1252', DIRECTOR), 0, 1, "C");
		$fpdi->setXY(178, 190);
		$fpdi->Cell(49, 3.5, iconv('UTF-8', 'windows-1252', VALIDACION), 0, 1, "C");
		$fpdi->setXY(270, 119);
		$fpdi->Cell(49, 3.5, iconv('UTF-8', 'windows-1252', $cicloEscolar), 0, 1, "L");
	}

	public static function printMaterias($fpdi, $materias) {
		$xMateria = 156;
		$contadorMateria = 0;
		//Descargamos las materias
		$fpdi->setFont('Arial', '', 6);
		foreach ($materias as $materia) {
			switch ($contadorMateria) {
				case 1:
					$xMateria = 165;
					break;
				case 2:
					$xMateria = 174;
					break;
				case 3:
					$xMateria = 182;
					break;
				case 4:
					$xMateria = 191;
					break;
				case 5:
					$xMateria = 200;
					break;
				case 6:
					$xMateria = 208;
					break;

			}
			$longitudTxt = strlen($materia->materia);
			if ($longitudTxt >= 25) {
				$arreglo = explode(" ", $materia->materia);

				if (strlen($arreglo[0] . " " . $arreglo[1]) >= 21) {
					$fpdi->RotatedText($xMateria, 92, $arreglo[0], 90);
					$fpdi->RotatedText($xMateria + 2, 92, $arreglo[1], 90);
					if (isset($arreglo[2]) && isset($arreglo[3])) {
						if (isset($arreglo[4]) && isset($arreglo[5])) {
							$fpdi->RotatedText($xMateria + 4, 92, $arreglo[2] . " " . $arreglo[3] . "" . $arreglo[4] . " " . $arreglo[5], 90);
						} elseif (isset($arreglo[4])) {
							$fpdi->RotatedText($xMateria + 4, 92, $arreglo[2] . " " . $arreglo[3] . " " . $arreglo[4], 90);
						} else {
							$fpdi->RotatedText($xMateria + 4, 92, $arreglo[2] . " " . $arreglo[3], 90);
						}
						if (isset($arreglo[6])) {
							$fpdi->RotatedText($xMateria + 6, 92, $arreglo[6], 90);
						}

					} else if (isset($arreglo[2])) {
						$fpdi->RotatedText($xMateria + 2, 92, $arreglo[2], 90);
					}
				} else {
					$fpdi->RotatedText($xMateria, 92, $arreglo[0] . " " . $arreglo[1], 90);
					if (isset($arreglo[2]) && isset($arreglo[3])) {
						if (isset($arreglo[4]) && isset($arreglo[5])) {
							$fpdi->RotatedText($xMateria + 2, 92, $arreglo[2] . " " . $arreglo[3] . "" . $arreglo[4] . " " . $arreglo[5], 90);
						} elseif (isset($arreglo[4])) {
							$fpdi->RotatedText($xMateria + 2, 92, $arreglo[2] . " " . $arreglo[3] . " " . $arreglo[4], 90);
						} else {
							$fpdi->RotatedText($xMateria + 2, 92, $arreglo[2] . " " . $arreglo[3], 90);
						}
						if (isset($arreglo[6])) {
							$fpdi->RotatedText($xMateria + 4, 92, $arreglo[6], 90);
						}

					} else if (isset($arreglo[2])) {
						$fpdi->RotatedText($xMateria + 2, 92, $arreglo[2], 90);
					}
				}
				/*if (isset($arreglo[2]) && isset($arreglo[3])){
					if(isset($arreglo[4]) && isset($arreglo[5])) {
						$fpdi->RotatedText($xMateria + 2, 92, $arreglo[2]." ".$arreglo[3]."".$arreglo[4] . " " . $arreglo[5], 90);
					}elseif(isset($arreglo[4])){
						$fpdi->RotatedText($xMateria+2, 92, $arreglo[2]." ".$arreglo[3]." ".$arreglo[4], 90);
					}else{
						$fpdi->RotatedText($xMateria+2, 92, $arreglo[2]." ".$arreglo[3], 90);
					}
					if (isset($arreglo[6])){
						$fpdi->RotatedText($xMateria+4, 92, $arreglo[6], 90);
					}

				}else if (isset($arreglo[2])){
					$fpdi->RotatedText($xMateria+2, 92, $arreglo[2], 90);
				}*/
			} else {
				if (strlen($materia->materia) >= 22) {
					$arreglo = explode(" ", $materia->materia);
					$fpdi->RotatedText($xMateria, 92, $arreglo[0], 90);
					if (isset($arreglo[2])) {
						if (isset($arreglo[3])) {
							$fpdi->RotatedText($xMateria + 2, 92, $arreglo[1] . " " . $arreglo[2] . " " . $arreglo[3], 90);
						} else
							$fpdi->RotatedText($xMateria + 2, 92, $arreglo[1] . " " . $arreglo[2], 90);

					} else {
						$fpdi->RotatedText($xMateria + 2, 92, $arreglo[1], 90);
					}
				} else {
					$fpdi->RotatedText($xMateria, 92, $materia->materia, 90);
				}
			}

			$contadorMateria++;
			//$xMateria += 9;
		}
	}

	public static function printMateriasBack($fpdi, $materias) {
		$xMateria = 167;
		$contadorMateria = 0;
		//Descargamos las materias
		$fpdi->setFont('Arial', '', 6);
		foreach ($materias as $materia) {
			switch ($contadorMateria) {
				case 1:
					$xMateria = 177;
					break;
				case 2:
					$xMateria = 186;
					break;
				case 3:
					$xMateria = 195;
					break;
				case 4:
					$xMateria = 205;
					break;
				case 5:
					$xMateria = 214;
					break;
				case 6:
					$xMateria = 224;
					break;

			}
			$longitudTxt = strlen($materia->materia);
			if ($longitudTxt >= 25) {
				$arreglo = explode(" ", $materia->materia);

				if (strlen($arreglo[0] . " " . $arreglo[1]) >= 21) {
					$fpdi->RotatedText($xMateria, 50, $arreglo[0], 90);
					$fpdi->RotatedText($xMateria + 2, 50, $arreglo[1], 90);
					if (isset($arreglo[2]) && isset($arreglo[3])) {
						if (isset($arreglo[4]) && isset($arreglo[5])) {
							$fpdi->RotatedText($xMateria + 4, 50, $arreglo[2] . " " . $arreglo[3] . "" . $arreglo[4] . " " . $arreglo[5], 90);
						} elseif (isset($arreglo[4])) {
							$fpdi->RotatedText($xMateria + 4, 50, $arreglo[2] . " " . $arreglo[3] . " " . $arreglo[4], 90);
						} else {
							$fpdi->RotatedText($xMateria + 4, 50, $arreglo[2] . " " . $arreglo[3], 90);
						}
						if (isset($arreglo[6])) {
							$fpdi->RotatedText($xMateria + 6, 50, $arreglo[6], 90);
						}

					} else if (isset($arreglo[2])) {
						$fpdi->RotatedText($xMateria + 2, 50, $arreglo[2], 90);
					}
				} else {
					$fpdi->RotatedText($xMateria, 50, $arreglo[0] . " " . $arreglo[1], 90);
					if (isset($arreglo[2]) && isset($arreglo[3])) {
						if (isset($arreglo[4]) && isset($arreglo[5])) {
							$fpdi->RotatedText($xMateria + 2, 50, $arreglo[2] . " " . $arreglo[3] . "" . $arreglo[4] . " " . $arreglo[5], 90);
						} elseif (isset($arreglo[4])) {
							$fpdi->RotatedText($xMateria + 2, 50, $arreglo[2] . " " . $arreglo[3] . " " . $arreglo[4], 90);
						} else {
							$fpdi->RotatedText($xMateria + 2, 50, $arreglo[2] . " " . $arreglo[3], 90);
						}
						if (isset($arreglo[6])) {
							$fpdi->RotatedText($xMateria + 4, 50, $arreglo[6], 90);
						}

					} else if (isset($arreglo[2])) {
						$fpdi->RotatedText($xMateria + 2, 50, $arreglo[2], 90);
					}
				}
				/*if (isset($arreglo[2]) && isset($arreglo[3])){
					if(isset($arreglo[4]) && isset($arreglo[5])) {
						$fpdi->RotatedText($xMateria + 2, 92, $arreglo[2]." ".$arreglo[3]."".$arreglo[4] . " " . $arreglo[5], 90);
					}elseif(isset($arreglo[4])){
						$fpdi->RotatedText($xMateria+2, 92, $arreglo[2]." ".$arreglo[3]." ".$arreglo[4], 90);
					}else{
						$fpdi->RotatedText($xMateria+2, 92, $arreglo[2]." ".$arreglo[3], 90);
					}
					if (isset($arreglo[6])){
						$fpdi->RotatedText($xMateria+4, 92, $arreglo[6], 90);
					}

				}else if (isset($arreglo[2])){
					$fpdi->RotatedText($xMateria+2, 92, $arreglo[2], 90);
				}*/
			} else {
				if (strlen($materia->materia) >= 22) {
					$arreglo = explode(" ", $materia->materia);
					$fpdi->RotatedText($xMateria, 50, $arreglo[0], 90);
					if (isset($arreglo[2])) {
						if (isset($arreglo[3])) {
							$fpdi->RotatedText($xMateria + 2, 50, $arreglo[1] . " " . $arreglo[2] . " " . $arreglo[3], 90);
						} else
							$fpdi->RotatedText($xMateria + 2, 50, $arreglo[1] . " " . $arreglo[2], 90);

					} else {
						$fpdi->RotatedText($xMateria + 2, 50, $arreglo[1], 90);
					}
				} else {
					$fpdi->RotatedText($xMateria, 50, $materia->materia, 90);
				}
			}

			$contadorMateria++;
			//$xMateria += 9;
		}
	}

	public function drawLines($materias, $y, $fpdi, $tsMateria) {
		for ($i = 0; $i < $materias; $i++) {
			$fpdi->setXY($tsMateria, $y);
			$fpdi->write(10, "-");
			$fpdi->setXY($tsMateria + 10, $y);
			$fpdi->write(10, "-");
			$fpdi->setXY($tsMateria + 20, $y);
			$fpdi->write(10, "-");
			$tsMateria += 33;
		}
	}

	public function drawLinesBack($materias, $y, $fpdi, $tsMateria) {
		for ($i = 0; $i < $materias; $i++) {
			$fpdi->setXY($tsMateria, $y);
			$fpdi->write(10, "-");
			$fpdi->setXY($tsMateria + 6, $y);
			$fpdi->write(10, "-");
			$fpdi->setXY($tsMateria + 12, $y);
			$fpdi->write(10, "-");
			$tsMateria += 19.5;
		}
	}

	public function imprimirFinal($fpdi, $y, $materias) {
		$fpdi->setXY(314, $y);
		if ($materias > 0)
			$fpdi->write(10, $materias);
		else {
			$fpdi->write(10, "-");
		}
		//325
	}

	public function PrintSituacion($fpdi, $y, $materias) {
		$fpdi->setXY(325, $y);
		if ($materias > 0)
			$fpdi->write(10, "PI");
		else {
			$fpdi->write(10, "P");
		}
	}

	public function diagonalesReg($materias, $y, $fpdi, $tsMateria) {
		//$tsMateria = 231;
		//$tsMateria += 19.5;

		for ($i = 0; $i < $materias; $i++) {
			$fpdi->setXY($tsMateria, $y);
			$fpdi->write(10, "/");
			$fpdi->setXY($tsMateria + 6, $y);
			$fpdi->write(10, "/");
			$fpdi->setXY($tsMateria + 12, $y);
			$fpdi->write(10, "/");
			$tsMateria += 19.5;
		}

	}

}