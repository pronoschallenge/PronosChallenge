<?php
  
/**
 *  
 * Permet de lister les informations pour les championnats en cours d'un utilisateur
 *    - classement
 *    - nombre de points sur la dernière journée
 *    - évolution sur la dernière journée
 *
 * An example resource
 * @uri /profilStat/(.*)?    
 *    
 */
class ProfilStatResource extends Resource {
    
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
		
		// recherche de l'id de la dernière journée
		$queryDerniereJournee = "SELECT max(evolutionGraph.fin)
								FROM phpl_pronos_graph evolutionGraph
								WHERE evolutionGraph.id_gr_champ = '$idSaisonEnCours'";
		$resultat = mysql_query($queryDerniereJournee);
		$row = mysql_fetch_array($resultat);
		$idDerniereJournee = $row[0];		

		// évolution sur la dernière journée
		$queryEvolution = 	"SELECT P2.type, P2.classement as place, (P2.points - P1.points) as points, (P1.classement - P2.classement) as evolution
    						 FROM phpl_membres membre
				             JOIN phpl_pronos_graph P1 ON P1.id_membre = membre.id_prono
				             						  AND P1.id_gr_champ = '$idSaisonEnCours'
				             						  AND P1.type in ('general', 'hourra', 'mixte')
							   						  AND P1.fin = '".($idDerniereJournee-1)."'		             						  
				             JOIN phpl_pronos_graph P2 ON P2.id_membre = P1.id_membre
				             						  AND P2.id_gr_champ = P1.id_gr_champ
				             						  AND P2.type = P1.type
				               						  AND P2.fin = '$idDerniereJournee'
				             WHERE pseudo = '$user'		             						  	             
							 ORDER BY P2.type";				
		
		$resultat = mysql_query ($queryEvolution) or die ("probleme " .mysql_error());
		
		while ($row = mysql_fetch_array($resultat))
		{

			$typeChamp = $row["type"];
			$numPlace = $row['place'];
			$nbPoints = $row['points'];
			$numEvolution = $row["evolution"];
				
			array_push($data, array("type" => $typeChamp, "place" => $numPlace, "points" => $nbPoints, "evolution" => $numEvolution));
			
		}


		// Retour du tableau au format JSON
		$response->body = json_encode(array("profilStat" => $data));

        return $response;
        
    }
    
}

?>

