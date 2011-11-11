<?php
  
/**
 * An example resource
 * @uri /profil/(.*)?
 */
class ProfilResource extends Resource {
 
    function isSecured() {
		// maybe we have caught authentication data in $_SERVER['REMOTE_USER']
		if((!$_SERVER['PHP_AUTH_USER'] || !$_SERVER['PHP_AUTH_PW']) && preg_match('/Basic+(.*)$/i', $_SERVER['REMOTE_USER'], $matches)) {
			list($name, $password) = explode(':', base64_decode($matches[1]));
			$_SERVER['PHP_AUTH_USER'] = strip_tags($name);
			$_SERVER['PHP_AUTH_PW'] = strip_tags($password);
		}
		
		/* 
        if(!(isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW']) && VerifSession($_SERVER['PHP_AUTH_USER'],$_SERVER['PHP_AUTH_PW'])=="1")) {
			throw new ResponseException("message!", Response::UNAUTHORIZED);
		}
		*/
		return (isset($_SERVER['PHP_AUTH_USER']) 
					&& isset($_SERVER['PHP_AUTH_PW']) 
					&& VerifSession($_SERVER['PHP_AUTH_USER'],$_SERVER['PHP_AUTH_PW'])=="1");
    }
    
    function get($request, $user) {
        
        $response = new Response($request);
        $response->code = Response::OK;
        $response->addHeader('content-type', 'text/plain');

        $data = array();

		ouverture ();

		$queryIdUser = "SELECT pseudo, id_prono FROM phpl_membres WHERE pseudo='$user'";
		$result = mysql_query($queryIdUser);
		$row = mysql_fetch_array($result);
		$user_id=$row[1];

		$queryInfosUser="SELECT clmnt.id_membre, membre.pseudo,
			membre.nom as nom,  membre.prenom,  membre.ville, membre.departement, membre.id_club_favori, 
			club.nom as nom_club, club.url_logo 
			FROM phpl_clmnt_pronos as clmnt, phpl_membres as membre, phpl_clubs as club
			WHERE membre.id=$user_id
			AND membre.id=clmnt.id_membre
			AND membre.actif='1'
			AND (membre.id_club_favori IS NULL OR club.id = membre.id_club_favori)
			GROUP by clmnt.pseudo
			ORDER by  clmnt.points desc, clmnt.participation asc, membre.pseudo";
		$resultInfosUser=mysql_query($queryInfosUser) or die ("probleme " .mysql_error());

		if ($rowInfosUser=mysql_fetch_array($resultInfosUser))
		{
			$data["id_membre"] = $rowInfosUser["id_membre"];
			$data["url_avatar"] = "http://".$_SERVER['SERVER_NAME']."/suopronos/prono/images/avatars/".$rowInfosUser["id_membre"].".gif";
			$data["nom"] = $rowInfosUser["nom"];
			$data["prenom"] = $rowInfosUser["prenom"];
			$data["ville"] = $rowInfosUser["ville"];
			$data["departement"] = $rowInfosUser["departement"];
			$data["url_logo"] = "http://".$_SERVER['SERVER_NAME']."/suopronos/prono/images/clubs/".rawurlencode($rowInfosUser["url_logo"]);
			$data["club_favori"] = $rowInfosUser["nom_club"];
		}

		/*
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
		*/








		$response->body = json_encode(array("profil" => $data));

        return $response;
        
    }
     
}

?>

