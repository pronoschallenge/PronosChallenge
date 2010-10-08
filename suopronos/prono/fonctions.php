<?
function affiche_points ($user_id, $gr_champ)
{
  $query="SELECT points FROM phpl_clmnt_pronos, phpl_membres WHERE phpl_membres.id=phpl_clmnt_pronos.id_membre and phpl_membres.id_prono='$user_id' and type='general' AND id_champ='$gr_champ'";
  $result=mysql_query($query) or die (mysql_error());
  if (mysql_num_rows($result)=="0") {$points=0;}
  while ($row=mysql_fetch_array($result))
  {
    $points=$row[0];
  }
  print $points;
}

// fonction permettant de récupérer la classement général de l'utilisateur pour le championnat donné
function getClmntUtilisateur ($user_id, $gr_champ, $type)
{
	$queryscore="SELECT points FROM phpl_clmnt_pronos, phpl_membres WHERE phpl_membres.id=phpl_clmnt_pronos.id_membre AND id_champ='$gr_champ' AND type='$type' AND id_prono='$user_id'";
	$resultscore=mysql_query($queryscore) or die (mysql_error());
	while ($row=mysql_fetch_array($resultscore))
	{
		$score=$row[0];
	}

	$query="SELECT id_prono, points FROM phpl_clmnt_pronos, phpl_membres WHERE phpl_membres.id=phpl_clmnt_pronos.id_membre AND id_champ='$gr_champ' AND type='$type' ORDER by points desc, participation asc, phpl_membres.pseudo";
	$result=mysql_query($query) or die (mysql_error());
	$i = 1;
	while ($row=mysql_fetch_array($result))
	{
		if ($row[0]==$user_id)
		{
			$clmnt=$i;
		}
		if($score<>$row[1])
		{
			$i++;
		}
	}

	return $clmnt;
/*
   $clmnt="NC";
 if (isset($class))
  {
  if ($class=="1"){print $class; echo PRONO_CLASSEMENT_PREMIER;}
  elseif ($class=="2"){print $class; echo PRONO_CLASSEMENT_SECOND;}
  elseif ($class=="3"){print $class; echo PRONO_CLASSEMENT_TROIS;}
  else {print $class; echo PRONO_CLASSEMENT_AUTRES;}
  }
  if (!isset($class)) print $clmnt;
*/
}

function getClmntUtilisateurFormate ($user_id, $gr_champ, $type)
{
	$clmnt = getClmntUtilisateur($user_id, $gr_champ, $type);
	
	if (isset($clmnt))
	{
		switch($clmnt)
		{
			case 1 : 
				$clmnt .= PRONO_CLASSEMENT_PREMIER;
				break;
			case 2 :
				$clmnt .= PRONO_CLASSEMENT_SECOND;
				break;
			case 3 :
				$clmnt .= PRONO_CLASSEMENT_TROIS;
				break;
			default :
				$clmnt .= PRONO_CLASSEMENT_AUTRES;
		}
	}
	else
	{
		$clmnt = PRONO_CLASSEMENT_NON_CLASSE;
	}
	
	return $clmnt;
}

/*
// fonction utilisée dans les stats
function affiche_clmnt_mensuel_en_cours ($user_id, $gr_champ)
{
  $queryscore="SELECT points FROM phpl_clmnt_pronos, phpl_membres WHERE phpl_membres.id=phpl_clmnt_pronos.id_membre AND id_champ='$gr_champ' AND type='general' AND id_prono='$user_id' AND id_champ='$gr_champ'";
  $resultscore=mysql_query($queryscore) or die (mysql_error());
  while ($row=mysql_fetch_array($resultscore))
  {
    $score=$row[0];
  }

  $query="SELECT id_prono, points
          FROM phpl_clmnt_pronos, phpl_membres
          WHERE phpl_membres.id=phpl_clmnt_pronos.id_membre AND id_champ='$gr_champ' AND type='mensuel_en_cours' AND id_champ='$gr_champ' ORDER by points desc, participation asc, phpl_membres.pseudo";
  $result=mysql_query($query) or die (mysql_error());
  $i = "1";
  while ($row=mysql_fetch_array($result))
  { 
    if ($row[0]==$user_id){$class=$i;}
    if($score<>$row[1]){$i++;}
  }

  if (!isset($class)) {echo PRONO_CLASSEMENT_NON_CLASSE;}
  else
  {
  if ($class=="1"){print $class; echo PRONO_CLASSEMENT_PREMIER;}
  elseif ($class=="2"){print $class; echo PRONO_CLASSEMENT_SECOND;}
  elseif ($class=="3"){print $class; echo PRONO_CLASSEMENT_TROIS;}
  else {print $class; echo PRONO_CLASSEMENT_AUTRES;}
  }

}

// fonction utilisée dans les stats
function affiche_clmnt_hourra_en_cours ($user_id, $gr_champ)
{
  $queryscore="SELECT points FROM phpl_clmnt_pronos, phpl_membres WHERE phpl_membres.id=phpl_clmnt_pronos.id_membre AND id_champ='$gr_champ' AND type='hourra' AND id_prono='$user_id'";
  $resultscore=mysql_query($queryscore) or die (mysql_error());
  while ($row=mysql_fetch_array($resultscore))
  {
    $score=$row[0];
  }

  $query="SELECT id_prono, points
          FROM phpl_clmnt_pronos, phpl_membres
          WHERE phpl_membres.id=phpl_clmnt_pronos.id_membre AND id_champ='$gr_champ' AND type='hourra' ORDER by points desc, participation asc, phpl_membres.pseudo";
  $result=mysql_query($query) or die (mysql_error());
  $i = "1";
  while ($row=mysql_fetch_array($result))
  { 
    if ($row[0]==$user_id){$class=$i;}
    //echo "<script>alert('".$score." - ".$row[1]."');</script>";
    if($score<>$row[1]){$i++;}
  }

  if (!isset($class)) {echo PRONO_CLASSEMENT_NON_CLASSE;}
  else
  {
  if ($class=="1"){print $class; echo PRONO_CLASSEMENT_PREMIER;}
  elseif ($class=="2"){print $class; echo PRONO_CLASSEMENT_SECOND;}
  elseif ($class=="3"){print $class; echo PRONO_CLASSEMENT_TROIS;}
  else {print $class; echo PRONO_CLASSEMENT_AUTRES;}
  }

}

// fonction utilisée pour afficher le classement de l'utilisateur dans le classement mixte
function affiche_clmnt_mixte ($user_id, $gr_champ)
{
  $queryscore="SELECT points FROM phpl_clmnt_pronos, phpl_membres WHERE phpl_membres.id=phpl_clmnt_pronos.id_membre AND id_champ='$gr_champ' AND type='mixte' AND id_prono='$user_id'";
  $resultscore=mysql_query($queryscore) or die (mysql_error());
  while ($row=mysql_fetch_array($resultscore))
  {
    $score=$row[0];
  }

  $query="SELECT id_prono, points
          FROM phpl_clmnt_pronos, phpl_membres
          WHERE phpl_membres.id=phpl_clmnt_pronos.id_membre AND id_champ='$gr_champ' AND type='mixte' ORDER by points desc, participation asc, phpl_membres.pseudo";
  $result=mysql_query($query) or die (mysql_error());
  $i = "1";
  while ($row=mysql_fetch_array($result))
  { 
    if ($row[0]==$user_id){$class=$i;}
    //echo "<script>alert('".$score." - ".$row[1]."');</script>";
    if($score<>$row[1]){$i++;}
  }

  if (!isset($class)) {echo PRONO_CLASSEMENT_NON_CLASSE;}
  else
  {
  if ($class=="1"){print $class; echo PRONO_CLASSEMENT_PREMIER;}
  elseif ($class=="2"){print $class; echo PRONO_CLASSEMENT_SECOND;}
  elseif ($class=="3"){print $class; echo PRONO_CLASSEMENT_TROIS;}
  else {print $class; echo PRONO_CLASSEMENT_AUTRES;}
  }

}
*/

