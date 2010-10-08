<?php include ("avant.php"); ?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
	   "http://www.w3.org/TR/html4/loose.dtd">
	   
<html>
	<head>
		<?php include("entete.php");?>
	</head>
	<body class="phpl">

  <!--[if lt IE 7]>
  <div style='border: 1px solid #F7941D; background: #FEEFDA; text-align: center; clear: both; height: 75px; position: relative;padding-bottom:5px;margin-bottom:5px;'>
    <div style='position: absolute; right: 3px; top: 3px; font-family: courier new; font-weight: bold;'><a href='#' onclick='javascript:this.parentNode.parentNode.style.display="none"; return false;'><img src='http://www.ie6nomore.com/files/theme/ie6nomore-cornerx.jpg' style='border: none;' alt='Close this notice'/></a></div>
    <div style='width: 640px; margin: 0 auto; text-align: left; padding: 0; overflow: hidden; color: black;'>
      <div style='width: 75px; float: left;'><img src='http://www.ie6nomore.com/files/theme/ie6nomore-warning.jpg' alt='Warning!'/></div>
      <div style='width: 275px; float: left; font-family: Arial, sans-serif;'>
        <div style='font-size: 14px; font-weight: bold; margin-top: 12px;'>Vous utilisez un navigateur d&eacute;pass&eacute; depuis pr&egrave;s de 8 ans!</div>
        <div style='font-size: 12px; margin-top: 6px; line-height: 12px;'>Pour une meilleure exp&eacute;rience web, prenez le temps de mettre votre navigateur &agrave; jour.</div>
      </div>
      <div style='width: 75px; float: left;'><a href='http://fr.www.mozilla.com/fr/' target='_blank'><img src='http://www.ie6nomore.com/files/theme/ie6nomore-firefox.jpg' style='border: none;' alt='Get Firefox 3.5'/></a></div>
      <div style='width: 75px; float: left;'><a href='http://www.microsoft.com/downloads/details.aspx?FamilyID=341c2ad5-8c3d-4347-8c03-08cdecd8852b&DisplayLang=fr' target='_blank'><img src='http://www.ie6nomore.com/files/theme/ie6nomore-ie8.jpg' style='border: none;' alt='Get Internet Explorer 8'/></a></div>
      <div style='width: 73px; float: left;'><a href='http://www.apple.com/fr/safari/download/' target='_blank'><img src='http://www.ie6nomore.com/files/theme/ie6nomore-safari.jpg' style='border: none;' alt='Get Safari 4'/></a></div>
      <div style='float: left;'><a href='http://www.google.com/chrome?hl=fr' target='_blank'><img src='http://www.ie6nomore.com/files/theme/ie6nomore-chrome.jpg' style='border: none;' alt='Get Google Chrome'/></a></div>
    </div>
  </div>
  <![endif]-->


<div id="page">

	<div id="entete">
		<?php include("haut.inc.php");?>
	</div>
	<div id="navigation">
		<?php include("menu.inc.php");?>
	</div>

	<div id="contenu" align="center">

<?php
if (isset($_REQUEST['action'])) {$action=$_REQUEST['action'];} else {$action='';}

if (isset($_POST['message'])) {$message=$_POST['message'];} else {$message='';}
if (isset($_POST['ancien_mdp'])) {$ancien_mdp=$_POST['ancien_mdp'];} else {$ancien_mdp='';}
if (isset($_POST['nouveau_mdp'])) {$nouveau_mdp=$_POST['nouveau_mdp'];} else {$nouveau_mdp='';}
if (isset($_POST['nouveau_mdp2'])) {$nouveau_mdp2=$_POST['nouveau_mdp2'];} else {$nouveau_mdp2='';}
if (isset($_POST['annee'])) {$annee=$_POST['annee'];} else {$annee='';}
if (isset($_POST['mois'])) {$mois=$_POST['mois'];} else {$mois='';}
if (isset($_POST['jour'])) {$jour=$_POST['jour'];} else {$jour='';}
if (isset($_POST['site'])) {$site=$_POST['site'];} else {$site='';}
if (isset($_POST['mail'])) {$mail=$_POST['mail'];} else {$mail='';}
if (isset($_POST['nom'])) {$nom=$_POST['nom'];} else {$nom='';}
if (isset($_POST['prenom'])) {$prenom=$_POST['prenom'];} else {$prenom='';}
if (isset($_POST['adresse'])) {$adresse=$_POST['adresse'];} else {$adresse='';}
if (isset($_POST['code_postal'])) {$code_postal=$_POST['code_postal'];} else {$code_postal='';}
if (isset($_POST['ville'])) {$ville=$_POST['ville'];} else {$ville='';}
if (isset($_POST['pays'])) {$pays=$_POST['pays'];} else {$pays='';}
if (isset($_POST['profession'])) {$profession=$_POST['profession'];} else {$profession='';}
if (isset($_POST['mobile'])) {$mobile=$_POST['mobile'];} else {$mobile='';}
if (isset($_REQUEST['confirm'])) {$confirm=$_REQUEST['confirm'];} else {$confirm='';}

