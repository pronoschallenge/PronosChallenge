<?php
  
/**
 * An example resource
 * @uri /pronos/(.*)?
 */
class PronosResource extends Resource {
    
    function get($request, $user) {
        
        $response = new Response($request);
        $response->code = Response::OK;
        $response->addHeader('content-type', 'text/plain');

        $data = array();

		ouverture ();

		// récupération de l'ide du championnat
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
    
}

?>

