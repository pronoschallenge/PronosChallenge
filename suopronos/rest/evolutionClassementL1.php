<?php
  
/**
 * An example resource
 * @uri /evolutionClassementL1/(.*)?
 * 
 */
class EvolutionClassementL1Resource extends Resource {
	
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
	    
    // Evolution du classement L1 pour 1 club
    function get($request, $club) {
	    
	    $response = new Response($request);
	    
		ouverture ();

	    $response->code = Response::OK;
	    $response->addHeader('content-type', 'text/plain');
	    
	    $data = array();
	    
	    $requete = "SELECT phpl_gr_championnats.id_champ FROM phpl_gr_championnats WHERE phpl_gr_championnats.activ_prono='1' ORDER by id desc";
	    $resultat = mysql_query ($requete) or die ("probleme " .mysql_error());
	    $row = mysql_fetch_array($resultat);
	    	
	    $gr_champ = $row[0];
	    
	    $query = "SELECT evol.fin, evol.classement
	    			FROM phpl_clubs club 
	    			JOIN phpl_equipes equipe On equipe.id_club = club.id
	    			                        And equipe.id_champ = '$gr_champ' 
	    			JOIN phpl_clmnt_graph evol On evol.id_equipe = equipe.id
			        WHERE club.nom = '$club'  
			        ORDER BY evol.fin";
	    $result = mysql_query($query) or die ("probleme " .mysql_error());
	    
	    while ($row=mysql_fetch_array($result)) {
	    	array_push($data, array("jour" => $row["fin"], "place" => $row["classement"]));
	    }
	    
	    $response->body = json_encode(array("evolutionClassementL1" => $data));
	    
	    return $response;
    
    }
    
}

?>

