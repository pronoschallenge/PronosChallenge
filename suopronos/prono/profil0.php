<?php
//***********************************************************************/
// Phpleague : gestionnaire de championnat                              */
// ============================================                         */
//                                                                      */
// Version : 0.82b                                                      */
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

if ($action == "supp" and !$confirm=="oui")
 {
   echo "<table><tr><td align=\"center\">".PRONO_PROFIL_SUR." $user_pseudo ?<br />";
   echo "<a href=\"index.php?page=profil&amp;action=supp&amp;confirm=oui\">".ADMIN_RENS_17."</a> - <a href=\"index.php\">".ADMIN_RENS_18."</a></td></tr></table>";
 }

elseif ($action == "supp" and $confirm=="oui")
 {
   $requete="SELECT id FROM phpl_membres WHERE pseudo='$user_pseudo'";
   $result=mysql_query($requete) or die ("probleme " .mysql_error());
   $row=mysql_fetch_array($result);
   
   mysql_query("DELETE FROM phpl_pronostics WHERE id_membre='$row[0]'")or die ("probleme " .mysql_error());
   mysql_query("DELETE FROM phpl_membres WHERE id='$row[0]'")or die ("probleme " .mysql_error());
   mysql_query("DELETE FROM phpl_clmnt_pronos WHERE id_membre='$row[0]'")or die ("probleme " .mysql_error());
  ?>
   <META HTTP-EQUIV="refresh"; CONTENT="0; URL=logout.php">
   <?php
   echo PRONO_PROFIL_SUPP;
 }