// fonction utilisée dans les stats
function affiche_clmnt_moyenne_en_cours ($user_id, $gr_champ)
{
  $queryscore="SELECT (points/participation) as moyenne_points FROM phpl_clmnt_pronos, phpl_membres WHERE phpl_membres.id=phpl_clmnt_pronos.id_membre AND id_champ='$gr_champ' AND type='general' AND id_prono='$user_id'";
  $resultscore=mysql_query($queryscore) or die (mysql_error());
  while ($row=mysql_fetch_array($resultscore))
  {
    $score=$row[0];
  }

  $query="SELECT id_prono, (points/participation) as moyenne_points
          FROM phpl_clmnt_pronos, phpl_membres
          WHERE phpl_membres.id=phpl_clmnt_pronos.id_membre AND id_champ='$gr_champ' AND type='general' ORDER by moyenne_points desc, participation asc, phpl_membres.pseudo";
  $result=mysql_query($query) or die (mysql_error());
  $i = "1";
  while ($row=mysql_fetch_array($result))
  { 
    if ($row[0]==$user_id){$class=$i;}
    //echo "<script>alert('".$score." - ".$row[1]."');</script>";
    if($score<>$row[1]){$i++;}
  }

  if (!isset($class)) {echo PRONO_CLASSEMENT_NON_CLASSE;}
  else
  {
  if ($class=="1"){print $class; echo PRONO_CLASSEMENT_PREMIER;}
  elseif ($class=="2"){print $class; echo PRONO_CLASSEMENT_SECOND;}
  elseif ($class=="3"){print $class; echo PRONO_CLASSEMENT_TROIS;}
  else {print $class; echo PRONO_CLASSEMENT_AUTRES;}
  }

}

// fonction utilisée dans les stats
function affiche_clmnt_mensuel_30_jours ($user_id, $gr_champ)
{
  $query="SELECT id_prono FROM phpl_clmnt_pronos, phpl_membres WHERE phpl_membres.id=phpl_clmnt_pronos.id_membre AND id_champ='$gr_champ' AND id_champ='$gr_champ' AND type='mensuel_30_jours' ORDER by points desc, participation asc, phpl_membres.pseudo";
  $result=mysql_query($query) or die (mysql_error());
  $i = "1";
  while ($row=mysql_fetch_array($result))
  { if ($row[0]==$user_id){$class=$i;}
    $i++;
  }
 if (!isset($class)) {$clmnt=PRONO_CLASSEMENT_NON_CLASSE;}
 else
  {
  if ($class=="1"){print $class; echo PRONO_CLASSEMENT_PREMIER;}
  elseif ($class=="2"){print $class; echo PRONO_CLASSEMENT_SECOND;}
  elseif ($class=="3"){print $class; echo PRONO_CLASSEMENT_TROIS;}
  else {print $class; echo PRONO_CLASSEMENT_AUTRES;}
  }

}

// fonction utilisée dans les stats
function affiche_clmnt_mensuel_hebdo ($user_id, $gr_champ)
{
  $query="SELECT id_prono FROM phpl_clmnt_pronos, phpl_membres WHERE phpl_membres.id=phpl_clmnt_pronos.id_membre AND id_champ='$gr_champ' AND type='hebdo' AND id_champ='$gr_champ' ORDER by points desc, participation asc, phpl_membres.pseudo";
  $result=mysql_query($query) or die (mysql_error());
  $i = "1";
  while ($row=mysql_fetch_array($result))
  { if ($row[0]==$user_id){$class=$i;}
    $i++;
  }
 if (!isset($class)) {$clmnt=PRONO_CLASSEMENT_NON_CLASSE;}
 else
  {
  if ($class=="1"){print $class; echo PRONO_CLASSEMENT_PREMIER;}
  elseif ($class=="2"){print $class; echo PRONO_CLASSEMENT_SECOND;}
  elseif ($class=="3"){print $class; echo PRONO_CLASSEMENT_TROIS;}
  else {print $class; echo PRONO_CLASSEMENT_AUTRES;}
  }
}

