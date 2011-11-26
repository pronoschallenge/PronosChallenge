<?php
  
/**
 * 
 * Méthode GET : 
 *  => doit avoir "type" en paramètre
 *    - "1" = liste des amis
 *    - "0" = liste des utilisateurs non amis (pour les ajouter)
 * 
 * Méthode POST : doit avoir "action" en paramètre.
 *    - "add" = ajout d'un ami
 *    - "del" = suppression d'un ami
 * 
 * An example resource
 * @uri /listeAmis/(.*)?
 */
class ListeAmisResource extends Resource {
    
    function post($request) {
    
    	ouverture ();
    
    	$response = new Response($request);
    
    	if(!($this->isSecured())) {
	    	$response->body = "401";
	    	return $response;
    	}
    
    	// ami à ajouter/retirer
    	$ami_pseudo = file_get_contents('php://input');
    	if(empty($ami_pseudo)) {
    		$response->body = "500:NO_DATA";
		    return $response;
    	}
    	$requete= "SELECT id_prono FROM phpl_membres WHERE pseudo='$ami_pseudo'";
    	$result = mysql_query($requete);
    	$row = mysql_fetch_array($result);
    	$ami_id=$row[0];
    
    	// utilisateur postant la requête
    	$user_pseudo = $_SERVER['PHP_AUTH_USER'];
    	$requete= "SELECT id_prono FROM phpl_membres WHERE pseudo='$user_pseudo'";
        $result = mysql_query($requete);
        $row = mysql_fetch_array($result);
    	$user_id=$row[0];
    
    	// récupération de l'action
    	if(isset($_GET['action']))  {
    		$action = $_GET['action'];
    	} else {
	    	$response->body = "401";
	    	return $response;
    	}    	 
    	
    	if ($action == "add") {
    		$requeteInsert = "INSERT INTO phpl_clmnt_filtre (id, idMembre, visible)
    		    			  VALUES ('$user_id', '$ami_id', '1')
    		    			  WHERE NOT EXISTS (SELECT id FROM phpl_clmnt_filtre WHERE id = '$user_id' AND idMembre = '$ami_id')";
    		mysql_query($requeteInsert) or die ("probleme " .mysql_error());
    	} elseif ($action == "del") {
    		$requeteDelete = "DELETE FROM phpl_clmnt_filtre 
    		    		      WHERE id = '$user_id' AND idMembre = '$ami_id')";
    		mysql_query($requeteDelete) or die ("probleme " .mysql_error());
    	}
            
    	$response->code = Response::OK;
    	$response->addHeader('content-type', 'text/plain');
    
    	return $response;
    
    }
		    
		    
	
    function get($request, $user) {
        
        $response = new Response($request);
        $response->code = Response::OK;
        $response->addHeader('content-type', 'text/plain');

        $data = array();

		ouverture ();
		
		// récupération du type de liste
		if(isset($_GET['type']))  {
			$type = $_GET['type'];
		} else {
			$type = "1";
		}
		
		if ($type == "1") {
			// Liste des amis triés par pseudo
			$queryClassementAmis =
				"SELECT classement.pseudo, classement.type, classement.place, classement.points
				FROM phpl_membres membre
				JOIN phpl_clmnt_filtre filtre ON filtre.id = membre.id_prono
				JOIN phpl_clmnt_pronos classement ON classement.id_membre = filtre.idMembre
				JOIN phpl_gr_championnats groupes ON groupes.id = classement.id_champ
												 AND groupes.activ_prono = '1'
				WHERE membre.pseudo = '$user' AND membre.actif = '1'
				  AND classement.type IN ('general', 'hourra', 'mixte')
				ORDER BY classement.pseudo ASC, classement.type ASC";
			
			$resultat = mysql_query ($queryClassementAmis) or die ("probleme " .mysql_error());
			
			// Remplissage du tableau avec le palmarès de l'utilisateur
			while ($row = mysql_fetch_array($resultat))
			{
				$numPlace = $row["place"];
				$typeChamp = $row["type"];
				$pseudoAmi = $row["pseudo"];
				$nbPoints = $row["points"];
					
				array_push($data, array("pseudo" => $pseudoAmi, "type" => $typeChamp, "place" => $numPlace, "point" => $nbPoints));
			}
				
		} else {
			// utilisateur postant la requête
			$requete = "SELECT id_prono FROM phpl_membres WHERE pseudo = '$user'";
			$result = mysql_query($requete);
			$row = mysql_fetch_array($result);
			$user_id = $row[0];
						
			// Liste des utilisateurs non amis
			$queryListeUtilisateur =
				"SELECT membre.pseudo, membre.nom, membre.prenom, ifnull(filtre.idMembre, 0) as ami
				FROM phpl_membres membre
				LEFT JOIN phpl_clmnt_filtre filtre ON filtre.idMembre = membre.id
												  AND filtre.id = '$user_id'
				WHERE membre.actif = '1'
				ORDER BY membre.pseudo";
			
			$resultat = mysql_query ($queryListeUtilisateur) or die ("probleme " .mysql_error());
			
			// Remplissage du tableau avec le palmarès de l'utilisateur
			while ($row = mysql_fetch_array($resultat))
			{
				$pseudo = $row["pseudo"];
				$nom = $row["nom"];
				$prenom = $row["prenom"];
				if ($row["ami"] == 0) {
					$ami = '0';
				} else {
					$ami = '1';
				}
					
				array_push($data, array("pseudo" => $pseudo, "nom" => $nom, "prenom" => $prenom, "ami" => $ami));
			}
			
		}

		// Retour du tableau au format JSON
		$response->body = json_encode(array("listeAmis" => $data));

        return $response;
        
    }
    
}

?>

