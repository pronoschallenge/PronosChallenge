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

include("../config.php");
require ("../consult/fonctions.php");
ouverture();
//ENTETE2 ();
echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"../league.css\">";


if (!isset($_GET["id_joueur"]))
{

$requete ="SELECT phpl_joueurs.id, phpl_joueurs.nom, phpl_joueurs.prenom
           FROM phpl_joueurs
           ORDER by phpl_joueurs.nom, prenom";
$result = mysql_query($requete) or die ("probleme " .mysql_error());
    ?>
    <div align="center">
    <form method="get">
    <select name="id_joueur">
    <?php
      while($row = mysql_fetch_array($result))
      {
        $row[1] = stripslashes($row[1]);
        $row[2] = stripslashes($row[2]);
        echo ("<option value=\"$row[0]\">$row[1] $row[2]\n");
        echo ("</option>\n");
      }
    ?>
    </select>
    <input type="submit" value="<?php print ENVOI;?>">
    </form></div>
    <?php
}

else
{
  $id_joueur=$_GET["id_joueur"];
  
$sql = "SELECT
phpl_joueurs.nom as nom_joueur,
phpl_clubs.nom as nom_club,
phpl_joueurs.prenom,
DATE_FORMAT(date_naissance, '%d/%m/%Y') as datefr,
phpl_joueurs.photo,
position_terrain

FROM phpl_joueurs, phpl_clubs, phpl_effectif, phpl_equipes

WHERE phpl_effectif.id_equipe=phpl_equipes.id
AND phpl_effectif.id_joueur=phpl_joueurs.id
AND phpl_equipes.id_club=phpl_clubs.id
AND phpl_joueurs.id='$id_joueur'";

// on envoie la requête
$req = mysql_query($sql) or die('Erreur SQL !<br>'.$sql.'<br>'.mysql_error());

// on fait une boucle qui va faire un tour pour chaque enregistrement
$data = mysql_fetch_array($req);
       echo "<div align=\"center\">";

    // on affiche les informations de l'enregistrement en cours
    echo '<table border=0 width=500 class="tablephpl2" cellspacing="0" cellpadding="0"><tr class="trphpl3"><td width="100%" height=:"26" colspan="2">';
    echo '<font color="#FFFFFF"><b>&nbsp; '.$data['prenom'].' '.$data['nom_joueur'].'<b></font></td></tr>';
    echo '<tr><td>&nbsp; </td></tr>';
    echo '<td width="40%" rowspan="4"><center>';

if( empty( $data['photo']))
echo '<img border="1" src="default.gif" width="100" height="130"></center></td>';

else {
    echo '<img border="1" src="../images/photo/'.$data['photo'].'" width="100" height="130"></center></td>'; }

    // calcul de l'age à partir de la date de naissance

$date_de_naissance = $data['datefr'];
$chiffre = explode('/',$date_de_naissance);
$time_naissance = mktime(0,0,0,$chiffre[1],$chiffre[0],$chiffre[2]);
$seconde_vecu = time() - $time_naissance;
$seconde_par_an = (1461*24*60*60)/4;
$age = floor(($seconde_vecu / $seconde_par_an));

    echo '<tr><td><b>'.FICHE_AGE.' :</b> '.$age.' ans</td></tr>';
    echo '<tr><td><b>'.FICHE_DATE.' :</b>  '.$date_de_naissance.'</td></tr>';
    echo '<tr><td><b>'.ADMIN_JOUEURS_MSG9.'</b>  '.$data['position_terrain'].'</td></tr>';
    echo '<tr><td>&nbsp; </td></tr>';
    echo '<tr><td>&nbsp; </td></tr>';

if (!isset($_GET["details_buts"]))
{

$requete="SELECT phpl_clubs.nom, phpl_saisons.annee, (phpl_saisons.annee)+1, phpl_divisions.nom, Sum(phpl_buteurs.buts) AS Total, phpl_championnats.id AS id_champ, phpl_matchs.date_reelle as date
          FROM phpl_clubs, phpl_effectif, phpl_saisons, phpl_divisions, phpl_championnats, phpl_buteurs, phpl_joueurs, phpl_equipes, phpl_matchs
          WHERE phpl_championnats.id_saison=phpl_saisons.id
          AND phpl_championnats.id_division=phpl_divisions.id
          AND phpl_equipes.id_champ=phpl_championnats.id
          AND phpl_equipes.id_club=phpl_clubs.id
          AND phpl_buteurs.id_effectif=phpl_effectif.id
          AND phpl_effectif.id_joueur=phpl_joueurs.id
          AND phpl_effectif.id_equipe=phpl_equipes.id
          AND phpl_matchs.id=phpl_buteurs.id_match
          AND phpl_joueurs.id='$id_joueur'
          GROUP by id_champ, phpl_clubs.nom
          ORDER by annee desc, date desc";
$resultats=mysql_query($requete) or die (mysql_error());
while ($row=mysql_fetch_array($resultats))
{
   echo "<tr><td colspan=\"2\">$row[3] $row[1]/$row[2], $row[0], $row[4] ".FICHE_BUTS."</td></tr>";
}

echo "<tr><td colspan=\"2\" align=\"right\"><a href=\"?id_joueur=$id_joueur&details_buts=1\">".FICHE_DETAIL."</a></td></tr>";
}

else
{
  $requete="SELECT cldom.nom AS cldom, clext.nom AS clext, phpl_matchs.buts_dom, phpl_matchs.buts_ext, date_reelle, dom.id AS eqdom, ext.id AS eqext, phpl_journees.numero, phpl_championnats.id as id_champ, phpl_divisions.nom, phpl_saisons.annee, (phpl_saisons.annee)+1, phpl_matchs.id as id_match
FROM phpl_equipes AS dom, phpl_equipes AS ext, phpl_matchs, phpl_journees, phpl_clubs AS cldom, phpl_clubs AS clext, phpl_buteurs, phpl_effectif, phpl_championnats, phpl_divisions, phpl_saisons
WHERE phpl_matchs.id_equipe_dom = dom.id
AND phpl_matchs.id_equipe_ext = ext.id
AND dom.id_club = cldom.id
AND ext.id_club = clext.id
AND phpl_matchs.id_journee = phpl_journees.id
AND phpl_buteurs.id_match = phpl_matchs.id
AND phpl_buteurs.id_effectif = phpl_effectif.id
AND phpl_effectif.id_joueur = '$id_joueur'
AND phpl_championnats.id_division=phpl_divisions.id
AND phpl_championnats.id_saison=phpl_saisons.id
AND phpl_championnats.id = ext.id_champ
AND phpl_championnats.id = dom.id_champ
ORDER BY annee, date_reelle ASC";
$id_champ=0;
$resultats=mysql_query($requete) or die (mysql_error());
while ($row=mysql_fetch_array($resultats))
{
 if (!($id_champ=="$row[8]")) {echo "<tr><td>$row[9] $row[10]/$row[11]</td>";}
 else {echo "<tr><td></td>";}

 echo "<td>".ADMIN_JOURNEES_MSG9."$row[7] : $row[0] <a href=\"match.php?id_match=$row[id_match]\">$row[2] - $row[3]</a> $row[1] </td></tr>";
 
 $id_champ=$row[8];
}

}

 echo "</table><br></div>";
}
// on ferme la connexion à mysql
mysql_close();

?>
