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





function choix_equipes($champ)
{
         echo "<div align=\"center\"><h1>".CONSULT_DUEL."</h1>";
         echo "<h5>".DUEL_MSG1."</h5></div>";
         echo "<form method=\"get\" action=\"\">";
         echo "<table class=\"tablephpl2\" align=\"center\" cellspacing=\"0\"><tr class=\"trphpl3\"><th>".DOMICILE."</th><th align=\"right\">".EXTERIEUR."</th></tr><tr><td align=\"left\">";
         $result =mysql_query("SELECT phpl_clubs.nom FROM phpl_clubs, phpl_equipes WHERE phpl_equipes.id_champ='$champ' AND phpl_clubs.id=phpl_equipes.id_club AND phpl_clubs.nom!='exempte' ORDER BY nom");
         while($row=mysql_fetch_array($result))
            {
            $row[0] = stripslashes($row[0]);
            echo "<input type=\"radio\" value=\"$row[0]\" name=\"IdEqDom\">$row[0]<br />";
            }
         echo "</td><td align=\"right\">";
         $result =mysql_query("select phpl_clubs.nom from phpl_clubs, phpl_equipes WHERE phpl_equipes.id_champ='$champ' AND phpl_clubs.id=phpl_equipes.id_club AND phpl_clubs.nom!='exempte' ORDER BY nom");
         while($row=mysql_fetch_array($result))
            {
            $row[0] = stripslashes($row[0]);
            echo "$row[0]<input type=\"radio\" value=\"$row[0]\" name=\"IdEqExt\"><br />";
            }
         echo "</td></tr><tr><td colspan=\"2\" align=\"center\"><input type=\"hidden\" name=\"champ\" value=\"$champ\"><input type=\"submit\" value=\"".ENVOI."\"></td></tr></table></form>";
}


// CORPS DU SCRIPT

if (!isset($_REQUEST['IdEqDom']) or !isset($_REQUEST['IdEqExt']))
{
if (!isset($_REQUEST['champ']))
 {
 demande_champ ();
 }
      else
         {
          $champ = $_REQUEST['champ'];
          choix_equipes($champ);
         }
      }
elseif ($_REQUEST['IdEqDom']==$_REQUEST['IdEqExt'])
   {
     echo "<div align=\"center\"<h5>choix impossible</h5></div><br />";
    echo "<br /><br /><a href=\"";
print $_SERVER['HTTP_REFERER'];
echo "\"><b>Autre duel ...</b></a>";
   }
else
     {
if (isset($_REQUEST['entete'])) {$entete = $_REQUEST['entete'];} else {$entete = 0;}
$IdEqDom = addslashes($_REQUEST['IdEqDom']);
$IdEqExt = addslashes($_REQUEST['IdEqExt']);
$champ = $_REQUEST['champ'];
//mysql_query("LOCK TABLE clmnt");
//@db_clmnt($champ, 0, 0, 0) ;
//db_clmnt($legende, $type, $accession, $barrage, $relegation,  $champ, $debut, $fin, $pts_victoire, $pts_nul, $pts_defaite) ;
$query="SELECT DOMG, DOMN, EXTG, DOMJOUES, EXTN,EXTJOUES FROM phpl_clmnt_cache WHERE NOM='$IdEqDom' AND ID_CHAMP='$champ'";


$result=mysql_query($query) or die (mysql_error());
while ($row=mysql_fetch_array($result))
    {
    $dom_points=(($row['DOMG']*2)+($row['DOMN']))/$row['DOMJOUES']*3;
    $dom_points+= ((($row['EXTG']*2)+($row['EXTN']))/$row['EXTJOUES']);

    }
$query="SELECT EXTG, EXTN, EXTJOUES, DOMG, DOMN, DOMJOUES FROM phpl_clmnt_cache WHERE NOM='$IdEqExt' AND ID_CHAMP='$champ'";
$result=mysql_query($query);
while ($row=mysql_fetch_array($result))
    {
    $ext_points=(($row['EXTG']*2)+($row['EXTN']))/$row['EXTJOUES']*3;
    $ext_points+= ((($row['DOMG']*2)+($row['DOMN']))/$row['DOMJOUES']);
    }
$x=$dom_points+$ext_points;
$domproba= intval((($dom_points/$x)+0.005)*100);
$extproba= intval((($ext_points/$x)+0.005)*100) ;
if (!$entete == 'non') {echo "<table class=\"tablephpl2\" align=\"center\" cellspacing=\"0\"><tr class=\"trphpl3\"> <td colspan=\"6\" align=\"center\"><b>".DUEL_MSG3."</b></td></tr><tr><td>".DUEL_MSG4."</td><td><b>".stripslashes($IdEqDom)."</b></td><td align=\"right\"><b>$domproba % </b></td><td>-</td><td align=\"left\"><b> $extproba % </b></td><td><b>".stripslashes($IdEqExt)."</b></td></tr>";}

// ***************

   $query2="SELECT DOMBUTSPOUR, DOMG, DOMN, DOMP, DOMBUTSCONTRE, DOMG, DOMN, DOMP FROM phpl_clmnt WHERE NOM='$IdEqDom' AND ID_CHAMP='$champ'";
  $result2=mysql_query($query2);
     while ($row2=mysql_fetch_array($result2))
               {
			   $dom_buts=($row2['DOMBUTSPOUR']);
			   $dom_joues=($row2['DOMG']+$row2['DOMN']+$row2['DOMP']);
			   $ext_buts=($row2['DOMBUTSCONTRE']);
			   $ext_joues=($row2['DOMG']+$row2['DOMN']+$row2['DOMP']);
			   }

 $query2="SELECT EXTG, EXTN, EXTP, EXTBUTSCONTRE, EXTBUTSPOUR  FROM phpl_clmnt WHERE NOM='$IdEqExt' AND ID_CHAMP='$champ'";
$result2=mysql_query($query2);
while ($row2=mysql_fetch_array($result2))
 {
  $dom_joues += ($row2['EXTG']+$row2['EXTN']+$row2['EXTP']);
  $ext_joues += $row2['EXTG']+$row2['EXTN']+$row2['EXTP'];
  $dom_buts += ($row2['EXTBUTSCONTRE']);
  $ext_buts+=($row2['EXTBUTSPOUR']);
  $dom_buts=intval((($dom_buts)/$dom_joues));
  $ext_buts=intval((($ext_buts)/$ext_joues));
 }
$domproba="<i>".$dom_buts."</i>";
$extproba="<i>".$ext_buts."</i>";
echo "<tr><td>SCORE : </td><td><b>".stripslashes($IdEqDom)."</b></td><td align=\"right\"><b>$domproba </b></td><td>-</td><td align=\"left\"><b> $extproba</b></td><td><b>".stripslashes($IdEqExt)."</b></td></tr></table>";

if (!$entete == "non") echo "<br /><br /><i>".DUEL_MSG5."</i><br />";

if ($entete=="non") Echo "$IdEqDom $domproba %-$extproba % $IdEqExt" ;
echo "<br /><br /><a href=\"";
print $_SERVER['HTTP_REFERER'];
echo "\"><b>Autre duel ...</b></a>";

//mysql_query("UNLOCK TABLE clmnt");
}
?>
<br />
<p align="right"><font face="Verdana" size="1">Powered by <a href="http://phpleague.univert.org" target="_blank">PhpLeague</a></font></p>
<?
 include ("apres.php");
?>
