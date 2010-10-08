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

if ($action2=="1")
 {
 $requete="SELECT id_champ FROM phpl_parametres WHERE id_champ='$champ'";
 $resultats = mysql_query ($requete);
 // if (!$resultats) die (mysql_error());
 $row=mysql_fetch_array($resultats);
 $nb_resultats=mysql_num_rows($resultats);

   if ($nb_resultats>0)
     {
       //Màj des paramètres
       mysql_query ("UPDATE phpl_parametres SET pts_victoire='$pts_victoire', pts_nul='$pts_nul', pts_defaite='$pts_defaite', accession='$accession', barrage='$barrage', relegation='$relegation', id_equipe_fetiche='$id_equipe_fetiche', fiches_clubs='$fiches_clubs', estimation='$estimation' WHERE id_champ='$champ'") or die (mysql_error());
     }

   else
     {
       // Insertion des paramètres dans la bdd
       $requete="INSERT INTO phpl_parametres (id_champ, pts_victoire, pts_nul, pts_defaite, accession, barrage, relegation,id_equipe_fetiche, fiches_clubs, estimation) VALUES ('$champ', '$pts_victoire', '$pts_nul', '$pts_defaite', '$accession', '$barrage', '$relegation', '$id_equipe_fetiche', '$fiches_clubs', '$estimation')";
       $resultats = mysql_query ($requete)or die (mysql_error());
       if (!$resultats) die (mysql_error());
     }  
 }



if ($malus)
 {
  $y=nb_equipes($champ);
  $x=0;

  while($x<$y)
   {
    mysql_query("UPDATE phpl_equipes SET penalite = '$malus[$x]'  WHERE id = '$id_equipe[$x]'") or die(mysql_error());
    $x++;
   }
 }
?>

<table class=phpl width="80%">
            <tr>
              <td class=phpl2 align="center" colspan="3"><? echo ADMIN_PARAM_TITRE." "; affich_champ ($champ); ?></td>
            </tr>
            <tr>
              <td>
<table align=center cellspacing="0" width="100%">

<?
//pamametres ($champ);
$requete="SELECT * FROM phpl_parametres WHERE id_champ='$champ'";
$resultats = mysql_query ($requete);
$existant=mysql_fetch_array($resultats);

echo "<tr>";
echo "<td class=phpl3><form method=\"post\" action=\"\">";

//points pour la victoire
echo ADMIN_PARAM_MSG2;
echo "<td class=phpl3>";
echo "<input type=\"text\" name=\"pts_victoire\" value=\"$existant[pts_victoire]\" size=3 maxlength=3>";

// points pour un nul
echo "<tr><td class=phpl4>";
echo ADMIN_PARAM_MSG3;
echo "<td class=phpl4>";
echo "<input type=\"text\" name=\"pts_nul\"  value=\"$existant[pts_nul]\" size=3 maxlength=3>";

// points pour une défaite
echo "<tr><td class=phpl3>";
echo ADMIN_PARAM_MSG4;
echo " <td class=phpl3>";
echo "<input type=\"text\" name=\"pts_defaite\"  value=\"$existant[pts_defaite]\" size=3 maxlength=3>";

// Nombre d'équipe pour l'accession directe
echo "<tr><td  class=phpl4>";
echo ADMIN_PARAM_MSG5;
echo " <td class=phpl4>";
echo "<input type=\"text\" name=\"accession\"  value=\"$existant[accession]\" size=3 maxlength=3>";

// Nombre d'équipe pour des l'accession en barrages
echo "<tr><td class=phpl3>";
echo ADMIN_PARAM_MSG6;
echo " <td class=phpl3>";
echo "<input type=\"text\" name=\"barrage\"  value=\"$existant[barrage]\" size=3 maxlength=3>";

// Nombre d'équipe pour la descente
echo "<tr><td class=phpl4>";
echo ADMIN_PARAM_MSG7;
echo " <td class=phpl4>";
echo "<input type=\"text\" name=\"relegation\"  value=\"$existant[relegation]\" size=3 maxlength=3>";

