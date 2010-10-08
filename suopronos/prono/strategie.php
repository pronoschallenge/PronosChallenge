<?php

	if (isset($_POST['action'])) {$action=$_POST['action'];} else {$action='';}
	
	if($action=='valide_strategie') 
	{
?>
		<script type="text/javascript">
			/* on sélectionne l'onglet sur lequel on validé le formulaire */
			$(function() {
				$('#tabs').tabs('option', 'selected', 2);
			});
		</script>
<?php		
		// on enregistre les infos modifiées du user
		if (isset($_POST['straglob'])) {$straglob=$_POST['straglob'];} else {$straglob='';}

		$requete = "SELECT * FROM phpl_strategie WHERE id_membre='$user_id'";
		$resultat = mysql_query($requete);
		if($row= mysql_fetch_array($resultat))
		{
			$strQuery = "UPDATE phpl_strategie SET id_type=$straglob, priorite=0 WHERE id_membre='$user_id'";
			mysql_query($strQuery) or die ("probleme " .mysql_error());
		}
		else
		{
			$strQuery = "INSERT INTO phpl_strategie (id_membre, id_type, priorite) VALUES ($user_id, $straglob, 0)";
			mysql_query($strQuery) or die ("probleme " .mysql_error());			
		}
	}
	
	// On vérifie s'il y a des matchs en cours pour savoir si on desactive temporairement le choix de la strategie
	$strategieActive = "class=\"bouton\"";
	$requete="SELECT phpl_matchs.id, phpl_matchs.date_reelle, TIMEDIFF( phpl_matchs.date_reelle, NOW( ) ) , TIME_TO_SEC( TIMEDIFF( phpl_matchs.date_reelle, NOW( ) ) )
		FROM phpl_matchs, phpl_journees, phpl_gr_championnats
		WHERE phpl_gr_championnats.id = $gr_champ
		AND phpl_journees.id_champ = phpl_gr_championnats.id_champ
		AND phpl_matchs.id_journee = phpl_journees.id
		AND phpl_matchs.buts_dom IS NULL
		AND phpl_matchs.buts_ext IS NULL
		AND TIME_TO_SEC( TIMEDIFF( phpl_matchs.date_reelle, NOW( ) ) ) < 0";
	$resultat = mysql_query($requete);
	while ($row= mysql_fetch_array($resultat))
    {
		$strategieActive = "disabled=\"disabled\"";
		break;
	}
?>

<br />
<div style="width:80%">
<p">Cette page vous permet de définir votre stratégie de pronostics. Si vous oubliez de pronostiquer une journée, votre stratégie sera utilisée pour pronostiquer automatiquement à votre place.</p>
</div>

<br/>

<form id="strategie" action="index.php?page=compte" method="post">
	<input type="hidden" name="action" value="valide_strategie" />
	
	<div style="width:80%;text-align:left;">
		<b>Strat&eacute;gie globale</b>
		<hr />
	</div>
	
	<div style="width:80%;text-align:left;padding-left: 20px;">
<?
	// stratégie globale de l'utilisateur
	$requete = "SELECT * FROM phpl_strategie WHERE id_membre=$user_id";
	$resultat = mysql_query($requete);
    if ($row= mysql_fetch_array($resultat))
	{
		$userstraglob = $row['id_type'];
	}

	// on va rechercher les stratégies globales
	$requete = "SELECT * FROM phpl_strategie_type WHERE global=1";
	$resultat = mysql_query($requete);
    while ($row= mysql_fetch_array($resultat))
    {
?>		
		<input type="radio" name="straglob" value="<?php print($row["id"])?>" 
<?
		if($userstraglob == $row["id"]) {
			print(" checked=\"checked\"");
		}
?>
		/>&nbsp;&nbsp;<?php print($row["description"])?>
		<br />
<?
    }
?>		
	</div>
	
	<br />
	<input type="submit" value="Valider" <?php print($strategieActive)?>/>
	
</form>
	

