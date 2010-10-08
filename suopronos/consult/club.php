<?php
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

//Choix du championnat
if (!isset($_REQUEST['champ']))
{
        demande_champ ();
}

// Choix du club
elseif (!isset($_REQUEST['id_clubs']))
{
$champ = $_REQUEST['champ'];

$query="SELECT phpl_clubs.id, phpl_clubs.nom, id_champ, id_club
FROM phpl_clubs, phpl_equipes
WHERE phpl_equipes.id_champ='$champ' and phpl_equipes.id_club=phpl_clubs.id
ORDER BY nom";
$result=mysql_query($query);

echo "<div align=\"center\"><font color=\"#000000\" size=\"2\"><u>".DETAILEQ_TITRE."</u></font>";
echo "<form action=\"\" method=\"get\">";

echo DETAILEQ_1;
echo "<select name=\"id_clubs\">";
echo "<option value=\"0\"> </option>";

      while($row = mysql_fetch_array($result))
      {
      $row[1] = stripslashes($row[1]);
      $a=$row[1]+1;
      echo (" <option value=\"$row[0]\">$row[1]");
      echo ("</option>\n");
      }
echo "</select>";
$button=ENVOI;
echo "<input type=\"submit\" value=\"$button\">";
echo "<input type=\"hidden\" name=\"champ\" value=\"$champ\">";
echo "</form></div>";
}

