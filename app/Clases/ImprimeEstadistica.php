<?php
/**
 * Created by PhpStorm.
 * User: OSORIO
 * Date: 28/12/2016
 * Time: 11:56 AM
 */

namespace App\Clases;


use App\ciclos;

class ImprimeEstadistica {

	public function imprime($idCiclo, $tipo) {
		$ciclos = ciclos::find($idCiclo);
		if ($tipo == 1) {
			$estadisticas = Estadisticas::getEstadisticasL($ciclos->id, 1);
			$modalidad = "MATUTINO Y SEMIESCOLARIZADO";
		} else {
			$estadisticas = Estadisticas::getEstadisticasP($ciclos->id, 1);
			$modalidad = "MIXTO";
		}

		if (!isset($estadisticas["error"])) {
			$fpdi = new \fpdi\FPDI('L', 'mm', 'letter');
			$link = "components/pdf/estadistica2.pdf";
			$pageCount = $fpdi->setSourceFile($link);
			$tplIdx = $fpdi->importPage(1, '/MediaBox');
			$size = $fpdi->getTemplateSize($tplIdx);
			$fpdi->addPage();
			$fpdi->useTemplate($tplIdx, 0, 0);
			$fpdi->setFont('Arial', '', 8);
			//ciclo
			$fpdi->setXY(45, 38.3);
			$fpdi->write(10, $ciclos->ciclo_escolar);
			$fpdi->setXY(194, 38.3);
			$fpdi->write(10, $ciclos->nombre_ciclo);
			$fpdi->setXY(55, 47);
			$fpdi->write(10, "CENTRO DE FORMACION PROFESIONAL DE CHIAPAS MAYA");
			$fpdi->setXY(189, 42.5);
			$fpdi->write(10, $modalidad);
			$fpdi->setXY(40, 42.5);
			$fpdi->write(10, "20 PONIENTE SUR N.960, COL. PENIPAK, TUXTLA GTZ CHIAPAS");
			$fpdi->SetTitle($ciclos->nombre_ciclo);
			$X = 10;
			$Y = 67;
			$i = 1;
			$fpdi->setFont('Arial', '', 8);
			$idCarrera = 0;
			$subHombres = 0;
			$subMujeres = 0;
			$subGrupos = 0;
			$subEgreso = 0;
			$totalEgreso = 0;
			$total = 0;
			foreach ($estadisticas as $estadistica) {
				if ($idCarrera == 0) {
					$idCarrera = $estadistica->idCarrera;
				}
				if ($idCarrera != $estadistica->idCarrera) {
					$idCarrera = $estadistica->idCarrera;
					$fpdi->setFont('Arial', 'B', 8);
					$fpdi->setXY(19, $Y);
					$fpdi->SetFillColor(164, 160, 160);
					$fpdi->Cell(37.5, 5, "", 1, 0, "C", true);
					$fpdi->setXY(56.5, $Y);
					$fpdi->Cell(63.2, 5, "", 1, 0, "C", true);
					$fpdi->setXY(119.6, $Y);
					$fpdi->Cell(22.3, 5, "SUBTOTAL", 1, 0, "C", true);
					$fpdi->setXY(142, $Y);
					$fpdi->Cell(22.2, 5, $subGrupos, 1, 0, "C", true);
					$fpdi->setXY(164.1, $Y);
					$fpdi->Cell(24, 5, $subHombres, 1, 0, "C", true);
					$fpdi->setXY(188.1, $Y);
					$fpdi->Cell(22.2, 5, $subMujeres, 1, 0, "C", true);
					$fpdi->setXY(210.2, $Y);
					$total += $subHombres + $subMujeres;
					$fpdi->Cell(24.4, 5, $subHombres + $subMujeres, 1, 0, "C", true);
					$fpdi->setXY(234.5, $Y);
					$totalEgreso+=$subEgreso;
					$fpdi->Cell(24.5, 5, $subEgreso, 1, 0, "C", true);
					$fpdi->setFont('Arial', '', 8);
					$subHombres = 0;
					$subMujeres = 0;
					$subGrupos = 0;
					$subEgreso = 0;
					if ($i > 22) {
						$fpdi->addPage();
						$tplIdx = $fpdi->importPage(2, '/MediaBox');
						$fpdi->useTemplate($tplIdx, 0, 0);
						$fpdi->setFont('Arial', '', 8);
						$X = 10;
						$Y = 20;
						$i = -7;
					} else {
						$Y += 5;
						$i++;
					}
				}
				$fpdi->setXY(19, $Y);
				if (strlen($estadistica->descripcion) > 12) {
					$fpdi->setFont('Arial', '', 7);
					$fpdi->Cell(37.5, 5, $estadistica->descripcion, 1, 0, "C");
				} else {
					$fpdi->Cell(37.5, 5, $estadistica->descripcion, 1, 0, "C");
				}
				/*$fpdi->Cell(37.5,5, $estadistica->descripcion,1,0,"C");*/
				$fpdi->setFont('Arial', '', 8);
				$fpdi->setXY(56.5, $Y);
				if (strlen($estadistica->nombre) > 35) {
					$fpdi->setFont('Arial', '', 7);
					$fpdi->Cell(63.2, 5, iconv('UTF-8', 'windows-1252', $estadistica->nombre), 1, 0, "C");
				} else {
					$fpdi->Cell(63.2, 5, iconv('UTF-8', 'windows-1252', $estadistica->nombre), 1, 0, "C");
				}
				$fpdi->setFont('Arial', '', 8);
				$fpdi->setXY(119.6, $Y);
				$fpdi->Cell(22.3, 5, $estadistica->grado, 1, 0, "C");
				$fpdi->setXY(142, $Y);
				$subGrupos += $estadistica->grupos;
				$fpdi->Cell(22.2, 5, $estadistica->grupos, 1, 0, "C");
				$fpdi->setXY(164.1, $Y);
				$subHombres += $estadistica->hombres;
				$fpdi->Cell(24, 5, $estadistica->hombres, 1, 0, "C");
				$fpdi->setXY(188.1, $Y);
				$subMujeres += $estadistica->mujeres;
				$fpdi->Cell(22.2, 5, $estadistica->mujeres, 1, 0, "C");
				$fpdi->setXY(210.2, $Y);
				$sub = $estadistica->hombres + $estadistica->mujeres;
				$fpdi->Cell(24.4, 5, $sub, 1, 0, "C");
				$fpdi->setXY(234.5, $Y);
				if ($estadistica->grado > 7) {
					$subEgreso += $sub;
				}elseif($estadistica->grado > 3 && $tipo == 2){
					$subEgreso += $sub;
				}
				if ($estadistica->grado > 7) {
					$fpdi->Cell(24.5, 5, $sub, 1, 0, "C");
				}elseif($estadistica->grado > 3 && $tipo == 2){
					$fpdi->Cell(24.5, 5, $sub, 1, 0, "C");
				}else{
					$fpdi->Cell(24.5, 5, "", 1, 0, "C");
				}

				if ($i > 22) {
					$fpdi->addPage();
					$tplIdx = $fpdi->importPage(2, '/MediaBox');
					$fpdi->useTemplate($tplIdx, 0, 0);
					$fpdi->setFont('Arial', '', 8);
					//ciclo
					$X = 10;
					$Y = 20;
					$i = -7;
				} else {
					$Y += 5;
					$i++;
				}

			}
			if ($i > 22) {
				$fpdi->addPage();
				$tplIdx = $fpdi->importPage(2, '/MediaBox');
				$fpdi->useTemplate($tplIdx, 0, 0);
				$fpdi->setFont('Arial', '', 8);
				//ciclo
				$X = 10;
				$Y = 20;
				$i = -7;
			}
			$fpdi->setFont('Arial', 'B', 8);
			$fpdi->setXY(19, $Y);
			$fpdi->SetFillColor(164, 160, 160);
			$fpdi->Cell(37.5, 5, "", 1, 0, "C", true);
			$fpdi->setXY(56.5, $Y);
			$fpdi->Cell(63.2, 5, "", 1, 0, "C", true);
			$fpdi->setXY(119.6, $Y);
			$fpdi->Cell(22.3, 5, "SUBTOTAL", 1, 0, "C", true);
			$fpdi->setXY(142, $Y);
			$fpdi->Cell(22.2, 5, $subGrupos, 1, 0, "C", true);
			$fpdi->setXY(164.1, $Y);
			$fpdi->Cell(24, 5, $subHombres, 1, 0, "C", true);
			$fpdi->setXY(188.1, $Y);
			$fpdi->Cell(22.2, 5, $subMujeres, 1, 0, "C", true);
			$fpdi->setXY(210.2, $Y);
			$total += $subHombres + $subMujeres;
			$fpdi->Cell(24.4, 5, $subHombres + $subMujeres, 1, 0, "C", true);
			$fpdi->setXY(234.5, $Y);
			$totalEgreso+=$subEgreso;
			$fpdi->Cell(24.5, 5, $subEgreso, 1, 0, "C", true);
			if ($i > 22) {
				$fpdi->addPage();
				$tplIdx = $fpdi->importPage(2, '/MediaBox');
				$fpdi->useTemplate($tplIdx, 0, 0);
				$fpdi->setFont('Arial', '', 8);
				//ciclo
				$X = 10;
				$Y = 20;
				$i = -7;
			} else {
				$Y += 5;
				$i++;
			}
			$fpdi->setXY(188.1, $Y);
			$fpdi->Cell(22.2, 5, "TOTAL:", 1, 0, "C");
			$fpdi->setXY(210.2, $Y);
			$fpdi->Cell(24.4, 5, $total, 1, 0, "C");
			$fpdi->setXY(234.5, $Y);
			$fpdi->Cell(24.5, 5, $totalEgreso, 1, 0, "C");
			$fpdi->Output($ciclos->nombre_ciclo . ".pdf", "I");
		}

	}


