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
			$data["url_logo"] = "http://".$_SERVER['SERVER_NAME']."/suopronos/prono/images/clubs/".$rowInfosUser["url_logo"];
			$data["club_favori"] = $rowInfosUser["nom_club"];
		}

		$response->body = json_encode(array("profil" => $data));

        return $response;
        
    }
     
}

?>

