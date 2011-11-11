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

    function post($request, $user) {
        
        $response = new Response($request);
        $response->code = Response::OK;
        $response->addHeader('content-type', 'text/plain');

		$data = array();

		ouverture ();

		if(!($this->isSecured($user))) {
			$response->body = "401";
			return $response;
		}

		// On vérifie s'il y a des matchs en cours pour savoir si on autrosie la mise à jour de la strategie
		$requete="SELECT phpl_matchs.id, phpl_matchs.date_reelle, TIMEDIFF( phpl_matchs.date_reelle, NOW( ) ) , TIME_TO_SEC( TIMEDIFF( phpl_matchs.date_reelle, NOW( ) ) )
			FROM phpl_matchs, phpl_journees, phpl_gr_championnats
			WHERE phpl_gr_championnats.id = $gr_champ
			AND phpl_journees.id_champ = phpl_gr_championnats.id_champ
			AND phpl_matchs.id_journee = phpl_journees.id
			AND phpl_matchs.buts_dom IS NULL
			AND phpl_matchs.buts_ext IS NULL
			AND TIME_TO_SEC( TIMEDIFF( phpl_matchs.date_reelle, NOW( ) ) ) < 0";
		$resultat = mysql_query($requete);
		if ($row=mysql_fetch_array($resultat))
		{
			$response->body = "La mise à jour de votre stratégie n'est pas autorisée quand des match sont en cours";
			return $response;
		}

		// récupération de la nouvelle stratégie
		$strategie = file_get_contents('php://input');
		if(empty($strategie)) {
			$response->body = "500:NO_DATA";
			return $response;
		}

		$user_pseudo = $_SERVER['PHP_AUTH_USER'];
        
		$requete= "SELECT pseudo, id_prono FROM phpl_membres WHERE pseudo='$user_pseudo'";
		$result = mysql_query($requete);
		$row = mysql_fetch_array($result);
		$user_id=$row[1];

		// mise à jour de la stratégie
		$requete = "SELECT * FROM phpl_strategie WHERE id_membre='$user_id'";
		$resultat = mysql_query($requete);
		if($row= mysql_fetch_array($resultat))
		{
			$strQuery = "UPDATE phpl_strategie SET id_type=$strategie, priorite=0 WHERE id_membre='$user_id'";
			mysql_query($strQuery) or die ("probleme " .mysql_error());
		}
		else
		{
			$strQuery = "INSERT INTO phpl_strategie (id_membre, id_type, priorite) VALUES ($user_id, $strategie, 0)";
			mysql_query($strQuery) or die ("probleme " .mysql_error());			
		}

        return $response;
        
    }
     
}

?>

