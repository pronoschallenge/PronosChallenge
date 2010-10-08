<?php

	/*
  	$queryMeilleuresPerfs="SELECT phpl_membres.pseudo, phpl_journees.numero, sum(phpl_pronostics.points) as pts FROM phpl_journees
		LEFT OUTER JOIN phpl_matchs ON phpl_journees.id=phpl_matchs.id_journee
		LEFT OUTER JOIN phpl_pronostics ON phpl_matchs.id=phpl_pronostics.id_match
		LEFT OUTER JOIN phpl_membres ON phpl_membres.id=phpl_pronostics.id_membre
		WHERE phpl_journees.id_champ=10
		GROUP BY phpl_journees.id, phpl_membres.id
		ORDER BY pts DESC, phpl_journees.id, phpl_membres.id";
  	$resultMeilleuresPerfs=mysql_query($queryMeilleuresPerfs) or die (mysql_error());
  	*/
	
	/* STATS GLOBALES */
	
	class Serie
	{
		var $user;
		var $equipe;
		var $journee_debut;
		var $journee_fin;
		var $en_cours;
	}
	
  	$queryEquipeMieuxPronoDom="SELECT phpl_matchs.id_equipe_dom, phpl_clubs.nom , count(*) as nb_pronos FROM phpl_journees
		LEFT OUTER JOIN phpl_matchs ON phpl_journees.id=phpl_matchs.id_journee
		LEFT OUTER JOIN phpl_pronostics ON phpl_matchs.id=phpl_pronostics.id_match
		LEFT OUTER JOIN phpl_equipes ON phpl_matchs.id_equipe_dom=phpl_equipes.id
		LEFT OUTER JOIN phpl_clubs ON phpl_equipes.id_club=phpl_clubs.id
		WHERE phpl_pronostics.id_champ=$gr_champ
		AND phpl_pronostics.points>0
		GROUP BY phpl_matchs.id_equipe_dom
		ORDER BY nb_pronos DESC";
 	$resultEquipeMieuxPronoDom=mysql_query($queryEquipeMieuxPronoDom) or die (mysql_error());
	
	$queryEquipeMieuxPronoExt="SELECT phpl_matchs.id_equipe_ext, phpl_clubs.nom , count(*) as nb_pronos FROM phpl_journees
		LEFT OUTER JOIN phpl_matchs ON phpl_journees.id=phpl_matchs.id_journee
		LEFT OUTER JOIN phpl_pronostics ON phpl_matchs.id=phpl_pronostics.id_match
		LEFT OUTER JOIN phpl_equipes ON phpl_matchs.id_equipe_ext=phpl_equipes.id
		LEFT OUTER JOIN phpl_clubs ON phpl_equipes.id_club=phpl_clubs.id
		WHERE phpl_pronostics.id_champ=$gr_champ
		AND phpl_pronostics.points>0
		GROUP BY phpl_matchs.id_equipe_ext
		ORDER BY nb_pronos DESC";
 	$resultEquipeMieuxPronoExt=mysql_query($queryEquipeMieuxPronoExt) or die (mysql_error());
	
	$tabEquipeMieuxPronoTotal = array();
	while ($rowEquipeMieuxPronoDom=mysql_fetch_array($resultEquipeMieuxPronoDom)) {
		$hasPronoDom = true;
		// domicile
		$tabEquipeMieuxPronoTotal[$rowEquipeMieuxPronoDom[1]][1] = $rowEquipeMieuxPronoDom[2];
	}
	while ($rowEquipeMieuxPronoExt=mysql_fetch_array($resultEquipeMieuxPronoExt)) {
		$hasPronoExt = true;
		//extérieur
		$tabEquipeMieuxPronoTotal[$rowEquipeMieuxPronoExt[1]][2] = $rowEquipeMieuxPronoExt[2];
		// total
		$tabEquipeMieuxPronoTotal[$rowEquipeMieuxPronoExt[1]][0] = $tabEquipeMieuxPronoTotal[$rowEquipeMieuxPronoExt[1]][1] + $rowEquipeMieuxPronoExt[2];
	}
	
	if($hasPronoDom) {	
		mysql_data_seek($resultEquipeMieuxPronoDom, 0);
	}
	if($hasPronoExt) {
		mysql_data_seek($resultEquipeMieuxPronoExt, 0);  
	}

	// Series en cours
  	$querySeriesEnCours="SELECT phpl_membres.pseudo as pseudo, club_dom.nom as club_dom, club_ext.nom as club_ext, phpl_pronostics.points as points, phpl_journees.numero as journee FROM phpl_pronostics
		LEFT OUTER JOIN phpl_matchs ON phpl_matchs.id=phpl_pronostics.id_match
		LEFT OUTER JOIN phpl_journees ON phpl_journees.id=phpl_matchs.id_journee
		LEFT OUTER JOIN phpl_equipes eq_dom ON eq_dom.id=phpl_matchs.id_equipe_dom
		LEFT OUTER JOIN phpl_clubs club_dom ON club_dom.id=eq_dom.id_club
		LEFT OUTER JOIN phpl_equipes eq_ext ON eq_ext.id=phpl_matchs.id_equipe_ext
		LEFT OUTER JOIN phpl_clubs club_ext ON club_ext.id=eq_ext.id_club
		LEFT OUTER JOIN phpl_membres ON phpl_membres.id=phpl_pronostics.id_membre
		WHERE phpl_pronostics.id_champ=$gr_champ
		AND phpl_matchs.buts_dom IS NOT NULL
		ORDER BY phpl_journees.numero";
 	$resultSeriesEnCours=mysql_query($querySeriesEnCours) or die (mysql_error());

	$derniere_journee = 0;

	$series = array();
	$seriesTmp = array();
	while ($rowSeriesEnCours=mysql_fetch_array($resultSeriesEnCours)) {
		if($derniere_journee < $rowSeriesEnCours['journee']) {
			$derniere_journee = $rowSeriesEnCours['journee'];
		}
		
		// si prono correct
		if($rowSeriesEnCours['points'] > 0) {
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
				$serie->en_cours = false;
				
				$series[] = $serie;
			}
						
			// remise à zéro des séries pour ces équipes
			$seriesTmp[$rowSeriesEnCours['pseudo']][$rowSeriesEnCours['club_dom']] = 0;
			$seriesTmp[$rowSeriesEnCours['pseudo']][$rowSeriesEnCours['club_ext']] = 0;
		}	
	}
	
	// Traitement des séries en cours
	foreach($seriesTmp as $user=>$serieFinSaison) {
		foreach($serieFinSaison as $equipe=>$nb) {
			if($nb > 5) {
				$serie = new Serie();
				$serie->user = $user;
				$serie->equipe = $equipe;
				$serie->journee_fin = $derniere_journee;
				$serie->journee_debut = $serie->journee_fin - $nb + 1;
				$serie->en_cours = true;
				
				$series[] = $serie;			
			}
		}
	}

	/* STATS PRONOSTIQUEUR */
	if($connecte) {
		$queryEquipeMieuxPronoDomUser="SELECT phpl_matchs.id_equipe_dom, phpl_clubs.nom , count(*) as nb_pronos FROM phpl_journees
			LEFT OUTER JOIN phpl_matchs ON phpl_journees.id=phpl_matchs.id_journee
			LEFT OUTER JOIN phpl_pronostics ON phpl_matchs.id=phpl_pronostics.id_match
			LEFT OUTER JOIN phpl_equipes ON phpl_matchs.id_equipe_dom=phpl_equipes.id
			LEFT OUTER JOIN phpl_clubs ON phpl_equipes.id_club=phpl_clubs.id
			WHERE phpl_pronostics.id_champ=$gr_champ
			AND phpl_pronostics.points>0
			AND phpl_pronostics.id_membre='$user_id'
			GROUP BY phpl_matchs.id_equipe_dom
			ORDER BY nb_pronos DESC";
		$resultEquipeMieuxPronoDomUser=mysql_query($queryEquipeMieuxPronoDomUser) or die (mysql_error());
		
		$queryEquipeMieuxPronoExtUser="SELECT phpl_matchs.id_equipe_ext, phpl_clubs.nom , count(*) as nb_pronos FROM phpl_journees
			LEFT OUTER JOIN phpl_matchs ON phpl_journees.id=phpl_matchs.id_journee
			LEFT OUTER JOIN phpl_pronostics ON phpl_matchs.id=phpl_pronostics.id_match
			LEFT OUTER JOIN phpl_equipes ON phpl_matchs.id_equipe_ext=phpl_equipes.id
			LEFT OUTER JOIN phpl_clubs ON phpl_equipes.id_club=phpl_clubs.id
			WHERE phpl_pronostics.id_champ=$gr_champ
			AND phpl_pronostics.points>0
			AND phpl_pronostics.id_membre='$user_id'
			GROUP BY phpl_matchs.id_equipe_ext
			ORDER BY nb_pronos DESC";
		$resultEquipeMieuxPronoExtUser=mysql_query($queryEquipeMieuxPronoExtUser) or die (mysql_error());
		
		$tabEquipeMieuxPronoTotalUser = array();
		while ($rowEquipeMieuxPronoDomUser=mysql_fetch_array($resultEquipeMieuxPronoDomUser)) {
			$hasPronoDomUser = true;
			// domicile
			$tabEquipeMieuxPronoTotalUser[$rowEquipeMieuxPronoDomUser[1]][1] = $rowEquipeMieuxPronoDomUser[2];
		}
		while ($rowEquipeMieuxPronoExtUser=mysql_fetch_array($resultEquipeMieuxPronoExtUser)) {
			$hasPronoExtUser = true;
			// extérieur
			$tabEquipeMieuxPronoTotalUser[$rowEquipeMieuxPronoExtUser[1]][2] = $rowEquipeMieuxPronoExtUser[2];
			// total
			$tabEquipeMieuxPronoTotalUser[$rowEquipeMieuxPronoExtUser[1]][0] = $tabEquipeMieuxPronoTotalUser[$rowEquipeMieuxPronoExtUser[1]][1] + $rowEquipeMieuxPronoExtUser[2];
		}
			
		if($hasPronoDomUser) {
			mysql_data_seek($resultEquipeMieuxPronoDomUser, 0);
		}
		if($hasPronoExtUser) {
			mysql_data_seek($resultEquipeMieuxPronoExtUser, 0);  
		}
	}
