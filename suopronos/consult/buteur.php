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

$gr_champ = isset($_POST['gr_champ']) ? $_POST['gr_champ'] : NULL;
$complet = isset($_GET['complet']) ? $_GET['complet'] : NULL;
$debut = isset($_REQUEST['debut']) ? $_REQUEST['debut'] : NULL;
$fin = isset($_REQUEST['fin']) ? $_REQUEST['fin'] : NULL;
$type = isset($_REQUEST['type']) ? $_REQUEST['type'] : NULL;
$equipe = isset($_REQUEST['equipe']) ? $_REQUEST['equipe'] : NULL;


if (!isset($_GET['gr_champ'])and !isset($_GET['champ']))
    {
    $value=GENERAL;
    demande_champ ();
    }



if (isset($_GET['champ']) or isset($_GET['gr_champ']) or isset($_GET['equipe']))
{

if (isset($_GET['champ']))    {$champ = $_GET['champ'];}
if (isset($_GET['gr_champ'])) {$gr_champ = $_GET['gr_champ'];}
if (isset($_GET['equipe']))   {$equipe = $_GET['equipe'];}
if (!(isset($type)))          {$type=GENERAL;}


if (!isset($gr_champ))
{
if (!(isset($debut))) {$debut="1";}
if (!(isset($fin))) {$fin=nb_journees($champ);
}

// MENU TYPES DE CLASSEMENT


echo "<div align=\"center\">";
echo "<form method=\"get\" action=\"\">".CONSULT_BUTEUR_MSG2." <select name=\"type\">\n";


switch($type)
{    
case GENERAL;
        {
	echo "<option value=\"".GENERAL."\" selected=\"selected\">".GENERAL."</option>\n";
	echo "<option value=\"".DOMICILE."\"> ".DOMICILE."</option>\n";
	echo "<option value=\"".EXTERIEUR."\"> ".EXTERIEUR."</option>\n";
	}
	break;


case DOMICILE;
        {
	echo "<option value=\"".GENERAL."\"> ".GENERAL."</option>\n";
	echo "<option value=\"".DOMICILE."\" selected=\"selected\"> ".DOMICILE."</option>\n";
	echo "<option value=\"".EXTERIEUR."\"> ".EXTERIEUR."</option>\n";
	}
	break;


case EXTERIEUR;
        {
	echo "<option value=\"".GENERAL."\"> ".GENERAL."</option>\n";
	echo "<option value=\"".DOMICILE."\"> ".DOMICILE."</option>\n";
	echo "<option value=\"".EXTERIEUR."\" selected=\"selected\"> ".EXTERIEUR."</option>\n";
	}
	break;


}


echo "</select>";


    echo " ".CONSULT_CLMNT_MSG2." <select name=\"debut\">\n";
    $f=1;
    // echo "<option value=\"1\" selected> 1</option>  ";
    While ($f<=nb_journees($champ))
        {
        if ($f==$debut) {$select=" selected=\"selected\"";} else {$select="";}
	echo "<option value=\"$f\"$select> $f</option>\n";
        $f++;
        }
    echo "</select>";
    echo " ".CONSULT_CLMNT_MSG3." <select name=\"fin\">";
    $f=1;
    $x=nb_journees($champ) ;





    While ($f<=$x)
        {
        if ($f==$fin) {$select=" selected=\"selected\"";} else {$select="";}
	echo "<option value=\"$f\"$select> $f</option>\n";
        $f++;
        }


    echo "</select><input type=\"hidden\" name=\"champ\" value=\"$champ\">";
    
    $query="SELECT phpl_clubs.nom, phpl_equipes.id 
            FROM phpl_equipes, phpl_clubs
            WHERE phpl_equipes.id_champ='$champ'
            AND phpl_clubs.id=phpl_equipes.id_club";
    $result = mysql_query ($query);
    
    if (!$result) die (mysql_error());

        echo " ".ADMIN_JOUEURS_3." ";
        echo "<select name=\"equipe\">";
        echo "<option value=\"\"> </option>";

                
                 while ($row = mysql_fetch_array($result))
                 { 
                  $clubs_nom = stripslashes($row[0]);
                  $e=$row[1];
                  if ($e==$equipe){$select="selected";} else{$select="";}
                  echo ("<option value=\"$row[1]\"$select>$clubs_nom");
                 echo ("</option>\n");
                 
                 }

        echo "</select>";
        echo "<input type=\"submit\" value=\"".ENVOI."\">";
    echo "</form>";
                 
}
if (!isset($gr_champ))
	{
	$query="SELECT phpl_divisions.nom, phpl_saisons.annee, (phpl_saisons.annee)+1
	        FROM phpl_championnats, phpl_divisions, phpl_saisons
	        WHERE phpl_championnats.id='$champ'
	        AND phpl_divisions.id=phpl_championnats.id_division 
	        AND phpl_saisons.id=phpl_championnats.id_saison";


	$result = mysql_query($query);
	echo "<h4>";
	while ($row=mysql_fetch_array($result))
  	{
           echo $row[0]."  ".$row[1]."/".$row[2]."<br />";
        }
	echo "</h4></div>";


	}
else
	{
	$query="SELECT phpl_divisions.nom, phpl_saisons.annee, (phpl_saisons.annee)+1 FROM phpl_championnats, phpl_divisions, phpl_saisons WHERE phpl_championnats.id IN (";



	$query2="SELECT id_champ, libelle FROM groupe_championnat WHERE id_groupe=$gr_champ";
	$result2=mysql_query($query2);
        $x=0;
	$tab_query="";
	while ($row2=mysql_fetch_array($result2)) 
		{ 
		$x++;
		if ($x!=1) $tab_query = $tab_query . ",";
		$tab_query=$tab_query . "'$row2[0]'";
		$nom_gr=$row2[1];
		}     


	$query=$query . $tab_query . ") AND phpl_divisions.id=phpl_championnats.id_division AND phpl_saisons.id=phpl_championnats.id_saison";


	$result = mysql_query($query); 
	echo "<h3>".CONSULT_BUTEUR_MSG3." $nom_gr</h3>".CONSULT_BUTEUR_MSG4."<h4>";
	while ($row=mysql_fetch_array($result)) 
  	{ 
           echo $row[0]."  ".$row[1]."/".$row[2]."<br />";
        }
	echo "</h4></div>";
	}


if (!isset($gr_champ))
	{
	// SELECTION DES PARAMETRES

	$result=(mysql_query("SELECT fiches_clubs, phpl_clubs.nom, id_equipe_fetiche, phpl_clubs.id
                               FROM phpl_parametres, phpl_clubs, phpl_equipes
                               WHERE phpl_parametres.id_champ='$champ'
                               AND phpl_equipes.id=id_equipe_fetiche
                               AND phpl_clubs.id=phpl_equipes.id_club"));

         $row=mysql_fetch_array($result);

	$EquipeFetiche=stripslashes($row['nom']);
	$id_club_fetiche=$row['id'];
	$id_equipe_fetiche=$row['id_equipe_fetiche'];
	}
else
	{
	// SELECTION DES PARAMETRES
	$result=(mysql_query("SELECT * FROM phpl_parametres WHERE id_champ IN ($tab_query)"));


        $x=0;
	$tab_id_equipe_fetiche="";
	
	while ($row=mysql_fetch_array($result))
	       {
		$x++;
		if ($x!=1) $tab_id_equipe_fetiche = $tab_id_equipe_fetiche . ",";
		$tab_id_equipe_fetiche=$tab_id_equipe_fetiche . "'$row[id_equipe_fetiche]'" ;
	       }


	$result=(mysql_query("SELECT id_club FROM phpl_equipes WHERE id IN ($tab_id_equipe_fetiche)"));
        $x=0;
	$id_club_fetiche="";

	while ($row=mysql_fetch_array($result))
	       {
		$x++;
		if ($x!=1) $id_club_fetiche = $id_club_fetiche . ",";
		$id_club_fetiche=$id_club_fetiche . "'$row[id_club]'" ;
	       }
	// $EquipeFetiche=$tab_id_club_fetiche;
	}

if (isset($gr_champ))
{
if (!(isset($type))) {$type=GENERAL;}
if (!(isset($debut))) {$debut="1";}
if (!(isset($fin)))
	{
	$array = explode(",",$tab_query);
	// echo "<TD>" . sizeof($array) ;
	$fin=1;
	for ($i="0"; $i < sizeof($array); $i++)
	{
		$nb = nb_journees(ereg_replace("'","",$array[$i]));
		if ($nb > $fin) { $fin=$nb ; }

	}
	}
}

// AFFICHAGE DE TOUS LE CHAMPIONNAT SI L UTILISATEUR Na PAS BORNE
switch($type)
{
case GENERAL;    // CLASSEMENT GENERAL
        {

//	if (!$gr_champ){
		$legende=CONSULT_BUTEUR_TITRE_1." : ".GENERAL.", ".CONSULT_CLMNT_MSG2.$debut.CONSULT_CLMNT_MSG3.$fin;
		
		$requete = "
		SELECT 	Sum(phpl_buteurs.buts) AS Total,
			phpl_joueurs.nom AS NomJoueur,
			phpl_joueurs.prenom as PrenomJoueur,
			phpl_clubs.nom,
			phpl_clubs.id as idClub,
			phpl_joueurs.id as id_joueur


			FROM 	phpl_joueurs, phpl_buteurs, phpl_matchs, phpl_journees, phpl_equipes, phpl_clubs, phpl_effectif


			WHERE	phpl_joueurs.id = phpl_effectif.id_joueur
				AND phpl_equipes.id = phpl_effectif.id_equipe
				AND phpl_buteurs.id_match = phpl_matchs.id
				AND phpl_journees.id = phpl_matchs.id_journee
				AND phpl_equipes.id_club=phpl_clubs.id
				AND phpl_journees.numero>=$debut
				AND phpl_journees.numero<=$fin
				AND phpl_effectif.id=phpl_buteurs.id_effectif
				AND phpl_effectif.id_equipe=phpl_equipes.id";


if (!isset($gr_champ)) {$requete = $requete . " AND phpl_journees.id_champ=$champ ";}else {$requete = $requete . " AND phpl_journees.id_champ IN ($tab_query) ";}
if (isset($equipe) and $equipe!=='') {$requete = $requete . "AND phpl_equipes.id='$equipe'";}
$requete = $requete . "
				AND (phpl_matchs.id_equipe_dom = phpl_equipes.id
				OR phpl_matchs.id_equipe_ext = phpl_equipes.id)
	
			GROUP BY
				phpl_joueurs.nom,
				phpl_joueurs.prenom,
				phpl_joueurs.photo,
				phpl_joueurs.date_naissance,
				phpl_joueurs.position_terrain
				


			ORDER BY Total DESC, NomJoueur ASC";

if (!isset($complet) or !($complet=="1")) {$requete = $requete . " LIMIT 0,10";}

	Buteur($legende, $requete, $type, $id_club_fetiche, $champ, $debut, $fin, $equipe, $complet);
	}
	break;


case "Domicile";
        {

	$legende=CONSULT_BUTEUR_TITRE_1." : ".DOMICILE.", ".CONSULT_CLMNT_MSG2.$debut.CONSULT_CLMNT_MSG3.$fin;

	$requete = "
		SELECT 	Sum(phpl_buteurs.buts) AS Total,
			phpl_joueurs.nom AS NomJoueur,
			phpl_joueurs.prenom as PrenomJoueur,
			phpl_clubs.nom,
			phpl_clubs.id as idClub,
			phpl_joueurs.id as id_joueur

		FROM 	phpl_joueurs, phpl_buteurs, phpl_matchs, phpl_journees, phpl_equipes, phpl_clubs, phpl_effectif


		WHERE	phpl_joueurs.id = phpl_effectif.id_joueur
			AND phpl_matchs.id_equipe_dom = phpl_equipes.id
			AND phpl_clubs.id = phpl_equipes.id_club
			AND phpl_matchs.id = phpl_buteurs.id_match
			AND phpl_journees.id = phpl_matchs.id_journee
			AND phpl_equipes.id=phpl_effectif.id_equipe
			AND phpl_journees.numero>=$debut
			AND phpl_journees.numero<=$fin
			AND phpl_journees.id_champ=$champ
			AND phpl_effectif.id=phpl_buteurs.id_effectif";
			
			if (!$gr_champ) {$requete = $requete . " AND phpl_journees.id_champ=$champ ";} 	else {$requete = $requete . " AND phpl_journees.id_champ IN ($tab_query) ";}


                        if ($equipe and $equipe!=''){ $requete=$requete."AND phpl_equipes.id='$equipe'";}
                        $requete=$requete."

			
	
		GROUP BY
				phpl_joueurs.nom,
				phpl_joueurs.prenom,
				phpl_joueurs.photo,
				phpl_joueurs.date_naissance,
				phpl_joueurs.position_terrain


		ORDER BY Total DESC ,NomJoueur ASC";

if (!isset($complet) or !($complet=="1")) {$requete = $requete . " LIMIT 0,10";}


	Buteur($legende, $requete, $type, $id_club_fetiche, $champ, $debut, $fin, $equipe, $complet);
	}
	break;


case "Extérieur";
        {
	$legende=CONSULT_BUTEUR_TITRE_1." : ".EXTERIEUR.", ".CONSULT_CLMNT_MSG2.$debut.CONSULT_CLMNT_MSG3.$fin;
	$requete = "
		SELECT 	Sum(phpl_buteurs.buts) AS Total,
			phpl_joueurs.nom AS NomJoueur,
			phpl_joueurs.prenom as PrenomJoueur,
			phpl_clubs.nom,
			phpl_clubs.id as idClub,
			phpl_joueurs.id as id_joueur
			
		FROM 	phpl_joueurs, phpl_buteurs, phpl_matchs, phpl_journees, phpl_equipes, phpl_clubs, phpl_effectif


		WHERE
			phpl_joueurs.id = phpl_effectif.id_joueur
			AND phpl_matchs.id_equipe_ext = phpl_equipes.id
			AND phpl_clubs.id = phpl_equipes.id_club
			AND phpl_matchs.id = phpl_buteurs.id_match
			AND phpl_journees.id = phpl_matchs.id_journee
			AND phpl_equipes.id=phpl_effectif.id_equipe
			AND phpl_journees.numero>=$debut
			AND phpl_journees.numero<=$fin
			AND phpl_effectif.id=phpl_buteurs.id_effectif";
			if (!$gr_champ) {$requete = $requete . " AND phpl_journees.id_champ=$champ ";} 	else {$requete = $requete . " AND phpl_journees.id_champ IN ($tab_query) ";}


                        if ($equipe and $equipe!=''){ $requete=$requete."AND phpl_equipes.id='$equipe'";}
                        $requete=$requete."


			AND phpl_journees.id_champ=$champ


		GROUP BY
				phpl_joueurs.nom,
				phpl_joueurs.prenom,
				phpl_joueurs.photo,
				phpl_joueurs.date_naissance,
				phpl_joueurs.position_terrain


		ORDER BY Total DESC , NomJoueur ASC";

if (!isset($complet) or !($complet=="1")) {$requete = $requete . " LIMIT 0,10";}


	Buteur($legende, $requete, $type, $id_club_fetiche, $champ, $debut, $fin, $equipe, $complet);
	}
	break;
}
}
?>
<br />
<p align="right"><font face="Verdana" size="1">Powered by <a href="http://phpleague.univert.org" target="_blank">PhpLeague</a></font></p>
<?php
 include ("apres.php");
?>

