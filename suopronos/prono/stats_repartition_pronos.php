<?php

	/* Répartion des pronostics */

	require ("fonctions.php");
	require("../config.php");

	ouverture ();

	include("../lib/pChart/pData.class");  
	include("../lib/pChart/pChart.class");

  	$queryRepartitionPronoUser="SELECT pronostic, count(*) FROM phpl_pronostics 
		WHERE id_champ=$gr_champ
		GROUP BY pronostic";
 	$resultRepartitionPronoUser=mysql_query($queryRepartitionPronoUser) or die (mysql_error());

  	// Dataset definition   
  	$DataSet = new pData;  
	$tabKeys = array();
	$tabVals = array();
	
	for ($i=0; $rowRepartitionPronoUser=mysql_fetch_array($resultRepartitionPronoUser); $i++) {
		$tabKeys[$i] = $rowRepartitionPronoUser[0];
		$tabVals[$i] = $rowRepartitionPronoUser[1];
	}
  	$DataSet->AddPoint($tabVals,"Serie1");  
  	$DataSet->AddPoint($tabKeys,"Serie2");  
  	$DataSet->AddAllSeries();  
  	$DataSet->SetAbsciseLabelSerie("Serie2");  
   
  	// Initialise the graph  
  	$chart = new pChart(350,200);  
  	$chart->drawFilledRoundedRectangle(7,7,373,193,5,240,240,240);  
  	$chart->drawRoundedRectangle(5,5,375,195,5,230,230,230);  
   
  	// Draw the pie chart  
  	$chart->setFontProperties("../lib/Fonts/tahoma.ttf",8);  
  	$chart->drawPieGraph($DataSet->GetData(),$DataSet->GetDataDescription(),150,90,110,PIE_PERCENTAGE,TRUE,50,20,5);  
  	$chart->drawPieLegend(310,15,$DataSet->GetData(),$DataSet->GetDataDescription(),250,250,250);  
   
	$chart->Stroke();

?>
