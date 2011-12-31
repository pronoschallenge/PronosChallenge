<?php
  
/**
 * Permet de rechercher dans l'historique l'ensemble des matchs entre 2 clubs
 * 
 * An example resource
 * @uri /confrontationClub/(.*)?
 * 
 */
class ConfrontationClubResource extends Resource {
	
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
	    
    // Serie en cours du club en paramètre (5 derniers matchs)
    function get($request, $club) {
	    
	    $response = new Response($request);
	    
		ouverture ();
		
		if(isset($_GET['clubAdverse']))  {
			$clubAdverse = $_GET['clubAdverse']; 
		} else {
			$response->body = "401";
			return $response;
		}
		
	    $response->code = Response::OK;
	    $response->addHeader('content-type', 'text/plain');
	    
	    $query = "SELECT cldom.nom as clubDom, clext.nom as clubExt, phpl_matchs.buts_dom, phpl_matchs.buts_ext
	    			FROM phpl_gr_championnats champ
	    			JOIN phpl_journees journee on journee.id_champ = champ.id_champ 
			        JOIN phpl_matchs ON phpl_matchs.id_journee = journee.id
							        AND phpl_matchs.buts_dom is not null
							        AND phpl_matchs.buts_ext is not null
			        JOIN phpl_equipes as dom ON dom.id = phpl_matchs.id_equipe_dom
			        JOIN phpl_equipes as ext ON ext.id = phpl_matchs.id_equipe_ext
			        JOIN phpl_clubs as cldom ON cldom.id = dom.id_club
			        JOIN phpl_clubs as clext ON clext.id = ext.id_club
			        WHERE (cldom.nom = '$club' AND clext.nom = '$clubAdverse')
			           OR (cldom.nom = '$clubAdverse' AND clext.nom = '$club')
			        ORDER BY journee.id DESC";
	    $result = mysql_query($query) or die ("probleme " .mysql_error());
	    
	    $data = array();
	    
	    while ($row = mysql_fetch_array($result)) {
	    	array_push($data, array("clubDom" => $row["clubDom"],
	    							"butDom" => $row["buts_dom"], 
	    							"clubExt" => $row["clubExt"],
	    							"butExt" => $row["buts_ext"]));
	    }
	    
	    $response->body = json_encode(array("confrontationClub" => $data));
	    
	    return $response;
    
    }
    
}

?>

