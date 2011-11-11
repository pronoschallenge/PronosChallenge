<?php
  
/**
 * An example resource
 * @uri /palmares/(.*)?
 */
class PalmaresResource extends Resource {
    
    function get($request, $user) {
        
        $response = new Response($request);
        $response->code = Response::OK;
        $response->addHeader('content-type', 'text/plain');

        $data = array();

		ouverture ();
		
		// recherche de l'id de l'utilisateur
		$queryUser = "SELECT id_prono FROM phpl_membres WHERE pseudo='$user'";
		$resultat = mysql_query($queryUser);
		$row = mysql_fetch_array($resultat);
		$idUser = $row[0];
		
		// palmarès de l'utilisateur
		$queryPalmares = "SELECT clmnt.id_champ, clmnt.place, clmnt.type, groupes.nom
							FROM phpl_clmnt_pronos clmnt
							JOIN phpl_gr_championnats groupes On groupes.id = clmnt.id_champ
															 AND groupes.activ_prono = '0'
							WHERE clmnt.id_membre = $idUser
								AND clmnt.type IN ('general', 'hourra', 'mixte')
							ORDER BY clmnt.id_champ DESC, clmnt.type ASC";
		$resultat = mysql_query ($queryPalmares) or die ("probleme " .mysql_error());

		// Remplissage du tableau avec le palmarès de l'utilisateur
	    while ($row = mysql_fetch_array($resultat))
	    {
			$numPlace = $row["place"];
			$typeChamp = $row["type"];
			$nomSaison = $row["nom"];
			
       		array_push($data, array("nomSaison" => $nomSaison, "typeChamp" => $typeChamp, "numPlace" => $numPlace));
		}

		// Retour du tableau au format JSON
		$response->body = json_encode(array("palmares" => $data));

        return $response;
        
    }
    
}

?>