// Equipe à suivre plus particulièrement
$requete="SELECT phpl_clubs.nom, phpl_equipes.id FROM phpl_equipes, phpl_clubs WHERE phpl_equipes.id_champ='$champ' AND phpl_clubs.id=phpl_equipes.id_club ORDER by nom";
$resultats = mysql_query ($requete);
echo "<tr><td class=phpl3>";
echo ADMIN_PARAM_MSG8;
echo " <td class=phpl3>";
echo "<select name=\"id_equipe_fetiche\">";
echo "<option value=\"0\"></option>";

  while ($row=mysql_fetch_array($resultats))
   {
    $row[0] = stripslashes($row[0]);
    if (isset($existant["id_equipe_fetiche"])){
      if ($row[1]==$existant[id_equipe_fetiche]) echo "<option value=\"$row[1]\" selected >$row[0]</option>";
      }
    else echo "<option value=\"$row[1]\">$row[0]</option>";
   }
   
echo "</select>";

// Activer fiches clubs ?
echo "<tr><td class=phpl4>";
echo ADMIN_TAPVERT_MSG4;
echo " <td class=phpl4>";

  if ($existant['fiches_clubs']=="1"){$checked1="checked"; $checked2="";}
  if ($existant['fiches_clubs']=="0"){$checked1=""; $checked2="checked";}
  else {$checked1="checked"; $checked2="";}

echo "<input type=\"radio\" value=\"1\" $checked1 name=\"fiches_clubs\">".ADMIN_RENS_17." <input type=\"radio\" value=\"0\" $checked2 name=\"fiches_clubs\">".ADMIN_RENS_18." ";

// Activer estimations dans la page classement ?
echo "<tr><td class=phpl3>";
echo ADMIN_TAPVERT_MSG6;
echo " <td class=phpl3>";

  if ($existant['estimation']=="1"){$checked1="checked"; $checked2="";}
  if ($existant['estimation']=="0"){$checked1=""; $checked2="checked";}

echo "<input type=\"radio\" value=\"1\" $checked1 name=\"estimation\">".ADMIN_RENS_17." <input type=\"radio\" value=\"0\" $checked2 name=\"estimation\">".ADMIN_RENS_18." ";


echo "</tr><tr><input type=\"hidden\" name=\"action2\" value=\"1\"><input type=\"hidden\" name=\"champ\" value=\"$champ\"><td colspan=2 align=\"center\"><input type=\"submit\" value=".ENVOI.">";
echo "</form>";
echo "</table></td></tr>";
echo "</table>"; 

?>

<br /><br />



              


<table class=phpl width="80%">
            <tr>
              <td class=phpl2 align="center" colspan="3"><? echo ADMIN_TAPVERT_TITRE." "; affich_champ ($champ); ?></td>
            </tr>
            <tr>
              <td><? echo ADMIN_TAPVERT_MSG1; echo "<br />";echo ADMIN_TAPVERT_MSG3;
              
 $result=mysql_query("SELECT id FROM phpl_equipes WHERE phpl_equipes.id_champ='$champ'");



//BONUS MALUS

echo "<form method=\"post\" action=\"\">";
$query = "SELECT DISTINCT phpl_clubs.nom, phpl_equipes.id, penalite FROM phpl_clubs, phpl_equipes WHERE phpl_equipes.id_champ='$champ' AND phpl_clubs.id=phpl_equipes.id_club ORDER BY phpl_clubs.nom";
$result=mysql_query($query) or die(mysql_error());
while($row=mysql_fetch_array($result))
   {
   $row[0] = stripslashes($row[0]);
   echo "<INPUT TYPE=\"TEXT\" name=\"malus[]\" value=\"$row[2]\" size=\"4\">";
   echo "<INPUT TYPE=\"HIDDEN\"  name=\"id_equipe[]\" value=\"$row[1]\">";
   echo "$row[0]<br />";
   }
echo "<INPUT TYPE=\"HIDDEN\"  name=\"champ\" value=\"$champ\">";
echo "<INPUT TYPE=\"HIDDEN\"  name=\"page\" value=\"championnat\">";
echo "<INPUT TYPE=\"HIDDEN\"  name=\"action\" value=\"parametres\">";
$button=ENVOI;
echo "<input type=\"submit\" value=$button></form><br />";

              ?>
              
              </td>
            </tr>
</table><br /><br />
