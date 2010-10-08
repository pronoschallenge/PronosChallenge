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


if (empty ($numero))
  {
    $requete="SELECT max(phpl_journees.numero) FROM phpl_journees, phpl_matchs WHERE phpl_journees.id=phpl_matchs.id_journee and buts_dom is not NULL and phpl_journees.id_champ='$champ'";
    $resultats=mysql_query($requete);
     while ($row=mysql_fetch_array($resultats))
       {
         $numero=$row[0];
       }
       if ($numero=="") {$numero="1";}
  }
if (empty ($numero)){$numero=1;}

if ($action3=="2")
{
 $a=0;
 $x=0;
 $b=0;
 while ($x<$nb_matchs)
       {
        $y=0;
        $r=0;
        while ($nbdom[$x]>$y)
              {
               if (!($nbdom[$x]=='') and !($nbdom[$x]=='0') and !($joueursDom[$a]=='0'))
                  {
                   $query5="INSERT INTO phpl_buteurs (id_match, buts, id_effectif) VALUES ('$matchs_id[$x]','1','$joueursDom[$a]') " ;
                   mysql_query($query5);
                  }
               $y++;
               $a++;
              }
        while ($nbext[$x]>$r)
              {
               if (!($nbext[$x]=='') and !($nbext[$x]=='0') and !($joueursExt[$b]=='0'))
                   {
                    $query5="INSERT INTO phpl_buteurs (id_match, buts, id_effectif) VALUES ('$matchs_id[$x]','1','$joueursExt[$b]') " ;
                    mysql_query($query5);
                   }
                $r++;
                $b++;
               }
         $x++;
        }
}

 if ($action4=='supp')
        {
          mysql_query ("DELETE FROM phpl_buteurs WHERE id='$id_buteur_supp' ") or die ("probleme " .mysql_error());
        }

?>

<table class=phpl width="80%">
  <tr>
    <td class=phpl2 align="center" colspan="3"><? echo ADMIN_BUTEUR_TITRE." "; affich_champ ($champ); ?></td>
  </tr>
  <td align="center"><? journees ($champ, $numero, $action);?>
  </td></tr>
  <tr><td>

