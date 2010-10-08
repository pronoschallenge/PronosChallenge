<?php
//***********************************************************************/
// Phpleague : gestionnaire de championnat                              */
// ============================================                         */
//                                                                      */
// Version : 0.82b                                                       */
// Copyright (c) 2004    Alexis MANGIN                                  */
// http://phpleague.univert.org                                         */
//                                                                      */
// This program is free software. You can redistribute it and/or modify */
// it under the terms of the GNU General Public License as published by */
// the Free Software Foundation; either version 2 of the License.       */
//                                                                      */
//***********************************************************************/
// Support technique : http://phpleague.univert.org/forum               */
//                                                                      */
//***********************************************************************/
?>
<table class=phpl width="80%">
            <tr>
              <td class=phpl2 align="center" colspan="3"><?php echo ADMIN_GRAPH_TITRE." "; affich_gr_champ ($gr_champ); ?></td>
            </tr>
            <tr>
            <td align="center">
<?php
include ("tps1.php3");

// Récupération de l'id du championnat
$query="SELECT id_champ
		FROM phpl_gr_championnats
		WHERE phpl_gr_championnats.id='$gr_champ'";
		
$result=mysql_query($query);

$row=mysql_fetch_array($result);
$champ=$row[0];

// Réinitialisation de la table des graphiques des pronos
mysql_query("DELETE FROM phpl_pronos_graph WHERE id_gr_champ='$gr_champ'") or die (mysql_error());

// Réinitialisation de la table des classements des pronos pour le groupe de championnat
mysql_query("DELETE FROM phpl_clmnt_pronos WHERE id_champ='$gr_champ'") or die (mysql_error());

$debut=0;
$fin=1;

// Récupération du nombre de journées jouées
$legende=CONSULT_CLMNT_MSG4.$debut.CONSULT_CLMNT_MSG5.$fin;
$query="SELECT max(phpl_journees.numero) 
		FROM phpl_journees, phpl_matchs 
		WHERE phpl_journees.id=phpl_matchs.id_journee 
		AND buts_dom is not NULL 
		AND phpl_journees.id_champ='$champ'";
		
$result=mysql_query ($query);
$row=mysql_fetch_array($result);
$max=$row[0];

/*********************************************************************/
/********** Remplissage de la table des graphes des pronos **************/
/*********************************************************************/

$arrayClmnt = array();
$arrayClmnt["general"] = array();
$arrayClmnt["hourra"] = array();

echo "<ul>";

/********** CLASSEMENT GENERAL **************/
                           
while ($fin<=$max)
{        
	// suppression des classements de type général
	mysql_query("DELETE FROM phpl_clmnt_pronos WHERE id_champ='$gr_champ' AND type='general'") or die (mysql_error());

	// récupération du total des points du classement général pour tous les utilisateurs
	// depuis le début du championnat jusqu'à la journée $fin
	$query="SELECT id_membre, pseudo, sum(points) as total, sum(participation) as participations 
			FROM phpl_membres, phpl_pronostics, phpl_matchs, phpl_journees
			WHERE phpl_pronostics.id_champ='$gr_champ' 
			AND id_membre=phpl_membres.id
			AND phpl_pronostics.id_match=phpl_matchs.id
			AND phpl_journees.id=phpl_matchs.id_journee			
			AND phpl_journees.id_champ='$champ'
			AND phpl_journees.numero>='$debut'
			AND phpl_journees.numero<='$fin'			
			GROUP by pseudo
			ORDER by total, participations";			
	
	$result=mysql_query ($query);

	// pour chaque utilisateur...
	while ($row=mysql_fetch_array($result))
	{	
		// on insère son classement pour la journée $fin
		
		// cette insertion est temporaire et servira pour la suite.
		// les données sont écrasées à chaque itération de la boucle pour contenir le classement de la journée $fin
		// la dernière itération correspond au classement actuel (dernière journée jouée)
		mysql_query("INSERT INTO phpl_clmnt_pronos (id_champ, id_membre, pseudo, points, participation, type) values ('$gr_champ', '$row[0]', '$row[1]', '$row[2]', '$row[3]', 'general')") or die (mysql_error());
	}	
	
	// on utilise les insertions précédentes pour mettre à jour la table phpl_clmnt_pronos
	$query="SELECT id_membre, points, participation 
			FROM phpl_clmnt_pronos 
			WHERE id_champ='$gr_champ'
			AND type='general'
			ORDER BY points DESC, participation ASC, pseudo";
	$result=mysql_query($query) or die (mysql_error());
	$pl=0;
	$i=0;
	$points_precedents="";
	
	$arrayClmnt["general"][$fin] = array();
	
	while ($row=mysql_fetch_array($result))
	{   
		$id_membre=$row["id_membre"];
		$points=$row["points"];
		$participations=$row["participation"];
		
		$i++;
		if($points!=$points_precedents)
		{
			$pl=$i;
			$points_precedents=$points; 
		} 	
			
		$arrayClmnt["general"][$fin][$id_membre] = $pl;
			
		$query="INSERT INTO phpl_pronos_graph (id_membre, id_gr_champ, fin, classement, type, points, participations) VALUES ('$id_membre', '$gr_champ', '$fin', '$pl', 'general', '$points', '$participations')" ;
		mysql_query($query); 	                 
	}
	
	$fin++;
}
// On génére le classement général une derniere fois en prenant en compte toutes les journées
// -> ces données seront utilisées pour l'affichage du classement général actuel
mysql_query("DELETE FROM phpl_clmnt_pronos WHERE id_champ='$gr_champ' AND type='general'") or die (mysql_error());
$query="SELECT id_membre, pseudo, sum(points) as total, sum(participation) as participations FROM phpl_membres, phpl_pronostics
WHERE id_champ='$gr_champ' AND id_membre=phpl_membres.id
GROUP by pseudo
ORDER by total, participations";

