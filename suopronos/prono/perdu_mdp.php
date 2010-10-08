<div class="bloc bloc_motdepasseoublie">
	<div class="rounded-block-top-left"></div>
	<div class="rounded-block-top-right"></div>
	<div class="rounded-outside">
		<div class="rounded-inside">
			<div class="bloc_entete">
				<div class="bloc_icone"></div>
				<div class="bloc_titre">Mot de passe oublié</div>
			</div>
			<div class="bloc_contenu">

<?php
if(empty($mail_field)) 
{
	include ("perdu_mdp.htm");
}
elseif (!empty($mail_field))
{
	// si un mail a été saisi -> création aléatoire du nouveau mot de passe
	$taille = 8;
    $lettres = "abcdefghijklmnopqrstuvwxyz0123456789";
	srand(time());
	for ($i=0;$i<$taille;$i++)
	{
		$new_mot_de_passe.=substr($lettres,(rand()%(strlen($lettres))),1);
	}
	$new_mot_de_passe_crypt=md5($new_mot_de_passe);
	
	// mise à jour en base du nouveau mot de passe
	mysql_query("UPDATE phpl_membres SET mot_de_passe='$new_mot_de_passe_crypt' WHERE mail='$mail_field' 
					OR mail LIKE '".$mail_field.",%' 
					OR mail LIKE '%,".$mail_field."' 
					OR mail LIKE '%,".$mail_field.",%' ");

	// récupération des infos de l'utilisateur
	$query = mysql_query("SELECT pseudo, mail FROM phpl_membres WHERE mail='$mail_field'					
							OR mail LIKE '".$mail_field.",%' 
							OR mail LIKE '%,".$mail_field."' 
							OR mail LIKE '%,".$mail_field.",%' ");
  	
  	// si le nouveau mot de passe a bien été enregistré
	if (list($pseudo, $mail) = mysql_fetch_array($query))
	{                                                                                           
		$to="$mail_field";
		$sujet="Votre nouveau mot de passe pour PronosChallenge";
		$message="<html>
					<head>
					<title>PronosChallenge</title>
					</head>
					<body>
					<p><font face=\"Verdana\" size=\"2\">Bonjour,</font></p>
					<p><font face=\"Verdana\" size=\"2\">Vous avez demandé à recevoir un nouveau mot
					de passe pour accéder à <a href=\"http://www.pronoschallenge.fr\">PronosChallenge</a>.</font></p>
					<p><font face=\"Verdana\" size=\"2\">Votre pseudo : $pseudo</font></p>
					<p><font face=\"Verdana\" size=\"2\">Votre nouveau mot de passe : $new_mot_de_passe</font><br /></p>
					<p><font face=\"Verdana\" size=\"2\">Les admins de PronosChallenge</font></p>
					<p><font face=\"Verdana\" size=\"2\"><a href=\"http://www.pronoschallenge.fr\">http://www.pronoschallenge.fr</a></font></p>
					<p><font face=\"Verdana\" size=\"2\"><a href=\"mailto:pronoschallenge@pronoschallenge.fr\">pronoschallenge@pronoschallenge.fr</a></font></p>
					</body>
					</html>";
		//$from="Content-Type: text/html; charset=\"iso-8859-15\"\nFrom: pronoschallenge.info@online.fr\n";
		$headers = "From: pronoschallenge@pronoschallenge.fr\n";
		$headers .= "Bcc: pronoschallenge@pronoschallenge.fr"."\r\n";
		$headers .= "MIME-version: 1.0\n";
		$headers .= "Content-type: text/html; charset= iso-8859-1\n";		
		$email=mail($to,$sujet,$message,$headers);
		if($email)
		{
			// tout s'est bien passé !
			echo "<div>".PRONO_OUBLIE_TEXTE_2." :<br /><br /><b>$mail_field</b><br /><br />Vous allez le recevoir dans un instant.</div>";;			
		}
		else
		{
			// erreur lors de l'envoi du mail
			include ("perdu_mdp.htm");
			echo "<div>".PRONO_OUBLIE_TEXTE_3."</div><br />";
		}
	}
	else
	{
		// aucun compte correspondant à l'adresse mail saisie
		include ("perdu_mdp.htm");
		echo "<div style=\"color: red;\">".PRONO_OUBLIE_TEXTE_4."</div><br />";
	}
}
?>
				<br/>
			</div>
		</div>
	</div>
	<div class="rounded-block-bottom-left"></div>
	<div class="rounded-block-bottom-right"></div>
</div>
