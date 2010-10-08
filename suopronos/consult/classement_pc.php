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

//require ("../config.php") ;
require ("../consult/fonctions.php");

ouverture ();

if (!isset($_GET['champ']))
{
	$value=GENERAL;
    	demande_champ ();
}
else
{
	$champ=$_GET['champ'];
	$nb_equipe = nb_equipes($champ);
	$nb_journees=($nb_equipe*2)-2;

	if (isset($_GET['debut'])) {$debut=$_GET['debut'];} else {$debut='1';}
	if (isset($_GET['fin'])) {$fin=$_GET['fin'];} else {$fin=($nb_equipe*2)-2;}
	if (isset($_GET['type'])) {$type=$_GET['type'];} else {$type=GENERAL;}


	// MENU TYPES DE CLASSEMENT
?>

<div class="bloc bloc_classement_ligue1">
	<div class="rounded-block-top-left"></div>
	<div class="rounded-block-top-right"></div>
	<div class="rounded-outside">
		<div class="rounded-inside">
			<div class="bloc_entete">
				<div class="bloc_icone"></div>
				<div class="bloc_titre">Ligue 1</div>
			</div>
			<div class="bloc_contenu">

<form method="get" action="">
	<div align="center">

       <select name="champ">
       <option value="0"> </option>
  
<?php       
$query = "SELECT phpl_divisions.nom, phpl_saisons.annee, phpl_championnats.id
                 FROM phpl_championnats, phpl_divisions, phpl_saisons
                 WHERE phpl_championnats.id_division=phpl_divisions.id
                 AND phpl_championnats.id_saison=phpl_saisons.id
                 ORDER BY phpl_saisons.annee DESC, phpl_championnats.id";

$result = mysql_query($query) or die (mysql_error());

while ($row = mysql_fetch_array($result))
{
	echo "<option value=\"$row[2]\"";
	if($champ == $row[2])
	{
		echo " selected=\"selected\" ";
	}
	echo ">$row[0]\n $row[1]/". ($row[1]+1)."\n";
	echo "</option>\n";
}
?>
        </select>
        <br /><br />
<?
echo CONSULT_CLMNT_MSG1;
echo "&nbsp;";
echo "<select name=\"type\">";

if (!(isset($type))) {$type=GENERAL;}
echo "<option value=\"$type\" selected=\"selected\">$type</option>\n";

if ($type!==GENERAL)
{
	$value=GENERAL;
	echo "<option value=\"$value\"> $value</option>\n";
}

if ($type!==DOMICILE)
{
	$value=DOMICILE;
	echo "<option value=\"$value\"> $value</option>\n";
}

if ($type!==EXTERIEUR)
{
	$value=EXTERIEUR;
	echo "<option value=\"$value\"> $value</option>\n";
}

if ($type!==ATTAQUE)
{
	$value=ATTAQUE;
	echo "<option value=\"$value\"> $value</option>\n";
}

if ($type!==DEFENSE)
{
	$value=DEFENSE;
	echo "<option value=\"$value\"> $value</option>\n";
}

if ($type!==GOALDIFF)
{
	$value=GOALDIFF;
	echo "<option value=\"$value\"> $value</option>\n";
}

echo "</select>";

echo CONSULT_CLMNT_MSG2;
echo "<select name=\"debut\">";


for($f=1;$f<=$nb_journees;$f++)
{
        if ($f == $debut)
        { 
?>
          <option value="<?php print $debut; ?>" selected="selected"><?php print $debut; ?></option>
<?php
        }
        else
        {
?>
          <option value="<?php print $f; ?>"><?php print $f; ?></option>
<?php
	}
}

echo "</select>";

// journée de fin
echo CONSULT_CLMNT_MSG3;
echo "<select name=\"fin\">";

for($f=1;$f<=$nb_journees;$f++)
{
        if ($f == $fin)
        { 
?>
          <option value="<?php print $fin; ?>" selected="selected"><?php print $fin; ?></option>
<?php
	}
        else
        { 
?>
          <option value="<?php print $f; ?>"><?php print $f; ?></option>
<?php
	}
}
        
//echo "</select><input type=\"hidden\" name=\"champ\" value=\"$champ\">\n";
echo "</select>\n";
echo "<input type=\"hidden\" name=\"page\" value=\"classement_pc\">\n";

$button=BOUTON_CONSULT_CLASSEMENT;
echo "<input type=\"submit\" value=\"$button\"></div>\n</form>\n";

$query = "SELECT phpl_divisions.nom, phpl_saisons.annee, (phpl_saisons.annee)+1 
          FROM phpl_championnats, phpl_divisions, phpl_saisons 
          WHERE phpl_championnats.id='$champ' 
          AND phpl_divisions.id=phpl_championnats.id_division
          AND phpl_saisons.id=phpl_championnats.id_saison";

$result = mysql_query($query) or die (mysql_error());

while ($row=mysql_fetch_array($result))
{
	//echo "<br>";
	echo "<div align=\"center\"><h4><b>".$row[0]."  ".$row[1]."/".$row[2]."</b></h4></div>\n";
}

$class=0;
$lien="oui";
if (isset($type))
{

    // RAPPEL DES PARAMETRES du CHAMPIONNAT
    $result=mysql_query("SELECT accession, barrage, estimation, relegation, id_equipe_fetiche, fiches_clubs
                         FROM phpl_parametres
                         WHERE id_champ='$champ'");
    $row=mysql_fetch_array($result);

    $accession = $row['accession'];
    $barrage = $row['barrage'] + $accession;
    $estimation = $row['estimation'];
    $fiches_clubs = $row['fiches_clubs'];
    $id_equipe_fetiche=$row['id_equipe_fetiche'];
    
    $requete = "SELECT phpl_equipes.id FROM phpl_equipes, phpl_clubs
                  WHERE phpl_clubs.id=phpl_equipes.id_club 
                  AND id_champ='$champ' 
                  AND phpl_clubs.nom='exempte'";
    $resultats=mysql_query($requete) or die (mysql_error());
    $exempte=mysql_num_rows($resultats);
    if ($exempte=='1') {$relegation = $nb_equipe - $row['relegation']-1;}
    else {$relegation = $nb_equipe - $row['relegation'];}
      
switch($type)
{
case GENERAL;    // CLASSEMENT GENERAL
        {

$legende=CONSULT_CLMNT_MSG4.$debut.CONSULT_CLMNT_MSG5.$fin;

if ($debut=="1" and $fin==$nb_journees)
 {
    $requete="SELECT DISTINCT * FROM phpl_clmnt_cache WHERE ID_CHAMP='$champ' ORDER BY POINTS DESC, DIFF DESC, BUTSPOUR DESC , BUTSCONTRE ASC, NOM";
    clmnt_pc($legende, $type, $accession, $barrage, $relegation, $champ, $requete, $lien, $id_equipe_fetiche);
  
    $query="SELECT max(phpl_journees.numero) FROM phpl_journees, phpl_matchs WHERE phpl_journees.id=phpl_matchs.id_journee and buts_dom is not NULL and phpl_journees.id_champ='$champ'";
    $result=mysql_query($query) or die (mysql_error());
    while ($row=mysql_fetch_array($result))
        {
        $numero=$row[0];
        }
    ?>
    <!--<br />--><br />
    <?
    /*aff_journee($champ, $numero, CONSULT_CLMNT_MSG6, 0, $fiches_clubs, $id_equipe_fetiche);

    if ($numero<$nb_journees)
     {
      aff_journee($champ, $numero+1, CONSULT_CLMNT_MSG62, 0, $fiches_clubs, $id_equipe_fetiche);
     }

    if ($estimation == "1" and $numero>=4)
    {
     echo "<br /><div align=\"center\"><h5><font color=\"red\">".CONSULT_CLMNT_MSG7."</font></h5></div>";
     aff_journee($champ, $numero+1, "<i>".CONSULT_CLMNT_MSG8."</i>", 1, $fiches_clubs, $id_equipe_fetiche);
    }*/

 }

else
 {
  $requete="SELECT DISTINCT * FROM phpl_clmnt WHERE ID_CHAMP='$champ' ORDER BY POINTS DESC, DIFF DESC, BUTSPOUR DESC , BUTSCONTRE ASC, NOM";

  @db_clmnt($champ, $debut, $fin, 0);

  //clmnt($legende, $type, $accession, $barrage, $relegation, $equipe_fetiche, $champ, $debut, $fin, $pts_victoire, $pts_nul, $pts_defaite, $requete);
  clmnt_pc($legende, $type, $accession, $barrage, $relegation,  $champ, $requete, $lien, $id_equipe_fetiche);
 }


}

break;



case DOMICILE;
 {
  $legende=CONSULT_CLMNT_MSG10.$debut.CONSULT_CLMNT_MSG5.$fin;
  
  if ($debut=="1" and $fin==$nb_journees)
 {
  $requete="SELECT NOM, DOMPOINTS, DOMJOUES, DOMG,  DOMN, DOMP, DOMBUTSPOUR, DOMBUTSCONTRE, DOMDIFF, ID_EQUIPE  FROM phpl_clmnt_cache WHERE ID_CHAMP='$champ' ORDER BY DOMPOINTS DESC, DOMDIFF DESC";
  clmnt_pc($legende, $type, $accession, $barrage, $relegation, $champ, $requete, $lien, $id_equipe_fetiche);
 }

  else
 { 
  $requete="SELECT NOM, DOMPOINTS, DOMJOUES, DOMG,  DOMN, DOMP, DOMBUTSPOUR, DOMBUTSCONTRE, DOMDIFF, ID_EQUIPE  FROM phpl_clmnt WHERE ID_CHAMP='$champ' ORDER BY DOMPOINTS DESC, DOMDIFF DESC";
  @db_clmnt($champ, $debut, $fin, 0);
  clmnt_pc($legende, $type, $accession, $barrage, $relegation,  $champ, $requete,$lien, $id_equipe_fetiche);  
 }
} 
break;


case ATTAQUE;
 {
   $legende=CONSULT_CLMNT_MSG11.$debut.CONSULT_CLMNT_MSG5.$fin;
   
   if ($debut=="1" and $fin==$nb_journees)
   {
    $requete="SELECT * FROM phpl_clmnt_cache WHERE ID_CHAMP='$champ' ORDER BY BUTSPOUR DESC, DIFF DESC";
    clmnt_pc($legende, $type, $accession, $barrage, $relegation, $champ, $requete, $lien, $id_equipe_fetiche);
   }

   else
   {
   $requete="SELECT * FROM phpl_clmnt WHERE ID_CHAMP='$champ' ORDER BY BUTSPOUR DESC, DIFF DESC";
   @db_clmnt($champ, $debut, $fin, 0);
   clmnt_pc($legende, $type, $accession, $barrage, $relegation,  $champ, $requete, $lien, $id_equipe_fetiche);
   }
 }
break;


case DEFENSE;
 {
  $legende=CONSULT_CLMNT_MSG12.$debut.CONSULT_CLMNT_MSG5.$fin;
  
  if ($debut=="1" and $fin==$nb_journees)
   {
    $requete="SELECT * FROM phpl_clmnt_cache WHERE ID_CHAMP='$champ' ORDER BY BUTSCONTRE ASC, DIFF DESC";
    clmnt_pc($legende, $type, $accession, $barrage, $relegation, $champ, $requete, $lien, $id_equipe_fetiche);
   }

   else
   {
    $requete="SELECT * FROM phpl_clmnt WHERE ID_CHAMP='$champ' ORDER BY BUTSCONTRE ASC, DIFF DESC";
    @db_clmnt($champ, $debut, $fin, 0);
    clmnt_pc($legende, $type, $accession, $barrage, $relegation, $champ, $requete, $lien, $id_equipe_fetiche);
   }
 }
        
break;


case GOALDIFF;
 {
  $legende=CONSULT_CLMNT_MSG13.$debut.CONSULT_CLMNT_MSG5.$fin;
  
  if ($debut=="1" and $fin==$nb_journees)
   {
    $requete="SELECT * FROM phpl_clmnt_cache WHERE id_champ='$champ' ORDER BY DIFF DESC, BUTSPOUR DESC, BUTSCONTRE ASC ";
    clmnt_pc($legende, $type, $accession, $barrage, $relegation, $champ, $requete, $lien, $id_equipe_fetiche);
   }

  else
  {
   $requete="SELECT * FROM phpl_clmnt WHERE id_champ='$champ' ORDER BY DIFF DESC, BUTSPOUR DESC, BUTSCONTRE ASC ";
   @db_clmnt($champ, $debut, $fin, 0);
   clmnt_pc($legende, $type, $accession, $barrage, $relegation,  $champ, $requete,$lien, $id_equipe_fetiche);
  }
 }
break;


case EXTERIEUR;
{
 $legende=CONSULT_CLMNT_MSG14.$debut.CONSULT_CLMNT_MSG5.$fin;
 
   if ($debut=="1" and $fin==$nb_journees)
   {
    $requete="SELECT NOM, EXTPOINTS, EXTJOUES, EXTG,  EXTN, EXTP, EXTBUTSPOUR, EXTBUTSCONTRE, EXTDIFF, ID_EQUIPE  FROM phpl_clmnt_cache WHERE ID_CHAMP='$champ' ORDER BY EXTPOINTS DESC, EXTDIFF DESC ";
    clmnt_pc($legende, $type, $accession, $barrage, $relegation, $champ, $requete, $lien, $id_equipe_fetiche);
   }

  else
  {
 $requete="SELECT NOM, EXTPOINTS, EXTJOUES, EXTG,  EXTN, EXTP, EXTBUTSPOUR, EXTBUTSCONTRE, EXTDIFF, ID_EQUIPE  FROM phpl_clmnt WHERE ID_CHAMP='$champ' ORDER BY EXTPOINTS DESC, EXTDIFF DESC ";
 @db_clmnt($champ, $debut, $fin, 0);
 clmnt_pc($legende, $type, $accession, $barrage, $relegation, $champ, $requete, $lien, $id_equipe_fetiche);
  }
}
break;
}

}
}
?>

			</div>
		</div>
	</div>
	<div class="rounded-block-bottom-left"></div>
	<div class="rounded-block-bottom-right"></div>
</div>	
