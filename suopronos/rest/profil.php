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

		$queryInfosUser="SELECT clmnt.id_membre, membre.pseudo,
			membre.nom as nom,  membre.prenom,  membre.ville, membre.departement, membre.id_club_favori, 
			club.nom as nom_club, club.url_logo
			FROM phpl_membres as membre
			JOIN phpl_clmnt_pronos as clmnt ON clmnt.id_membre = membre.id
			LEFT JOIN phpl_clubs as club ON club.id = membre.id_club_favori
			WHERE membre.pseudo = '$user'
			  AND membre.actif = '1'
			GROUP by clmnt.pseudo
			ORDER by  clmnt.points desc, clmnt.participation asc, membre.pseudo";
		$resultInfosUser=mysql_query($queryInfosUser) or die ("probleme " .mysql_error());

		if ($rowInfosUser=mysql_fetch_array($resultInfosUser))
		{
			$data["id_membre"] = $rowInfosUser["id_membre"];
			$data["url_avatar"] = "http://".$_SERVER['SERVER_NAME']."/suopronos/prono/images/avatars/".$rowInfosUser["id_membre"].".gif";
			$data["nom"] = utf8_encode($rowInfosUser["nom"]);
			$data["prenom"] = utf8_encode($rowInfosUser["prenom"]);
			$data["ville"] = utf8_encode($rowInfosUser["ville"]);
			$data["departement"] = $rowInfosUser["departement"];
			$data["url_logo"] = "http://".$_SERVER['SERVER_NAME']."/suopronos/prono/images/clubs/".rawurlencode($rowInfosUser["url_logo"]);
			$data["club_favori"] = $rowInfosUser["nom_club"];
		}

		$response->body = json_encode(array("profil" => $data));

        return $response;
        
    }
     
}

?>