function login_form()
{
 echo"<form action=login.php method='post'>
   <table border='0' class='univert' cellspacing='0' align='center' width='80%'>
   <tr>
   <td class='univert' align='center'>Veuillez entrer votre nom d'utilisateur et votre mot de passe pour vous connecter<br /><br />
   </td>
   </tr>
   <tr>
  <td class='univert' align='center'>
   <input type='text' name='user'>
   </td>
   </tr>
   <tr>
  <td class='univert' align='center'>
   <input type='password' name='password'>
   </td>
   <tr><td align='center' class='univert'>Se connecter automatiquement à chaque visite: <input type='checkbox' class='checkbox' name='autoidentification' value='1'></td></tr>
   </tr>
   <tr><td colspan='2' class='univert' align='center'><input type='submit' name='submit' value='Connexion' >
   <br />
   <a href='perdu_mdp.php'>J'ai oublié mon mot de passe</a><br />
   <a href='inscription.php'>Inscription</a><br />
   </td>
   </tr></form></table>";  }

function perdu_mot_de_passe()
{
  echo"<table border='0' class='univert' cellspacing='0' cellpadding='10' align='center' class='textfield2' width='300'>
  <tr>
  <td colspan='2' class='univert' align='center'>
  <div class=\"blanc\"><strong>Mot de passe perdu</strong></div><br /><br />
<div class=\"blanc\">Entrez votre pseudo,
<br />un nouveau mot de passe vous sera alors envoyé par mail.
</div>
<form action='perdu_mdp.php' method='get'>
<input class=textfield type=text name=pseudo size='35'>
<br />
<input type='submit' class='textfield' name='submit' value='ok'>
</form>
</td></tr></table>";
  
}

function classement_general ($gr_champ, $user_pseudo)
{
	$query="SELECT pseudo, points, participation, champ_gen as champion FROM phpl_clmnt_pronos
				WHERE id_champ='$gr_champ' AND type='general'
				ORDER by points desc, participation asc, pseudo LIMIT 0, 10";
	$result=mysql_query($query) or die ("probleme " .mysql_error());
	$i=1;

       	while ($row=mysql_fetch_array($result))
       	{
       		echo "<div align=\"center\" class=\"blanc\">$i. ";
       
       		if ($user_pseudo==$row[0]) echo "<b>$row[0]</b>";
       		else  echo "$row[0]";

	       echo "</div>";
	       $i++;
       }
       
	echo "<a href=\"index.php?page=classement&amp;type=general&amp;complet=1&amp;gr_champ=$gr_champ\" class=\"blanc\"><b>".PRONO_CLASSEMENT_SUITE."</b></a>";
}



//mise au format d'une date
function format_date_fr_red($date){

  list($annee,$mois,$jour) = explode("-",substr($date,0,10));

  return $jour."/".$mois;
}

//date en timestamps
function format_date_timestamp($date){

  list($annee,$mois,$jour) = explode("-",substr($date,0,10));
  list($heure,$minute,$seconde) = explode(":",substr($date, 11,7));
  $timestamp=mktime ($heure,$minute,$seconde,$mois,$jour,$annee);
  return $timestamp;
}


function grille_admin ($gr_champ)
{
$query="SELECT phpl_clubs.nom, CLEXT.nom, phpl_matchs.buts_dom, phpl_matchs.buts_ext, phpl_matchs.id, phpl_matchs.date_reelle, phpl_journees.numero
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
LIMIT 0, 10";

$i=0;
$result=mysql_query($query) or die ("probleme " .mysql_error());

while ($row=mysql_fetch_array($result) and $i<10)
  {
$clubs_nom = stripslashes($row[0]);
$clubs_nom1 = stripslashes($row[1]);
$query2= "SELECT pronostic FROM phpl_pronostics, phpl_gr_championnats WHERE phpl_pronostics.id_match='$row[4]' AND phpl_gr_championnats.id='$gr_champ' AND id_membre=id_master";
$result2=mysql_query($query2) or die ("probleme " .mysql_error());
$nb_pronos= mysql_num_rows($result2);

if ($nb_pronos == "0") {$prono="0";}
{
  while ($row2=mysql_fetch_array($result2))
  {
    $prono=$row2["0"];
    if ($row2["0"] == ""){$prono="0";}
  }
}
  $date=format_date_fr_red($row[5]);



  echo "<tr><td><div class=\"blanc\">$row[6]</div></td>";
  echo "<td><div class=\"blanc\">$date</div></td>";
  echo "<td align=\"right\"><div class=\"blanc\">$clubs_nom</div></td>";

  echo "<td><table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" align=\"center\" >";
  echo "<tr>";
  echo "<td width=\"45\" height=\"10\" valign=\"middle\" align=\"center\">";
  echo "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" align=\"center\" width=\"50\"><tr><td>";

         if ($prono=="1")
           {
           ?>
             <img src="barre.gif" border="no" alt=""> <img src="N.gif" border="no" alt=""> <img src="2.gif"  border="no" alt="">
           <?php
           }

         if ($prono=="N")
           {
           ?>
             <img src="1.gif" border="no" alt=""> <img src="barre.gif" border="no" alt=""> <img src="2.gif"  border="no" alt="">
           <?php
           }

         if ($prono=="2")
         {
         ?>
             <img src="1.gif" border="no" alt=""> <img src="N.gif" border="no" alt=""> <img src="barre.gif"  border="no" alt="">
         <?php
         }
  
         if ($prono=="0")
         {
          ?> 
             <img src="1.gif" border="no" alt=""> <img src="N.gif" border="no" alt=""> <img src="2.gif"  border="no" alt="">
          <?php
         }
  echo "</td></tr></table>";

  echo "</td></tr></table></td>";

  echo "<td><div class=\"blanc\">$clubs_nom1</div></td>";

  $requete2="SELECT tps_avant_prono FROM phpl_gr_championnats WHERE id='$gr_champ'";
  $resultats2=mysql_query($requete2) or die ("probleme " .mysql_error());
   while ($row2=mysql_fetch_array($resultats2))
   {
    $temps_avantmatch=$row2[0];
   }

  $date_match_timestamp=format_date_timestamp($row[5]);
  $date_actuelle=time();
  $ecart_secondes=$date_match_timestamp-$date_actuelle;
  $ecart_heures = floor($ecart_secondes / (60*60))-$temps_avantmatch;
  $ecart_minutes = floor($ecart_secondes / 60)-$temps_avantmatch*60;
  $ecart_jours = floor($ecart_secondes / (60*60*24)-$temps_avantmatch/60);
  $date=format_date_fr_red($row[5]);

  echo "<td align=\"center\">";
  if ($ecart_heures>48) echo "<div class=\"blanc\">$ecart_jours jours</div>";
  elseif ($ecart_heures>0) echo "<div class=\"blanc\">$ecart_heures h</div>";
  elseif ($ecart_heures == 0) echo "<div class=\"blanc\">$ecart_minutes min</div>";
  else {echo"<div class=\"blanc\">expiré</div>";}
  echo "</td>";
  echo "</tr>";
  $i++;
}

}

/**
 *	Affiche le libellé du type du classement passé en paramètre
  */
function classement_type ($type)
{
	if ($type=="") {echo PRONO_CLASSEMENT_GENERAL;}
	else if ($type=="general") {echo PRONO_CLASSEMENT_GENERAL;}
	else if ($type=="mensuel_en_cours") {echo PRONO_CLASSEMENT_MOIS;}
	else if ($type=="mensuel_30_jours") {echo PRONO_CLASSEMENT_30;}
	else if ($type=="hebdo") {echo PRONO_CLASSEMENT_HEBDO;}
	else if ($type=="derniere_journee") {echo PRONO_CLASSEMENT_DERNIERE_JOURNEE;}
	else if ($type=="moyenne") {echo PRONO_CLASSEMENT_MOYENNE;}
	else if ($type=="hourra") {echo PRONO_CLASSEMENT_HOURRA;}
	else if ($type=="hourra_derniere_journee") {echo PRONO_CLASSEMENT_HOURRA_DERNIERE_JOURNEE;}
	else if ($type=="mixte") {echo PRONO_CLASSEMENT_MIXTE;}
}

/**
 *	Affichage des différents classements
 *	Tous les classements sont calculés à la volée sauf le classement général
 *	qui est généré par une action dans l'interface d'admin (pourquoi ??)
 */
function classement ($gr_champ, $type, $user_id, $user_pseudo, $filtre)
{
if (!($type=="general" or $type=="mensuel_en_cours" or $type=="mensuel_30_jours" 
		or $type=="hebdo" or $type=="derniere_journee" or $type=="moyenne" 
		or $type=="hourra" or $type=="hourra_derniere_journee" or $type=="mixte"))
	{
		$type="general";
	}

/*
if ($type=="mensuel_en_cours")
{
   mysql_query("DELETE FROM phpl_clmnt_pronos WHERE id_champ='$gr_champ' AND type='mensuel_en_cours'") or die (mysql_error());

   $query="SELECT id_membre, pseudo, sum(points) as total, sum(participation) as participations
   FROM phpl_membres, phpl_pronostics, phpl_matchs, phpl_gr_championnats
   WHERE phpl_pronostics.id_champ=phpl_gr_championnats.id
   AND phpl_gr_championnats.id='$gr_champ'
   AND id_membre=phpl_membres.id
   AND phpl_matchs.id=id_match
   AND MONTH (date_reelle) = MONTH (NOW())
   AND YEAR (date_reelle) = YEAR (NOW())
   GROUP by pseudo
   ORDER by total DESC, participations ASC";

   $result=mysql_query ($query) or die ("probleme " .mysql_error());
   while ($row=mysql_fetch_array($result))
         {
         mysql_query("INSERT INTO phpl_clmnt_pronos (id_champ, id_membre, pseudo, points, participation, type) values ('$gr_champ', '$row[0]', '$row[1]', '$row[2]', '$row[3]', 'mensuel_en_cours')") or die (mysql_error());
         }
}          
else if ($type=="mensuel_30_jours")
{
   mysql_query("DELETE FROM phpl_clmnt_pronos WHERE id_champ='$gr_champ' AND type='mensuel_30_jours'") or die (mysql_error());

   $query="SELECT id_membre, pseudo, sum(points) as total, sum(participation) as participations
   FROM phpl_membres, phpl_pronostics, phpl_matchs, phpl_gr_championnats
   WHERE phpl_pronostics.id_champ=phpl_gr_championnats.id
   AND phpl_gr_championnats.id='$gr_champ'
   AND id_membre=phpl_membres.id
   AND phpl_matchs.id=id_match
   AND DATE_ADD(date_reelle, INTERVAL 30 DAY) >= NOW()
   GROUP by pseudo
   ORDER by total DESC, participations ASC";

   $result=mysql_query ($query) or die ("probleme " .mysql_error());
       while ($row=mysql_fetch_array($result))
       {
       mysql_query("INSERT INTO phpl_clmnt_pronos (id_champ, id_membre, pseudo, points, participation, type) values ('$gr_champ', '$row[0]', '$row[1]', '$row[2]', '$row[3]', 'mensuel_30_jours')") or die (mysql_error());
       }
}
else if ($type=="hebdo")
{
   mysql_query("DELETE FROM phpl_clmnt_pronos WHERE id_champ='$gr_champ' AND type='hebdo'") or die (mysql_error());

   $query="SELECT id_membre, pseudo, sum(points) as total, sum(participation) as participations
   FROM phpl_membres, phpl_pronostics, phpl_matchs, phpl_gr_championnats
   WHERE phpl_pronostics.id_champ=phpl_gr_championnats.id
   AND phpl_gr_championnats.id='$gr_champ'
   AND id_membre=phpl_membres.id
   AND phpl_matchs.id=id_match
   AND DATE_ADD(date_reelle, INTERVAL 7 DAY) >= NOW()
   GROUP by pseudo
   ORDER by total DESC, participations ASC";

   $result=mysql_query ($query) or die ("probleme " .mysql_error());
       while ($row=mysql_fetch_array($result))
       {
       mysql_query("INSERT INTO phpl_clmnt_pronos (id_champ, id_membre, pseudo, points, participation, type) values ('$gr_champ', '$row[0]', '$row[1]', '$row[2]', '$row[3]', 'hebdo')") or die (mysql_error());
       }
}
else if ($type=="hourra")
{
    	mysql_query("DELETE FROM phpl_clmnt_pronos WHERE id_champ='$gr_champ' AND type='hourra'") or die (mysql_error());


//	$id_last_journee="";

//	$requete="SELECT phpl_matchs.id_journee  
//				FROM phpl_matchs, phpl_journees, phpl_gr_championnats
//				WHERE phpl_gr_championnats.id='$gr_champ'   
//				AND phpl_matchs.buts_dom is not null
//				AND phpl_matchs.buts_ext is not null	
//				ORDER by phpl_matchs.date_reelle DESC LIMIT 0, 1";

//	$resultat=mysql_query($requete) or die ("probleme " .mysql_error());
//	while ($row=mysql_fetch_array($resultat))
//  	{
//	    $id_last_journee=$row[0];
//	}
	
	
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
}
else if($type=="hourra_derniere_journee")
{
	mysql_query("DELETE FROM phpl_clmnt_pronos WHERE id_champ='$gr_champ' AND type='hourra_derniere_journee'") or die (mysql_error());
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
	$query="SELECT id_membre, pseudo, sum(points_hourra) as total, sum(participation) as participations
	FROM phpl_membres, phpl_pronostics, phpl_matchs
	WHERE id_champ='$gr_champ'
	AND id_membre=phpl_membres.id
	AND phpl_matchs.id=id_match
	AND phpl_matchs.id_journee=$id_last_journee
	GROUP by pseudo
	ORDER by total, participations";
	
	$result=mysql_query ($query) or die ("probleme " .mysql_error());
	while ($row=mysql_fetch_array($result))
	{
	mysql_query("INSERT INTO phpl_clmnt_pronos (id_champ, id_membre, pseudo, points, participation, type) values ('$gr_champ', '$row[0]', '$row[1]', '$row[2]', '$row[3]', 'hourra_derniere_journee')") or die (mysql_error());
	}
}
else if ($type=="derniere_journee")
{
    	mysql_query("DELETE FROM phpl_clmnt_pronos WHERE id_champ='$gr_champ' AND type='derniere_journee'") or die (mysql_error());
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
	$query="SELECT id_membre, pseudo, sum(points) as total, sum(participation) as participations
	FROM phpl_membres, phpl_pronostics, phpl_matchs
	WHERE id_champ='$gr_champ'
	AND id_membre=phpl_membres.id
	AND phpl_matchs.id=id_match
	AND phpl_matchs.id_journee=$id_last_journee
	GROUP by pseudo
	ORDER by total, participations";
	
	$result=mysql_query ($query) or die ("probleme " .mysql_error());
	while ($row=mysql_fetch_array($result))
	{
	mysql_query("INSERT INTO phpl_clmnt_pronos (id_champ, id_membre, pseudo, points, participation, type) values ('$gr_champ', '$row[0]', '$row[1]', '$row[2]', '$row[3]', 'derniere_journee')") or die (mysql_error());
	}		
}
else if ($type=="mixte")
{
    	mysql_query("DELETE FROM phpl_clmnt_pronos WHERE id_champ='$gr_champ' AND type='mixte'") or die (mysql_error());
	
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
}
else if ($type=="moyenne")
{
    mysql_query("DELETE FROM phpl_clmnt_pronos WHERE id_champ='$gr_champ' AND type='moyenne'") or die (mysql_error());   

	$query="SELECT id_membre, pseudo, sum(points) as total, sum(participation) as participations
	FROM phpl_membres, phpl_pronostics, phpl_matchs
	WHERE id_champ='$gr_champ'
	AND id_membre=phpl_membres.id
	AND phpl_matchs.id=id_match
	AND phpl_matchs.buts_dom is not null
	AND phpl_matchs.buts_ext is not null
	AND phpl_pronostics.pronostic is not null 
	GROUP by pseudo
	ORDER by total, participations";

	$result=mysql_query ($query) or die ("probleme " .mysql_error());
	while ($row=mysql_fetch_array($result))
	{
	mysql_query("INSERT INTO phpl_clmnt_pronos (id_champ, id_membre, pseudo, points, participation, type) values ('$gr_champ', '$row[0]', '$row[1]', '$row[2]', '$row[3]', 'moyenne')") or die (mysql_error());
	}
			
}
*/

if (isset($_REQUEST['complet'])) {$complet=$_REQUEST['complet'];} else {$complet='';}

$query="";

$pts_prono_exact="";
if($type=="moyenne")
{
	$query="SELECT pts_prono_exact, pts_prono_participation FROM phpl_gr_championnats";
	$result=mysql_query($query) or die ("probleme " .mysql_error());
	while ($row=mysql_fetch_array($result))
	{
		$pts_prono_exact = $row[0];
		$pts_prono_participation = $row[1];
	}
}

if($type=="moyenne")
{
$query="SELECT clmnt.id_membre, membre.pseudo, clmnt.place, clmnt.points, clmnt.participation,
		membre.nom,  membre.prenom,  membre.ville, membre.departement, membre.id_club_favori, 
		club.nom, club.url_logo, 
		(clmnt.points/clmnt.participation) as moyenne_points, membre.avatar
		FROM phpl_clmnt_pronos as clmnt, phpl_membres as membre, phpl_clubs as club
		WHERE clmnt.id_champ='$gr_champ' AND clmnt.type='$type'
		AND membre.id=clmnt.id_membre
		AND membre.actif='1'
		AND (membre.id_club_favori IS NULL OR club.id = membre.id_club_favori)
		GROUP by membre.pseudo
		ORDER by  moyenne_points desc, clmnt.points desc, clmnt.participation asc, membre.pseudo";				
}
else
{		
$query="SELECT clmnt.id_membre, membre.pseudo, clmnt.place, clmnt.points, clmnt.participation,
		membre.nom,  membre.prenom,  membre.ville, membre.departement, membre.id_club_favori, 
		club.nom, club.url_logo, membre.avatar, membre.champ_gen, membre.champ_hourra, 
		membre.nb_champ_gen, membre.nb_champ_hourra 
		FROM phpl_clmnt_pronos as clmnt, phpl_membres as membre, phpl_clubs as club
		WHERE clmnt.id_champ='$gr_champ' AND clmnt.type='$type'
		AND membre.id=clmnt.id_membre
		AND membre.actif='1'
		AND (membre.id_club_favori IS NULL OR club.id = membre.id_club_favori)
		GROUP by clmnt.pseudo
		ORDER by  clmnt.points desc, clmnt.participation asc, membre.pseudo";
}
// A decommenter si on veut afficher seulement les 10 premiers au classement
//if (!($complet== '1')){$query = $query. " LIMIT 0, 10";}
$result=mysql_query($query) or die ("probleme " .mysql_error());
$class="noir";

// calcul du numero de la derniere journee
$journeemax = "";
$queryMaxJournee="SELECT max(fin) FROM phpl_pronos_graph
			WHERE type='$type' AND id_gr_champ='$gr_champ'";
$resultMaxJournee=mysql_query($queryMaxJournee) or die ("probleme " .mysql_error());
while ($rowMaxJournee=mysql_fetch_array($resultMaxJournee)) 
{
	$journeemax = $rowMaxJournee[0];
}

// calcul des évolutions des utilisateurs dans le classement		
if($journeemax > 1) 
{
	$queryVariation="SELECT P1.id_membre, (P1.classement - P2.classement) as evolution
						FROM phpl_pronos_graph P1
						JOIN phpl_pronos_graph P2 ON P1.id_membre = P2.id_membre
						WHERE P1.id_gr_champ='$gr_champ'
						AND P2 .id_gr_champ='$gr_champ'
						AND P1.fin = '".($journeemax-1)."'
						AND P2.fin = '$journeemax'
						AND P1.type = '$type'
						AND P2.type = '$type'
						ORDER BY evolution";					
	$resultVariation=mysql_query($queryVariation) or die ("probleme " .mysql_error());
	$usersEvolution = "";
		
	while ($rowVariation=mysql_fetch_array($resultVariation))
	{
		//echo $rowVariation[0]."-".$rowVariation[1]."<br />";
		$usersEvolution[$rowVariation[0]] = $rowVariation[1];
	}				
}

// Récupération des pronostiqueurs mis dans le filtre
$usersFiltre = "";
$nbUsersFiltre=0;
if($filtre == 1)
{
	$queryFiltre="SELECT filtre.idMembre
					 FROM phpl_clmnt_filtre filtre
					 WHERE filtre.id = '$user_id'
					 ORDER BY filtre.idMembre";					
	$resultFiltre=mysql_query($queryFiltre) or die ("probleme " .mysql_error());	
	while ($rowFiltre=mysql_fetch_array($resultFiltre))
	{
		$usersFiltre[$rowFiltre[0]] = true;
		$nbUsersFiltre=$nbUsersFiltre+1;
	}				
}

if($nbUsersFiltre==0 && $filtre==1)
{
	echo"<tr align='center' bgcolor='white'><td colspan='7'><font color='red'>Vous n'avez sélectionné aucun pronostiqueur dans votre filtre. Vous pouvez le modifier dans Options > Paramétrer le filtre.</font></td></tr>";
}
else
{

// récupération des palmarès des pronostiqueurs
$resultPalmares = null;
$queryPalmares = '';
if($type=='general' || $type=='hourra')
{
	$queryPalmares="SELECT membres.id, membres.pseudo, clmnt.place, clmnt.type, clmnt.id_champ, groupes.nom
						FROM phpl_membres membres, phpl_clmnt_pronos clmnt, phpl_gr_championnats groupes
						WHERE membres.id = clmnt.id_membre
						AND clmnt.id_champ = groupes.id
						AND groupes.activ_prono = '0'
						AND clmnt.place <=3
						AND clmnt.type = '$type'
						ORDER BY membres.pseudo, clmnt.id_champ";						
	$resultPalmares=mysql_query($queryPalmares) or die ("probleme " .mysql_error());	
}

$iColor=1;
$placePrecedente=0;
$placeIndex=1;
while ($row=mysql_fetch_array($result))
{
	// si le filtre est activé et que le pronostiqueurs est dans le filtre
	// OU
	// si le filtre n'est pas activé
	if(($filtre==1 && $usersFiltre[$row["id_membre"]] == true) || $filtre==0)
	{
		$userPalmares = array();
		if($type=='general' || $type=='hourra')
		{
			// on remet le curseur du resultset au début
			
			mysql_data_seek($resultPalmares, 0);
			
			while ($rowPalmares=mysql_fetch_array($resultPalmares))
			{
				if($row["pseudo"]==$rowPalmares["pseudo"])
				{
					$palmaresTemp = $userPalmares[$rowPalmares["place"]];
					if($palmaresTemp == null)
					{
						$palmaresTemp = array();
					}
					array_push($palmaresTemp, $rowPalmares["nom"]);
					$userPalmares[$rowPalmares["place"]] = $palmaresTemp;
				}
			}
		
		}		

		if($placeIndex==1) 
		{
			$class = "vert1";
		}	
		else if($placeIndex == 2) 
		{
			$class = "vert2";
		}
		else if($placeIndex== 3) 
		{
			$class = "vert3";
		}
		else if($placeIndex == mysql_numrows($result) or $placeIndex == (mysql_numrows($result)-1) or $placeIndex == (mysql_numrows($result)-2)) 
		{
			$class = "rouge";
		}
		else if(1 == $iColor%2) 
		{
			$class = "ligne_impaire";
		}
		else	
		{
			$class = "ligne_paire";
		}

		if ($user_pseudo==$row["pseudo"])
		{
			$class .= " utilisateur_connecte";
		}

		echo "<tr class=\"$class\" onclick=\"showProfil(".$row["id_membre"].");\">";
	
		// Affichage de l'évolution au classement pour les classements autres que "derniere journée"
		if(!($type=="derniere_journee" || $type=="hourra_derniere_journee"))
		{
			if($journeemax != "1" && $usersEvolution[$row["id_membre"]] > 0) 
			{
				echo "<td align=\"left\" style=\"font-size:7pt;padding-left:2px;padding-right:2px;\"><img src=\"./images/icon-top.png\" border=\"0\" alt=\"\">&nbsp;(+".$usersEvolution[$row["id_membre"]].")</td>";
			} 
			else if($journeemax != "1" && $usersEvolution[$row["id_membre"]] < 0)
			{
				echo "<td align=\"left\" style=\"font-size:7pt;padding-left:2px;padding-right:2px;\"><img src=\"./images/icon-flop.png\" border=\"0\" alt=\"\">&nbsp;(".$usersEvolution[$row["id_membre"]].")</td>";
			}
			else
			{
				echo "<td align=\"left\" style=\"padding-left:2px;padding-right:2px;\"><img src=\"./images/cla_non.gif\" border=\"0\" alt=\"\"></td>";	
			}
		}

		// Affichage du classement
		if($row["place"] == $placePrecedente)
		{
			echo "<td><div align=\"center\" style=\"padding-left:2px;padding-right:2px\">-</div></td>";
		}
		else
		{
			echo "<td><div align=\"center\" style=\"padding-left:2px;padding-right:2px\">".$row["place"]."</div></td>";
		}
		
		// Affichage des étoiles
		if($type=="general" or $type=="hourra")
		{
			echo "<td align=\"center\" style=\"padding-left:2px;padding-right:2px\">";
			if(count($userPalmares) > 0)
			{				
				echo "<div style=\"white-space:nowrap\">";
				if($userPalmares["1"] != null)
				{
					echo "<img src=\"./images/etoile-or-petit.png\" border=\"0\" />";
				}
				if($userPalmares["2"] != null)
				{
					echo "<img src=\"./images/etoile-argent-petit.png\" border=\"0\" />";				
				}
				if($userPalmares["3"] != null)
				{
					echo "<img src=\"./images/etoile-bronze-petit.png\" border=\"0\" />";
				}
				echo "</div>";
			}
			else
			{
				echo "<div align=center></div>";
			}
			echo "</td>";
		}

		// Affichage du pseudo
		echo "<td style=\"text-align:left;\">".$row["pseudo"]."</td>";



/*
			echo "<td onmouseover=\"javascript:showDiv('divInfosUser$row[0]');\" onmouseout=\"javascript:hideDiv('divInfosUser$row[0]');\">";
			if(($row["champ_gen"]==1 && $type=="general") || ($row["champ_hourra"]==1 && $type=="hourra"))
			{
				echo "<div class=$class>&nbsp;".$row["pseudo"]." <img src=\"./images/etoile.gif\" border=\"0\" alt=\"Champion de la saison passée\" title=\"Champion de la saison passée\"></div>";
			}
			else	
			{
				echo "<div class=$class>&nbsp;$row[1]</div>";
			}
			echo "<div id=\"divInfosUser$row[0]\" class=\"infosUser\">";
			echo "<table><tr>";
			if($row[9] != null) {
				echo "<td><img src=\"./images/clubs/".$row["url_logo"]."\" /></td>";
			}
			if($row[8] != '') {
				echo "<td><b>$row[6] $row[5]</b><br />$row[7] ($row[8])</td>";
			} else {
				echo "<td><b>$row[6] $row[5]</b><br />$row[7]</td>";
			}
			if($row["avatar"]==1)
			{
				echo "<td><img src=\"./images/avatars/$row[0].gif\" /></td>";
			}
			echo "</tr></table>";
			echo "</div>";
			echo "</td>";	}
*/
		
		if($type=="moyenne")
		{		
			// on récupère le nombre de points total, on soustrait les points 
			// de participations (=nb de matchs paronostiqués - nb de points
			// pour la participation à un prono), et on divise par le nombre de matchs pronostiqués
			$reussite = round(((($row[3]-($pts_prono_participation*$row[4]))/$pts_prono_exact)/$row[4])*100, 2);
			$fraction = (($row[3]-($pts_prono_participation*$row[4]))/$pts_prono_exact);
			echo "<td><div align=\"center\">$reussite%</div></td>";
	 		echo "<td><div align=\"center\">".$fraction."/".$row[4]."</div></td>";		
		}
		else
		{
			if($type=="mixte")
			{
				echo "<td><div align=\"center\">";
				printf("%01.2f",$row[3]);
				echo "</div></td>";
			}
			else
			{
				echo "<td><div align=\"center\">$row[3]</div></td>";
			}
	 		echo "<td><div align=\"center\">$row[4]</div></td>";
		}
		
		// colonne des graphes
		if($type=="general" || $type=="derniere_journee" || $type=="moyenne" || $type=="hourra" || $type=="hourra_derniere_journee" || $type=="mixte")
		{
			//if($type=="derniere_journee") $graph_type="journee";
			//else $graph_type=$type;
			echo "<td><div align=\"center\"><img src=\"images/graph.png\" border=\"0\" alt=\"Evolution du classement\" onclick=\"showGraph('".$type."', ".$row["id_membre"].");\"></div></td>";
		}	
	

		echo "</tr>";
		
		/* Fenêtre des infos du pronostiqueur */
		/*
			if(count($userPalmares) > 0)
			{				
				echo "<div style=\"white-space:nowrap\">";
				$contenuPalmaresDiv = "<table>";
				if($userPalmares["1"] != null)
				{
					echo "<img src=\"./images/etoile-or-petit.png\" border=\"0\" />";
					
					$contenuPalmaresDiv .= "<tr>";
					$contenuPalmaresDiv .= "<td valign=\"top\"><img src=\"./images/etoile-or.png\" border=\"0\" /> : </td>";
					$contenuPalmaresDiv .= "<td>";
					foreach($userPalmares["1"] as $nomChamp)
					{
						//foreach($champs as $nomChamp)
						//{					
							$contenuPalmaresDiv .= $nomChamp."<br />";
						//}
					}
					$contenuPalmaresDiv .= "</td></tr>";
				}
				if($userPalmares["2"] != null)
				{
					echo "<img src=\"./images/etoile-argent-petit.png\" border=\"0\" />";
					
					$contenuPalmaresDiv .= "<tr>";
					$contenuPalmaresDiv .= "<td valign=\"top\"><img src=\"./images/etoile-argent.png\" border=\"0\" /> : </td>";
					$contenuPalmaresDiv .= "<td>";
					foreach($userPalmares["2"] as $nomChamp)
					{
						//foreach($champs as $nomChamp)
						//{					
							$contenuPalmaresDiv .= $nomChamp."<br />";
						//}
					}
					$contenuPalmaresDiv .= "</td></tr>";					
				}
				if($userPalmares["3"] != null)
				{
					echo "<img src=\"./images/etoile-bronze-petit.png\" border=\"0\" />";
					
					$contenuPalmaresDiv .= "<tr>";
					$contenuPalmaresDiv .= "<td valign=\"top\"><img src=\"./images/etoile-bronze.png\" border=\"0\" /> : </td>";
					$contenuPalmaresDiv .= "<td>";
					foreach($userPalmares["3"] as $nomChamp)
					{
						//foreach($champs as $nomChamp)
						//{
							$contenuPalmaresDiv .= $nomChamp."<br />";
						//}
					}
					$contenuPalmaresDiv .= "</td></tr>";					
				}
				$contenuPalmaresDiv .= "</table>";
				echo "</div>";
				//layer du palmarès
				echo "<div id=\"divPalmaresUser".$row["id_membre"]."\" class=\"palmaresUser\"><b>Palmarès sur le classement ".$type."</b><br />";
				echo $contenuPalmaresDiv;	
				echo "</div>";		



		echo "<div>&nbsp;".$pseudo."</div>";
		echo "<div id=\"divInfosUser".$row["id_membre"]."\" class=\"infosUser\">";
		echo "<table><tr>";
		if($row[9] != null) {
			echo "<td><img src=\"./images/clubs/".$row["url_logo"]."\" /></td>";
		}
		if($row[8] != '') {
			echo "<td><b>$row[6] $row[5]</b><br />$row[7] ($row[8])</td>";
		} else {
			echo "<td><b>$row[6] $row[5]</b><br />$row[7]</td>";
		}
		if($row["avatar"]==1)
		{
			echo "<td><img src=\"./images/avatars/".$row["id_membre"].".gif\" /></td>";
		}		
		echo "</tr></table>";
		echo "</div>";
		*/
		
		$placePrecedente = $row["place"];

		$iColor++;	

	}
	
	$placeIndex++;

 
}
}
// A decommenter si on veut afficher seulement les 10 premiers au classement
//if (!($complet=='1')) echo "<tr><td colspan=\"4\" align = \"right\"><a href=\"index.php?page=classement&amp;type=$type&amp;complet=1&amp;gr_champ=$gr_champ\" class=\"blanc\"><b>".PRONO_CLASSEMENT_COMPLET."</b></a></td></tr>";
}

function date_form_inscription ()

{
  for($i=1;$i<=31;$i++){echo "<option value=\"$i\">$i</option>";}
echo "</select> ";

echo "<select size=\"1\" name=\"mois\"><option value=\"\"></option>";
for($i=1;$i<=12;$i++){echo "<option value=\"$i\">$i</option>";}
echo "</select> ";

echo "<select size=\"1\" name=\"annee\"><option value=\"\"></option>";
for($i=2000;$i>1923;$i--){echo "<option value=\"$i\">$i</option>";}
echo "</select>";
}

function pseudo_admin ($gr_champ)
{
  $requete="SELECT pseudo FROM phpl_membres, phpl_gr_championnats WHERE phpl_gr_championnats.id_master=phpl_membres.id AND phpl_gr_championnats.id='$gr_champ'";
  $resultat=mysql_query ($requete) or die ("probleme " .mysql_error());;

  while ($row= mysql_fetch_array($resultat))
  {  
    $pseudo = $row[0];
  }
print $pseudo;
}

function champ_prono ($gr_champ)
{
  $requete="SELECT DISTINCT id, nom FROM phpl_gr_championnats WHERE  phpl_gr_championnats.activ_prono='1' ORDER by id";
  $resultat=mysql_query ($requete) or die ("probleme " .mysql_error());;

  while ($row= mysql_fetch_array($resultat))
  {
    echo "<a href=\"index.php?gr_champ=$row[0]\">";
    if ($gr_champ==$row[0]){echo "<b>";}
    echo "$row[1]";
    if ($gr_champ==$row[0]){echo "</b>";}
    echo "</a><br />";
  }

}

// Nombres d equipes dans un championnat
/*function nb_equipes($id_champ)
         {
         $query="SELECT id FROM phpl_equipes WHERE id_champ='$id_champ'";
         $result=mysql_query($query);
         $nb_equipes=mysql_num_rows( $result );
         return("$nb_equipes");
         }
*/
function VerifSession ($user_pseudo,$user_mdp)
{
	if ($user_pseudo and $user_mdp)
	{
        $requete= "SELECT mot_de_passe, id_prono FROM phpl_membres WHERE pseudo='$user_pseudo'";
        $result = mysql_query($requete);
        $row = mysql_fetch_array($result);
        
        if ($row["mot_de_passe"] == $user_mdp){;$a=1;}
        else {$a=0;}

	//session_start();
	}
	else 
	{
		$a=0;
	}

	return ("$a");
}

// permet de tester l'existence de l'image de l'avatar ou non !
function remote_file_exists ($url)
{
	ini_set('allow_url_fopen', '1');
	if (@fclose(@fopen($url, 'r')))
	{
		return true;
	}
	else
	{
		return false;
	}
}
?>
