<?
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

//if (!$go=="1"){include ("inscription obligatoire.htm");}
if (!$go=="1"){include ("inscription.htm");}

elseif ($go=="1")
{
       // On vérifie que le pseudo n'est pas utilisé
       $requete = "SELECT * FROM phpl_membres where pseudo='$pseudo'";
       $resultat=mysql_query($requete);
       $nb_pseudo=mysql_num_rows($resultat);
       if ($nb_pseudo>=1){$message.=PRONO_INSCRIPTION_PSEUDO_UTILISE."<br />";}
       elseif (strlen($pseudo)<4 or strlen($pseudo)>20) {$message.=PRONO_INSCRIPTION_PSEUDO_TAILLE."<br />";}
       else {$pseudo_verif="ok";}
       
       // On vérifie que le mail
       $requete = "SELECT * FROM phpl_membres where mail='$mail'";
       $resultat=mysql_query($requete);
       $nb_mail=mysql_num_rows($resultat);
       if ($nb_mail>=1){$message.=PRONO_INSCRIPTION_MAIL_UTILISE."<br />";}
       else {$mail_verif="ok";}

       if (empty($mail)){$message.= PRONO_INSCRIPTION_MAIL_VIDE."\n<br />\n";}
       else //l'email a ete entree, on la verifie
	{
		//verification de la syntaxe
		$mail_ok = eregi("^[_\.0-9a-z-]+@([0-9a-z-]+\.)+[a-z]{2,4}$",$mail);

	
	if (!$mail_ok)
	{
		$message .= PRONO_INSCRIPTION_MAIL_INVALIDE_1." \"$mail\ ".PRONO_INSCRIPTION_MAIL_INVALIDE_2."\n<br />\n";
	}
	else {$email_verif="ok";}
	}

	if (empty($mdp) or empty($mdp2)){$message.= PRONO_INSCRIPTION_JS_MDP."\n<br />\n";}
	elseif ($mdp!==$mdp2) {$message.= PRONO_INSCRIPTION_JS_DIFF."\n<br />\n";}
	elseif (strlen($mdp)<4 or strlen($mdp)>20) {$message.=PRONO_INSCRIPTION_PSEUDO_TAILLE."<br />";}
	else {$mdp_verif="ok";}
	


if ($email_verif=="ok" and $pseudo_verif=="ok" and $mdp_verif=="ok" and $mail_verif=="ok")
{
  $taille = 19;
  $lettres = "abcdefghijklmnopqrstuvwxyz0123456789";
  srand(time());
    for ($i=0;$i<$taille;$i++)
    {
     $id_prono.=substr($lettres,(rand()%(strlen($lettres))),1);
    }
        
  $mdpcrypt=md5($mdp);


  $date_naissance=$annee."-".$mois."-".$jour;
  $adresse=$adresse1." ".$adresse2;
    if (!empty($mobile1) and !empty($mobile2) and !empty($mobile3) and !empty($mobile4) and !empty($mobile5))
    {
     $mobile=$mobile1."-".$mobile2."-".$mobile3."-".$mobile4."-".$mobile5;
    }

  mysql_query("INSERT INTO phpl_membres (pseudo, id_prono, mot_de_passe, mail, nom_site, nom, prenom, adresse, code_postal, ville, pays, date_naissance, profession, mobile )
               VALUES ('$pseudo', '$id_prono', '$mdpcrypt', '$mail', '$site', '$nom', '$prenom', '$adresse', '$code_postal', '$ville', '$pays', '$date_naissance', '$profession', '$mobile' )") or die ("probleme " .mysql_error());

  $requete="SELECT id FROM phpl_membres WHERE id_prono='$id_prono'";
  $result=mysql_query($requete) or die ("probleme " .mysql_error());
  $row=mysql_fetch_array($result);
  $id_membre=$row[0];

  mysql_query("INSERT INTO phpl_pronostics (id_membre, id_champ) VALUES ('$id_membre', '$gr_champ')") or die ("probleme " .mysql_error());

  $requete = "SELECT pseudo, mail, nom_site, url_site FROM phpl_membres WHERE admin='1'";
  $result=mysql_query($requete) or die ("probleme " .mysql_error());
  $row=mysql_fetch_array($result);
  $pseudo_admin=$row[0];
  $mail_admin=$row[1];
  $nom_site_admin=$row[2];
  $url_site_admin=$row[3];  


$to="$pseudo <$mail>";

$sujet="Inscription aux pronostics sur $nom_site_admin";

$message="<html><head><title>Phpleague</title></head><body>
<p><font size=\"2\" face=\"Verdana\">Bonjour et bienvenue au concours
de pronostics !</font></p>
<p><font size=\"2\" face=\"Verdana\">Vous venez de vous inscrire au jeu concours de pronostics du site <a href=\"$url_site_admin\">$nom_site_admin</a>.</font></p>
<p><font size=\"2\" face=\"Verdana\">Voici les informations qui vous
permettront d'accéder à votre compte :</font></p>
<p><font face=\"Verdana\" size=\"2\">Login :&nbsp;  $pseudo 
<br />
Mot de passe :  $mdp </font></p>
<p><font size=\"2\" face=\"Verdana\">Attention votre mot de passe est crypté dans
notre base de donnée il est donc inutile de nous le réclamer en cas de perte.
<br />
<br />
Bonne chance !</font></p>
<p><font face=\"Verdana\" size=\"2\"><a href=\"mailto:$mail_admin\">$pseudo_admin</a></font></p>
<p><font face=\"Verdana\" size=\"2\">--------------------------------------------------------------------</font></p>
<p><font face=\"Verdana\" size=\"2\">Ce script a été créé par
<a href=\"http://phpleague.univert.org\">Phpleague</a> : Gestionnaire de championnats sportifs et de pronostics !
</font></p>
</body></html>";



  $from="Content-Type: text/html; charset=\"iso-8859-15\"\nFrom: $mail_admin\n";
  $email=@mail($to,$sujet,$message,$from);
  if ($email)
  {
    echo "<table align=\"center\">";
    echo "<tr><td colspan=\"2\" align=\"center\"><font face=\"Verdana\" color=\"#3b487f\" size=\"1\">Inscription réalisée avec succès !
    Un email vous a été adressé pour vous communiquez votre mot de passe !<br /><a href=\"\">Connexion</a></font></td></tr>";
    echo "</table>";
  }
  else 
  {
    echo "<table align=\"center\">";
    echo "<tr><td colspan=\"2\" align=\"center\"><font face=\"Verdana\" color=\"#3b487f\" size=\"1\">L'envoie de l'e-mail a échoué !<br /><a href=\"\">Connexion !</a></font></td></tr>";
    echo "</table>";
  }

}


else
{
  echo "<table align=\"center\">";
  echo "<tr><td colspan=\"2\" align=\"center\"><font face=\"Verdana\" color=\"#3b487f\" size=\"1\">$message<br /><a href=\"javascript:history.back(1)\">Retour</a></font></td></tr>";
  echo "</table>";
}

}
?>
