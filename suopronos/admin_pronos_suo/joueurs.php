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

if ($action3=="editer")
{ $prenom=addslashes($prenom);
$nom=addslashes($nom);
$position = addslashes($position);

$date_naissance=date_fr_vers_us($date_naissance);
  mysql_query ("UPDATE phpl_joueurs SET position_terrain = '$position',
                prenom='$prenom',
                photo='$photo',
                date_naissance='$date_naissance',
                nom='$nom'
                WHERE id = '$id_joueur'") or die ("probleme " .mysql_error());
}



if ($action2=="supp")
{
$requete="SELECT id FROM phpl_effectif WHERE id_joueur='$data'";
$resultats=mysql_query($requete);
while ($row=mysql_fetch_array($resultats))
       {
        mysql_query ("DELETE FROM phpl_buteurs WHERE id_effectif='$row[0]' ") or die ("probleme " .mysql_error());
       }
mysql_query ("DELETE FROM phpl_effectif WHERE id_joueur='$data' ") or die ("probleme " .mysql_error());
}                                                           

if ($action2=="creer")
{
  
	 $dateFR = date_fr_vers_us($date_naissance);
	 $joueurs_nom = addslashes($nom);
	 $joueurs_prenom = addslashes($prenom);
	 $position = addslashes($position);

	mysql_query ("INSERT INTO phpl_joueurs (nom, prenom, photo, date_naissance, position_terrain) values ('$joueurs_nom','$joueurs_prenom','$photo','$dateFR','$position')") or die ("probleme " .mysql_error());
	$requete="SELECT id FROM phpl_joueurs WHERE nom='$joueurs_nom' AND prenom='$joueurs_prenom'";
        $resultats=mysql_query($requete);
        $row=mysql_fetch_array($resultats);
	if (!$id_equipe=="0") {mysql_query ("INSERT INTO phpl_effectif (id_joueur, id_equipe) values ('$row[0]','$id_equipe')") or die ("probleme " .mysql_error());}


}
 if ($action_transfert=='1')
        {
         reset ($joueurs_id);
         while ( list($cle, $val)= each($joueurs_id))
          {
            
             $requete="SELECT id FROM phpl_effectif WHERE id_joueur='$val' AND id_equipe='$equipe_id'";
             $resultat = mysql_query($requete) or die ("probleme " .mysql_error());
             $nb=mysql_num_rows( $resultat );

             if ($nb=="0")
              {
               mysql_query("INSERT INTO phpl_effectif (id_joueur, id_equipe) values ('$val','$equipe_id')") or die ("probleme " .mysql_error());
              }

          }


         }


        

?>

<table class=phpl width="80%">
            <tr>
              <td class=phpl2 align="center" colspan="3"><? echo ADMIN_JOUEURS_TITRE ;?></td>
            </tr>
            <tr>
              <td align="center" class=phpl6 colspan="3"><? echo ADMIN_JOUEURS_TRANSFERT; ?>
              </td>
            </tr>
            <tr>
             <td class=phpl3>
<form method="post">
<?php
    $requete ="SELECT phpl_joueurs.id, phpl_joueurs.nom, phpl_joueurs.prenom
               FROM phpl_joueurs
               ORDER by phpl_joueurs.nom, prenom";
    $result = mysql_query($requete) or die ("probleme " .mysql_error());
    echo "<select name=\"joueurs_id[]\" multiple size=\"8\">";
      while($row = mysql_fetch_array($result))
      {
        $row[1] = stripslashes($row[1]);
        $row[2] = stripslashes($row[2]);
        echo ("<option value=\"$row[0]\">$row[1] $row[2]\n");
        echo ("</option>\n");
      }
echo "</select>";
?>

             </td>
             <td class=phpl3 align="center">
             <?php echo ADMIN_JOUEURS_TRANSFERT_VERS." ";

    $requete ="SELECT phpl_clubs.nom, phpl_equipes.id FROM phpl_clubs, phpl_equipes
               WHERE phpl_equipes.id_champ='$champ' 
               AND phpl_clubs.id=phpl_equipes.id_club
               ORDER BY phpl_clubs.nom";
    $result = mysql_query($requete) or die ("probleme " .mysql_error());
    echo "<select name=\"equipe_id\">";
      while($row = mysql_fetch_array($result))
      {
        $row[1] = stripslashes($row[1]);
        echo ("<option value=\"$row[1]\">$row[0]\n");
        echo ("</option>\n");
      }
echo "</select>";
?>
             </td>
             <td class=phpl3>

<?php
echo "<input type=\"hidden\" name=\"action_transfert\" value=\"1\">
<input type=\"hidden\" name=\"page\" value=\"championnat\">
<input type=\"hidden\" name=\"action\" value=\"joueurs\">
<input type=\"hidden\" name=\"champ\" value=\"$champ\">";
echo "<input type=\"submit\" value=".ENVOI."></form>";
?>

             </td>
            </tr>
            <tr>
              <td align="center" class=phpl6 colspan="3"><? echo ADMIN_CHAMP_CREER_3; ?>
              </td>
            </tr>
            <tr>
              <td class=phpl3>

<form method="post" action="">
<?php
echo ADMIN_JOUEURS_MSG4."</td><td class=phpl3><input type=\"text\" size=\"30\" name=\"prenom\"></td><td class=phpl3></tr>
<tr><td class=phpl4>".ADMIN_JOUEURS_MSG5."</td><td class=phpl4><input type=\"text\" size=\"30\" name=\"nom\"></td><td class=phpl4></tr>
<tr><td class=phpl3>".ADMIN_JOUEURS_MSG6;



echo "</td><td class=phpl3><select name=\"id_equipe\">";
echo "<option value=\"0\"> </option>";
$result = mysql_query("SELECT phpl_equipes.id, phpl_clubs.nom
                       FROM phpl_clubs, phpl_equipes
                       WHERE phpl_clubs.id = phpl_equipes.id_club
                       AND phpl_equipes.id_champ='$champ'
                       ORDER BY nom");
while($row = mysql_fetch_array($result))
	{
         $row[1] = stripslashes($row[1]);
	echo (" <option value=\"$row[0]\">".$row[1]);
	echo ("</option>\n");
	}
echo "</select></td><td class=phpl3></tr>
<tr><td class=phpl4>".ADMIN_JOUEURS_MSG7."</td><td class=phpl4><input type=\"text\" size=\"30\" name=\"photo\"><td class=phpl4></tr>
<tr><td class=phpl3>".ADMIN_JOUEURS_MSG8."</td><td class=phpl3><input type=\"text\" length=\"8\" name=\"date_naissance\"></td><td class=phpl3></td></tr>
<tr><td class=phpl4>".ADMIN_JOUEURS_MSG9."</td><td class=phpl4><input size=\"30\" name=\"position\"></td><td class=phpl4></td></tr>
<input type=\"hidden\" name=\"action2\" value=\"creer\">
<input type=\"hidden\" name=\"page\" value=\"championnat\">
<input type=\"hidden\" name=\"action\" value=\"joueurs\">
<input type=\"hidden\" name=\"champ\" value=\"$champ\">



<tr><td class=phpl3 colspan=\"3\" align=center><input type=\"submit\" value=".ENVOI."></form></td></tr>";

?>
              
              </td>
            </tr>
            <tr>
              <td align="center" class=phpl6 colspan="3"><? echo ADMIN_RENS_8; ?>
              </td>
            </tr>
            <tr>
              <td align="center" class=phpl6 colspan="3">
<?
echo "<form method=\"post\" action=\"\">";
echo "</tr><tr><td>".ADMIN_JOUEURS_MSG1;
echo "</td><td><select name=\"data\">";
echo "<option value=\"0\"> </option>";
$result = mysql_query("SELECT phpl_joueurs.id, phpl_joueurs.nom, phpl_joueurs.prenom, phpl_clubs.nom
                       FROM phpl_joueurs, phpl_clubs, phpl_equipes, phpl_effectif
                       WHERE phpl_effectif.id_equipe=phpl_equipes.id
                       AND phpl_effectif.id_joueur=phpl_joueurs.id
                       AND phpl_equipes.id_club=phpl_clubs.id
                       AND phpl_equipes.id_champ='$champ' ORDER BY phpl_clubs.nom, phpl_joueurs.nom, phpl_joueurs.prenom") or die ("probleme " .mysql_error());
while($row = mysql_fetch_array($result))
	{$a=$row[1]+1;
	 $joueurs_nom = stripslashes($row[1]);
	 $joueurs_prenom = stripslashes($row[2]);
	 $clubs_nom = stripslashes($row[3]);

	echo (" <option value=\"$row[0]\">$clubs_nom => $joueurs_nom $joueurs_prenom");
	echo ("</option>\n");
	}
	echo "</select></td><td>";
 $value=ADMIN_RENS_8; echo "<input type=\"submit\" value=\"$value\">";
echo " <input type=\"hidden\" name=\"action2\" value=\"supp\">
<input type=\"hidden\" name=\"page\" value=\"championnat\">
<input type=\"hidden\" name=\"action\" value=\"joueurs\">
<input type=\"hidden\" name=\"champ\" value=\"$champ\"></form>";
?>
              </td>
            </tr>            <tr>
              <td align="center" class=phpl6 colspan="3"><? echo EDITER; ?>
              </td>
            </tr>
            <tr>
              <td align="center" class=phpl6 colspan="3">
<?
echo "<form method=\"post\" action=\"\">";
echo "</tr><tr><td>".ADMIN_JOUEURS_EDITER;
echo "</td><td><select name=\"id_joueur\">";
echo "<option value=\"0\"> </option>";
$result = mysql_query("SELECT phpl_joueurs.id, phpl_joueurs.nom, phpl_joueurs.prenom, phpl_clubs.nom
                       FROM phpl_joueurs, phpl_clubs, phpl_equipes, phpl_effectif
                       WHERE phpl_effectif.id_equipe=phpl_equipes.id
                       AND phpl_effectif.id_joueur=phpl_joueurs.id
                       AND phpl_equipes.id_club=phpl_clubs.id
                       AND phpl_equipes.id_champ='$champ' ORDER BY phpl_clubs.nom, phpl_joueurs.nom, phpl_joueurs.prenom") or die ("probleme " .mysql_error());
while($row = mysql_fetch_array($result))
	{$a=$row[1]+1;
	 $joueurs_nom = stripslashes($row[1]);
	 $joueurs_prenom = stripslashes($row[2]);
	 $clubs_nom = stripslashes($row[3]);

	echo (" <option value=\"$row[0]\">$clubs_nom => $joueurs_nom $joueurs_prenom");
	echo ("</option>\n");
	}
	echo "</select></td><td>";
 $value=ENVOI; echo "<input type=\"submit\" value=\"$value\">";
echo " <input type=\"hidden\" name=\"action2\" value=\"edit\">
<input type=\"hidden\" name=\"page\" value=\"championnat\">
<input type=\"hidden\" name=\"action\" value=\"joueurs\">
<input type=\"hidden\" name=\"champ\" value=\"$champ\"></form>";

if (isset($id_joueur) and $action2=="edit")
{
 ?>

<form method="post" action="">
<?
$requete="SELECT prenom, nom, photo, date_naissance, position_terrain FROM phpl_joueurs WHERE id='$id_joueur'";
$resultats = mysql_query($requete);

$row=mysql_fetch_array($resultats);

$row[3] = date_us_vers_fr($row[3]);
  $value=ENVOI;

echo "<tr><td colspan=\"3\" align=\"center\">&nbsp;</td></tr>";
echo "<tr><td colspan=\"3\" align=\"center\">&nbsp;</td></tr>";
echo "<tr><td colspan=\"3\" align=\"center\">".ADMIN_JOUEURS_EDITER_2." $row[0] $row[1]</td></tr>";

echo "<tr><td class=phpl3>".ADMIN_JOUEURS_MSG4."</td><td class=phpl3><input type=\"text\" size=\"30\" name=\"prenom\" value=\"$row[0]\"></td><td class=phpl3></tr>
<tr><td class=phpl4>".ADMIN_JOUEURS_MSG5."</td><td class=phpl4><input type=\"text\" size=\"30\" name=\"nom\" value=\"$row[1]\"></td><td class=phpl4></tr>";
 echo "<tr><td class=phpl4>".ADMIN_JOUEURS_MSG7."</td><td class=phpl4><input type=\"text\" size=\"30\" name=\"photo\" value=\"$row[2]\"><td class=phpl4></tr>
 <tr><td class=phpl3>".ADMIN_JOUEURS_MSG8."</td><td class=phpl3><input type=\"text\" length=\"8\" name=\"date_naissance\" value=\"$row[3]\"></td><td class=phpl3></td></tr>
 <tr><td class=phpl4>".ADMIN_JOUEURS_MSG9."</td><td class=phpl4><input size=\"30\" name=\"position\" value=\"$row[4]\"></td><td class=phpl4></td></tr>
 <input type=\"hidden\" name=\"action2\" value=\"edit\">
 <input type=\"hidden\" name=\"action3\" value=\"editer\">
 <input type=\"hidden\" name=\"page\" value=\"championnat\">
 <input type=\"hidden\" name=\"action\" value=\"joueurs\">
 <input type=\"hidden\" name=\"id_joueur\" value=\"$id_joueur\">
 <input type=\"hidden\" name=\"champ\" value=\"$champ\">";
  echo "<tr><td class=phpl3 colspan=\"3\" align=\"center\"><input type=\"submit\" value=\"$value\"></form></td></tr>";


}
?>
              </td>
            </tr>
</table><br /><br />


<br /><br />
