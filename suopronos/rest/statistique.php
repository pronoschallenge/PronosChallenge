<?php
  
/**
 * An example resource
 * 
 *  Méthode GET :
 *  Parm obligatoire :
 *   - "filtre" = "0"=saison OU "1"=dernière journée
 *  Param facultatif 
 *   - "user" = Utilisateur (si "" alors tous les utilisateurs)
 * 
 *  Statistiques retournées :
 *   - meilleur Top par classement
 *   - pire flop par classement
 *   - plus grand nombre de point par classement
 *   - plus petit nombre de point par classement   
 *   - meilleure série (plus grand nombre de pronos Ok d'affilée)
 *   - pire série      (plus grand nombre de pronos Ko d'affilée)
 *   - répartition des pronos (1, N, 2)
 *   - répartition des résultats (1, N, 2)
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
		$querySaisonEnCours = 	"SELECT phpl_gr_championnats.id, phpl_gr_championnats.id_champ 
								FROM phpl_gr_championnats 
								WHERE phpl_gr_championnats.activ_prono = '1' 
								ORDER by id desc";
		$resultat = mysql_query ($querySaisonEnCours) or die ("probleme " .mysql_error());
		$row = mysql_fetch_array($resultat);
		$idSaisonEnCours = $row[0];
		$idChampEnCours = $row[1];
		
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
		
		// 1 occurence TOP, 1 occurence FLOP
		for ($i = 0; $i < 2; $i++) {
			
			// 1 occurence par classement (général, hourra, mixte)
			for ($j = 0; $j < 3; $j++) {
				
				$queryVariableP1 = "	JOIN phpl_pronos_graph P1 ON P1.id_membre = P2.id_membre
											AND P1.id_gr_champ = P2.id_gr_champ
											AND P1.type = P2.type
											AND P1.fin = P2.fin - 1
										WHERE P2.id_gr_champ = '$idSaisonEnCours' AND P2.type = '$listeChamp[$j]'";				
				
				// Construction de la requête
				if ($pseudo == "") {
					$queryEvolution = $queryInitEvol . $queryVariableP1;
				} else {
					$queryEvolution = $queryInitEvol . $queryVariableUSER . $queryVariableP1;
				}
				
				if ($filtre == "1") {
					$queryEvolution = $queryEvolution . $queryVariableJ1;
				}
				
				// Tri
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
					if ($i == 0) {
						$numEvolution = "+" . $row["evolution"];
					} else {
						$numEvolution = $row["evolution"];
					}					
					$numJournee = $row["fin"];
					array_push($data, array("stat" => $typeStat, "quoi" => $type, "result" => $numEvolution, "pseudo" => $nomPseudo, "quand" => $numJournee));
				}
			
			}
				
		}

		
// STATISTIQUE NB POINT +/- PAR CLASSEMENT
		//liste des stats
		$listeEvol = array('Point Top','Point Flop');
		// Préparation requête Plus grand / Plus petit nombre de points
		$queryInitEvol = 	"SELECT P2.type, P2.fin, (P2.points - P1.points) as evolution, membre.pseudo
							FROM phpl_pronos_graph P2
							JOIN phpl_membres membre ON membre.id_prono = P2.id_membre
													AND membre.actif = '1'";
		$queryVariableJ1 = "							AND P2.fin = '$idDerniereJournee'";
		$queryVariableUSER = " 	AND membre.pseudo = '$pseudo'";
		$queryVariableTOP = "	ORDER BY P2.type, evolution DESC, P2.participations DESC, P2.fin DESC, membre.pseudo
								LIMIT 0, 1";
		$queryVariableFLOP = "	ORDER BY P2.type, evolution ASC, P2.participations DESC, P2.fin DESC, membre.pseudo
								LIMIT 0, 1";
		
		// 1 occurence TOP, 1 occurence FLOP
		for ($i = 0; $i < 2; $i++) {
				
			// 1 occurence par classement (général, hourra)
			for ($j = 0; $j < 2; $j++) {
		
				$queryVariableP1 = "	JOIN phpl_pronos_graph P1 ON P1.id_membre = P2.id_membre
																AND P1.id_gr_champ = P2.id_gr_champ
																AND P1.type = P2.type
																AND P1.fin = P2.fin - 1
										WHERE P2.id_gr_champ = '$idSaisonEnCours' AND P2.type = '$listeChamp[$j]'";
		
				// Construction de la requête
				if ($pseudo == "") {
					$queryEvolution = $queryInitEvol . $queryVariableP1;
				} else {
					$queryEvolution = $queryInitEvol . $queryVariableUSER . $queryVariableP1;
				}
				
				if ($filtre == "1") {
					$queryEvolution = $queryEvolution . $queryVariableJ1;
				}
				
				// Tri
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
					$nbPoints = $row["evolution"];
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
		
		if ($pseudo == "") {
			$querySerie = $queryInitSerie . $querySerieVariable;
		} else {
			$querySerie = $queryInitSerie . $queryVariableUSER . $querySerieVariable;
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
			
			if ($filtre == '0') {
				array_push($data, array("stat" => $listeSerie[$i], "quoi" => "histo", "result" => $series[0]->equipe, "pseudo" => $series[0]->user, "quand" => $series[0]->duree));
			} else {				
				// Traitement des séries en cours
				foreach($series as $serie_encours) {
					if($serie_encours->en_cours == true) {
						$series_encours[] = $serie_encours;
					}
				}
				array_push($data, array("stat" => $listeSerie[$i], "quoi" => "en cours", "result" => $series_encours[0]->equipe, "pseudo" => $series_encours[0]->user, "quand" => $series_encours[0]->duree));
			}
		}
		
		
// REPARTITION DES PRONOS
		// Préparation requête répartition des pronos
		$queryInitPronos = 	"SELECT prono.pronostic, count(*) as nbPronos
							FROM phpl_journees journee
							JOIN phpl_matchs matchs on matchs.id_journee = journee.id
							  AND matchs.buts_dom is not null
							JOIN phpl_pronostics prono on prono.id_match = matchs.id
							JOIN phpl_membres membre on membre.id = prono.id_membre
							  AND membre.actif = '1'";							
		$queryVariableJ1 = " AND journee.numero = '$idDerniereJournee'";
		$queryVariableUSER = " AND membre.pseudo = '$pseudo'";
		$queryVariableWHERE = " WHERE journee.id_champ = '$idChampEnCours'";
		$queryVariableFIN = " GROUP BY prono.pronostic";
		
		// Construction de la requête
		if ($pseudo == "") {
			$queryPronos = $queryInitPronos . $queryVariableWHERE;
		} else {
			$queryPronos = $queryInitPronos . $queryVariableUSER . $queryVariableWHERE;
		}
		
		if ($filtre == "1") {
			$queryPronos = $queryPronos . $queryVariableJ1;
		}
		
		$queryPronos = $queryPronos . $queryVariableFIN;
		
		$resultat = mysql_query ($queryPronos) or die ("probleme " .mysql_error());
		
		// Remplissage du tableau avec les tops / flops
		while ($row = mysql_fetch_array($resultat)) {
			$typeStat = "Repart Prono";
			$type = 0;
			$nomPseudo = $row["pronostic"];
			$nbPoints = $row["nbPronos"];
			$numJournee = 0;
			array_push($data, array("stat" => $typeStat, "quoi" => $type, "result" => $nbPoints, "pseudo" => $nomPseudo, "quand" => $numJournee));
		}

		
// REPARTITION DES RESULTATS
		// Préparation requête répartition des résultats
		$queryInitPronos = 	"SELECT case when matchs.buts_dom > matchs.buts_ext then '1' 
										 when matchs.buts_dom = matchs.buts_ext then 'N' 
										 else '2' end as type, count(*) as nbPronos
							FROM phpl_journees journee
							JOIN phpl_matchs matchs on matchs.id_journee = journee.id
								AND matchs.buts_dom is not null";
		$queryVariableJ1 = " AND journee.numero = '$idDerniereJournee'";
		$queryVariableWHERE = " WHERE journee.id_champ = '$idChampEnCours'";
		$queryVariableFIN = " GROUP BY type";
		
		// Construction de la requête
		if ($pseudo == "") {
			$queryPronos = $queryInitPronos . $queryVariableWHERE;
		} else {
			$queryPronos = $queryInitPronos . $queryVariableUSER . $queryVariableWHERE;
		}
		
		if ($filtre == "1") {
			$queryPronos = $queryPronos . $queryVariableJ1;
		}
		
		$queryPronos = $queryPronos . $queryVariableFIN;
		
		$resultat = mysql_query ($queryPronos) or die ("probleme " .mysql_error());
		
		// Remplissage du tableau avec les tops / flops
		while ($row = mysql_fetch_array($resultat)) {
			$typeStat = "Repart Result";
			$type = 0;
			$nomPseudo = $row["type"];
			$nbPoints = $row["nbPronos"];
			$numJournee = 0;
			array_push($data, array("stat" => $typeStat, "quoi" => $type, "result" => $nbPoints, "pseudo" => $nomPseudo, "quand" => $numJournee));
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

