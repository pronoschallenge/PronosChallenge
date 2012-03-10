<?php
  
/**
 * An example resource
 * 
 *  Méthode GET :
 *  Parm obligatoire :
 *   - "filtre" = "0"=saison OU "1"=dernière journée OU "2"=utilisateur
 *  Param facultatif 
 *   - "user" = Utilisateur (pour le filtre utilisateur)
 * 
 *  Statistiques retournées :
 *   - meilleur Top par classement
 *   - pire flop par classement
 *   - plus grand nombre de point par classement
 *   - plus petit nombre de point par classement   
 * @uri /statistique/(.*)?
 * 
 */


class StatistiqueResource extends Resource {
	
    function get($request, $filtre) {
    	
        $response = new Response($request);
        $response->code = Response::OK;
        $response->addHeader('content-type', 'text/plain');

        $data = array();

		ouverture ();
		
    	// récupération de l'utilisateur à filtrer
	    if(isset($_GET['user']))  { 
	    	$pseudo = $_GET['pseudo']; 
	    } else {
	    	$pseudo = "";
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

		//liste des classements
		$listeChamp = array('general','hourra', 'mixte');		
		
		
// STATISTIQUE MEILLEUR TOP/FLOP PAR CLASSEMENT
		//liste des stats
		$listeEvol = array('Evol Top','Evol Flop');		
		// Préparation requête Meilleur Top / Pire Flop
		$queryInitEvol = 	"SELECT P2.type, P2.fin, (P1.classement - P2.classement) as evolution, membre.pseudo
							FROM phpl_pronos_graph P2
							JOIN phpl_membres membre ON membre.id_prono = P2.id_membre
													AND membre.actif = '1'";
		$queryVariableJ1 = "							AND P2.fin = '$idDerniereJournee'";
		$queryVariableUSER = " 	AND membre.pseudo = '$pseudo'";
		$queryVariableTOP = "	ORDER BY P2.type, evolution DESC, P2.participations DESC, P2.fin DESC, membre.pseudo
								LIMIT 0, 1";
		$queryVariableFLOP = "	ORDER BY P2.type, evolution ASC, P2.participations DESC, P2.fin DESC, membre.pseudo
								LIMIT 0, 1";
		
		// 1 occurence par classement (général, hourra, mixte)
		for ($j = 0; $j < 3; $j++) {
			
			// 1 occurence TOP, 1 occurence FLOP
			for ($i = 0; $i < 2; $i++) {
				
				$queryVariableP1 = "	JOIN phpl_pronos_graph P1 ON P1.id_membre = P2.id_membre
											AND P1.id_gr_champ = P2.id_gr_champ
											AND P1.type = P2.type
											AND P1.fin = P2.fin - 1
										WHERE P2.id_gr_champ = '$idSaisonEnCours' AND P2.type = '$listeChamp[$j]'";				
				
				if ($filtre == "0") {
					$queryEvolution = $queryInitEvol . $queryVariableP1;
				} else if ($filtre == "1") {
					$queryEvolution = $queryInitEvol . $queryVariableP1 . $queryVariableJ1;
				} else {
					$queryEvolution = $queryInitEvol . $queryVariableUSER . $queryVariableP1;
				}
				if ($i == 0) {
					$queryEvolution = $queryEvolution . $queryVariableTOP;
				} else {
					$queryEvolution = $queryEvolution . $queryVariableFLOP;
				}
				$resultat = mysql_query ($queryEvolution) or die ("probleme " .mysql_error());
					
				// Remplissage du tableau avec les tops / flops
				if ($row = mysql_fetch_array($resultat)) {
					$typeStat = $listeEvol[$i];
					$type = $row["type"];
					$nomPseudo = $row["pseudo"];
					$numEvolution = $row["evolution"];
					$numJournee = $row["fin"];
					array_push($data, array("stat" => $typeStat, "quoi" => $type, "result" => $numEvolution, "pseudo" => $nomPseudo, "quand" => $numJournee));
				}
			
			}
				
		}

		
// STATISTIQUE NB POINT +/- PAR CLASSEMENT
		//liste des stats
		$listeEvol = array('Point Top','Point Flop');
		// Préparation requête Plus grand / Plus petit nombre de points
		$queryInitEvol = 	"SELECT P2.type, P2.fin, P2.points, membre.pseudo
							FROM phpl_pronos_graph P2
							JOIN phpl_membres membre ON membre.id_prono = P2.id_membre
													AND membre.actif = '1'";
		$queryVariableJ1 = "							AND P2.fin = '$idDerniereJournee'";
		$queryVariableUSER = " 	AND membre.pseudo = '$user'";
		$queryVariableTOP = "	ORDER BY P2.type, points DESC, P2.participations DESC, P2.fin DESC, membre.pseudo
								LIMIT 0, 1";
		$queryVariableFLOP = "	ORDER BY P2.type, points ASC, P2.participations DESC, P2.fin DESC, membre.pseudo
								LIMIT 0, 1";
		
		// 1 occurence par classement (général, hourra, mixte)
		for ($j = 0; $j < 3; $j++) {
				
			// 1 occurence TOP, 1 occurence FLOP
			for ($i = 0; $i < 2; $i++) {
		
				$queryVariableP1 = "	WHERE P2.id_gr_champ = '$idSaisonEnCours' AND P2.type = '$listeChamp[$j]'";
		
				if ($filtre == "0") {
					$queryEvolution = $queryInitEvol . $queryVariableP1;
				} else if ($filtre == "1") {
					$queryEvolution = $queryInitEvol . $queryVariableP1 . $queryVariableJ1;
				} else {
					$queryEvolution = $queryInitEvol . $queryVariableUSER . $queryVariableP1;
				}
				if ($i == 0) {
					$queryEvolution = $queryEvolution . $queryVariableTOP;
				} else {
					$queryEvolution = $queryEvolution . $queryVariableFLOP;
				}
				$resultat = mysql_query ($queryEvolution) or die ("probleme " .mysql_error());
					
				// Remplissage du tableau avec les tops / flops
				if ($row = mysql_fetch_array($resultat)) {
					$typeStat = $listeEvol[$i];
					$type = $row["type"];
					$nomPseudo = $row["pseudo"];
					$nbPoints = $row["points"];
					$numJournee = $row["fin"];
					array_push($data, array("stat" => $typeStat, "quoi" => $type, "result" => $nbPoints, "pseudo" => $nomPseudo, "quand" => $numJournee));
				}
					
			}
		
		}		


// MEILLEUR SERIE
		//liste des stats
		$listeSerie = array('Serie Top','Serie Flop');
		
		// Series en cours
		$queryInitSerie = 
			"SELECT membre.pseudo as pseudo, club_dom.nom as club_dom, club_ext.nom as club_ext, phpl_pronostics.points as points, phpl_journees.numero as journee 
			FROM phpl_pronostics
			JOIN phpl_matchs ON phpl_matchs.id = phpl_pronostics.id_match
							AND phpl_matchs.buts_dom IS NOT NULL
			JOIN phpl_membres membre ON membre.id = phpl_pronostics.id_membre
							AND membre.actif = '1'";
		$querySerieVariable = 
			" JOIN phpl_journees ON phpl_journees.id = phpl_matchs.id_journee
			  JOIN phpl_equipes eq_dom ON eq_dom.id = phpl_matchs.id_equipe_dom
			  JOIN phpl_clubs club_dom ON club_dom.id = eq_dom.id_club
			  JOIN phpl_equipes eq_ext ON eq_ext.id = phpl_matchs.id_equipe_ext
			  JOIN phpl_clubs club_ext ON club_ext.id = eq_ext.id_club
			  WHERE phpl_pronostics.id_champ = $idSaisonEnCours				
			  ORDER BY phpl_journees.numero";		
		
		if ($filtre == "0" || $filtre == "1") {
			$querySerie = $queryInitSerie . $querySerieVariable;
		} else {
			$querySerie  =  $queryInitSerie . $queryVariableUSER . $querySerieVariable;
		}
		
		// 1 occurence TOP, 1 occurence FLOP
		for ($i = 0; $i < 2; $i++) {
			
			$resultSeriesEnCours = mysql_query($querySerie) or die (mysql_error());
			
			$derniere_journee = 0;
			
			$series = array();
			$seriesTmp = array();
			$series_encours = array();
			while ($rowSeriesEnCours = mysql_fetch_array($resultSeriesEnCours)) {
				if($derniere_journee < $rowSeriesEnCours['journee']) {
					$derniere_journee = $rowSeriesEnCours['journee'];
				}
			
				// si prono correct (série TOP) ou incorrect (série FLOP)
				if(($i == 0 && $rowSeriesEnCours['points'] > 0) || ($i == 1 && $rowSeriesEnCours['points'] == 0)) {
					$seriesTmp[$rowSeriesEnCours['pseudo']][$rowSeriesEnCours['club_dom']] += 1;
					$seriesTmp[$rowSeriesEnCours['pseudo']][$rowSeriesEnCours['club_ext']] += 1;
				} else {
				// si une série était en cours pour l'équipe dom...
					if($seriesTmp[$rowSeriesEnCours['pseudo']][$rowSeriesEnCours['club_dom']] > 5) {
						$serie = new Serie();
						$serie->user = $rowSeriesEnCours['pseudo'];
						$serie->equipe = $rowSeriesEnCours['club_dom'];
						$serie->journee_fin = $rowSeriesEnCours['journee'] - 1;
						$serie->journee_debut = $serie->journee_fin - $seriesTmp[$rowSeriesEnCours['pseudo']][$rowSeriesEnCours['club_dom']] + 1;
						$serie->duree = $serie->journee_fin - $serie->journee_debut + 1; 
						$serie->en_cours = false;					
						$series[] = $serie;
					}
				// si une série était en cours pour l'équipe ext...
					if($seriesTmp[$rowSeriesEnCours['pseudo']][$rowSeriesEnCours['club_ext']] > 5) {
						$serie = new Serie();
						$serie->user = $rowSeriesEnCours['pseudo'];
						$serie->equipe = $rowSeriesEnCours['club_ext'];
						$serie->journee_fin = $rowSeriesEnCours['journee'] - 1;
						$serie->journee_debut = $serie->journee_fin - $seriesTmp[$rowSeriesEnCours['pseudo']][$rowSeriesEnCours['club_ext']] + 1;
						$serie->duree = $serie->journee_fin - $serie->journee_debut + 1;
						$serie->en_cours = false;
						$series[] = $serie;
					}			
				// remise à zéro des séries pour ces équipes
					$seriesTmp[$rowSeriesEnCours['pseudo']][$rowSeriesEnCours['club_dom']] = 0;
					$seriesTmp[$rowSeriesEnCours['pseudo']][$rowSeriesEnCours['club_ext']] = 0;
				}
			}
			
			// Traitement des séries
			foreach($seriesTmp as $user=>$serieFinSaison) {
				foreach($serieFinSaison as $equipe=>$nb) {
					if($nb > 5) {
						$serie = new Serie();
						$serie->user = $user;
						$serie->equipe = $equipe;
						$serie->journee_fin = $derniere_journee;
						$serie->journee_debut = $serie->journee_fin - $nb + 1;
						$serie->duree = $serie->journee_fin - $serie->journee_debut + 1;
						$serie->en_cours = true;					
						$series[] = $serie;
					}
				}
			}
			// Tri du tableau par durée, pseudo
			usort($series, array("Serie", "cmp_serie"));
	
			array_push($data, array("stat" => $listeSerie[$i], "quoi" => "histo", "result" => $series[0]->equipe, "pseudo" => $series[0]->user, "quand" => $series[0]->duree));
			
			// Traitement des séries en cours
			foreach($series as $serie_encours) {
				if($serie_encours->en_cours == true) {
					$series_encours[] = $serie_encours;
				}
			}
			array_push($data, array("stat" => $listeSerie[$i], "quoi" => "en cours", "result" => $series_encours[0]->equipe, "pseudo" => $series_encours[0]->user, "quand" => $series_encours[0]->duree));
			
		}
		
		// Retour du tableau au format JSON
		$response->body = json_encode(array("statistique" => $data));

        return $response;
        
    }
    
}


class Serie {
	var $user;
	var $equipe;
	var $journee_debut;
	var $journee_fin;
	var $en_cours;
	var $duree;
	
	/* Ceci est une fonction de comparaison statique */
	static function cmp_serie($a, $b)
	{
		
		$cmp_return = strcasecmp($a->duree, $b->duree);
		if ($cmp_return == 0) {
			$cmp_return = strcasecmp($a->user, $b->user);
		}
		return $cmp_return;
		
	}	
}



?>

