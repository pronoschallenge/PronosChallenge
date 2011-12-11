<?php
  
/**
 * 
 * Cette fonction retourne les informations d'un club
 *   - nom
 *   - classement L1 
 *   - logo
 * 
 * An example resource
 * @uri /infoClub/(.*)?
 * 
 */
class InfoClubResource extends Resource {
	
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
	    
    function get($request, $club) {
	    
	    $response = new Response($request);
	    
		ouverture ();

	    $response->code = Response::OK;
	    $response->addHeader('content-type', 'text/plain');
	    
	    $data = array();
	    	    
	    $requete = "SELECT max(id) FROM phpl_championnats";
	    $resultat = mysql_query ($requete) or die ("probleme " .mysql_error());
	    $row = mysql_fetch_array($resultat);
	    	
	    $gr_champ = $row[0];

	    $query = "SELECT club.nom, club.url_logo
    			FROM phpl_clubs club 
	    		WHERE club.nom = '$club'";
	    $result = mysql_query($query) or die ("probleme " .mysql_error());

	    while ($row = mysql_fetch_array($result)) {
			
			$queryPlace = "SELECT rownum FROM (
							    SELECT @rownum:=@rownum+1 rownum, classement.*
							    FROM (SELECT @rownum:=0) r, phpl_clmnt_cache classement
							    WHERE ID_CHAMP = '$gr_champ'
							    ORDER BY POINTS DESC, DIFF DESC, BUTSPOUR DESC , BUTSCONTRE ASC, NOM) tt
						    WHERE tt.nom = '$club'";
			$resultPlace = mysql_query ($queryPlace) or die ("probleme " .mysql_error());
			$rowPlace = mysql_fetch_array($resultPlace);				
						    
			array_push($data, array("club" => $row["nom"], 
									"place" => $rowPlace[0], 
									"url_logo" => "http://".$_SERVER['SERVER_NAME']."/suopronos/prono/images/clubs/hdpi/".rawurlencode($row["url_logo"])));
	    }
	    
	    $response->body = json_encode(array("infoClub" => $data));
	    
	    return $response;
    
    }
    
}

?>