$result=mysql_query ($query);
while ($row=mysql_fetch_array($result))
{
	mysql_query("INSERT INTO phpl_clmnt_pronos (id_champ, id_membre, pseudo, points, participation, type) values ('$gr_champ', '$row[0]', '$row[1]', '$row[2]', '$row[3]', 'general')") or die (mysql_error());
}

// Mise à jour de la colonne 'place'
$queryPlace="SELECT clmnt.id_membre, clmnt.pseudo, clmnt.points, clmnt.participation 
			FROM phpl_clmnt_pronos as clmnt
			WHERE clmnt.id_champ='$gr_champ' AND clmnt.type='general'
			GROUP by clmnt.pseudo
			ORDER by  clmnt.points desc, clmnt.participation asc, clmnt.pseudo";
$resultPlace=mysql_query($queryPlace) or die ("probleme " .mysql_error());
$i=1;
$classement=$i;
$points_precedents=0;
while ($rowPlace=mysql_fetch_array($resultPlace))
{
	if($rowPlace[2] != $points_precedents)
	{
		$points_precedents = $rowPlace[2];
		$classement = $i;
	}

	mysql_query("UPDATE phpl_clmnt_pronos SET place='".$classement."' WHERE pseudo='".$rowPlace[1]."' AND type='general' AND id_champ='$gr_champ'") or die (mysql_error());
	$i++;
}
		
echo "<li>Graphiques et classement générés pour le classement général</li>";

/************************/

/********** CLASSEMENT HOURRA **************/
 
// on vérifie d'abord si le classement hourra était activé pour ce championnat
$hourra_activated = false;
$query="SELECT points_hourra 
			FROM phpl_pronostics
			WHERE id_champ='$gr_champ' AND points_hourra > 0";			
	
$result=mysql_query ($query);

if ($row=mysql_fetch_array($result))
{	
	$hourra_activated = true;
}         
 
