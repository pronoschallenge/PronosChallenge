<?php
	// Déclaration des paramètres de connexion
	$host = "sql.free.fr";  
	$bdd = "www_pronoschallenge";
	$user = "www.pronoschallenge";
	$passwd  = "24lemans";

	// Connexion au serveur
	mysql_connect($host, $user,$passwd) or die("erreur de connexion au serveur");
	mysql_select_db($bdd) or die("erreur de connexion a la base de donnees");
	
	$result = mysql_query("SELECT mail FROM phpl_membres");
	
	// liste des destinataires du message
	$adresse="";
		
	while($row = mysql_fetch_row($result))
	{
		if($row[0] != "")
		{
			if($adresse != "")
			{		
				$adresse = $adresse.",";
			}
			$adresse = $adresse.$row[0];
		}
	}
	
	mysql_close();
	
	// titre du message : zone sujet
	$sujet="Pensez à faire vos pronostics !!";
	
	// contenu du message
	$corps="Pensez à faire vos pronostics sur http://www.pronoschallenge.online.fr !!";
	
	// Création de l'entête du message
	// cette entete contient l'email de l'expéditeur ainsi que l'email pour la réponse.
	//$entete="From:Administrateur de www.pronoschallenge.online.fr\r\nReply-To:thomas.delhomenie@wanadoo.fr";
	$entete="";
	
	// envoi du mail
	mail ($adresse,$sujet,$corps,$entete);
?>