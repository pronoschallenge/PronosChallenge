<?php
	require_once("fonctions.php");
	require_once("../config.php") ;

	ouverture ();

	$id_membre = $_REQUEST['id_membre'];

	// permet de redimensionner une image
	function redimage($img_src,$largeur_max,$hauteur_max) {
		// Lit les dimensions de l'image
		$size = GetImageSize($img_src);  
		$largeur_image_originale = $size[0];
		$hauteur_image_originale = $size[1];
		// Test si il faut redimensionner
		if($largeur_image_originale > $largeur_max || $hauteur_image_originale > $hauteur_max)
		{
			if(($largeur_image_originale/$hauteur_image_originale) > ($largeur_max/$hauteur_max))
			{
				echo "WIDTH=".$largeur_max."px";
			}
			else
			{
				echo "HEIGHT=".$hauteur_max."px";
			}
		}
	}
	
	$url_avatar = "./images/avatars/".$id_membre.".gif";
	if(!remote_file_exists($url_avatar))
	{
		$url_avatar = "./images/avatars/no_avatar.png";
	}
?>

<div style="float: left;height: 100px;padding-right: 10px;">
	<img src="<? echo $url_avatar ?>" <?php redimage($url_avatar, 100, 100) ?>/>
</div>

<?php
	$queryInfosUser="SELECT clmnt.id_membre, membre.pseudo,
		membre.nom as nom,  membre.prenom,  membre.ville, membre.departement, membre.id_club_favori, 
		club.nom as nom_club, club.url_logo 
		FROM phpl_clmnt_pronos as clmnt, phpl_membres as membre, phpl_clubs as club
		WHERE membre.id=$id_membre
		AND membre.id=clmnt.id_membre
		AND membre.actif='1'
		AND (membre.id_club_favori IS NULL OR club.id = membre.id_club_favori)
		GROUP by clmnt.pseudo
		ORDER by  clmnt.points desc, clmnt.participation asc, membre.pseudo";
	$resultInfosUser=mysql_query($queryInfosUser) or die ("probleme " .mysql_error());

	if ($rowInfosUser=mysql_fetch_array($resultInfosUser))
	{
		echo "<div>";
		echo "<div style=\"font-size: 13pt;font-weight: bold;padding-bottom: 5px;\">".$rowInfosUser["pseudo"]."</div>";
		echo "<div>".$rowInfosUser["nom"]." ".$rowInfosUser["prenom"]."</div>";
		if($rowInfosUser["ville"] != null) 
		{
			echo "<div>".$rowInfosUser["ville"]." (".$rowInfosUser["departement"].")</div>";
		}
		if($rowInfosUser["url_logo"] != null) 
		{
			echo "<div><img src=\"./images/clubs/".$rowInfosUser["url_logo"]."\" /></div>";
		}		
		echo "</div>";
	}

	$queryPalmares="SELECT membres.id, membres.pseudo, clmnt.place, clmnt.type, clmnt.id_champ, groupes.nom
		FROM phpl_membres membres, phpl_clmnt_pronos clmnt, phpl_gr_championnats groupes
		WHERE membres.id = $id_membre
		AND membres.id = clmnt.id_membre
		AND clmnt.id_champ = groupes.id
		AND groupes.activ_prono = '0'
		AND (clmnt.type='general' OR clmnt.type='hourra' OR clmnt.type='mixte')
		ORDER BY clmnt.id_champ DESC, clmnt.type ASC";						
	$resultPalmares=mysql_query($queryPalmares) or die ("probleme " .mysql_error());
	
	echo "<div style=\"clear: both;padding-top: 10px;\">";
	echo "<fieldset>";
	echo "<legend>Palmarès</legend>";
	
	if(mysql_num_rows($resultPalmares) == 0)
	{
		echo "<div style=\"width:100%;text-align:center;\">Aucun palmarès</div>";
	}
	
	$id_nom_champ = "";
	while ($rowPalmares=mysql_fetch_array($resultPalmares))
	{
		if($rowPalmares["nom"] != $id_nom_champ)
		{
			if($id_nom_champ != "")
			{
				echo "</table>";
			}
			echo "<div style=\"background-color: #000000;font-weight: bold;padding: 1px 0px 1px 2px;margin-top: 3px;\">".$rowPalmares["nom"]."</div>";
			echo "<table style=\"width: 100%\">";
			
			$id_nom_champ = $rowPalmares["nom"];
		}
		echo "<tr><td>Classement ".$rowPalmares["type"]."</td><td>";
		if($rowPalmares["place"] == 1)
		{
			echo "&nbsp;<img src=\"./images/etoile-or-petit.png\" border=\"0\" />";
		}
		else if($rowPalmares["place"] == 2)
		{
			echo "&nbsp;<img src=\"./images/etoile-argent-petit.png\" border=\"0\" />";
		}
		else if($rowPalmares["place"] == 3)
		{
			echo "&nbsp;<img src=\"./images/etoile-bronze-petit.png\" border=\"0\" />";
		}
		echo "<td style=\"width: 20px;text-align:right;\">".$rowPalmares["place"]."</td></tr>";
	}
	if($id_nom_champ != "")
	{
		echo "</table>";
	}
	
	echo "</fieldset>";
	echo "</div>";
?>
