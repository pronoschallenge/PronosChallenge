<?php

require ("fonctions.php");
require("../config.php");

ouverture ();

/* Paging */
$sLimit = "";
if ( isset( $_GET['iDisplayStart'] ) )
{
	$sLimit = "LIMIT ".mysql_real_escape_string( $_GET['iDisplayStart'] ).", ".
		mysql_real_escape_string( $_GET['iDisplayLength'] );
}

/* Ordering */
if ( isset( $_GET['iSortCol_0'] ) )
{
	$sOrder = "ORDER BY  ";
	for ( $i=0 ; $i<mysql_real_escape_string( $_GET['iSortingCols'] ) ; $i++ )
	{
		$sOrder .= fnColumnToField(mysql_real_escape_string( $_GET['iSortCol_'.$i] ))."
			".mysql_real_escape_string( $_GET['iSortDir_'.$i] ) .", ";
	}
	$sOrder = substr_replace( $sOrder, "", -2 );
}


$sQuery="SELECT phpl_membres.pseudo, phpl_journees.numero, sum(phpl_pronostics.points) as pts FROM phpl_journees
	LEFT OUTER JOIN phpl_matchs ON phpl_journees.id=phpl_matchs.id_journee
	LEFT OUTER JOIN phpl_pronostics ON phpl_matchs.id=phpl_pronostics.id_match
	LEFT OUTER JOIN phpl_membres ON phpl_membres.id=phpl_pronostics.id_membre
	WHERE phpl_pronostics.id_champ=$gr_champ
	GROUP BY phpl_journees.id, phpl_membres.id
	$sOrder
	$sLimit";

$rResult=mysql_query($sQuery) or die (mysql_error());


$sQuery = "SELECT phpl_membres.id FROM phpl_journees
	LEFT OUTER JOIN phpl_matchs ON phpl_journees.id=phpl_matchs.id_journee
	LEFT OUTER JOIN phpl_pronostics ON phpl_matchs.id=phpl_pronostics.id_match
	LEFT OUTER JOIN phpl_membres ON phpl_membres.id=phpl_pronostics.id_membre
	WHERE phpl_journees.id_champ=10
	GROUP BY phpl_journees.id, phpl_membres.id";
	
$rResultTotal = mysql_query( $sQuery ) or die(mysql_error());
$iTotal = mysql_num_rows($rResultTotal);
	
	



$sOutput = '{';
$sOutput .= '"iTotalRecords": '.$iTotal.', ';
$sOutput .= '"iTotalDisplayRecords": '.$iTotal.', ';
$sOutput .= '"aaData": [ ';
while ( $aRow = mysql_fetch_array( $rResult ) )
{
	if($aRow[0] != '')
	{
		$sOutput .= "[";
		$sOutput .= "\"".$aRow[0]."\",";
		$sOutput .= "\"".$aRow[1]."\",";
		$sOutput .= "\"".$aRow[2]."\"";
		$sOutput .= "],";
	}
}
$sOutput = substr_replace( $sOutput, "", -1 );
$sOutput .= '] }';

echo $sOutput;


function fnColumnToField( $i )
{
	if ( $i == 0 )
		return "phpl_membres.pseudo";
	else if ( $i == 1 )
		return "phpl_journees.numero";
	else if ( $i == 2 )
		return "pts";
}

?>