if($hourra_activated)
{
	$debut=0;
	$fin=1;
                   
	while ($fin<=$max)
	{        
		mysql_query("DELETE FROM phpl_clmnt_pronos WHERE id_champ='$gr_champ' AND type='hourra'") or die (mysql_error());

		// Regénération du classement hourra
		$query="SELECT id_membre, pseudo, sum(points_hourra) as total, sum(participation) as participations 
				FROM phpl_membres, phpl_pronostics, phpl_matchs, phpl_journees
				WHERE phpl_pronostics.id_champ='$gr_champ' 
				AND id_membre=phpl_membres.id
				AND phpl_pronostics.id_match=phpl_matchs.id
				AND phpl_journees.id=phpl_matchs.id_journee			
				AND phpl_journees.id_champ='$champ'
				AND phpl_journees.numero>='$debut'
				AND phpl_journees.numero<='$fin'			
				GROUP by pseudo
				ORDER by total, participations";			
	
		$result=mysql_query ($query);

		while ($row=mysql_fetch_array($result))
		{	
			mysql_query("INSERT INTO phpl_clmnt_pronos (id_champ, id_membre, pseudo, points, participation, type) values ('$gr_champ', '$row[0]', '$row[1]', '$row[2]', '$row[3]', 'hourra')") or die (mysql_error());
		}	
		                   
		$query="SELECT * 
				FROM phpl_clmnt_pronos 
				WHERE id_champ='$gr_champ'
				AND type='hourra'
				ORDER BY points DESC, participation ASC, pseudo";
		$result=mysql_query($query) or die (mysql_error());
		$pl=0;
		$i=0;
		$points_precedents="";
		
		$arrayClmnt["hourra"][$fin] = array();
		
		while ($row=mysql_fetch_array($result))
		{   
			$id_membre=$row["id_membre"];
			$points=$row["points"];
			$participations=$row["participation"];
			
			$i++;
			if($points!=$points_precedents)
			{
				$pl=$i;
				$points_precedents=$points; 
			} 	
			
			$arrayClmnt["hourra"][$fin][$id_membre] = $pl;
				
			$query="INSERT INTO phpl_pronos_graph (id_membre, id_gr_champ, fin, classement, type, points, participations) VALUES ('$id_membre', '$gr_champ', '$fin', '$pl', 'hourra', '$points', '$participations')" ;
			mysql_query($query); 		                 
		}
		
		$fin++;
	}
	// On génére le classement hourra une derniere fois en prenant en compte toutes les journées
	mysql_query("DELETE FROM phpl_clmnt_pronos WHERE id_champ='$gr_champ' AND type='hourra'") or die (mysql_error());
	
	$query="SELECT id_membre, pseudo, sum(points_hourra) as total, sum(participation) as participations
			FROM phpl_membres, phpl_pronostics, phpl_matchs
			WHERE id_champ='$gr_champ'
			AND id_membre=phpl_membres.id
			AND phpl_matchs.id=id_match	
			GROUP by pseudo
			ORDER by total, participations";
	
	$result=mysql_query ($query) or die ("probleme " .mysql_error());
	while ($row=mysql_fetch_array($result))
	{
		mysql_query("INSERT INTO phpl_clmnt_pronos (id_champ, id_membre, pseudo, points, participation, type) values ('$gr_champ', '$row[0]', '$row[1]', '$row[2]', '$row[3]', 'hourra')") or die (mysql_error());
	}
	
	// Mise à jour de la colonne 'place'
	$queryPlace="SELECT clmnt.id_membre, clmnt.pseudo, clmnt.points, clmnt.participation 
				FROM phpl_clmnt_pronos as clmnt
				WHERE clmnt.id_champ='$gr_champ' AND clmnt.type='hourra'
				GROUP by clmnt.pseudo
				ORDER by  clmnt.points desc, clmnt.participation asc, clmnt.pseudo";
	$resultPlace=mysql_query($queryPlace) or die ("probleme " .mysql_error());
	$i=1;
	$classement=$i;
	$points_precedents=0;
	while ($rowPlace=mysql_fetch_array($resultPlace))
	{
		if($rowPlace[2] != $points_precedents)
		{
			$points_precedents = $rowPlace[2];
			$classement = $i;
		}
	
		mysql_query("UPDATE phpl_clmnt_pronos SET place='".$classement."' WHERE pseudo='".$rowPlace[1]."' AND type='hourra' AND id_champ='$gr_champ'") or die (mysql_error());
		$i++;
	}
}

echo "<li>Graphiques et classement générés pour le classement hourra</li>";

/************************/

/********** CLASSEMENT MENSUEL (plus utilisé) **************/
/*
// Regénération du classement mensuel en cours
$query="SELECT id_membre, pseudo, sum(points) as total, sum(participation) as participations
FROM phpl_membres, phpl_pronostics, phpl_matchs
WHERE id_champ='$gr_champ'
AND id_membre=phpl_membres.id
AND phpl_matchs.id=id_match
AND MONTH (date_reelle) = MONTH (NOW())
AND YEAR (date_reelle) = YEAR (NOW())
GROUP by pseudo
ORDER by total, participations";

$result=mysql_query ($query) or die ("probleme " .mysql_error());
while ($row=mysql_fetch_array($result))
{
mysql_query("INSERT INTO phpl_clmnt_pronos (id_champ, id_membre, pseudo, points, participation, type) values ('$gr_champ', '$row[0]', '$row[1]', '$row[2]', '$row[3]', 'mensuel_en_cours')") or die (mysql_error());
}
*/
/************************/

