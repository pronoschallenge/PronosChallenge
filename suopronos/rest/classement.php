<?php
  
/**
 * An example resource
 * @uri /classement/(.*)?
 * 
 */
class ClassementResource extends Resource {
	
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
	    
    // Classement avec prise en compte du filtre (yes/no) -> nouvelle version
    function get($request, $type) {
	    
	    $response = new Response($request);
	    
		ouverture ();

	    $response->code = Response::OK;
	    $response->addHeader('content-type', 'text/plain');
	    
	    $data = array();
	    
	    if(isset($_GET['filtre']))  { 
	    	$filtre = $_GET['filtre']; 
			if($filtre == "1") {
				if(!($this->isSecured())) {
					$response->body = "401";
					return $response;
				} else {
					$user = $_SERVER['PHP_AUTH_USER'];
				}
			} else {
				$filtre = "0";
			}
	    } else {
	    	$filtre = "0";
	    }
	    	    
	    $requete = "SELECT phpl_gr_championnats.id FROM phpl_gr_championnats WHERE phpl_gr_championnats.activ_prono='1' ORDER by id desc";
	    $resultat = mysql_query ($requete) or die ("probleme " .mysql_error());
	    $row = mysql_fetch_array($resultat);
	    	
	    $gr_champ = $row[0];
	    
	    if ($filtre == "0") {
		    $query = "SELECT classement.pseudo, classement.points, classement.participation as champion, 
		    				 classement.place
		    		  FROM phpl_membres membre 
		    		  JOIN phpl_clmnt_pronos classement ON classement.id_membre = membre.id_prono
		    		  								   AND classement.id_champ = '$gr_champ'
		    		  								   AND classement.type = '$type'
		    		  WHERE membre.actif = '1' 
		    		  ORDER by classement.points desc, classement.participation asc, classement.pseudo";
	    } else {
	    	$query = "SELECT classement.pseudo, classement.points, classement.participation as champion,
	    					 classement.place
	    			  FROM phpl_membres membre
	    			  JOIN phpl_clmnt_filtre filtre ON filtre.id = membre.id_prono
	    			  JOIN phpl_clmnt_pronos classement ON classement.id_champ = '$gr_champ' 
	    			  								   AND classement.type = '$type' 
	    				  							   AND classement.id_membre = filtre.idMembre
	    			  WHERE membre.pseudo = '$user' AND membre.actif = '1'
	    			  ORDER by classement.points desc, classement.participation asc, classement.pseudo";
	    }
	    $result = mysql_query($query) or die ("probleme " .mysql_error());
	    
	    while ($row=mysql_fetch_array($result)) {
	    	array_push($data, array("place" => $row["place"], "pseudo" => $row["pseudo"], "points" => $row["points"]));
	    }
	    
	    $response->body = json_encode(array("classement" => $data));
	    
	    return $response;
    
    }
    
}

?>