?>

<!--<script type="text/javascript" src="../javascript/dataTables-1.5/media/js/jquery.js"></script>-->
<script type="text/javascript" src="../lib/jquery/plugins/dataTables/js/jquery.dataTables.js"></script>
<link rel="stylesheet" href="../lib/jquery/plugins/dataTables/css/demo_page.css" type="text/css" media="screen" />
<link rel="stylesheet" href="../lib/jquery/plugins/dataTables/css/demo_table.css" type="text/css" media="screen" />
<style>
.stats_row {
	width: 100%;
	float: left;
	padding-bottom: 5px;
}
.stats {
	padding-top: 0px;
	padding-right: 5px;
	padding-bottom: 3px;
	padding-left: 5px;
}
.stats_title {
	padding: 5px 10px;
	cursor: pointer;
	position: relative;
	background-color:#F4EC99;
	font-weight: bold;
	font-size: 9pt;
}
.stats_body {
	padding: 5px 5px;
	border: 1px solid #CCCCCC
}
</style>
<script>

$(document).ready(function() {

	//hide the all of the element with class msg_body
	//$(".stats_body").hide();
	//toggle the componenet with class msg_body
	$(".stats_title").click(function()
	{
		$(this).next(".stats_body").slideToggle(600);
	});

	$('#stats_table_0').dataTable( {
		"bProcessing": true,
		"bServerSide": false,
		"aaSorting": [[1,'desc']],
		"bFilter": false,
		"bPaginate": false,
		"bInfo": false,
		"sDom": 'lirtfp',
		"aoColumns": [ 
			null,
			{ "sClass": "center" },
			{ "sClass": "center" },
			{ "sClass": "center" }
		],		
		"oLanguage": {
			"sProcessing": "Chargement...",
			"sInfoEmpty": "Aucune donnée"
		}
	} );	
	
	$('#stats_table_3').dataTable( {
		"bProcessing": true,
		"bServerSide": true,
		"aaSorting": [[2,'desc'], [1,'asc']],
		"bFilter": false,
		"sDom": 'lirtfp',
		"sPaginationType": "full_numbers",
		"aoColumns": [ 
			null,
			{ "sClass": "center" },
			{ "sClass": "center" }
		],		
		"oLanguage": {
			"oPaginate": {
				"sFirst": "<<",
				"sLast": ">>",
				"sNext": ">",
				"sPrevious": "<"
			},
			"sProcessing": "Chargement...",
			"sInfo": "_START_-_END_ / _TOTAL_",
			"sInfoEmpty": "Aucune donnée",
			"sLengthMenu": "_MENU_ lignes"
		},
		"sAjaxSource": "./stats_perfs_json.php?gr_champ=<? print $gr_champ?>"
	} );

	$('#stats_table_series').dataTable( {
		"bProcessing": true,
		"bServerSide": false,
		"aaSorting": [[2,'desc']],
		"bFilter": false,
		"bPaginate": true,
		"bInfo": true,
		"sDom": 'lirtfp',
		"aoColumns": [ 
			{ "sClass": "center" },
			{ "sClass": "center" },
			{ "sClass": "center" }
		],	
		"oLanguage": {
			"oPaginate": {
				"sFirst": "<<",
				"sLast": ">>",
				"sNext": ">",
				"sPrevious": "<"
			},
			"sProcessing": "Chargement...",
			"sInfo": "_START_-_END_ / _TOTAL_",
			"sInfoEmpty": "Aucune donnée",
			"sLengthMenu": "_MENU_ lignes"
		}
	} );
	
	$('#stats_table_10').dataTable( {
		"bProcessing": true,
		"bServerSide": false,
		"aaSorting": [[1,'desc']],
		"bFilter": false,
		"bPaginate": false,
		"bInfo": false,
		"sDom": 'lirtfp',
		"aoColumns": [ 
			null,
			{ "sClass": "center" }
		],		
		"oLanguage": {
			"sProcessing": "Chargement...",
			"sInfoEmpty": "Aucune donnée"
		}
	} );

	$('#stats_table_11').dataTable( {
		"bProcessing": true,
		"bServerSide": false,
		"aaSorting": [[1,'desc']],
		"bFilter": false,
		"bPaginate": false,
		"bInfo": false,
		"sDom": 'lirtfp',
		"aoColumns": [ 
			null,
			{ "sClass": "center" }
		],		
		"oLanguage": {
			"sProcessing": "Chargement...",
			"sInfoEmpty": "Aucune donnée"
		}
	} );
	
	$('#stats_table_12').dataTable( {
		"bProcessing": true,
		"bServerSide": false,
		"aaSorting": [[1,'desc']],
		"bFilter": false,
		"bPaginate": false,
		"bInfo": false,
		"sDom": 'lirtfp',
		"aoColumns": [ 
			null,
			{ "sClass": "center" }
		],		
		"oLanguage": {
			"sProcessing": "Chargement...",
			"sInfoEmpty": "Aucune donnée"
		}
	} );	
	
} );