/********** CLASSEMENT MENSUEL 30 JOURS (plus utilisé) **************/
/*
// Regénération du classement mensuel 30 jours
$query="SELECT id_membre, pseudo, sum(points) as total, sum(participation) as participations
FROM phpl_membres, phpl_pronostics, phpl_matchs
WHERE id_champ='$gr_champ'
AND id_membre=phpl_membres.id
AND phpl_matchs.id=id_match
AND DATE_ADD(date_reelle, INTERVAL 30 DAY) >= NOW()
GROUP by pseudo
ORDER by total, participations";

$result=mysql_query ($query) or die ("probleme " .mysql_error());
while ($row=mysql_fetch_array($result))
{
mysql_query("INSERT INTO phpl_clmnt_pronos (id_champ, id_membre, pseudo, points, participation, type) values ('$gr_champ', '$row[0]', '$row[1]', '$row[2]', '$row[3]', 'mensuel_30_jours')") or die (mysql_error());
}
*/
/************************/

/********** CLASSEMENT HEBDO (plus utilisé) **************/
/*
// Regénération du classement hebdo
$query="SELECT id_membre, pseudo, sum(points) as total, sum(participation) as participations
FROM phpl_membres, phpl_pronostics, phpl_matchs
WHERE id_champ='$gr_champ'
AND id_membre=phpl_membres.id
AND phpl_matchs.id=id_match
AND DATE_ADD(date_reelle, INTERVAL 7 DAY) >= NOW()
GROUP by pseudo
ORDER by total, participations";

$result=mysql_query ($query) or die ("probleme " .mysql_error());
while ($row=mysql_fetch_array($result))
{
mysql_query("INSERT INTO phpl_clmnt_pronos (id_champ, id_membre, pseudo, points, participation, type) values ('$gr_champ', '$row[0]', '$row[1]', '$row[2]', '$row[3]', 'hebdo')") or die (mysql_error());
}

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
/************************/

/********** CLASSEMENT DERNIERE JOURNEE **************/

// Regénération du classement dernière journée

$debut=0;
$fin=1;

while ($fin<=$max)
{          
	mysql_query("DELETE FROM phpl_clmnt_pronos WHERE id_champ='$gr_champ' AND type='derniere_journee'") or die (mysql_error());

	// Régénération du classement
	$query="SELECT id_membre, pseudo, sum(points) as total, sum(participation) as participations 
			FROM phpl_membres, phpl_pronostics, phpl_matchs, phpl_journees
			WHERE phpl_pronostics.id_champ='$gr_champ' 
			AND id_membre=phpl_membres.id
			AND phpl_pronostics.id_match=phpl_matchs.id
			AND phpl_journees.id=phpl_matchs.id_journee			
			AND phpl_journees.id_champ='$champ'
			AND phpl_journees.numero='$fin'			
			GROUP by pseudo
			ORDER by total DESC, participations ASC, pseudo";			
	
	$result=mysql_query ($query);
	
	$i=1;
	$classement=$i;
	$points_precedents=0;
	while ($row=mysql_fetch_array($result))
	{
		if($row[2] != $points_precedents)
		{
			$points_precedents = $row[2];
			$classement = $i;
		}
		
		mysql_query("INSERT INTO phpl_clmnt_pronos (id_champ, id_membre, place, pseudo, points, participation, type) values ('$gr_champ', '$row[0]', '".$classement."', '$row[1]', '$row[2]', '$row[3]', 'derniere_journee')") or die (mysql_error());
	
		$i++;
	}	

	
	// Régénération des graphes 	                   
	$query="SELECT * 
			FROM phpl_clmnt_pronos 
			WHERE id_champ='$gr_champ'
			AND type='derniere_journee'
			ORDER BY points DESC, participation ASC, pseudo";
	$result=mysql_query($query) or die (mysql_error());
	$pl=0;
	$i=0;
	$points_precedents="";
	
	while ($row=mysql_fetch_array($result))
	{   
		$id_membre=$row["id_membre"];
		$points=$row["points"];
		$participations=$row["participation"];
				
		$i++;
		if($points!=$points_precedents)
		{
			$pl=$i;
			$points_precedents=$points; 
		} 	
		
		$query="INSERT INTO phpl_pronos_graph (id_membre, id_gr_champ, fin, classement, type, points, participations) VALUES ('$id_membre', '$gr_champ', '$fin', '$pl', 'derniere_journee', '$points', '$participations')" ;
		mysql_query($query); 		                 
	}
	
	$fin++;
}

