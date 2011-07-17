<?php
  
/**
 * An example resource
 * @uri /pronos/(.*)?
 */
class PronosResource extends Resource {
 
    function isSecured($user) {
		// maybe we have caught authentication data in $_SERVER['REMOTE_USER']
		if((!$_SERVER['PHP_AUTH_USER'] || !$_SERVER['PHP_AUTH_PW']) && preg_match('/Basic+(.*)$/i', $_SERVER['REMOTE_USER'], $matches)) {
			list($name, $password) = explode(':', base64_decode($matches[1]));
			$_SERVER['PHP_AUTH_USER'] = strip_tags($name);
			$_SERVER['PHP_AUTH_PW'] = strip_tags($password);
		}
		
		/* 
        if(!(isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW']) && VerifSession($_SERVER['PHP_AUTH_USER'],$_SERVER['PHP_AUTH_PW'])=="1")) {
			throw new ResponseException("message!", Response::UNAUTHORIZED);
		}
		*/
		return (isset($_SERVER['PHP_AUTH_USER']) 
					&& isset($_SERVER['PHP_AUTH_PW']) 
					&& $user == $_SERVER['PHP_AUTH_USER']
					&& VerifSession($_SERVER['PHP_AUTH_USER'],$_SERVER['PHP_AUTH_PW'])=="1");
    }
    
    function get($request, $user) {
        
        $response = new Response($request);
        $response->code = Response::OK;
        $response->addHeader('content-type', 'text/plain');

        $data = array();

		ouverture ();

		// récupération de l'id du championnat
		$requete="SELECT phpl_gr_championnats.id FROM phpl_gr_championnats WHERE phpl_gr_championnats.activ_prono='1' ORDER by id desc";
		$resultat=mysql_query ($requete) or die ("probleme " .mysql_error());
		$row= mysql_fetch_array($resultat);
		 
		$gr_champ=$row[0];

		// récupération de la dernière journée
		/*
		$id_last_journee="";
		$requete="SELECT phpl_matchs.id_journee  
					FROM phpl_matchs, phpl_journees, phpl_gr_championnats
					WHERE phpl_gr_championnats.id='$gr_champ'   
					AND phpl_matchs.buts_dom is not null
					AND phpl_matchs.buts_ext is not null	
					ORDER by phpl_matchs.date_reelle DESC LIMIT 0, 1";
		$resultat=mysql_query($requete) or die ("probleme " .mysql_error());
		while ($row=mysql_fetch_array($resultat))
		{
			$id_last_journee=$row[0];
		}
		*/

	    // requete pour récupérer les matchs à pronostiquer
	    $requete="SELECT phpl_clubs.nom, CLEXT.nom, phpl_matchs.id, phpl_matchs.date_reelle, phpl_journees.numero
				    FROM phpl_clubs, phpl_clubs as CLEXT, phpl_matchs, phpl_journees, phpl_equipes, phpl_equipes as EXT, phpl_gr_championnats
				    WHERE phpl_clubs.id=phpl_equipes.id_club
				    AND CLEXT.id=EXT.id_club
				    AND phpl_equipes.id=phpl_matchs.id_equipe_dom
				    AND EXT.id=phpl_matchs.id_equipe_ext
				    AND phpl_matchs.id_journee=phpl_journees.id
				    AND phpl_journees.id_champ=phpl_gr_championnats.id_champ
				    AND phpl_gr_championnats.id='$gr_champ'	
				    AND phpl_matchs.buts_dom is null
				    AND phpl_matchs.buts_ext is null			    
				    AND phpl_clubs.nom!='exempte'
				    AND CLEXT.nom!='exempte'
				    ORDER by phpl_matchs.date_reelle, phpl_clubs.nom
				    LIMIT 0, 10 ";
				    				    	
	    $i=0;
	    $x=0;
	    $resultat=mysql_query($requete);
	    
	    if (mysql_num_rows($resultat)=="0") 
	    {
			//
		}

	    while ($row=mysql_fetch_array($resultat))
	    {
		    // nom du club domicile et du club exterieur
	    	$clubs_dom = stripslashes($row[0]);
	       	$clubs_ext = stripslashes($row[1]);
			$id = $row["id"];
			$date = $row["date_reelle"];

	       	// on regarde si le prono a déjà été pronostiqué
	       	$requete2= "SELECT pronostic FROM phpl_pronostics, phpl_membres 
				WHERE phpl_pronostics.id_match='$row[2]' 
				AND phpl_membres.id=phpl_pronostics.id_membre 
				AND phpl_membres.pseudo='$user'";
	       	$resultat2=mysql_query($requete2) or die ("probleme " .mysql_error());
	       	$nb_pronos= mysql_num_rows($resultat2);
		
	       	if ($nb_pronos == "0") 
	       	{
		       	$prono="0";
		    }
		    
			while ($row2=mysql_fetch_array($resultat2))
			{
				$prono=$row2["0"];

				if ($row2["0"] == "")
				{
					$prono="0";
				}

			}

       		array_push($data, array("id" => $id, "equipe_dom" => $clubs_dom, "equipe_ext" => $clubs_ext, "date" => $date, "prono" => $prono));

		}

		$response->body = json_encode(array("pronos" => $data));

        return $response;
        
    }
 