// Le choix du club étant fait on affiche la fiche du club
else
{
$id_clubs = $_REQUEST['id_clubs'];
$champ = $_REQUEST['champ'];

$query="SELECT id, url_logo FROM phpl_clubs WHERE id='$id_clubs'";
$result = mysql_query($query);

$row = mysql_fetch_array($result);
        
echo "<div align=\"center\"><img src=\"$row[1]\" alt=\"\"></div><br /><br /><br /><br />";
        
$query="SELECT phpl_classe.nom, phpl_classe.id FROM phpl_classe order by rang";
$result=mysql_query ($query);

        while($row = mysql_fetch_array($result))
        {
        echo "<table class=\"tablephpl2\" cellspacing=\"0\" align=\"center\" width=\"90%\">";
        echo "<tr class=\"trphpl3\"><td align=\"center\"><b>$row[0]</b></td></tr>";
        $id_classe=$row[1];
        echo "<tr><td><table cellspacing=\"0\"><tr class=\"trphpl\"><td>";
        $aff_rens=aff_rens ($id_classe, $id_clubs);
        echo "$aff_rens";
        echo "</td></tr></table></td></tr></table>\n";
        echo "<br /><br /><br /><br />\n";
        }

echo "<table class=\"tablephpl2\" cellspacing=\"0\" align=\"center\" width=\"90%\"><tr class=\"trphpl3\"><td align=\"center\"><b>Effectif</b></td></tr><tr><td>";
$query="SELECT phpl_equipes.id FROM phpl_equipes, phpl_clubs 
        WHERE phpl_clubs.id='$id_clubs' AND id_champ='$champ' AND phpl_clubs.id=phpl_equipes.id_club";
$result = mysql_query($query);
        
$row = mysql_fetch_array($result);
        
        $equipe=$row[0];
        
$requete="SELECT position_terrain
FROM phpl_joueurs
GROUP BY position_terrain desc";

$resultats=mysql_query($requete) or die (mysql_error());

while ($row=mysql_fetch_array($resultats))
{ $row[0]=addslashes($row[0]);
 $requete1="SELECT nom, prenom, phpl_joueurs.id
            FROM phpl_joueurs, phpl_effectif
            WHERE phpl_joueurs.id=phpl_effectif.id_joueur
            AND phpl_effectif.id_equipe='$equipe'
            AND position_terrain='$row[0]'";
 $resultats1=mysql_query($requete1) or die (mysql_error());
 if (!$row[0]=="") {$row[0]=stripslashes($row[0]);echo "$row[0] : ";}
 while ($row1=mysql_fetch_array($resultats1))
  {
   echo "<a href=\"#\" onclick=\"window.open('joueurs.php?id_joueur=$row1[2]','Fichejoueur','toolbar=0,location=0,directories=0,status=0,scrollbars=1,resizable=0,copyhistory=0,menuBar=0,width=560,height=320');return false;\">$row1[1] $row1[0]</a> ";
  }
  echo "<br>";
}


echo"</td></tr></table><br /><br /><br />";


echo "<table class=\"tablephpl2\" cellspacing=\"0\" align=\"center\" width=\"90%\"><tr class=\"trphpl3\"><td align=\"center\"><b>".CONSULT_CLUB_4."</b></td></tr>";
        echo "<tr><td><center><img src=\"graph.php?equipe=$equipe\" alt=\"\"></center></td></tr>";


echo"</table><br /><br /><br />";

echo "<table class=\"tablephpl2\" cellspacing=\"0\" align=\"center\" width=\"90%\"><tr class=\"trphpl3\" align=\"center\"><td><b>".CONSULT_CLUB_3."</b></td></tr>";
$query="SELECT annee, phpl_divisions.nom, phpl_championnats.id, phpl_equipes.id
FROM phpl_saisons, phpl_championnats, phpl_divisions, phpl_clubs, phpl_equipes
WHERE phpl_equipes.id_champ=phpl_championnats.id
AND id_division=phpl_divisions.id
AND phpl_clubs.id=id_club
AND phpl_equipes.id_club='$id_clubs'
AND phpl_saisons.id=phpl_championnats.id_saison order by annee desc";
$result = mysql_query($query);
        
        while($row = mysql_fetch_array($result))
        {
        echo "<tr><td></td></tr>";
        echo "<tr class=\"trphpl2\"><td align=\"center\">$row[0]/". ($row[0]+1)." ($row[1])</td></tr>";
        echo "<tr><td align=\"center\"><a href=\"classement.php?champ=$row[2]&amp;type=G%E9n%E9ral\">".CONSULT_CLUB_1."</a> - <a href=\"detaileq.php?champ=$row[2]&amp;id_equipe=$row[3]\">".CONSULT_CLUB_2."</a> - <a href=\"#\" onclick=\"window.open('graph.php?equipe=$row[3]','Stats','toolbar=0,location=0,directories=0,status=0,scrollbars=0,resizable=0,copyhistory=0,menuBar=0,width=560,height=320');return false;\">".CONSULT_CLUB_4."</a><br /><br /></td></tr>\n";

        }
echo"</table><br /><br />";

$query="SELECT phpl_clubs.id, phpl_clubs.nom, id_champ, id_club
FROM phpl_clubs, phpl_equipes
WHERE phpl_equipes.id_champ='$champ' and phpl_equipes.id_club=phpl_clubs.id
ORDER BY nom";
$result=mysql_query($query);

echo "<div align=\"center\"><form action=\"\" method=\"get\" onsubmit=\"\">";
echo "&nbsp;";
echo "&nbsp;";
//echo ADMIN_EQUIPE_2;

echo "<select name=\"id_clubs\">";
echo "<option value=\"0\"> </option>";

      while($row = mysql_fetch_array($result))
      {
      $row[1] = stripslashes($row[1]);
      $a=$row[1]+1;
      echo (" <option value=\"$row[0]\">$row[1]");
      echo ("</option>\n");
      }
echo "</select>";



$button=ENVOI;
echo "<input type=\"submit\" value=\"$button\">";
echo "<input type=\"hidden\" name=\"champ\" value=\"$champ\">";
echo "</form>";

echo "<a href=\"";
if (isset($_SERVER['HTTP_REFERER'])) {print $_SERVER['HTTP_REFERER'];}
else {echo "club.php";}
echo "\"><b>".RETOUR."</b></a></div>";

}
?>
<br />
<p align="right"><font face="Verdana" size="1">Powered by <a href="http://phpleague.univert.org" target="_blank">PhpLeague</a></font></p>

<?php
include ("apres.php");
?>