echo "<li>Graphiques et classement générés pour le classement général dernière journée</li>";

/************************/

/********** CLASSEMENT HOURRA DERNIERE JOURNEE **************/

// Regénération du classement hourra dernière journée

if($hourra_activated)
{
	$debut=0;
	$fin=1;

	while ($fin<=$max)
	{          
		mysql_query("DELETE FROM phpl_clmnt_pronos WHERE id_champ='$gr_champ' AND type='hourra_derniere_journee'") or die (mysql_error());

		// Régénération du classement
		$query="SELECT id_membre, pseudo, sum(points_hourra) as total, sum(participation) as participations 
				FROM phpl_membres, phpl_pronostics, phpl_matchs, phpl_journees
				WHERE phpl_pronostics.id_champ='$gr_champ' 
				AND id_membre=phpl_membres.id
				AND phpl_pronostics.id_match=phpl_matchs.id
				AND phpl_journees.id=phpl_matchs.id_journee			
				AND phpl_journees.id_champ='$champ'
				AND phpl_journees.numero='$fin'			
				GROUP by pseudo
				ORDER by total DESC, participations ASC, pseudo";			
		
		$result=mysql_query ($query);
		
		$i=1;
		$classement=$i;
		$points_precedents=0;
		while ($row=mysql_fetch_array($result))
		{
			if($row[2] != $points_precedents)
			{
				$points_precedents = $row[2];
				$classement = $i;
			}
			
			mysql_query("INSERT INTO phpl_clmnt_pronos (id_champ, id_membre, place, pseudo, points, participation, type) values ('$gr_champ', '$row[0]', '".$classement."', '$row[1]', '$row[2]', '$row[3]', 'hourra_derniere_journee')") or die (mysql_error());
		
			$i++;
		}	
			
			
		// Régénération des graphes                   
		$query="SELECT * 
				FROM phpl_clmnt_pronos 
				WHERE id_champ='$gr_champ'
				AND type='hourra_derniere_journee'
				ORDER BY points DESC, participation ASC, pseudo";
		$result=mysql_query($query) or die (mysql_error());
		$pl=0;
		$i=0;
		$points_precedents="";
		
		while ($row=mysql_fetch_array($result))
		{   
			$id_membre=$row["id_membre"];
			$points=$row["points"];
			$participations=$row["participation"];
					
			$i++;
			if($points!=$points_precedents)
			{
				$pl=$i;
				$points_precedents=$points; 
			} 	
				
			$query="INSERT INTO phpl_pronos_graph (id_membre, id_gr_champ, fin, classement, type, points, participations) VALUES ('$id_membre', '$gr_champ','$fin', '$pl', 'hourra_derniere_journee', '$points', '$participations')" ;
			mysql_query($query); 		                 
		}
		
		$fin++;
	}
	
	echo "<li>Graphiques et classement générés pour le classement général dernière journée</li>";
}

/************************/

/********** CLASSEMENT MOYENNE **************/

$debut=0;
$fin=1;

