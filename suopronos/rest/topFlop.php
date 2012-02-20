<?php
  
/**
 * An example resource
 * 
 *  Méthode GET :
 *  Parm obligatoire :
 *   - "typeEvol" = top="0" OU flop="1"
 *  Param facultatif 
 *   - "nbUser" = nb top/flop par classement (3 par défaut)
 *    
 * @uri /topFlop/(.*)?
 * 
 */
class TopFlopResource extends Resource {
    
    function get($request, $typeChamp) {
        
        $response = new Response($request);
        $response->code = Response::OK;
        $response->addHeader('content-type', 'text/plain');

        $data = array();

		ouverture ();
		
    	// récupération du nombre d'utilisateur en retour par top/flop/classement
	    if(isset($_GET['nbUser']))  { 
	    	$nbUser = $_GET['nbUser']; 
	    } else {
	    	$nbUser = 3;
	    }
		
    	// récupération du type d'évolution
	    if(isset($_GET['typeEvol']))  { 
	    	$typeEvol = $_GET['typeEvol']; 
	    } else {
	    	$typeEvol = "0";
	    }
		
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
		
		$queryEvolution = 	"SELECT P1.type, (P1.classement - P2.classement) as evolution, membre.pseudo
							 FROM phpl_membres membre
				             JOIN phpl_pronos_graph P1 ON P1.id_membre = membre.id_prono
				             						  AND P1.id_gr_champ = '$idSaisonEnCours'
				             						  AND P1.type = '$typeChamp'
							   						  AND P1.fin = '".($idDerniereJournee-1)."'		             						  
				             JOIN phpl_pronos_graph P2 ON P2.id_membre = P1.id_membre
				             						  AND P2.id_gr_champ = P1.id_gr_champ
				             						  AND P2.type = P1.type
				               						  AND P2.fin = '$idDerniereJournee'
				             WHERE membre.actif = '1'";
		if ($typeEvol == "0") {
			$queryEvolution = $queryEvolution . " ORDER BY P1.type, evolution DESC, P2.participations DESC, membre.pseudo
												  LIMIT 0, $nbUser";
		} else {
			$queryEvolution = $queryEvolution . " ORDER BY P1.type, evolution ASC, P2.participations DESC, membre.pseudo
												  LIMIT 0, $nbUser";						
		}
		$resultat = mysql_query ($queryEvolution) or die ("probleme " .mysql_error());
		
		// Remplissage du tableau avec les tops / flops
		while ($row = mysql_fetch_array($resultat))
		{
			$typeChamp = $row["type"];
			$nomPseudo = $row["pseudo"];
			$numEvolution = $row["evolution"];						
			array_push($data, array("type" => $typeChamp, "evol" => $numEvolution, "pseudo" => $nomPseudo));
		}
				
		// Retour du tableau au format JSON
		$response->body = json_encode(array("topFlop" => $data));

        return $response;
        
    }
    
}

?>

