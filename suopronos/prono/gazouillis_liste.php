<?php
	header('Content-Type: text/html; charset=ISO-8859-15');

	require_once("fonctions.php");
	require_once("../config.php");

	ouverture ();

	$debut = 0;
	if (isset($_GET['debut']))
	{
		$debut = $_GET['debut'];
	}

	// requete pour récupérer tous les gazouillis
	$requete="SELECT id_membre, pseudo, contenu, date_creation 
		FROM phpl_gazouillis, phpl_membres 
		WHERE phpl_gazouillis.id_membre=phpl_membres.id
		ORDER BY date_creation DESC
		LIMIT ".$debut.",10";

	$resultat=mysql_query($requete);
	
	$first = true;
	    
	while ($row=mysql_fetch_array($resultat))
	{
		$url_avatar = "./images/avatars/".$row["id_membre"].".gif";
		if(!remote_file_exists($url_avatar))
		{
			$url_avatar = "./images/avatars/no_avatar.png";
		}
		
		if($first)
		{
			$gazouilliClass = "premier_gazouilli";
			$first = false;
		}
		else
		{
			$gazouilliClass = "gazouilli";
		}
?>
<div class="<?php echo $gazouilliClass?>">
	<div class="gazouilleur_avatar"><img src="<?php echo $url_avatar; ?>" width="40px"/></div>
	<div class="gazouilleur"><?echo $row["pseudo"]?></div>
	<div class="contenu_gazouilli">
		<?echo $row["contenu"]?>
		<br/>
		<span class="date_gazouilli"><?echo date("d/m H:i", strtotime($row["date_creation"]));?></span>
	</div>
</div>
<?php		
	}
?>