// Regénération du classement moyenne
while ($fin<=$max)
{          
	// pas utile, on efface déjà tout avant 
	//mysql_query("DELETE FROM phpl_clmnt_pronos WHERE id_champ='$gr_champ' AND type='moyenne'") or die (mysql_error());
	
	// Regénération du classement moyenne
	$query="SELECT id_membre, pseudo, sum(points) as total, sum(participation) as participations 
			FROM phpl_membres, phpl_pronostics, phpl_matchs, phpl_journees
			WHERE phpl_pronostics.id_champ='$gr_champ' 
			AND id_membre=phpl_membres.id
			AND phpl_pronostics.id_match=phpl_matchs.id
			AND phpl_journees.id=phpl_matchs.id_journee			
			AND phpl_journees.id_champ='$champ'
			AND phpl_journees.numero>='$debut'
			AND phpl_journees.numero<='$fin'	
			AND phpl_matchs.buts_dom is not null
			AND phpl_matchs.buts_ext is not null					
			GROUP by pseudo
			ORDER by total, participations";			
	
	$result=mysql_query ($query);

	while ($row=mysql_fetch_array($result))
	{	
		mysql_query("INSERT INTO phpl_clmnt_pronos (id_champ, id_membre, pseudo, points, participation, type) values ('$gr_champ', '$row[0]', '$row[1]', '$row[2]', '$row[3]', 'moyenne')") or die (mysql_error());
	}	

	$query="SELECT id_membre, pseudo, points, participation, (points/participation) as moyenne_points 
			FROM phpl_clmnt_pronos
			WHERE id_champ='$gr_champ' AND type='moyenne'
			GROUP by pseudo
			ORDER by moyenne_points desc, points desc, participation asc, pseudo";	
				              
	$result=mysql_query($query) or die (mysql_error());
	$pl=0;
	$i=0;
	$points_precedents="";
	
	while ($row=mysql_fetch_array($result))
	{   
		$id_membre=$row["id_membre"];
		$points=$row["points"];
		$participations=$row["participation"];
		
		$i++;
		if($points!=$points_precedents)
		{
			$pl=$i;
			$points_precedents=$points; 
		} 	
			
		$query="INSERT INTO phpl_pronos_graph (id_membre, id_gr_champ, fin, classement, type, points, participations) VALUES ('$id_membre', '$gr_champ', '$fin', '$pl', 'moyenne', '$points', '$participations')" ;
		mysql_query($query); 		                 
	}
	
	$fin++;
}
// On génére le classement moyenne une derniere fois en prenant en compte toutes les journées
mysql_query("DELETE FROM phpl_clmnt_pronos WHERE id_champ='$gr_champ' AND type='moyenne'") or die (mysql_error());
$query="SELECT id_membre, pseudo, sum(points) as total, sum(participation) as participations
FROM phpl_membres, phpl_pronostics, phpl_matchs
WHERE id_champ='$gr_champ'
AND id_membre=phpl_membres.id
AND phpl_matchs.id=id_match
AND phpl_matchs.buts_dom is not null
AND phpl_matchs.buts_ext is not null
GROUP by pseudo
ORDER by total, participations";

$result=mysql_query ($query);
while ($row=mysql_fetch_array($result))
{
	mysql_query("INSERT INTO phpl_clmnt_pronos (id_champ, id_membre, pseudo, points, participation, type) values ('$gr_champ', '$row[0]', '$row[1]', '$row[2]', '$row[3]', 'moyenne')") or die (mysql_error());
}


echo "<li>Graphiques et classement générés pour le classement moyenne</li>";

/************************/

/********** CLASSEMENT MIXTE **************/

