<?
//***********************************************************************/
// Phpleague : gestionnaire de championnat                              */
// ============================================                         */
//                                                                      */
// Version : 0.82b                                                       */
// Copyright (c) 2004    Alexis MANGIN                                  */
// http://phpleague.univert.org                                         */
//                                                                      */
// This program is free software. You can redistribute it and/or modify */
// it under the terms of the GNU General Public License as published by */
// the Free Software Foundation; either version 2 of the License.       */
//                                                                      */
//***********************************************************************/
// Support technique : http://phpleague.univert.org/forum               */
//                                                                      */
//***********************************************************************/

require ("../config.php") ;

ouverture ();

$membre = $_REQUEST['id_membre'];
if (isset($_REQUEST['type'])) {$type=$_REQUEST['type'];} else {$type='general';}

$pseudo="";

// On détermine le pseudo du membre à partir de son id
$query="SELECT pseudo FROM phpl_membres WHERE id='$membre'";
$result=mysql_query($query);
while ($row=mysql_fetch_array($result))
{
  $pseudo=$row[0];
}

// Récupération de l'id du championnat
$query="SELECT id_champ
		FROM phpl_gr_championnats
		WHERE phpl_gr_championnats.id='$gr_champ'";
		
$result=mysql_query($query);

$row=mysql_fetch_array($result);
$champ=$row[0];

//on détermine le nombre de joueurs
$query="SELECT phpl_membres.id FROM phpl_membres WHERE actif='1'";
$result=mysql_query($query);
// on supprimer l'admin (suo) et Guit qui est en double
$nb_joueurs=mysql_num_rows($result);

// on détermine la dernière journee jouée
$query="SELECT max(phpl_journees.numero) FROM phpl_journees, phpl_matchs where phpl_journees.id=phpl_matchs.id_journee and buts_dom is not NULL AND phpl_journees.id_champ='$champ'";
$result=mysql_query($query);
while ($row=mysql_fetch_array($result)){$fin=$row[0];}

// On détermine le nombre de journée total
$query="SELECT id FROM phpl_journees WHERE id_champ='$champ'";
$result=mysql_query($query);
$nb_journees=mysql_num_rows($result);

// On déterrmine l'année et le nom de la division
$query="SELECT phpl_saisons.annee, phpl_divisions.nom FROM phpl_saisons, phpl_divisions, phpl_championnats where phpl_championnats.id=$champ AND phpl_divisions.id=phpl_championnats.id_division and phpl_championnats.id_saison=phpl_saisons.id";
$result=mysql_query($query);

while ($row=mysql_fetch_array($result))
{
	$annee=($row[0]+1); 
	$nom_champ="$row[1] $row[0]/$annee";
}

/*
$largeur=500;
$hauteur=350;
$marge_gauche=10;
$marge_haut=20;

Header("Content-type: image/png");

$image = ImageCreate($largeur+40,$hauteur+40+$marge_haut);
$rouge = ImageColorAllocate($image,255,0,0);
$vert = ImageColorAllocate($image,0,106,54);
$bleu = ImageColorAllocate($image,0,0,255);
$blanc = ImageColorAllocate($image,255,255,255);
$noir = ImageColorAllocate($image,0,0,0);
$gris = ImageColorAllocate($image,150,150,150);


ImageFilledRectangle($image,0,0,$largeur+40,$hauteur+40+$marge_haut,$blanc);
//ImageFilledRectangle($image,20+$marge_gauche,5+$marge_haut,20+$marge_gauche,$hauteur+5+$marge_haut,$noir); // trait vertical à gauche
$titre = "Evolution du classement de $pseudo ($nom_champ)";
$titrePolice = 4;
imageString($image, $titrePolice, ($largeur+40+$marge_gauche-ImageFontWidth($titrePolice)*strlen($titre))/2, 0, $titre, $noir); // titre


$y=1;

while ($y<=$nb_journees)
{
if (!($y%2)==0)
{
$titre = $y;
$titrePolice = 2;
imageString($image, $titrePolice, ($y-1)*($largeur)/$nb_journees+$marge_gauche+20, $hauteur+$marge_haut, $titre, $noir); // numérotation journées
$y++;
}
else {$y++;}
}

$x=$hauteur/$nb_joueurs;
$y=1;
while ($x<=$hauteur)
{	
	$titre = $y;
	$titrePolice = "2";
	if ($y<$nb_joueurs && ($y==1 || ($y%5)==0))
	{
		ImageString($image, $titrePolice, $marge_gauche+1, $x-8+$marge_haut, $titre, $noir); // numérotation place
		ImageFilledRectangle($image,$marge_gauche+15,$x+ $marge_haut,$largeur+15,$x+$marge_haut,$noir); // traits par place
	} 
	$x=$x+($hauteur/$nb_joueurs);
	$y++;
}

$requete_sql = "SELECT classement, fin 
				FROM phpl_pronos_graph 
				WHERE id_membre='$membre' 
				AND type='$type'
				AND id_gr_champ='$gr_champ'
				ORDER BY fin";
$resultat_requete = mysql_query($requete_sql);

$x=$marge_gauche+20; //pas nb equipes !
$i=0;
$points=0;
$place=0;
$place=array();
$points=array();
while($colonne=mysql_fetch_array($resultat_requete))
{
	$i=$colonne['fin'];
        $place[$i] = $colonne['classement'];
        $x = $i*(($largeur-$marge_gauche+11)/$nb_journees)+20;
	$points[$i][0] = $x;
	$points[$i][1] = $hauteur+$marge_haut-($nb_joueurs-$colonne['classement'])*$hauteur/$nb_joueurs;
	
	$titrePolice=2;

	//$i++;
}

for($i=0;$i<$fin;$i++)
{
	if($points[$i] != null)
	{
		ImageLine($image, $points[$i][0],$points[$i][1],$points[$i+1][0],$points[$i+1][1],$rouge);
	}
}



for($i=0;$i<$fin+1;$i++)
{
        if (!isset($place[$i+1])){imageString($image, $titrePolice, $points[$i][0],$points[$i][1] , $place[$i], $rouge);}

	elseif ($place[$i+1]<=$place[$i]){imageString($image, $titrePolice, $points[$i][0],$points[$i][1] , $place[$i], $rouge);}

	elseif ($place[$i+1]>$place[$i]){imageString($image, $titrePolice, $points[$i][0],$points[$i][1]-11 , $place[$i], $rouge);}
	else {imageString($image, $titrePolice, $points[$i][0], $points[$i][1], $place[$i], $rouge);}
}




$titre = "";
$titrePolice = 4;
imageString($image, $titrePolice, ($largeur+$marge_gauche-ImageFontWidth($titrePolice)*strlen($titre))/2, $hauteur+30, $titre, $vert);

ImagePNG($image);
ImageDestroy($image);
*/

  // pChart inclusions      
  include("../lib/pChart/pData.class");   
  include("../lib/pChart/pChart.class");   
 
