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
		
		// Saison en cours de l'utilisateur + palmarès de l'utilisateur
		$queryPalmares =   "SELECT classement.id_champ, classement.place, classement.type, 
								case when groupes.activ_prono = '0' then groupes.nom else 'Saison en cours' end as nom
							FROM phpl_clmnt_pronos classement
							JOIN phpl_gr_championnats groupes ON groupes.id = classement.id_champ
							WHERE classement.id_membre = $idUser
								AND classement.type IN ('general', 'hourra', 'mixte')
							ORDER BY classement.id_champ DESC, classement.type ASC";
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

