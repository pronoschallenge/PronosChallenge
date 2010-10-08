<?php

	/* Historique des journées */

	require ("fonctions.php");
	require("../config.php");

	ouverture ();

	include("../lib/pChart/pData.class");  
	include("../lib/pChart/pChart.class");

  	$queryHistoJournees="SELECT phpl_journees.numero, sum( points ) as nb
		FROM phpl_pronostics
		LEFT OUTER JOIN phpl_matchs ON phpl_matchs.id = phpl_pronostics.id_match
		LEFT OUTER JOIN phpl_journees ON phpl_journees.id=phpl_matchs.id_journee
		WHERE phpl_pronostics.id_champ=$gr_champ
		AND phpl_pronostics.id_membre=$user_id
		GROUP BY phpl_matchs.id_journee";
 	$resultHistoJournees=mysql_query($queryHistoJournees) or die (mysql_error());

	// Dataset definition   
	$dataSet = new pData;
	$tabDays = array();
	$tabVals = array();

	for ($i = 1; $i <= 38; $i++) {
		$tabDays[$i] = $i;
		$tabVals[$i] = null;
	}

	while ($rowHistoJournees=mysql_fetch_array($resultHistoJournees)) {
		$tabVals[$rowHistoJournees[0]] = $rowHistoJournees[1];
	}
			
	$dataSet->AddPoint($tabVals, "Serie1");
	$dataSet->AddPoint($tabDays, "Serie2");  
	$dataSet->AddSerie("Serie1");
	$dataSet->SetAbsciseLabelSerie("Serie2");

	// Graph width
	$width = 800;
	
	// Graph height
	$height = 230;
	
	$heightForAngle = 0;
	$absLabelAngle = 0;
	
	// Initialize the graph
	$chart = new pChart($width,$height+$heightForAngle);
	$chart->setGraphArea(40,30,$width-20,$height-30);
	$chart->setFontProperties("../lib/Fonts/tahoma.ttf",10);
	$chart->drawFilledRoundedRectangle(7,7,$width-7,$height-7+$heightForAngle,5,240,240,240);  
	$chart->drawRoundedRectangle(5,5,$width-5,$height-5+$heightForAngle,5,230,230,230);		
	$chart->drawGraphArea(252,252,252);
	// definition of drawScale method : drawScale($Data,$DataDescription,$ScaleMode,$R,$G,$B,$DrawTicks=TRUE,$Angle=0,$Decimals=1,$WithMargin=FALSE,$SkipLabels=1,$RightScale=FALSE)  
	$chart->setFixedScale(0,10);
	$chart->drawScale($dataSet->GetData(),$dataSet->GetDataDescription(),SCALE_NORMAL,150,150,150,TRUE,$absLabelAngle,2,TRUE);
	$chart->drawGrid(4,TRUE,230,230,230,255);

	// Draw the bar graph
	$chart->drawBarGraph($dataSet->GetData(),$dataSet->GetDataDescription(),TRUE);

	// Finish the graph
	$chart->setFontProperties("../lib/Fonts/tahoma.ttf",10);
	$chart->drawTitle(0,0,"",50,50,50,$width,35);

	$chart->Stroke();

?>