</script>

<div>
	<form method="get" action=""><div align="center">
   		<input type="hidden" name="page" value="stats">
   Changer de saison : 
   <select name="gr_champ" onchange="submit();">
	<?php       
	$query = "SELECT phpl_gr_championnats.nom, phpl_gr_championnats.id
			 FROM phpl_gr_championnats
			 ORDER BY phpl_gr_championnats.id DESC";

	$result = mysql_query($query) or die (mysql_error());

	while ($row = mysql_fetch_array($result)) {
		echo ("<option value=\"$row[1]\" ");
		if(isset($gr_champ) && $gr_champ == $row[1]) {
			echo "selected";
		}
		echo (">$row[0]</option>");
	}
	$button=ENVOI;
	?>
	</select>

	</div></form>
</div>


<div class="bloc bloc_stats">
	<div class="rounded-block-top-left"></div>
	<div class="rounded-block-top-right"></div>
	<div class="rounded-outside">
		<div class="rounded-inside">
			<div class="bloc_entete">
				<div class="bloc_icone"></div>
				<div class="bloc_titre">Stats générales</div>
			</div>
			<div class="bloc_contenu">

	<div class="stats_row">
		<!-- Equipe la mieux pronostiquée -->
		<div class="stats" style="float:left;width:48%;">
			<div class="stats_title">
				Equipes les mieux pronostiquées
			</div>
			<div class="stats_body">
				<table id="stats_table_0" class="display">
					<thead>
						<tr>
							<th align="center" width="40%">Equipe</th>
							<th align="center" width="20%">Total</th>
							<th align="center" width="20%">Dom.</th>
							<th align="center" width="20%">Ext.</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach($tabEquipeMieuxPronoTotal as $equipe=>$nb) { ?>
						<tr>
							<td><?php echo $equipe?></td>
							<td style="text-align: center"><?php echo $nb[0]?></td>
							<td style="text-align: center"><?php echo $nb[1]?></td>
							<td style="text-align: center"><?php echo $nb[2]?></td>
						</tr>
						<?php } ?>
					</tbody>				
				</table>		
			</div>
		</div>

		<div class="stats" style="float:left;width:48%;">
			<div class="stats_title">
				Répartition des pronostics
			</div>
			<div class="stats_body">
				<img src="stats_repartition_pronos.php?gr_champ=<? print $gr_champ?>" />
			</div>
		</div>

		<div class="stats" style="float:left;width:48%;">
			<div class="stats_title">
				Meilleures perfs sur une journée
			</div>
			<div class="stats_body">
				<table id="stats_table_3" class="display">
					<thead>
						<tr>
							<th align="center" width="50%">Pronostiqueur</th>
							<th align="center" width="25%">Journée</th>
							<th align="center" width="25%">Score</th>
						</tr>
					</thead>
					<tbody>

					</tbody>				
				</table>		
			</div>
		</div>

		<div class="stats" style="float:left;width:48%;">
			<div class="stats_title">
				Meilleures séries
			</div>
			<div class="stats_body">
				<table id="stats_table_series" class="display">
					<thead>
						<tr>
							<th align="center" width="50%">Pronostiqueur</th>
							<th align="center" width="25%">Equipe</th>
							<th align="center" width="25%">Nb</th>
						</tr>
					</thead>
					<tbody>