	public function imprimeMaep($idCiclo) {
		$ciclos = ciclos::find($idCiclo);

		$estadisticas = Estadisticas::getEstadisticasL($ciclos->id, 1);
		$modalidad = "MATUTINO Y SEMIESCOLARIZADO";

		/*	$estadisticas = Estadisticas::getEstadisticasP($ciclos->id, 1);
			$modalidad = "MIXTO";*/

		$fpdi = new \fpdi\FPDI('L', 'mm', 'letter');
		if (!isset($estadisticas["error"])) {
			$link = "components/pdf/MAEP.pdf";
			$pageCount = $fpdi->setSourceFile($link);
			$tplIdx = $fpdi->importPage(1, '/MediaBox');
			$size = $fpdi->getTemplateSize($tplIdx);
			$fpdi->addPage();
			$fpdi->useTemplate($tplIdx, 0, 0);
			$fpdi->setFont('Arial', '', 8);
			//ciclo
			$fpdi->setXY(49, 45);
			$fpdi->write(10, $ciclos->ciclo_escolar);
			$fpdi->setXY(190, 45);
			$fpdi->write(10, $ciclos->nombre_ciclo);

			$fpdi->setXY(178, 52);
			$fpdi->write(10, $modalidad);

			$fpdi->SetTitle($ciclos->nombre_ciclo);
			$Y = 70.6;
			$i = 1;
			$fpdi->setFont('Arial', '', 8);
			$idCarrera = 0;
			$subHombres = 0;
			$subMujeres = 0;
			$subGrupos = 0;
			$subEgreso = 0;
			$total = 0;
			$nombreCarrera = "";
			$totalEgreso = 0;
			foreach ($estadisticas as $estadistica) {
				if ($idCarrera == 0){
					$idCarrera = $estadistica->idCarrera;
					$nombreCarrera = $estadistica->nombre;
				}
				if ($idCarrera != $estadistica->idCarrera){
					$fpdi->setXY(27.5, $Y);
					$fpdi->Cell(35, 10, "MATUTINO", 1, 0, "C");
					$fpdi->setXY(62.5, $Y);
					if (strlen($nombreCarrera) > 35) {
						$fpdi->setFont('Arial', '', 7);
						$fpdi->Cell(69.3, 10, iconv('UTF-8', 'windows-1252', $nombreCarrera), 1, 0, "C");
					} else {
						$fpdi->Cell(69.3, 10, iconv('UTF-8', 'windows-1252', $nombreCarrera), 1, 0, "C");
					}
					$fpdi->setXY(131.8, $Y);
					$fpdi->setFont('Arial', '', 8);
					$fpdi->Cell(28.2, 10, $subGrupos, 1, 0, "C");
					$fpdi->setXY(160.1, $Y);
					$fpdi->Cell(19.8, 10, $subHombres, 1, 0, "C");
					$fpdi->setXY(180, $Y);
					$fpdi->Cell(19.8, 10, $subMujeres, 1, 0, "C");
					$fpdi->setXY(199.8, $Y);
					$sub = $subMujeres + $subHombres;
					$total += $sub;
					$fpdi->Cell(27, 10, $sub, 1, 0, "C");
					$fpdi->setXY(227, $Y);
					$fpdi->Cell(22.5, 10, $subEgreso, 1, 0, "C");
					if ($i > 11) {
						$fpdi->addPage();
						$tplIdx = $fpdi->importPage(2, '/MediaBox');
						$fpdi->useTemplate($tplIdx, 0, 0);
						$fpdi->setFont('Arial', '', 8);
						//ciclo
						$X = 10;
						$Y = 20;
						$i = -7;
					} else {
						$Y += 10;
						$i++;
					}
					$idCarrera = $estadistica->idCarrera;
					$nombreCarrera = $estadistica->nombre;
					$subHombres = 0;
					$subMujeres = 0;
					$subGrupos = 0;
					$subEgreso = 0;
				}
				$subGrupos += $estadistica->grupos;
				$subMujeres += $estadistica->mujeres;
				$subHombres += $estadistica->hombres;
				if ($estadistica->grado > 7) {
					$subEgreso += $estadistica->hombres + $estadistica->mujeres;
					$totalEgreso += $subEgreso;
				}
			}
			$fpdi->setXY(27.5, $Y);
			$fpdi->Cell(35, 10, "MATUTINO", 1, 0, "C");
			$fpdi->setXY(62.5, $Y);
			if (strlen($nombreCarrera) > 35) {
				$fpdi->setFont('Arial', '', 7);
				$fpdi->Cell(69.3, 10, iconv('UTF-8', 'windows-1252', $nombreCarrera), 1, 0, "C");
			} else {
				$fpdi->Cell(69.3, 10, iconv('UTF-8', 'windows-1252', $nombreCarrera), 1, 0, "C");
			}
			$fpdi->setXY(131.8, $Y);
			$fpdi->Cell(28.2, 10, $subGrupos, 1, 0, "C");
			$fpdi->setXY(160.1, $Y);
			$fpdi->Cell(19.8, 10, $subHombres, 1, 0, "C");
			$fpdi->setXY(180, $Y);
			$fpdi->Cell(19.8, 10, $subMujeres, 1, 0, "C");
			$fpdi->setXY(199.8, $Y);
			$sub = $subMujeres + $subHombres;
			$total += $sub;
			$fpdi->Cell(27, 10, $sub, 1, 0, "C");
			$fpdi->setXY(227, $Y);
			$fpdi->Cell(22.5, 10, $subEgreso, 1, 0, "C");
			if ($i > 11) {
				$fpdi->addPage();
				$tplIdx = $fpdi->importPage(2, '/MediaBox');
				$fpdi->useTemplate($tplIdx, 0, 0);
				$fpdi->setFont('Arial', '', 8);
				//ciclo
				$X = 10;
				$Y = 20;
				$i = -7;
			} else {
				$Y += 10;
				$i++;
			}
			$fpdi->setXY(180, $Y);
			$fpdi->Cell(19.8, 10, "TOTAL:", 1, 0, "C");
			$fpdi->setXY(199.8, $Y);
			$fpdi->Cell(27, 10, $total, 1, 0, "C");
			$fpdi->setXY(227, $Y);
			$fpdi->Cell(22.5, 10, $totalEgreso, 1, 0, "C");
		}



		/**
		 * Sigue maestrias
		 */

		$estadisticasp = Estadisticas::getEstadisticasP($ciclos->id, 1);
		$modalidad = "MIXTO";
		if (!isset($estadisticasp["error"])) {
			$link = "components/pdf/MAEP.pdf";
			$pageCount = $fpdi->setSourceFile($link);
			$tplIdx = $fpdi->importPage(1, '/MediaBox');
			$size = $fpdi->getTemplateSize($tplIdx);
			$fpdi->addPage();
			$fpdi->useTemplate($tplIdx, 0, 0);
			$fpdi->setFont('Arial', '', 8);
			//ciclo
			$fpdi->setXY(49, 45);
			$fpdi->write(10, $ciclos->ciclo_escolar);
			$fpdi->setXY(190, 45);
			$fpdi->write(10, $ciclos->nombre_ciclo);

			$fpdi->setXY(178, 52);
			$fpdi->write(10, $modalidad);

			$fpdi->SetTitle($ciclos->nombre_ciclo);
			$Y = 70.6;
			$i = 1;
			$fpdi->setFont('Arial', '', 8);
			$idCarrera = 0;
			$subHombres = 0;
			$subMujeres = 0;
			$subGrupos = 0;
			$subEgreso = 0;
			$total = 0;
			$nombreCarrera = "";
			$totalEgreso = 0;
			foreach ($estadisticasp as $estadistica) {
				if ($idCarrera == 0){
					$idCarrera = $estadistica->idCarrera;
					$nombreCarrera = $estadistica->nombre;
				}
				if ($idCarrera != $estadistica->idCarrera){
					$fpdi->setXY(27.5, $Y);
					$fpdi->Cell(35, 10, "MATUTINO", 1, 0, "C");
					$fpdi->setXY(62.5, $Y);
					if (strlen($nombreCarrera) > 35) {
						$fpdi->setFont('Arial', '', 7);
						$fpdi->Cell(69.3, 10, iconv('UTF-8', 'windows-1252', $nombreCarrera), 1, 0, "C");
					} else {
						$fpdi->Cell(69.3, 10, iconv('UTF-8', 'windows-1252', $nombreCarrera), 1, 0, "C");
					}
					$fpdi->setXY(131.8, $Y);
					$fpdi->setFont('Arial', '', 8);
					$fpdi->Cell(28.2, 10, $subGrupos, 1, 0, "C");
					$fpdi->setXY(160.1, $Y);
					$fpdi->Cell(19.8, 10, $subHombres, 1, 0, "C");
					$fpdi->setXY(180, $Y);
					$fpdi->Cell(19.8, 10, $subMujeres, 1, 0, "C");
					$fpdi->setXY(199.8, $Y);
					$sub = $subMujeres + $subHombres;
					$total += $sub;
					$fpdi->Cell(27, 10, $sub, 1, 0, "C");
					$fpdi->setXY(227, $Y);
					$fpdi->Cell(22.5, 10, $subEgreso, 1, 0, "C");
					if ($i > 11) {
						$fpdi->addPage();
						$tplIdx = $fpdi->importPage(2, '/MediaBox');
						$fpdi->useTemplate($tplIdx, 0, 0);
						$fpdi->setFont('Arial', '', 8);
						//ciclo
						$X = 10;
						$Y = 20;
						$i = -7;
					} else {
						$Y += 10;
						$i++;
					}
					$idCarrera = $estadistica->idCarrera;
					$nombreCarrera = $estadistica->nombre;
					$subHombres = 0;
					$subMujeres = 0;
					$subGrupos = 0;
					$subEgreso = 0;
				}
				$subGrupos += $estadistica->grupos;
				$subMujeres += $estadistica->mujeres;
				$subHombres += $estadistica->hombres;
				if ($estadistica->grado > 3) {
					$subEgreso += $estadistica->hombres + $estadistica->mujeres;
					$totalEgreso += $subEgreso;
				}
			}
			$fpdi->setXY(27.5, $Y);
			$fpdi->Cell(35, 10, "MATUTINO", 1, 0, "C");
			$fpdi->setXY(62.5, $Y);
			if (strlen($nombreCarrera) > 35) {
				$fpdi->setFont('Arial', '', 7);
				$fpdi->Cell(69.3, 10, iconv('UTF-8', 'windows-1252', $nombreCarrera), 1, 0, "C");
			} else {
				$fpdi->Cell(69.3, 10, iconv('UTF-8', 'windows-1252', $nombreCarrera), 1, 0, "C");
			}
			$fpdi->setXY(131.8, $Y);
			$fpdi->Cell(28.2, 10, $subGrupos, 1, 0, "C");
			$fpdi->setXY(160.1, $Y);
			$fpdi->Cell(19.8, 10, $subHombres, 1, 0, "C");
			$fpdi->setXY(180, $Y);
			$fpdi->Cell(19.8, 10, $subMujeres, 1, 0, "C");
			$fpdi->setXY(199.8, $Y);
			$sub = $subMujeres + $subHombres;
			$total += $sub;
			$fpdi->Cell(27, 10, $sub, 1, 0, "C");
			$fpdi->setXY(227, $Y);
			$fpdi->Cell(22.5, 10, $subEgreso, 1, 0, "C");
			if ($i > 11) {
				$fpdi->addPage();
				$tplIdx = $fpdi->importPage(2, '/MediaBox');
				$fpdi->useTemplate($tplIdx, 0, 0);
				$fpdi->setFont('Arial', '', 8);
				//ciclo
				$X = 10;
				$Y = 20;
				$i = -7;
			} else {
				$Y += 10;
				$i++;
			}
			$fpdi->setXY(180, $Y);
			$fpdi->Cell(19.8, 10, "TOTAL:", 1, 0, "C");
			$fpdi->setXY(199.8, $Y);
			$fpdi->Cell(27, 10, $total, 1, 0, "C");
			$fpdi->setXY(227, $Y);
			$fpdi->Cell(22.5, 10, $totalEgreso, 1, 0, "C");
		}
		$fpdi->Output($ciclos->nombre_ciclo . ".pdf", "I");
	}
}