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

	public function imprime($idCiclo) {
		$ciclos = ciclos::find($idCiclo);
		$estadisticas = Estadisticas::getEstadisticasL($ciclos->id, 1);
		if (!isset($estadisticas["error"])) {
			$fpdi = new \fpdi\FPDI('L', 'mm', 'A4');
			$link = "components/pdf/estadistica.pdf";
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

			$X = 10;
			$Y = 63;
			$i=1;
			$fpdi->setXY(10,56 );
			$fpdi->setFont('Arial', '', 10);
			$fpdi->Cell(42,8,"TURNO",1,0,"C");
			$fpdi->Cell(80,8,"TOTAL POR AREAS O CARRERAS",1,0,"C");
			$fpdi->Cell(20,8,"GRADO",1,0,"C");
			$fpdi->Cell(20,8,"GRUPOS",1,0,"C");
			$fpdi->Cell(20,8,"HOMBRES",1,0,"C");
			$fpdi->Cell(20,8,"MUJERES",1,0,"C");
			$fpdi->Cell(20,8,"TOTAL ALUMNOS",1,0,"C");
			$fpdi->Cell(20,8,"ALUMNOS A EGRESAR",1,0,"C");
			$fpdi->setFont('Arial', '', 8);
			foreach ($estadisticas as $estadistica){
				$fpdi->setXY(10, $Y);
				$fpdi->write(10, $estadistica->descripcion);
				$fpdi->setXY(50, $Y);
				$fpdi->write(10, iconv('UTF-8', 'windows-1252',$estadistica->nombre));
				$fpdi->setXY(120, $Y);
				$fpdi->write(10, $estadistica->grado);
				$fpdi->setXY(140, $Y);
				$fpdi->write(10, $estadistica->grupos);
				$fpdi->setXY(160, $Y);
				$fpdi->write(10, $estadistica->hombres);
				$fpdi->setXY(170, $Y);
				$fpdi->write(10, $estadistica->mujeres);
				if ($i>23){
					$fpdi->addPage();
					$tplIdx = $fpdi->importPage(2, '/MediaBox');
					$fpdi->useTemplate($tplIdx, 0, 0);
					$fpdi->setFont('Arial', '', 8);
					//ciclo
					/*$fpdi->setXY(45, 38.3);
					$fpdi->write(10, $ciclos->ciclo_escolar);
					$fpdi->setXY(194, 38.3);
					$fpdi->write(10, $ciclos->nombre_ciclo);*/
					$X = 10;
					$Y = 20;
					$i=-7;
				}else{
					$Y+=5;
					$i++;
				}

			}
			$fpdi->Output("ciclo.pdf", "I");
		}

	}
}