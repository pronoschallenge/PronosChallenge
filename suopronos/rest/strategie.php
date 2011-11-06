<?php
  
/**
 * An example resource
 * @uri /strategie/(.*)?
 */
class StrategieResource extends Resource {
 
    function isSecured($user) {
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
					&& $user == $_SERVER['PHP_AUTH_USER'] 
					&& VerifSession($_SERVER['PHP_AUTH_USER'],$_SERVER['PHP_AUTH_PW'])=="1");
    }
    
    function get($request, $user) {
        
        $response = new Response($request);
        $response->code = Response::OK;
        $response->addHeader('content-type', 'text/plain');

		$data = array();

		ouverture ();

		if(!($this->isSecured($user))) {
			$response->body = "401";
			return $response;
		}

		$user_pseudo = $_SERVER['PHP_AUTH_USER'];
        
		$requete= "SELECT pseudo, id_prono FROM phpl_membres WHERE pseudo='$user_pseudo'";
		$result = mysql_query($requete);
		$row = mysql_fetch_array($result);
		$user_id=$row[1];

		// stratégie globale de l'utilisateur
		$requete = "SELECT * FROM phpl_strategie WHERE id_membre=$user_id";
		$resultat = mysql_query($requete);
		if ($row= mysql_fetch_array($resultat))
		{
			$userstraglob = $row['id_type'];
		}

		$response->body = json_encode(array("strategie" => $userstraglob));

        return $response;
        
    }
     
}

?>

