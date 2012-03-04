<?php
  
/**
 *  
 * Permet de lister les informations pour les championnats en cours d'un utilisateur
 *    - classement
 *    - nombre de points sur la dernière journée
 *    - évolution sur la dernière journée
 *
 * An example resource
 * @uri /profilEvolution/(.*)?    
 *    
 */
class ProfilEvolutionResource extends Resource {
    
    function get($request, $user) {
        
        $response = new Response($request);
        $response->code = Response::OK;
        $response->addHeader('content-type', 'text/plain');

        $data = array();

		ouverture ();
		
		// recherche du championnat en cours
		$querySaisonEnCours = 	"SELECT phpl_gr_championnats.id
								FROM phpl_gr_championnats 
								WHERE phpl_gr_championnats.activ_prono = '1' 
								ORDER by id desc";
		$resultat = mysql_query ($querySaisonEnCours) or die ("probleme " .mysql_error());
		$row = mysql_fetch_array($resultat);
		$idSaisonEnCours = $row[0];

		// évolution sur la dernière journée
		$queryEvolution = 	"SELECT graph.type, graph.classement as place, graph.fin
    						 FROM phpl_membres membre
				             JOIN phpl_pronos_graph graph ON graph.id_membre = membre.id_prono
				             						  AND graph.id_gr_champ = '$idSaisonEnCours'
				             						  AND graph.type in ('general', 'hourra', 'mixte')
				             WHERE membre.pseudo = '$user'		             						  	             
							 ORDER BY graph.type, graph.fin";				
		
		$resultat = mysql_query ($queryEvolution) or die ("probleme " .mysql_error());
		
		while ($row = mysql_fetch_array($resultat))
		{

			$typeChamp = $row["type"];
			$numPlace = $row['place'];
			$numJournee = $row['fin'];
				
			array_push($data, array("type" => $typeChamp, "place" => $numPlace, "jour" => $numJournee));
			
		}


		// Retour du tableau au format JSON
		$response->body = json_encode(array("profilEvolution" => $data));

        return $response;
        
    }
    
}

?>

