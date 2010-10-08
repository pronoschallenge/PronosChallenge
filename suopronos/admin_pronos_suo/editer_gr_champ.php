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


if ($action3=="2")
 {
 $requete="SELECT id FROM phpl_gr_championnats WHERE id='$gr_champ'";
 $resultats = mysql_query ($requete);
 // if (!$resultats) die (mysql_error());
 $row=mysql_fetch_array($resultats);
 $nb_resultats=mysql_num_rows($resultats);

   if ($nb_resultats>0)
     {
       //Màj des paramètres
       mysql_query ("UPDATE phpl_gr_championnats SET pts_prono_exact='$pts_prono_exact', pts_prono_participation='$pts_prono_participation', id_master='$id_master', tps_avant_prono='$tps_avant_prono', activ_prono='$activ_prono' WHERE id='$gr_champ'") or die (mysql_error());
     }

   else
     {
       // Insertion des paramètres dans la bdd
       $requete="INSERT INTO phpl_gr_championnats (id_champ, pts_prono_exact, pts_prono_participation, id_master, tps_avant_prono) VALUES ('$champ', '$pts_prono_exact', '$pts_prono_participation', '$id_master', '$tps_avant_prono')";
       $resultats = mysql_query ($requete) or die (mysql_error());
       if (!$resultats) die (mysql_error());
     }
 }