<?
//buteurs



 echo "<br />".ADMIN_JOUEURS_1;
 echo "<form method=\"post\" action=\"\">";
 echo "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" valign=\"bottom\" align=\"center\" width=\"100%\"><tr><td>";

 echo"<tr class=phpl4 align =\"left\"><td align=\"right\" class=phpl2>".DOMICILE."<td class=phpl2><td class=phpl2>".BUTEUR."<td class=phpl2><td class=phpl2><td class=phpl2>".BUTEUR."<td class=phpl2><td class=phpl2>".EXTERIEUR."</tr>";
 $query="SELECT phpl_clubs.nom, CLEXT.nom, phpl_matchs.buts_dom, phpl_matchs.buts_ext, phpl_matchs.id, phpl_equipes.id, EXT.id, date_reelle
         FROM phpl_clubs, phpl_clubs as CLEXT, phpl_matchs, phpl_journees, phpl_equipes, phpl_equipes as EXT
         WHERE phpl_clubs.id = phpl_equipes.id_club
         AND CLEXT.id = EXT.id_club
         AND phpl_equipes.id = phpl_matchs.id_equipe_dom
         AND EXT.id = phpl_matchs.id_equipe_ext
         AND phpl_matchs.id_journee = phpl_journees.id
         AND phpl_journees.numero = '$numero'
         AND phpl_journees.id_champ = '$champ'
         AND CLEXT.nom!='exempte'
         AND phpl_clubs.nom!= 'exempte'
         ORDER by date_reelle";
 $result=mysql_query($query) or die (mysql_error());
 $e=0;
 while($row = mysql_fetch_array($result)) 
       {
	 $row[0] = stripslashes($row[0]);
         if (($e%2)==0){$class="phpl3";}
         else {$class="phpl4";}
         echo "<tr>";
         print "<td align=\"right\" class=$class>$row[0]<td class=$class>";
         echo "<input type=\"hidden\" name=\"nbdom[]\" value=\"$row[2]\">";
         echo "<input type=\"hidden\" name=\"matchs_id[]\" value=\"$row[4]\">";
         echo "<input type=\"hidden\" name=\"butd[]\" value=\"1\">";
         echo "<input type=\"hidden\" name=\"butv[]\" value=\"1\">";
         echo "<input type=\"hidden\" name=\"nbext[]\" value=\"$row[3]\">";
         $x=0;
          while ($x<$row[2])
               {  
                $queryJ="SELECT phpl_effectif.id, phpl_effectif.id_equipe, phpl_joueurs.nom, phpl_joueurs.prenom
                         FROM phpl_joueurs, phpl_clubs, phpl_equipes, phpl_effectif
                         WHERE phpl_effectif.id_equipe=phpl_equipes.id
                         AND phpl_equipes.id_club=phpl_clubs.id
                         AND phpl_equipes.id=$row[5]
                         AND phpl_joueurs.id=phpl_effectif.id_joueur
                         ORDER BY phpl_joueurs.nom, phpl_joueurs.prenom";
                $resultJ=mysql_query($queryJ);
                if (!$resultJ) die (mysql_error());
                echo "<select name=\"joueursDom[]\">";
		echo "<option value=\"0\"></option>";
                while ($rowJ=mysql_fetch_array($resultJ))
                       {
	 $joueurs_nom = stripslashes($rowJ[2]);
	 $joueurs_prenom = stripslashes($rowJ[3]);
		        echo "<option value=\"$rowJ[0]\">$joueurs_nom $joueurs_prenom";
                        echo ("</option>");
                       }
         echo "</select><br />";
         $x++;
        }
 echo"<td class=$class>";
 $query3="SELECT phpl_joueurs.nom, phpl_joueurs.prenom, phpl_buteurs.id
          FROM phpl_buteurs, phpl_joueurs, phpl_effectif
          WHERE phpl_effectif.id_joueur=phpl_joueurs.id
          AND phpl_effectif.id=phpl_buteurs.id_effectif
          AND phpl_effectif.id_equipe=$row[5]
          AND phpl_buteurs.id_match='$row[4]'";
 $result3=mysql_query($query3);
 if (!$result3) die (mysql_error());
 while ($row3=mysql_fetch_array($result3))
       {
         $joueurs_nom = stripslashes($row3[0]);
	 $joueurs_prenom = stripslashes($row3[1]);

        echo "<a href=?action4=supp&page=championnat&action=buteurs&numero=$numero&champ=$champ&id_buteur_supp=$row3[2]>$joueurs_nom $joueurs_prenom</a><br />";
       }
 echo "<td width=\"20\" align=\"center\" class=$class>";
 echo " <b>$row[2] </b>";
 echo "<td width=\"20\" align=\"center\" class=$class>";
 echo "<b> $row[3]</b> ";
 echo "<td class=$class class=$class>";
 $y=0;
  while ($y<$row[3])
        {
         $queryJ="SELECT phpl_effectif.id, phpl_effectif.id_equipe, phpl_joueurs.nom, phpl_joueurs.prenom
                  FROM phpl_joueurs, phpl_clubs, phpl_equipes, phpl_effectif
                  WHERE phpl_effectif.id_equipe=phpl_equipes.id
                  AND phpl_equipes.id_club=phpl_clubs.id
                  AND phpl_effectif.id_joueur=phpl_joueurs.id
                  AND phpl_effectif.id_equipe=$row[6] ORDER BY phpl_joueurs.nom, phpl_joueurs.prenom";
         $resultJ=mysql_query($queryJ) or die ("probleme " .mysql_error());
         //if (!$resultJ) die (mysql_error());
	 echo "<select name=\"joueursExt[]\">";
	 echo "<option value=\"0\"></option>";
         while ($rowJ=mysql_fetch_array($resultJ))
              	{
                 $joueurs_nom = stripslashes($rowJ[2]);
	         $joueurs_prenom = stripslashes($rowJ[3]);

		 echo "<option value=\"$rowJ[0]\">$joueurs_nom $joueurs_prenom";
                 echo ("</option>");
                }
         echo "</select><br />";
         $y++;
        }
 echo"<td class=$class>";
 $query3="SELECT phpl_joueurs.nom, phpl_joueurs.prenom, phpl_buteurs.id
          FROM phpl_buteurs, phpl_joueurs, phpl_effectif
          WHERE phpl_effectif.id_joueur=phpl_joueurs.id
          AND phpl_effectif.id=phpl_buteurs.id_effectif
          AND phpl_effectif.id_equipe=$row[6]
          AND phpl_buteurs.id_match=$row[4]";
  $result3=mysql_query($query3);
  if (!$result3) die (mysql_error());
  while ($row3=mysql_fetch_array($result3))
     	{
         $joueurs_nom = stripslashes($row3[0]);
	 $joueurs_prenom = stripslashes($row3[1]);
         echo "<a href=?action4=supp&page=championnat&action=buteurs&numero=$numero&champ=$champ&id_buteur_supp=$row3[2]>$joueurs_nom $joueurs_prenom</a><br />";
        }
 $e++;
 echo  "<td class=$class>$row[1]";
}

echo "<input type=\"hidden\" name=\"champ\" value=\"$champ\">";
echo "<input type=\"hidden\" name=\"action3\" value=\"2\">";
echo "<input type=\"hidden\" name=\"page\" value=\"championnat\">
<input type=\"hidden\" name=\"action\" value=\"buteurs\">

<input type=\"hidden\" name=\"champ\" value=\"$champ\">";
$query2="select phpl_matchs.id from phpl_matchs where phpl_matchs.id_journee=$numero";
$result2=mysql_query($query2);
$nb_matchs=nb_matchs($numero, $champ);

$numero=$numero+1;
echo "</td></tr><tr><td colspan=\"8\"><input type=\"hidden\" name=\"numero\" value=\"$numero\">";
echo "<input type=\"hidden\" name=\"journee_suivante\" value=1><input type=\"hidden\" name=\"nb_matchs\" value=\"$nb_matchs\">";
$button=ENVOI;
echo "<br /><center><input type=\"submit\" value=$button></center>";
echo "</td></tr></table>";
echo "</form>";
?>
</td></tr></table><br /><br />
