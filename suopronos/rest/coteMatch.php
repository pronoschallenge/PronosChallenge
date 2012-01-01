<?php
  
/**
 * 
 * Cette fonction retourne les cotes d'un match ainsi que l'image du type de match
 * 
 * An example resource
 * @uri /coteMatch/(.*)?
 * 
 */
class CoteMatchResource extends Resource {
	
	function isSecured() {
	// maybe we have caught authentication data in $_SERVER['REMOTE_USER']
		if((!$_SERVER['PHP_AUTH_USER'] || !$_SERVER['PHP_AUTH_PW']) && preg_match('/Basic+(.*)$/i', $_SERVER['REMOTE_USER'], $matches)) {
			list($name, $password) = explode(':', base64_decode($matches[1]));
			$_SERVER['PHP_AUTH_USER'] = strip_tags($name);
			$_SERVER['PHP_AUTH_PW'] = strip_tags($password);
		}

		return (isset($_SERVER['PHP_AUTH_USER'])
			&& isset($_SERVER['PHP_AUTH_PW'])
			&& VerifSession($_SERVER['PHP_AUTH_USER'],$_SERVER['PHP_AUTH_PW'])=="1");
	}
	    
    function get($request, $idMatch) {
	    
	    $response = new Response($request);
	    
		ouverture ();

	    $response->code = Response::OK;
	    $response->addHeader('content-type', 'text/plain');
	    
	    $data = array();

	    $requete = "SELECT max(id) FROM phpl_championnats";
	    $resultat = mysql_query ($requete) or die ("probleme " .mysql_error());
	    $row = mysql_fetch_array($resultat);	    	
	    $gr_champ = $row[0];
	    
	    // Recherche du nombre de points max
	    $requete = "SELECT pts_prono_exact FROM phpl_gr_championnats WHERE id_champ = '$gr_champ'";
	    $resultat = mysql_query ($requete) or die ("probleme " .mysql_error());
	    $row = mysql_fetch_array($resultat);
	    $points_prono_exact = $row[0];
	    
	    // si il y des pronos automatiques à faire sur ce match, on calcule les cotes
	    //On compte le nombre de parieurs sur le match
	    $requete = "SELECT COUNT(*) FROM phpl_pronostics WHERE id_match = '$idMatch'";
	    $resultat = mysql_query ($requete) or die ("probleme " .mysql_error());
	    $row = mysql_fetch_array($resultat);
	    $nb_parieurs_total = $row[0];

	    // Retour au format JSON
	    for ($i = 0; $i < 3; $i++) {
	    	
	    	switch ($i) {
	    		case 0:
	    			$type_prono = "1";
	    			break;
	    		case 1:
	    			$type_prono = "N";
	    			break;
	    		case 2:
	    			$type_prono = "2";
	    			break;
	    	}

	    	//On compte le nombre de parieurs sur une victoire de l'equipe à l'exterieur
	    	$requete = "SELECT COUNT(*) FROM phpl_pronostics WHERE id_match = '$idMatch' AND pronostic = '$type_prono'";
	    	$resultat = mysql_query ($requete) or die ("probleme " .mysql_error());
	    	$row = mysql_fetch_array($resultat);
	    	$nb_parieurs = $row[0];
	    	
	    	if ($nb_parieurs == "0") {
	    		$points_prono = "0";
	    	} else {
	    		$points_prono = floor(($points_prono_exact*$nb_parieurs_total)/$nb_parieurs);
	    	}	    	
	    	
	    	array_push($data, array("type" => $type_prono,
    								"cote" => $points_prono));

	    }
	    
	    $response->body = json_encode(array("coteMatch" => $data));

	    return $response;
    
    }
    
}

?>