    function post($request, $user) {

		ouverture ();
		
		$response = new Response($request);
		
		if(!($this->isSecured($user))) {
			$response->body = "401";
			return $response;
		}
		
		$json = file_get_contents('php://input');
		if(empty($json)) {
			$response->body = "500:NO_DATA";
			return $response;
		}
		
		$pronos = json_decode($json);		

		// récupération de l'id du championnat
		$requete="SELECT phpl_gr_championnats.id FROM phpl_gr_championnats WHERE phpl_gr_championnats.activ_prono='1' ORDER by id desc";
		$resultat=mysql_query ($requete) or die ("probleme " .mysql_error());
		$row= mysql_fetch_array($resultat);
		 
		$gr_champ=$row[0];
		
		foreach($pronos as $prono) {
	     	$valeur_prono = $prono->prono;
	    	$id_match = $prono->id;
		
			// on récupère la date du match
	     	$requete="SELECT phpl_matchs.date_reelle FROM phpl_matchs WHERE phpl_matchs.id='$id_match'";
	     	$resultat=mysql_query($requete);
	
	       	while ($row= mysql_fetch_array($resultat))
	       	{      
	         	$date_relle=$row[0];
	       	}
	
			// on récupère le temps avant l'expiration des pronos
	     	$requete="SELECT tps_avant_prono FROM phpl_gr_championnats WHERE id='$gr_champ'";
	     	$resultat=mysql_query($requete);
	
	       	while ($row= mysql_fetch_array($resultat))
	       	{
	         	$temps_avant_prono=$row[0];
	       	}
	
	     	$date_match_timestamp=format_date_timestamp($date_relle);
	     	$date_actuelle=time();
	
	     	if ($valeur_prono !== "undefined")
	     	{
	       		mysql_query("DELETE FROM phpl_pronostics WHERE pronostic=' '")or die ("probleme " .mysql_error());
	       		$requete = "SELECT * FROM phpl_matchs, phpl_pronostics, phpl_membres WHERE phpl_membres.pseudo='$user'
			                   AND phpl_membres.id=phpl_pronostics.id_membre
			                   AND phpl_pronostics.id_match=phpl_matchs.id
			                   AND phpl_pronostics.id_match='$id_match'";
	       		$resultat=mysql_query($requete);
	       		$nb_prono=mysql_num_rows($resultat);
	
	       		$requete = "SELECT id FROM phpl_membres WHERE pseudo='$user'";
	       		$resultat = mysql_query($requete);
	
	         	while ($row= mysql_fetch_array($resultat))
	         	{
	           		$id=$row["id"];
	         	}
	
	       		// on prend en compte le prono si la date d'expiration n'est pas passée
	         	if ($date_actuelle<($date_match_timestamp+$temps_avant_prono*60))
	         	{	
					// si l'utilisateur avait déjà pronostiqué ce match...
		       		if ($nb_prono == "1")
		       		{
						if($valeur_prono == "0") {
							mysql_query("DELETE FROM phpl_pronostics WHERE phpl_pronostics.id_match='$id_match' AND phpl_pronostics.id_membre='$id' AND phpl_pronostics.id_champ='$gr_champ'")or die ("probleme " .mysql_error());
						} else {
							mysql_query("UPDATE phpl_pronostics SET pronostic='$valeur_prono'
				                        WHERE phpl_pronostics.id_membre='$id'
				                        AND phpl_pronostics.id_match='$id_match'") or die ("probleme " .mysql_error());
						}
		       		}
			       	if ($nb_prono == "0" && $valeur_prono != "0")
			       	{
		           		mysql_query("INSERT INTO phpl_pronostics (id_membre, pronostic, id_match, id_champ) VALUES ('$id','$valeur_prono','$id_match', '$gr_champ')") or die ("probleme " .mysql_error());
			       	}
			       	elseif ($nb_prono!= "1" and $nb_prono != "0") 
			       	{
						$response->body = "500:DATA_ERROR";
						return $response;
			    	}
				}
			}
			
		}
        
        $response->code = Response::OK;
        $response->addHeader('content-type', 'text/plain');
        
        return $response;		
	} 
    
}

?>