$requete_sql = "SELECT classement, fin 
				FROM phpl_pronos_graph 
				WHERE id_membre='$membre' 
				AND type='$type'
				AND id_gr_champ='$gr_champ'
				ORDER BY fin";
$resultat_requete = mysql_query($requete_sql);

$places = array_pad(array(), 39, null);
$i=1;
while($journee=mysql_fetch_array($resultat_requete))
{
	$places[$i++] = $journee['classement'];
}
 
 
  
  // Dataset definition    
  $DataSet = new pData;   
  $DataSet->AddPoint($places,"SerieClassement");
  $DataSet->AddAllSeries();   
  $DataSet->SetAbsciseLabelSerie();   
  //$DataSet->SetSerieName("January","Serie1");   
  
  // Initialise the graph   
  $Test = new pChart(700,400);   
  $Test->setFontProperties("../lib/Fonts/tahoma.ttf",8);   
  $Test->setGraphArea(50,30,670,370);   
  $Test->drawFilledRoundedRectangle(5,5,695,395,5,191,191,191);
  /*
  $Test->drawRoundedRectangle(0,0,700,400,5,33,33,33);
  $Test->drawRoundedRectangle(1,1,699,399,5,33,33,33);
  $Test->drawRoundedRectangle(2,2,698,398,5,33,33,33);
  $Test->drawRoundedRectangle(3,3,697,397,5,33,33,33);   
  */
  $Test->drawGraphArea(150,150,150); 
  
  $nb_joueurs_graph = ($nb_joueurs - $nb_joueurs % 10) + 10;
  
  $Test->setFixedScale($nb_joueurs_graph,0,$nb_joueurs_graph/5,0,38,1);
  $Test->drawScale($DataSet->GetData(),$DataSet->GetDataDescription(),SCALE_NORMAL,20,20,20,TRUE,0,2);   
  $Test->drawGrid(4,FALSE,0,0,0);
  
  // Draw the 0 line   
  $Test->setFontProperties("../lib/Fonts/tahoma.ttf",6);   
  $Test->drawTreshold(0,143,55,72,TRUE,TRUE);   
  
  // Draw the line graph
  //$Test->setLineStyle(3);
  $Test->drawLineGraph($DataSet->GetData(),$DataSet->GetDataDescription());   
  $Test->drawPlotGraph($DataSet->GetData(),$DataSet->GetDataDescription(),2,0,255,0,0);   
  //$Test->writeValues($DataSet->GetData(),$DataSet->GetDataDescription(),"SerieClassement");
  
  // Finish the graph   
  $Test->setFontProperties("../lib/Fonts/tahoma.ttf",8);   
  //$Test->drawLegend(75,35,$DataSet->GetDataDescription(),255,255,255);   
  $Test->setFontProperties("../lib/Fonts/tahoma.ttf",10);   
  $Test->drawTitle(60,22,"Evolution du classement ".$type." de ".$pseudo,50,50,50,585);   
  $Test->Stroke();      

?>