if($hourra_activated)
{
	$debut=0;
	$fin=1;                    
                           
	while ($fin<=$max)
	{        
		// pas utile, on efface déjà tout avant  
		mysql_query("DELETE FROM phpl_clmnt_pronos WHERE id_champ='$gr_champ' AND type='mixte'") or die (mysql_error());

		// Calcul des points de chaque membre jusqu'à cette journée
		$query="SELECT id_membre, pseudo, sum(points) as total, sum(points_hourra) as total_hourra, sum(participation) as participations 
				FROM phpl_membres, phpl_pronostics, phpl_matchs, phpl_journees
				WHERE phpl_pronostics.id_champ='$gr_champ' 
				AND id_membre=phpl_membres.id
				AND phpl_pronostics.id_match=phpl_matchs.id
				AND phpl_journees.id=phpl_matchs.id_journee			
				AND phpl_journees.id_champ='$champ'
				AND phpl_journees.numero>='$debut'
				AND phpl_journees.numero<='$fin'			
				GROUP by pseudo
				ORDER by total, participations";			
		
		$result=mysql_query ($query);
	
		$total_max = 0;
		$total_hourra_max = 0;
		$nb_users = 0;
		while ($row=mysql_fetch_array($result))
		{	
			if($row["total"] > $total_max)
			{
				$total_max = $row["total"];
			}
			 if($row["total_hourra"] > $total_hourra_max)
			{
				$total_hourra_max = $row["total_hourra"];
			} 
			
			$nb_users++;
		}
	
		mysql_data_seek($result, 0);

		while ($row=mysql_fetch_array($result))
		{	
			
			$pts_mixte = ($row["total"]/$total_max)*25 + ($row["total_hourra"]/$total_hourra_max)*25 + (($nb_users-$arrayClmnt["general"][$fin][$row[0]]+1)/$nb_users)*25 + (($nb_users-$arrayClmnt["hourra"][$fin][$row[0]]+1)/$nb_users)*25;
			mysql_query("INSERT INTO phpl_clmnt_pronos (id_champ, id_membre, pseudo, points, participation, type) values ('$gr_champ', '$row[0]', '$row[1]', '".$pts_mixte."', '$row[4]', 'mixte')") or die (mysql_error());
		}	
		                   
		$query="SELECT id_membre, points, participation 
				FROM phpl_clmnt_pronos 
				WHERE id_champ='$gr_champ'
				AND type='mixte'
				ORDER BY points DESC, participation ASC, pseudo";
		$result=mysql_query($query) or die (mysql_error());
		$pl=0;
		$i=0;
		$points_precedents="";
		
		while ($row=mysql_fetch_array($result))
		{   
			$id_membre=$row["id_membre"];
			$points=$row["points"];
			$participations=$row["participation"];
			
			$i++;
			if($points!=$points_precedents)
			{
				$pl=$i;
				$points_precedents=$points; 
			} 		
				
			$query="INSERT INTO phpl_pronos_graph (id_membre, id_gr_champ, fin, classement, type, points, participations) VALUES ('$id_membre', '$gr_champ', '$fin', '$pl', 'mixte', '$points', '$participations')" ;
			mysql_query($query); 	                 
		}
		
		$fin++;
	}
	// On génére le classement général une derniere fois en prenant en compte toutes les journées
	// -> ces données seront utilisées pour l'affichage du classement général actuel
	mysql_query("DELETE FROM phpl_clmnt_pronos WHERE id_champ='$gr_champ' AND type='mixte'") or die (mysql_error());
	$query="SELECT id_membre, pseudo, sum(points) as total, sum(points_hourra) as total_hourra, sum(participation) as participations FROM phpl_membres, phpl_pronostics
	WHERE id_champ='$gr_champ' AND id_membre=phpl_membres.id
	GROUP by pseudo
	ORDER by total, participations";
	
	$result=mysql_query ($query);
	
	$total_max = 0;
	$total_hourra_max = 0;
	while ($row=mysql_fetch_array($result))
	{	
		if($row["total"] > $total_max)
		{
			$total_max = $row["total"];
		}
		 if($row["total_hourra"] > $total_hourra_max)
		{
			$total_hourra_max = $row["total_hourra"];
		} 
	}
	
	mysql_data_seek($result, 0);
	
	while ($row=mysql_fetch_array($result))
	{	
		$pts_mixte = ($row["total"]/$total_max)*25 + ($row["total_hourra"]/$total_hourra_max)*25 + (($nb_users-$arrayClmnt["general"][$fin-1][$row[0]]+1)/$nb_users)*25 + (($nb_users-$arrayClmnt["hourra"][$fin-1][$row[0]]+1)/$nb_users)*25;
		mysql_query("INSERT INTO phpl_clmnt_pronos (id_champ, id_membre, pseudo, points, participation, type) values ('$gr_champ', '$row[0]', '$row[1]', '".$pts_mixte."', '$row[4]', 'mixte')") or die (mysql_error());
	}
	
	
	// Mise à jour de la colonne 'place'
	$queryPlace="SELECT clmnt.id_membre, clmnt.pseudo, clmnt.points, clmnt.participation 
				FROM phpl_clmnt_pronos as clmnt
				WHERE clmnt.id_champ='$gr_champ' AND clmnt.type='mixte'
				GROUP by clmnt.pseudo
				ORDER by  clmnt.points desc, clmnt.participation asc, clmnt.pseudo";
	$resultPlace=mysql_query($queryPlace) or die ("probleme " .mysql_error());
	$i=1;
	$classement=$i;
	$points_precedents=0;
	while ($rowPlace=mysql_fetch_array($resultPlace))
	{
		if($rowPlace[2] != $points_precedents)
		{
			$points_precedents = $rowPlace[2];
			$classement = $i;
		}
	
		mysql_query("UPDATE phpl_clmnt_pronos SET place='".$classement."' WHERE pseudo='".$rowPlace[1]."' AND type='mixte' AND id_champ='$gr_champ'") or die (mysql_error());
		$i++;
	}
			
	echo "<li>Graphiques et classement générés pour le classement mixte</li>";
}
	
/************************/

echo "</ul>";


echo ADMIN_GRAPH_PRONO; include ("tps2.php3");

?>
</td></tr></table>
