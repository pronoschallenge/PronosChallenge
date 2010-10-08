<?
require_once("fonctions.php");
require_once("../config.php");

ouverture ();

if (isset($_REQUEST['gr_champ'])) 
{
	$gr_champ=$_REQUEST['gr_champ'];
} 
else 
{
    $requete="SELECT phpl_gr_championnats.id FROM phpl_gr_championnats WHERE phpl_gr_championnats.activ_prono='1' ORDER by id desc";
    $resultat=mysql_query ($requete) or die ("probleme " .mysql_error());
    $row= mysql_fetch_array($resultat);
     
    $gr_champ=$row[0];
}

if (isset($_REQUEST['champ'])) 
{
	$champ=$_REQUEST['champ'];
} 
else 
{
    $requete="SELECT phpl_gr_championnats.id_champ FROM phpl_gr_championnats WHERE phpl_gr_championnats.activ_prono='1' ORDER by id desc";
    $resultat=mysql_query ($requete) or die ("probleme " .mysql_error());
    $row= mysql_fetch_array($resultat);
     
    $champ=$row[0];   
}

$connecte = false;

if (isset($_COOKIE['user'])) {$user_pseudo=$_COOKIE['user'];} else {$user_pseudo='';}
if (isset($_COOKIE['mot_de_passe'])) {$user_mdp=$_COOKIE['mot_de_passe'];} else {$user_mdp='';}

if (VerifSession ($user_pseudo,$user_mdp)=="1")
{
	$requete= "SELECT pseudo, id_prono FROM phpl_membres WHERE pseudo='$user_pseudo'";
	$result = mysql_query($requete);
	$row = mysql_fetch_array($result);
	$user_pseudo=$row[0];
	$user_id=$row[1];
	$connecte = true;
}
?>

