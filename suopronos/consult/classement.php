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

include ("avant.php");
require ("../config.php") ;
require ("../consult/fonctions.php");

ouverture ();
ENTETE2 ();

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

?><form method="get" action="">
<div align="center">
<?php echo CONSULT_CLMNT_MSG1; ?>
<select name="type">

<?
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
echo "<option value=\"$value\">Goal average</option>\n";
}

echo "</select>";

echo CONSULT_CLMNT_MSG2;
echo "<select name=\"debut\">";


    for($f=1;$f<=$nb_journees;$f++)
        {
        if ($f == $debut)
          { ?>
          <option value="<?php print $debut; ?>" selected="selected"><?php print $debut; ?></option>
           <?php
          }
        else
         { ?>
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
          { ?>
          <option value="<?php print $fin; ?>" selected="selected"><?php print $fin; ?></option>
           <?php
          }
        else
         { ?>
          <option value="<?php print $f; ?>"><?php print $f; ?></option>
           <?php
          }
        }
        
    echo "</select><input type=\"hidden\" name=\"champ\" value=\"$champ\">\n";
    $button=ENVOI;
    echo "<input type=\"submit\" value=\"$button\"></div>\n</form>\n";


$query = "SELECT phpl_divisions.nom, phpl_saisons.annee, (phpl_saisons.annee)+1 
          FROM phpl_championnats, phpl_divisions, phpl_saisons 
          WHERE phpl_championnats.id='$champ' 
          AND phpl_divisions.id=phpl_championnats.id_division
          AND phpl_saisons.id=phpl_championnats.id_saison";

$result = mysql_query($query) or die (mysql_error());

        while ($row=mysql_fetch_array($result))
        {
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
    clmnt($legende, $type, $accession, $barrage, $relegation, $champ, $requete, $lien, $id_equipe_fetiche);
  
    $query="SELECT max(phpl_journees.numero) FROM phpl_journees, phpl_matchs WHERE phpl_journees.id=phpl_matchs.id_journee and buts_dom is not NULL and phpl_journees.id_champ='$champ'";
    $result=mysql_query($query) or die (mysql_error());
    while ($row=mysql_fetch_array($result))
        {
        $numero=$row[0];
        }
    ?>
    <br /><br />
    <?
    aff_journee($champ, $numero, CONSULT_CLMNT_MSG6, 0, $fiches_clubs, $id_equipe_fetiche);

    if ($numero<$nb_journees)
     {
      aff_journee($champ, $numero+1, CONSULT_CLMNT_MSG62, 0, $fiches_clubs, $id_equipe_fetiche);
     }

    if ($estimation == "1" and $numero>=4)
    {
     echo "<br /><div align=\"center\"><h5><font color=\"red\">".CONSULT_CLMNT_MSG7."</font></h5></div>";
     aff_journee($champ, $numero+1, "<i>".CONSULT_CLMNT_MSG8."</i>", 1, $fiches_clubs, $id_equipe_fetiche);
    }

 }

else
 {
  $requete="SELECT DISTINCT * FROM phpl_clmnt WHERE ID_CHAMP='$champ' ORDER BY POINTS DESC, DIFF DESC, BUTSPOUR DESC , BUTSCONTRE ASC, NOM";

  @db_clmnt($champ, $debut, $fin, 0);

  //clmnt($legende, $type, $accession, $barrage, $relegation, $equipe_fetiche, $champ, $debut, $fin, $pts_victoire, $pts_nul, $pts_defaite, $requete);
  clmnt($legende, $type, $accession, $barrage, $relegation,  $champ, $requete, $lien, $id_equipe_fetiche);
 }


}

break;



case DOMICILE;
 {
  $legende=CONSULT_CLMNT_MSG10.$debut.CONSULT_CLMNT_MSG5.$fin;
  
  if ($debut=="1" and $fin==$nb_journees)
 {
  $requete="SELECT NOM, DOMPOINTS, DOMJOUES, DOMG,  DOMN, DOMP, DOMBUTSPOUR, DOMBUTSCONTRE, DOMDIFF, ID_EQUIPE  FROM phpl_clmnt_cache WHERE ID_CHAMP='$champ' ORDER BY DOMPOINTS DESC, DOMDIFF DESC";
  clmnt($legende, $type, $accession, $barrage, $relegation, $champ, $requete, $lien, $id_equipe_fetiche);
 }

  else
 { 
  $requete="SELECT NOM, DOMPOINTS, DOMJOUES, DOMG,  DOMN, DOMP, DOMBUTSPOUR, DOMBUTSCONTRE, DOMDIFF, ID_EQUIPE  FROM phpl_clmnt WHERE ID_CHAMP='$champ' ORDER BY DOMPOINTS DESC, DOMDIFF DESC";
  @db_clmnt($champ, $debut, $fin, 0);
  clmnt($legende, $type, $accession, $barrage, $relegation,  $champ, $requete,$lien, $id_equipe_fetiche);  
 }
} 
break;