if ($action3=="creer" and $champ)
{
  $requete = "SELECT * FROM phpl_gr_championnats WHERE id='$gr_champ'";
  $result = mysql_query($requete);
  $row = mysql_fetch_array($result);
  
  $nom = isset($row['nom']) ? $row['nom'] : NULL;
  $activ_prono = isset($row['activ_prono']) ? $row['activ_prono'] : NULL;
  $pts_prono_exact = isset($row['pts_prono_exact']) ? $row['pts_prono_exact'] : NULL;
  $pts_prono_participation = isset($row['pts_prono_participation']) ? $row['pts_prono_participation'] : NULL;
  $id_master = isset($row['id_master']) ? $row['id_master'] : NULL;
  $tps_avant_prono = isset($row['tps_avant_prono']) ? $row['tps_avant_prono'] : NULL;

  if (isset($row["nom"])){$nom_gr_champ = $row["nom"];} else {$nom_gr_champ=0;}
  if (isset($row["activ_prono"])){$activ_prono = $row["activ_prono"];} else {$activ_prono=0;}
  if (isset($row["pts_prono_exact"])){$pts_prono_exact = $row["pts_prono_exact"];} else {$pts_prono_exact=0;}
  if (isset($row["pts_prono_participation"])){$pts_prono_participation = $row["pts_prono_participation"];} else {$pts_prono_participation=0;}
  if (isset($row["id_master"])){$id_master = $row["id_master"];} else {$id_master=0;}
  if (isset($row["tps_avant_prono"])){$tps_avant_prono = $row["tps_avant_prono"];} else {$tps_avant_prono=0;}
  

  
        mysql_query ("DELETE FROM phpl_gr_championnats WHERE id = '$gr_champ'") or die ("probleme " .mysql_error());

         reset ($champ);
	 while ( list($cle, $val)= each($champ))
         {
mysql_query ("INSERT INTO phpl_gr_championnats (id, nom, id_champ, activ_prono, pts_prono_exact, pts_prono_participation, id_master,tps_avant_prono)
           	                                  VALUES ('$gr_champ', '$nom_gr_champ', $val, $activ_prono, $pts_prono_exact, $pts_prono_participation, $id_master, $tps_avant_prono)") or die ("probleme " .mysql_error());

         }
         
}
?>


<table class=phpl width="80%">
            <tr>
              <td class=phpl2 align="center" colspan="4"><? echo ADMIN_GR_CHAMP_EDIT." " ; affich_gr_champ ($gr_champ); ?></td><td class=phpl2 align="right"><a href="#" onclick="window.open('Assistant_fr/equipes_2.htm','Assistant','toolbar=0,location=0,directories=0,status=0,scrollbars=1,resizable=0,copyhistory=0,menuBar=0,width=512,height=512');return false;"><img border="0" alt="Assistant" src="aide.gif"></a></td>
            </tr>

            <tr>
              <td align="center" class=phpl6 colspan="4"><b><? echo ADMIN_GR_CHAMP_EDIT_2; ?></b></td>
            </tr>
            
            <tr>
              <td class=phpl3 colspan="3">
                <form method="post" action=""><? echo ADMIN_GR_CHAMP_EDIT_1; ?><b><? affich_gr_champ ($gr_champ); ?></b> : <? champ_menu (); ?><br /><? echo ADMIN_EQUIPE_3; ?>
              </td>
              
              <td class=phpl3 align=right colspan="2">
                <? $value=ADMIN_GR_CHAMP_EDIT_4; echo "<input type=\"submit\" value=\"$value\">";?>
                <input type="hidden" name="action3" value="creer">
                <input type="hidden" name="action" value="editer">
                <input type="hidden" name="page" value="groupes_championnats">
                <? echo "<input type=\"hidden\" name=\"gr_champ\" value=\"$gr_champ\">";?>
              </td>
              
              </form>
            </tr>

            <tr>
              <td align="center" class=phpl6 colspan="4"><b><? echo ADMIN_GR_CHAMP_EDIT_5; ?></b></td>
            </tr>
              <td class=phpl3 colspan="5" align="center"><?  champ_gr_menu ($gr_champ); ?></td>


              </td>
             </tr>

</table><br />

<table class=phpl width="80%">
            <tr>
              <td class=phpl2 align="center" colspan="3"><? echo ADMIN_PARAM_MSG13." "; affich_gr_champ ($gr_champ); ?></td>
            </tr>
            <tr>

<?

$requete="SELECT * FROM phpl_gr_championnats WHERE id='$gr_champ'";
$resultats = mysql_query ($requete) or die ("probleme " .mysql_error());
$existant=mysql_fetch_array($resultats);


echo "<form method=\"post\"  action=\"\">";

// Activer les pronostics ?
echo "<tr><td class=phpl4>";
echo "Activer les pronostics ?";
echo "<td class=phpl4>";

  if ($existant['activ_prono']=="1"){$checked1="checked"; $checked2="";}
  if ($existant['activ_prono']=="0"){$checked1=""; $checked2="checked";}
    else {$checked1="checked"; $checked2="";}

echo "<input type=\"radio\" value=\"1\" $checked1 name=\"activ_prono\">".ADMIN_RENS_17." <input type=\"radio\" value=\"0\" $checked2 name=\"activ_prono\">".ADMIN_RENS_18." </td></tr>";


// Points pour prono exact
echo "<td class=phpl3>";
echo ADMIN_PARAM_MSG9;
echo "</td><td class=phpl3>";
echo "<input type=\"text\" name=\"pts_prono_exact\" value=\"$existant[pts_prono_exact]\" size=3 maxlength=3></td></tr>";

// Points pour prono participation
echo "<tr><td class=phpl4>";
echo ADMIN_PARAM_MSG10;
echo "</td><td class=phpl4>";
echo "<input type=\"text\" name=\"pts_prono_participation\"  value=\"$existant[pts_prono_participation]\" size=3 maxlength=3></td></tr>";

// Master
echo "<tr><td class=phpl3>";
echo ADMIN_PARAM_MSG11;
echo "</td><td class=phpl3>";

$query2="SELECT pseudo, id FROM phpl_membres order by pseudo";
$result2 = mysql_query ($query2);
echo "<select name=\"id_master\">";
echo "<option></option>";
  while ($row2=mysql_fetch_array($result2))
   {
     if ($row2[id]==$existant[id_master]){echo "<option value=\"$row2[id]\" selected>$row2[pseudo]</option>";}
     else echo "<option value=\"$row2[id]\">$row2[pseudo]</option>";
   }

echo "</select></td></tr>";

// Temps de validation avant match
echo "<tr><td class=phpl4>";
echo ADMIN_PARAM_MSG12;
echo "</td><td class=phpl4>";
echo "<input type=\"text\" name=\"tps_avant_prono\"  value=\"$existant[tps_avant_prono]\" size=3 maxlength=3></td></tr>";

echo "<input type=\"hidden\" name=\"action3\" value=\"2\"><input type=\"hidden\" name=\"gr_champ\" value=\"$champ\"><td colspan=2 align=\"center\"><input type=\"submit\" value=".ENVOI.">";
echo "</form>";
echo "</tr></table><br /><br />";

?>

