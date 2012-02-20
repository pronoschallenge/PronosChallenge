<?php
  
/**
 * An example resource
 * @uri /calendrierClub/(.*)?
 * 
 */
class CalendrierClubResource extends Resource {
	
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
	    
	    $query = "SELECT numJour.numero, rencontre.date_reelle, rencontre.buts_dom, rencontre.buts_ext, 
	    				Case When cldom.nom = '$club' then cldom.nom_court else cldom.nom end as clubDom, 
	    				Case When clext.nom = '$club' then clext.nom_court else clext.nom end as clubExt,
	    				Case When cldom.nom = '$club' then 'D' else 'E' end as type
	    			FROM phpl_journees as numJour
			        JOIN phpl_matchs as rencontre ON rencontre.id_journee = numJour.id
			        JOIN phpl_equipes as dom ON dom.id = rencontre.id_equipe_dom
			        JOIN phpl_equipes as ext ON ext.id = rencontre.id_equipe_ext
			        JOIN phpl_clubs as cldom ON cldom.id = dom.id_club
			        JOIN phpl_clubs as clext ON clext.id = ext.id_club
			        WHERE numJour.id_champ = '$gr_champ'
			        	AND (cldom.nom = '$club' OR clext.nom = '$club')
			        ORDER BY numJour.numero";
	    $result = mysql_query($query) or die ("probleme " .mysql_error());
	    
	    while ($row=mysql_fetch_array($result)) {
	    	array_push($data, array("numJour" => $row["numero"],
	    							"date" => $row["date_reelle"],
	    							"butDom" => $row["buts_dom"], 
	    							"butExt" => $row["buts_ext"],
	    							"clubDom" => $row["clubDom"],
	    							"clubExt" => $row["clubExt"],
	    							"type" => $row["type"]));
	    }
	    
	    $response->body = json_encode(array("calendrierClub" => $data));
	    
	    return $response;
    
    }
    
}

?>