case ATTAQUE;
 {
   $legende=CONSULT_CLMNT_MSG11.$debut.CONSULT_CLMNT_MSG5.$fin;
   
   if ($debut=="1" and $fin==$nb_journees)
   {
    $requete="SELECT * FROM phpl_clmnt_cache WHERE ID_CHAMP='$champ' ORDER BY BUTSPOUR DESC, DIFF DESC";
    clmnt($legende, $type, $accession, $barrage, $relegation, $champ, $requete, $lien, $id_equipe_fetiche);
   }

   else
   {
   $requete="SELECT * FROM phpl_clmnt WHERE ID_CHAMP='$champ' ORDER BY BUTSPOUR DESC, DIFF DESC";
   @db_clmnt($champ, $debut, $fin, 0);
   clmnt($legende, $type, $accession, $barrage, $relegation,  $champ, $requete, $lien, $id_equipe_fetiche);
   }
 }
break;


case DEFENSE;
 {
  $legende=CONSULT_CLMNT_MSG12.$debut.CONSULT_CLMNT_MSG5.$fin;
  
  if ($debut=="1" and $fin==$nb_journees)
   {
    $requete="SELECT * FROM phpl_clmnt_cache WHERE ID_CHAMP='$champ' ORDER BY BUTSCONTRE ASC, DIFF DESC";
    clmnt($legende, $type, $accession, $barrage, $relegation, $champ, $requete, $lien, $id_equipe_fetiche);
   }

   else
   {
    $requete="SELECT * FROM phpl_clmnt WHERE ID_CHAMP='$champ' ORDER BY BUTSCONTRE ASC, DIFF DESC";
    @db_clmnt($champ, $debut, $fin, 0);
    clmnt($legende, $type, $accession, $barrage, $relegation, $champ, $requete, $lien, $id_equipe_fetiche);
   }
 }
        
break;


case GOALDIFF;
 {
  $legende=CONSULT_CLMNT_MSG13.$debut.CONSULT_CLMNT_MSG5.$fin;
  
  if ($debut=="1" and $fin==$nb_journees)
   {
    $requete="SELECT * FROM phpl_clmnt_cache WHERE id_champ='$champ' ORDER BY DIFF DESC, BUTSPOUR DESC, BUTSCONTRE ASC ";
    clmnt($legende, $type, $accession, $barrage, $relegation, $champ, $requete, $lien, $id_equipe_fetiche);
   }

  else
  {
   $requete="SELECT * FROM phpl_clmnt WHERE id_champ='$champ' ORDER BY DIFF DESC, BUTSPOUR DESC, BUTSCONTRE ASC ";
   @db_clmnt($champ, $debut, $fin, 0);
   clmnt($legende, $type, $accession, $barrage, $relegation,  $champ, $requete,$lien, $id_equipe_fetiche);
  }
 }
break;


case EXTERIEUR;
{
 $legende=CONSULT_CLMNT_MSG14.$debut.CONSULT_CLMNT_MSG5.$fin;
 
   if ($debut=="1" and $fin==$nb_journees)
   {
    $requete="SELECT NOM, EXTPOINTS, EXTJOUES, EXTG,  EXTN, EXTP, EXTBUTSPOUR, EXTBUTSCONTRE, EXTDIFF, ID_EQUIPE  FROM phpl_clmnt_cache WHERE ID_CHAMP='$champ' ORDER BY EXTPOINTS DESC, EXTDIFF DESC ";
    clmnt($legende, $type, $accession, $barrage, $relegation, $champ, $requete, $lien, $id_equipe_fetiche);
   }

  else
  {
 $requete="SELECT NOM, EXTPOINTS, EXTJOUES, EXTG,  EXTN, EXTP, EXTBUTSPOUR, EXTBUTSCONTRE, EXTDIFF, ID_EQUIPE  FROM phpl_clmnt WHERE ID_CHAMP='$champ' ORDER BY EXTPOINTS DESC, EXTDIFF DESC ";
 @db_clmnt($champ, $debut, $fin, 0);
 clmnt($legende, $type, $accession, $barrage, $relegation, $champ, $requete, $lien, $id_equipe_fetiche);
  }
}
break;
}

}
}
?>
<br />
<p align="right"><font face="Verdana" size="1">Powered by <a href="http://phpleague.univert.org" target="_blank">PhpLeague</a></font></p>

<?
include ("apres.php");
?>