// inscription
if (isset($_POST['go'])) {$go=$_POST['go'];} else {$go='';}
if (isset($_POST['mdp_verif'])) {$mdp_verif=$_POST['mdp_verif'];} else {$mdp_verif='';}
if (isset($_POST['pseudo'])) {$pseudo=$_POST['pseudo'];} else {$pseudo='';}
if (isset($_POST['email_verif'])) {$email_verif=$_POST['email_verif'];} else {$email_verif='';}
if (isset($_POST['pseudo_verif'])) {$pseudo_verif=$_POST['pseudo_verif'];} else {$pseudo_verif='';}
if (isset($_POST['mail_verif'])) {$mail_verif=$_POST['mail_verif'];} else {$mail_verif='';}
if (isset($_POST['mdp'])) {$mdp=$_POST['mdp'];} else {$mdp='';}
if (isset($_POST['mdp2'])) {$mdp2=$_POST['mdp2'];} else {$mdp2='';}
if (isset($_POST['id_prono'])) {$id_prono=$_POST['id_prono'];} else {$id_prono='';}
if (isset($_POST['adresse1'])) {$adresse1=$_POST['adresse1'];} else {$adresse1='';}
if (isset($_POST['adresse2'])) {$adresse2=$_POST['adresse2'];} else {$adresse2='';}

//Pronos
if (isset($_POST['f_prono_0'])) {$f_prono_0=$_POST['f_prono_0'];} else {$f_prono_0='';}
if (isset($_POST['f_prono_1'])) {$f_prono_1=$_POST['f_prono_1'];} else {$f_prono_1='';}
if (isset($_POST['f_prono_2'])) {$f_prono_2=$_POST['f_prono_2'];} else {$f_prono_2='';}
if (isset($_POST['f_prono_3'])) {$f_prono_3=$_POST['f_prono_3'];} else {$f_prono_3='';}
if (isset($_POST['f_prono_4'])) {$f_prono_4=$_POST['f_prono_4'];} else {$f_prono_4='';}
if (isset($_POST['f_prono_5'])) {$f_prono_5=$_POST['f_prono_5'];} else {$f_prono_5='';}
if (isset($_POST['f_prono_6'])) {$f_prono_6=$_POST['f_prono_6'];} else {$f_prono_6='';}
if (isset($_POST['f_prono_7'])) {$f_prono_7=$_POST['f_prono_7'];} else {$f_prono_7='';}
if (isset($_POST['f_prono_8'])) {$f_prono_8=$_POST['f_prono_8'];} else {$f_prono_8='';}
if (isset($_POST['f_prono_9'])) {$f_prono_9=$_POST['f_prono_9'];} else {$f_prono_9='';}
if (isset($_POST['id_match_0'])) {$id_match_0=$_POST['id_match_0'];} else {$id_match_0='';}
if (isset($_POST['id_match_1'])) {$id_match_1=$_POST['id_match_1'];} else {$id_match_1='';}
if (isset($_POST['id_match_2'])) {$id_match_2=$_POST['id_match_2'];} else {$id_match_2='';}
if (isset($_POST['id_match_3'])) {$id_match_3=$_POST['id_match_3'];} else {$id_match_3='';}
if (isset($_POST['id_match_4'])) {$id_match_4=$_POST['id_match_4'];} else {$id_match_4='';}
if (isset($_POST['id_match_5'])) {$id_match_5=$_POST['id_match_5'];} else {$id_match_5='';}
if (isset($_POST['id_match_6'])) {$id_match_6=$_POST['id_match_6'];} else {$id_match_6='';}
if (isset($_POST['id_match_7'])) {$id_match_7=$_POST['id_match_7'];} else {$id_match_7='';}
if (isset($_POST['id_match_8'])) {$id_match_8=$_POST['id_match_8'];} else {$id_match_8='';}
if (isset($_POST['id_match_9'])) {$id_match_9=$_POST['id_match_9'];} else {$id_match_9='';}

// Classements
if (isset($_GET['type'])) {$type=$_GET['type'];} else {$type='';}

//Perdu mdp
if (isset($_POST['new_mot_de_passe'])) {$new_mot_de_passe=$_POST['new_mot_de_passe'];} else {$new_mot_de_passe='';}

//if (!isset($_GET['page'])) {include ("pronos1.php");}
//else{$page= $_GET['page'];
if (isset($_GET['page'])) {
	$page= $_GET['page'];

	if ($page=="pronos" and $connecte=="oui") {include ("pronos.php");}
	elseif ($page=="pronospalm" and $connecte=="oui") {include ("pronospalm1.php");}
	elseif ($page=="derniers_pronos" and $connecte=="oui") {include ("derniers_pronos.php");}
	elseif ($page=="profil" and $connecte=="oui") {include ("profil.php");}
	elseif ($page=="mes_resultats" and $connecte=="oui") {include ("mes_resultats.php");}
	elseif ($page=="baremes" and $connecte=="oui") {include ("baremes.php");}
	elseif ($page=="classement") {include ("classement.htm");}
	elseif ($page=="classement_pc") {include ("../consult/classement_pc.php");}
	elseif ($page=="detaileq_pc") {include ("../consult/detaileq_pc.php");}
	elseif ($page=="inscription") {include ("inscription.php");}
	elseif ($page=="erreur_login") {include ("erreur_login.php");}
	elseif ($page=="perdu_mdp") {include ("perdu_mdp.php");}
	elseif ($page=="cotes") {include ("cotes.php");}
	elseif ($page=="statistiques") {include ("tendances.php");}
	elseif ($page=="compte" and $connecte=="oui") {include ("compte.php");}
	elseif ($page=="reglement") {include ("reglement.htm");}
	elseif ($page=="lots") {include ("lots.htm");}
	elseif( $page=="adminpronos") {include("adminpronos.php");} 
	elseif( $page=="stats") {include("stats.php");}
	elseif( $page=="gazouillis") {include("gazouillis.php");} 
	//elseif ($connecte=="oui") {include ("pronos1.php");}	
	elseif ($connecte=="oui") {include ("home.php");}	
	else {include ("authentification.php");}
} else {
	//include ("pronos1.php");
	include ("home.php");
}
?>
	</div>
	<div id="pied">
       	<?php include("pied.php");?>
	</div>
</div>

<script src="../util.js" type="text/javascript"></script>

</BODY>
</HTML>
