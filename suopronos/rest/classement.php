<?php
  
/**
 * An example resource
 * @uri /classement/(.*)?
 */
class ClassementResource extends Resource {
    
	// Classement avec pris en compte du filtre (yes/no) -> ancienne version (à déprécier)
    function get($request, $type) {
        
        $response = new Response($request);
        $response->code = Response::OK;
        $response->addHeader('content-type', 'text/plain');

        $data = array();

		ouverture ();

		$requete="SELECT phpl_gr_championnats.id FROM phpl_gr_championnats WHERE phpl_gr_championnats.activ_prono='1' ORDER by id desc";
		$resultat=mysql_query ($requete) or die ("probleme " .mysql_error());
		$row= mysql_fetch_array($resultat);
		 
		$gr_champ=$row[0];

		$query="SELECT pseudo, points, participation as champion FROM phpl_clmnt_pronos
					WHERE id_champ='$gr_champ' AND type='$type'
					ORDER by points desc, participation asc, pseudo";
		$result=mysql_query($query) or die ("probleme " .mysql_error());
		$i=1;

       	while ($row=mysql_fetch_array($result))
       	{
       		array_push($data, array("place" => $i, "pseudo" => $row["pseudo"], "points" => $row["points"]));
		    $i++;
       	}

		$response->body = json_encode(array("classement" => $data));

        return $response;
        
    }

    
    // Classement avec pris en compte du filtre (yes/no) -> nouvelle version
    function get($request, $type, $user, $filtre) {
	    
	    $response = new Response($request);
	    $response->code = Response::OK;
	    $response->addHeader('content-type', 'text/plain');
	    
	    $data = array();
	    
	    ouverture ();
	    
	    $requete="SELECT phpl_gr_championnats.id FROM phpl_gr_championnats WHERE phpl_gr_championnats.activ_prono='1' ORDER by id desc";
	    $resultat=mysql_query ($requete) or die ("probleme " .mysql_error());
	    $row= mysql_fetch_array($resultat);
	    	
	    $gr_champ=$row[0];
	    
	    if ($filtre == "no" || $filtre == "") {
		    $query="SELECT pseudo, points, participation as champion 
		    		FROM phpl_clmnt_pronos
		    		WHERE id_champ='$gr_champ' AND type='$type'
		    		ORDER by points desc, participation asc, pseudo";
	    } else {
	    	$query="SELECT classement.pseudo, classement.points, classement.participation as champion
	    			FROM phpl_membres membre
	    			JOIN phpl_clmnt_filtre filtre ON filtre.id = membre.id_prono
	    			JOIN phpl_clmnt_pronos classement ON classement.id_champ='$gr_champ' AND classement.type='$type'
	    			WHERE membre.pseudo='$user'
	    			ORDER by points desc, participation asc, pseudo";
	    }
	    $result=mysql_query($query) or die ("probleme " .mysql_error());
	    $i=1;
	    
	    while ($row=mysql_fetch_array($result)) {
	    	array_push($data, array("place" => $i, "pseudo" => $row["pseudo"], "points" => $row["points"]));
	    	$i++;
	    }
	    
	    $response->body = json_encode(array("classement" => $data));
	    
	    return $response;
    
    }
    
}

?>

