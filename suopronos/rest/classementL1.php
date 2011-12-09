<?php
  
/**
 * An example resource
 * @uri /classementL1/
 * 
 */
class ClassementL1Resource extends Resource {
	
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
	    
    // Classement de la L1
    function get($request) {
	    
	    $response = new Response($request);
	    
		ouverture ();

	    $response->code = Response::OK;
	    $response->addHeader('content-type', 'text/plain');
	    
	    $data = array();
	    	    
	    $requete = "SELECT max(id) FROM phpl_championnats";
	    $resultat = mysql_query ($requete) or die ("probleme " .mysql_error());
	    $row = mysql_fetch_array($resultat);
	    	
	    $gr_champ = $row[0];
	    
	    $query = "SELECT classement.nom, points, joues, g, n, p, butspour, butscontre, diff, club.url_logo 
    			FROM phpl_clmnt_cache classement
    			JOIN phpl_clubs club ON club.nom = classement.nom
	    		WHERE ID_CHAMP='$gr_champ' 
	    		ORDER BY POINTS DESC, DIFF DESC, BUTSPOUR DESC , BUTSCONTRE ASC, classement.NOM";
	    $result = mysql_query($query) or die ("probleme " .mysql_error());
	    
	    while ($row = mysql_fetch_array($result)) {
	    	
			array_push($data, array("club" => $row["nom"], 
									"points" => $row["points"], 
									"joues" => $row["joues"], 
									"g" => $row["g"], 
									"n" => $row["n"],
									"p" => $row["p"], 
									"butspour" => $row["butspour"],
									"butscontre" => $row["butscontre"], 
									"diff" => $row["diff"], 
									"url_logo" => "http://".$_SERVER['SERVER_NAME']."/suopronos/prono/images/clubs/".rawurlencode($row["url_logo"])));
	    }
	    
	    $response->body = json_encode(array("classementL1" => $data));
	    
	    return $response;
    
    }
    
}

?>