else
        {

?>
<table width="90%" align = "center">
<tr>
<td colspan="2">
<table cellSpacing="0" cellPadding="0" width="100%" align="center" border="1"  class="prono">
<tr><td>

<?php
        
 	echo "<table align=\"center\" width=\"100%\">";
 	echo"<tr><td colspan=\"2\" align=\"center\"><div class=\"blanc\"><strong>".PRONO_PROFIL_TITRE." $user_pseudo</strong></div></td></tr>";
 //	ouverture ();

  

  if ($action == "1")
 	 {
          $query = "SELECT mot_de_passe FROM phpl_membres WHERE id_prono='$user_id' and pseudo='$user_pseudo'";
          $result=mysql_query($query) or die (mysql_error());
          while ($row=mysql_fetch_array($result)){$mot_de_passe_correct=$row["0"];}
          $ancien_mdp_crypt=md5($ancien_mdp);

          $date_naissance=$annee."-".$mois."-".$jour;
          if (!empty($mobile1) and !empty($mobile2) and !empty($mobile3) and !empty($mobile4) and !empty($mobile5))
          {$mobile=$mobile1."-".$mobile2."-".$mobile3."-".$mobile4."-".$mobile5;}

          if (empty ($ancien_mdp) and empty ($nouveau_mdp) and empty ($nouveau_mdp2))
           {
             mysql_query ("UPDATE phpl_membres SET nom_site='$site', mail='$mail', nom='$nom', prenom='$prenom', adresse='$adresse', code_postal='$code_postal', ville='$ville', pays='$pays', date_naissance='$date_naissance', profession='$profession', mobile='$mobile' WHERE id_prono='$user_id' and pseudo='$user_pseudo'") or die ("probleme " .mysql_error());
             $message.="profil mis à jour";
           }
          elseif (empty ($ancien_mdp)) {$message.= PRONO_PROFIL_ANCIEN_MDP;}
          elseif (empty ($nouveau_mdp)) {$message.=PRONO_PROFIL_MDP_2_FOIS;}
          elseif (empty ($nouveau_mdp2)) {$message.=PRONO_PROFIL_MDP_2_FOIS_2;}
          elseif ($nouveau_mdp!=$nouveau_mdp2) {$message.=PRONO_PROFIL_MDP_DIFF;}
          elseif ($ancien_mdp_crypt!=$mot_de_passe_correct) {$message.=PRONO_PROFIL_MDP_ERREUR;}
          elseif (!empty ($ancien_mdp) and !empty ($nouveau_mdp) and !empty ($nouveau_mdp2))
           {
             
             print $row["0"];
             if ($ancien_mdp_crypt=$mot_de_passe_correct and $nouveau_mdp=$nouveau_mdp2)
             {
              $nouveau_mdp_crypt=md5($nouveau_mdp2);

              mysql_query ("update phpl_membres SET nom_site='$site', mail='$mail', mot_de_passe='$nouveau_mdp_crypt', nom='$nom', prenom='$prenom', adresse='$adresse', code_postal='$code_postal', ville='$ville', pays='$pays', date_naissance='$date_naissance', profession='$profession', mobile='$mobile'  WHERE id_prono='$user_id' and pseudo='$user_pseudo'") or die ("probleme " .mysql_error());
              $message.="profil mis à jour";
             }
             //elseif ($nouveau_mdp!=$nouveau_mdp2) {$message.="Nouveaux mots de passe différents";}
             
           }
         }
        echo "<tr><td><form action=\"\" method=\"post\" name=\"profil\" onsubmit=\"javascript:return VerifForm()\"><table><tr><td colspan=\"2\" align=\"center\"><div class=\"blanc\">$message</div></td></tr>";
 	$query = "SELECT pseudo, mot_de_passe, mail, nom_site, nom, prenom, adresse, code_postal, ville, pays, date_naissance, profession, mobile  FROM phpl_membres WHERE pseudo='$user_pseudo' AND id_prono = '$user_id' ";
 	$result=mysql_query($query) or die (mysql_error());
 	
        while ($row=mysql_fetch_array($result))
       { 
         echo "<tr><td align=\"right\" width=\"50%\">\n";
         echo "<div class=\"blanc\">".PRONO_CLASSEMENT_PSEUDO." : </div></td><td><div class=\"blanc\">$row[pseudo]</div></td></tr>\n";
         echo "<tr><td align=\"right\"><div class=\"blanc\">".PRONO_PROFIL_ANCIEN_MDP_2." :</div></td><td><input type=\"password\" name=\"ancien_mdp\"></td></tr>\n";
         echo "<tr><td align=\"right\"><div class=\"blanc\">".PRONO_PROFIL_NOUVEAU_MDP." :</div></td><td><input type=\"password\" name=\"nouveau_mdp\"></td></tr>\n";
         echo "<tr><td align=\"right\"><div class=\"blanc\">".PRONO_PROFIL_NOUVEAU_MDP_2." :</div></td><td><input type=\"password\" name=\"nouveau_mdp2\"></td></tr>\n";
         echo "<tr><td align=\"right\"><div class=\"blanc\">".SETUP_MAIL." :</div></td><td ><input type=\"text\" name=\"mail\" value=\"$row[mail]\"></td></tr>\n";

         echo "<tr><td width=\"50%\" align=\"center\"><br /></td>\n";
         echo "<td></td></tr>";

//echo "<tr><td colspan=\"2\"  align=\"center\"><font face=\"Verdana, Arial, Helvetica, sans-serif\" color=\"#ffffff\"><strong>Coordonnées</strong></font></td></tr>";

// Nom
//echo "<tr><td width=\"50%\" align=\"right\"><font face=\"Verdana\" color=\"#ffffff\" size=\"1\">Nom :</font></td>";
//echo "<td><input type=\"text\" name=\"nom\" value=\"$row[nom]\" maxlength=\"50\"></td></tr>";

// Prénom
//echo "<tr><td width=\"50%\" align=\"right\"><font face=\"Verdana\" color=\"#ffffff\" size=\"1\">Prénom :</font></td>";
//echo "<td><input type=\"text\" name=\"prenom\" value=\"$row[prenom]\" maxlength=\"50\"></td></tr>";

// Adresse
//echo "<tr><td width=\"50%\" align=\"right\"><font face=\"Verdana\" color=\"#ffffff\" size=\"1\">Adresse :</font></td>";
//echo "<td><input type=\"text\" name=\"adresse\" value=\"$row[adresse]\" maxlength=\"100\" size=\"45\"></td></tr>";

// Code postal
//echo "<tr><td width=\"50%\" align=\"right\"><font face=\"Verdana\" color=\"#ffffff\" size=\"1\">Code postal :</font></td>";
//echo "<td><input type=\"text\" name=\"code_postal\" value=\"$row[code_postal]\" maxlength=\"5\" size=\"5\"></td></tr>";

// Ville
//echo "<tr><td width=\"50%\" align=\"right\"><font face=\"Verdana\" color=\"#ffffff\" size=\"1\">Ville :</font></td>";
//echo "<td><input type=\"text\" name=\"ville\" value=\"$row[ville]\" maxlength=\"200\"></td></tr>";

// Pays
//echo "<tr><td width=\"50%\" align=\"right\"><font face=\"Verdana\" color=\"#ffffff\" size=\"1\">Pays :</font></td>";
//echo "<td><input type=\"text\" name=\"pays\" value=\"$row[pays]\" maxlength=\"200\"></td></tr>";

// Date de naissance
$elementsdate=explode("-",$row['date_naissance']);
$jour=$elementsdate[2];
$mois= $elementsdate[1];
$annee=$elementsdate[0];

//echo "<tr><td width=\"50%\" align=\"right\"><font face=\"Verdana\" color=\"#ffffff\" size=\"1\">Date de naissance :</font></td>";
//echo "<td>";
//echo "<select size=\"1\" name=\"jour\"><option value=\"\"></option>";
//for($i=1;$i<32;$i++)
//{
//if ($i==$jour) echo "<option value=\"$i\" selected>$i</option>";
//else echo "<option value=\"$i\">$i</option>";
//}
//echo "</select> ";
//echo "<select size=\"1\" name=\"mois\"><option value=\"\"></option>";
//for($i=1;$i<13;$i++)
//{
//if ($i==$mois) echo "<option value=\"$i\" selected>$i</option>";
//else echo "<option value=\"$i\">$i</option>";
//}
//echo "</select> ";

//echo "<select size=\"1\" name=\"annee\"><option value=\"\"></option>";
//for($i=2000;$i>1923;$i--)
//{
//if ($i==$annee) echo "<option value=\"$i\" selected>$i</option>";
//else echo "<option value=\"$i\">$i</option>";
//}
//echo "</select>";

//echo "</td></tr>";

// Profession
//echo "<tr><td width=\"50%\" align=\"right\"><font face=\"Verdana\" color=\"#ffffff\" size=\"1\">Profession :</font></td>";
//echo "<td><input type=\"text\" name=\"profession\" value=\"$row[profession]\" maxlength=\"200\"></td></tr>";

// N° Mobile
//$elementsmobile=explode("-",$row[mobile]);
//$mobile1=$elementsmobile[0];
//$mobile2=$elementsmobile[1];
//$mobile3=$elementsmobile[2];
//$mobile4=$elementsmobile[3];
//$mobile5=$elementsmobile[4];

//echo "<tr><td width=\"50%\" align=\"right\"><font face=\"Verdana\" color=\"#ffffff\" size=\"1\">N° de mobile :</font></td>";
//echo "<td><input type=\"text\" name=\"mobile1\" value=\"$mobile1\" maxlength=\"2\" size=\"2\">
//<input type=\"text\" name=\"mobile2\" value=\"$mobile2\" maxlength=\"2\" size=\"2\">
//<input type=\"text\" name=\"mobile3\" value=\"$mobile3\" maxlength=\"2\" size=\"2\">
//<input type=\"text\" name=\"mobile4\" value=\"$mobile4\" maxlength=\"2\" size=\"2\">
//<input type=\"text\" name=\"mobile5\" value=\"$mobile5\" maxlength=\"2\" size=\"2\">
//</td></tr>";
         
         echo "<tr bgcolor=\"#FFFFFF\"><td colspan=\"2\" align=\"center\">\n";
         echo "<input type=\"hidden\" name=\"page\" value=\"profil\">\n";
         echo "<input type=\"hidden\" name=\"action\" value=\"1\">\n";
         echo "<a href=\"index.php?page=profil&amp;action=supp\"><font face=\"Verdana\" color=\"#000000\" size=\"1\">>> ".PRONO_PROFIL_SUPP_2." <<</font></a></td></tr>\n";
         echo "<tr><td colspan=\"2\" align=\"center\"><br /><input type=\"submit\" value=".ENVOI."></td></tr></table></form></td></tr></table>\n";
      echo "</table>";
 	 echo "</td></tr></table>"; 
 	 }
       }
 	 
?>
