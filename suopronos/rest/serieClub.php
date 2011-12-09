<?php
  
/**
 * An example resource
 * @uri /serieClub/(.*)?
 * 
 */
class SerieClubResource extends Resource {
	
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

	    $response->code = Response::OK;
	    $response->addHeader('content-type', 'text/plain');
	    
	    $data = array();
	    	    
	    $requete = "SELECT phpl_gr_championnats.id_champ FROM phpl_gr_championnats WHERE phpl_gr_championnats.activ_prono='1' ORDER by id desc";
	    $resultat = mysql_query ($requete) or die ("probleme " .mysql_error());
	    $row = mysql_fetch_array($resultat);
	    	
	    $gr_champ = $row[0];
	    
	    $query = "SELECT phpl_matchs.buts_dom, phpl_matchs.buts_ext, cldom.nom as clubDom, clext.nom as clubExt
	    			FROM phpl_journees 
			        JOIN phpl_matchs ON phpl_matchs.id_journee = phpl_journees.id
							        AND phpl_matchs.buts_dom is not null
							        AND phpl_matchs.buts_ext is not null
			        JOIN phpl_equipes as dom ON dom.id = phpl_matchs.id_equipe_dom
			        JOIN phpl_equipes as ext ON ext.id = phpl_matchs.id_equipe_ext
			        JOIN phpl_clubs as cldom ON cldom.id = dom.id_club
			        JOIN phpl_clubs as clext ON clext.id = ext.id_club
			        WHERE phpl_journees.id_champ = '$gr_champ'
			        	AND (cldom.nom = '$club' OR clext.nom = '$club')
			        ORDER BY phpl_journees.numero DESC
			        LIMIT 0,5";
	    $result = mysql_query($query) or die ("probleme " .mysql_error());
	    
	    while ($row=mysql_fetch_array($result)) {
	    	array_push($data, array("butDom" => $row["buts_dom"], 
	    							"butExt" => $row["buts_ext"],
	    							"clubDom" => $row["clubDom"],
	    							"clubExt" => $row["clubExt"]));
	    }
	    
	    $response->body = json_encode(array("serieClub" => $data));
	    
	    return $response;
    
    }
    
}

?>