<?
				for($i=0; $i < sizeof($series); $i++) {
?>
						<tr <?if($series[$i]->en_cours) echo "class='encours'"?>>
							<td><? echo $series[$i]->user ?></td>
							<td><? echo $series[$i]->equipe ?></td>
							<td><? echo ($series[$i]->journee_fin - $series[$i]->journee_debut +1) ?></td>
						</tr>
<?
				}
?>
					</tbody>				
				</table>		
			</div>
		</div>

	</div>

			</div>
		</div>
	</div>
	<div class="rounded-block-bottom-left"></div>
	<div class="rounded-block-bottom-right"></div>
</div>	

<br/>

<? if($connecte) { ?>

<div class="bloc bloc_stats">
	<div class="rounded-block-top-left"></div>
	<div class="rounded-block-top-right"></div>
	<div class="rounded-outside">
		<div class="rounded-inside">
			<div class="bloc_entete">
				<div class="bloc_icone"></div>
				<div class="bloc_titre">Mes stats</div>
			</div>
			<div class="bloc_contenu">

<div>
	<div class="stats_row">
		<!-- Equipe la mieux pronostiquée -->
		<div class="stats" style="float:left;width:48%;">
			<div class="stats_title">
				Equipes les mieux pronostiquées
			</div>
			<div class="stats_body">
				<table id="stats_table_10" class="display">
					<thead>
						<tr>
							<th align="center" width="50%">Equipe</th>
							<th align="center" width="50%">Total</th>
							<th align="center" width="50%">Dom.</th>
							<th align="center" width="50%">Ext.</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach($tabEquipeMieuxPronoTotalUser as $equipe=>$nb) { ?>
						<tr>
							<td><?php echo $equipe?></td>
							<td style="text-align: center"><?php echo $nb[0]?></td>
							<td style="text-align: center"><?php echo $nb[1]?></td>
							<td style="text-align: center"><?php echo $nb[2]?></td>
						</tr>
						<?php } ?>
					</tbody>				
				</table>		
			</div>
		</div>

		<!-- Equipe la mieux pronostiquée à domicile -->
<!--
		<div class="stats" style="float:left;width:32%;">
			<div class="stats_title">
				... à domicile
			</div>
			<div class="stats_body">
				<table id="stats_table_11" class="display">
					<thead>
						<tr>
							<th align="center" width="50%">Equipe</th>
							<th align="center" width="50%">Nb</th>
						</tr>
					</thead>
					<tbody>
						<?php while ($rowEquipeMieuxPronoDomUser=mysql_fetch_array($resultEquipeMieuxPronoDomUser)) { ?>
						<tr>
							<td><?php echo $rowEquipeMieuxPronoDomUser[1]?></td>
							<td style="text-align: center"><?php echo $rowEquipeMieuxPronoDomUser[2]?></td>
						</tr>
						<?php } ?>
					</tbody>				
				</table>		
			</div>
		</div>
-->
		<!-- Equipe la mieux pronostiquée à l'extérieur -->
<!--
		<div class="stats" style="float:left;width:32%;">
			<div class="stats_title">
				... à l'ext&eacute;rieur
			</div>
			<div class="stats_body">
				<table id="stats_table_12" class="display">
					<thead>
						<tr>
							<th align="center" width="50%">Equipe</th>
							<th align="center" width="50%">Nb</th>
						</tr>
					</thead>
					<tbody>
						<?php while ($rowEquipeMieuxPronoExtUser=mysql_fetch_array($resultEquipeMieuxPronoExtUser)) { ?>
						<tr>
							<td><?php echo $rowEquipeMieuxPronoExtUser[1]?></td>
							<td style="text-align: center"><?php echo $rowEquipeMieuxPronoExtUser[2]?></td>
						</tr>
						<?php } ?>
					</tbody>				
				</table>		
			</div>
		</div>
-->
		<div class="stats" style="float:left;width:48%;">
			<div class="stats_title">
				Répartition des pronostics
			</div>
			<div class="stats_body">
				<img src="stats_repartition_pronos_user.php?user_id=<? print $user_id ?>&gr_champ=<? print $gr_champ?>" />
			</div>
		</div>
	</div>

	<div class="stats" style="float:left;width:98%;">
		<div class="stats_title">
			Historique des journées
		</div>
		<div class="stats_body">	
			<img src="stats_histo_journees.php?user_id=<? print $user_id ?>&gr_champ=<? print $gr_champ?>" />
		</div>
	</div>
</div>

			</div>
		</div>
	</div>
	<div class="rounded-block-bottom-left"></div>
	<div class="rounded-block-bottom-right"></div>
</div>	
<? } ?>
