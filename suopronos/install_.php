<link rel="stylesheet" type="text/css" href="league.css">
<body class=phpl>
 <table class=tablephpl2 border="0" cellspacing="0" width="100%">
    <tr class=trphpl3>
      <td width="100%">
        <p align="center">Installation</td>
    </tr>
    <tr>
      <td width="100%" align="center">
<?php

function send_sql($sql,$message)
     //envoi la requete $sql a la base et affiche un message si il y a eu un probleme sinon on rend le resultat
{
  $i=0;
  while ($requete = explode(";", $sql) and $i<(count($requete)))
  {
	if(!$res = @mysql_query($requete[$i]))
	{       
                $mysql_errno=mysql_errno();
                $mysql_error=mysql_error();

		$message_erreur = "<font color=\"#ff0000\">\n";
		$message_erreur .= SETUP_ERREUR." $message\n";
		$message_erreur .= "<br /><br />\n";
		$message_erreur .= SETUP_ERREUR_2." :\n<br />\n";
		die ("$message_erreur".$mysql_errno." : ".$mysql_error."</font>\n");
      	}
      	
	$i++;
  }
return $res;
}

if (isset($_POST["buteurs"])) {$buteurs=$_POST["buteurs"];} else {$buteurs='';}
if (isset($_POST["nb_joueur"])) {$nb_joueur=$_POST["nb_joueur"];} else {$nb_joueur='';}


if ($buteurs=="1")
{
include ("data_connect.php") ;
$idconnect=mysql_connect($hostname,$login,$password);
mysql_select_db($database,$idconnect);


  $i=0;
 while ($i<$nb_joueur)
 {
if (isset($_POST["id_joueur-$i"])) {$id_joueur=$_POST["id_joueur-$i"];} else {$id_joueur='';}
if (isset($_POST["id_equipes-$i"])) {$id_equipes=$_POST["id_equipes-$i"];} else {$id_equipes='';}
if (isset($_POST["id_match-$i"])) {$id_match=$_POST["id_match-$i"];} else {$id_match='';}

// pour éviter les doublons dans la table effectif
$requete="SELECT id FROM phpl_effectif WHERE id_joueur='$id_joueur' AND id_equipe='$id_equipes'";
$resultats=mysql_query($requete);
//if (!$result) die mysql_error();
$nb=mysql_num_rows( $resultats );

     if ($nb=="0") { mysql_query ("INSERT INTO phpl_effectif (id_joueur, id_equipe) VALUES ('$id_joueur', '$id_equipes')");}

          $requete="SELECT DISTINCT phpl_effectif.id
FROM phpl_joueurs, phpl_buteurs, phpl_matchs, phpl_equipes, phpl_effectif
WHERE phpl_matchs.id = phpl_buteurs.id_match
AND (
phpl_matchs.id_equipe_dom = phpl_equipes.id
OR phpl_matchs.id_equipe_ext = phpl_equipes.id
)
AND phpl_matchs.id = '$id_match'
AND phpl_equipes.id ='$id_equipes'
AND phpl_joueurs.id ='$id_joueur'
AND phpl_joueurs.id = phpl_effectif.id_joueur
AND phpl_effectif.id_equipe=phpl_equipes.id";

    
          $resultats = mysql_query ($requete) or die (mysql_error());
          while ($row=mysql_fetch_array($resultats))
          {
           mysql_query ("UPDATE phpl_buteurs SET phpl_buteurs.id_effectif='$row[0]' WHERE id_match='$id_match' and id_effectif='0'");
          }

 $i++;
 }
 echo SETUP_MAJ_ACHEVEE;
}
else
{

if (!isset($_POST['lang']) or !$_POST['lang']=="fr" or !$_POST['lang']=="en" or !$_POST['lang']=="de")
{  include("lang/lang_fr.php");
    ?>
 <form method="post" action="">
  <p><select size="1" name="lang">
    <option value="fr">Français</option>
    <option value="en">English</option>
    <option value="de">Deutch</option>
  </select>
  <input type="hidden" name="action" value="1">
  <input type="submit" value="Envoyer"></p>
 </form>
 <?php
}

else
{
  $lang=$_POST['lang'];
  include("lang/lang_".$lang.".php");

  if (!isset($_POST['action'])) {$action=0;} else {$action = $_POST['action'];}
  if (!isset($_POST['action2'])) {$action2=0;}  else {$action2 = $_POST['action2'];}

  if ($action=="1")
  {
    ?>

    <form method="post" action="">
    <p><?php echo SETUP_HOST_NAME;echo "<font color=\"#FF0000\">*</font>"; ?> : <input type="text" name="hostname" size="20"></p>
    <p><?php echo SETUP_DATABASE;echo "<font color=\"#FF0000\">*</font>"; ?> : <input type="text" name="database" size="20"></p>
    <p><?php echo SETUP_LOGIN;echo "<font color=\"#FF0000\">*</font>"; ?> : <input type="text" name="login" size="20"></p>
    <p><?php echo SETUP_PASSWORD;echo "<font color=\"#FF0000\">*</font>"; ?> : <input type="password" name="password" size="20"></p>

     <?php echo SETUP_TYPE; ?> :
      <select size="1" name="action2">
         <option selected value="install"><?php echo SETUP_NORMALE; ?></option>
         <option value="majv081"><?php echo SETUP_MAJ; ?> 0.81</option>
         <option value="majv071"><?php echo SETUP_MAJ; ?> 0.71</option>
     </select><hr>
     <p><?php echo SETUP_TITRE_SITE; echo "<font color=\"#FF0000\">*</font>"; ?> : <input maxLength="60" size="30" name="nom_site"><br />
     <br />
     <?php echo SETUP_URL_SITE; echo "<font color=\"#FF0000\">*</font>"; ?> : <input maxLength="120" size="40" value="http://<? print $_SERVER["HTTP_HOST"]; ?>" name="url_site"><br />
     <br />
     <?php echo SETUP_REPERTOIRE_SCRIPT; echo "<font color=\"#FF0000\">*</font>"; ?> : <input maxLength="120" size="40" value="http://<?php
$repertoire = explode("/", $_SERVER["SCRIPT_NAME"]);
$nb=count($repertoire);
//echo "$repertoire[0] - $repertoire[1] - $repertoire[2]";
$repertoire2="";
for ($i="1"; $i < (count($repertoire)-1); $i++)
{
  if ($i=="1") {$repertoire2 = $repertoire2.$repertoire[$i];}
  else {$repertoire2 = "$repertoire2/$repertoire[$i]";}
}

print $_SERVER["HTTP_HOST"];
echo "/";
print $repertoire2;
echo "/";
?>" name="repertoire_script"><br />
     <br />
     <?php echo SETUP_PSEUDO; echo "<font color=\"#FF0000\">*</font>"; ?> : <input maxLength="50" size="30" name="pseudo"><br />
     <br />
     <?php echo SETUP_MDP; echo "<font color=\"#FF0000\">*</font>";?>  : <input type="password"  maxLength="50" size="30" name="mot_de_pass"><br />
     <br />
     <?php echo SETUP_MAIL; echo "<font color=\"#FF0000\">*</font>"; ?> : <input maxLength="75" size="35"  name="mail"></p>


     <p align="center"><font color="#FF0000">A lire attentivement !<br />
Si vous avez choisi de mettre à jour de la version 0.71
vers la 0.80 ou 0.81 : avant de continuer il est fortement conseillé d'effectuer une
sauvegarde de votre base de donnée. En effet la mise à jour va modifier la
structure de la base de donnée et une erreur lors de l'exécution du script
pourrait détruire vos données !</font></p>



     <br /><?php  $button=ENVOI;
     echo "<input type=\"hidden\" name=\"lang\" value=\"$lang\"><input type=\"hidden\" name=\"action\" value=\"2\"><input type=\"submit\" value=\"$button\">"; ?>
     </p>
     </form>

     <?php
  }
  

  if ($action=="2" and isset($_POST['hostname']) and isset($_POST['login']) and isset($_POST['database']) and isset($_POST['password']) )
  {
   $hostname = $_POST['hostname'];
   $login = $_POST['login'];
   $database = $_POST['database'];
   $password = $_POST['password'];
   $repertoire_script = $_POST['repertoire_script'];


    if(($idconnect = @mysql_connect($hostname, $login, $password)) == false or @mysql_select_db($database, $idconnect) == false)
    {
      $test="erreur";
      $message=SETUP_ID_INCORRECTS."<br />"  ;
      print $message;
      $action=="1";
    }
    else {$test="0";}

  if (!$test=="erreur")
    {
     // on configure data_connect.php
     define("CHMOD_KO",1);

     $fp = @fopen("data_connect.php","w");

     $contenu  = "<?php\n";
     $contenu  .= "\$hostname=\"$hostname\";\n";
     $contenu .= "\$database=\"$database\";\n";
     $contenu .= "\$login=\"$login\";\n";
     $contenu .= "\$password=\"$password\";\n";
     $contenu .= "\$lang=\"$lang\";\n";
     $contenu .= "\$PHPLEAGUE_RACINE=\"$repertoire_script\";\n";
     $contenu .= "?>\n";

     @fwrite($fp,$contenu);
    }
  }

if ($action2=="majv071" and $action=="2" and isset($_POST['nom_site']) and !$_POST['nom_site']==""  and isset($_POST['url_site']) and !$_POST['url_site']==""and isset($_POST['repertoire_script']) and !$_POST['repertoire_script']=="" and isset($_POST['pseudo']) and !$_POST['pseudo']=="" and isset($_POST['mot_de_pass']) and !$_POST['mot_de_pass']=="" and isset($_POST['mail']) and !$_POST['mail']=="" and !$test=="erreur")
  {
  $nom_site = $_POST['nom_site'];
  $url_site = $_POST['url_site'];
  $repertoire_script = $_POST['repertoire_script'];
  $pseudo = $_POST['pseudo'];
  $mot_de_pass = $_POST['mot_de_pass'];
  $mail = $_POST['mail'];

     $taille = 19;
     $lettres = "abcdefghijklmnopqrstuvwxyz0123456789";
     srand(time());
     for ($i=0;$i<$taille;$i++)
     {
         if ($i=="0") {$id_prono = substr($lettres,(rand()%(strlen($lettres))),1);}
         else {$id_prono.= substr($lettres,(rand()%(strlen($lettres))),1);}
     }

     $mdp=md5($_POST['mot_de_pass']);

     // On modifie les tables

     $sql_clmnt_pronos="CREATE TABLE `clmnt_pronos` (
  `id_champ` int(3) unsigned NOT NULL default '0',
  `id_membre` int(4) unsigned NOT NULL default '0',
  `pseudo` varchar(50) NOT NULL default '',
  `points` int(3) unsigned NOT NULL default '0',
  `participation` int(3) unsigned NOT NULL default '0',
  `type` varchar(50) NOT NULL default '',
  KEY `id_membre` (`id_membre`),
  KEY `pseudo` (`pseudo`(1))
)";

     $sql_membres="CREATE TABLE `membres` (
  `id` int(3) unsigned NOT NULL auto_increment,
  `id_prono` varchar(50) NOT NULL default '',
  `pseudo` varchar(30) NOT NULL default '',
  `mot_de_passe` varchar(200) NOT NULL default '',
  `mail` varchar(40) NOT NULL default '',
  `nom_site` varchar(200) NOT NULL default '',
  `url_site` varchar(200) NOT NULL default '',
  `nom` varchar(50) NOT NULL default '',
  `prenom` varchar(50) NOT NULL default '',
  `adresse` varchar(200) NOT NULL default '',
  `code_postal` varchar(10) NOT NULL default '',
  `ville` varchar(100) NOT NULL default '',
  `pays` varchar(100) NOT NULL default '',
  `date_naissance` date NOT NULL default '0000-00-00',
  `profession` varchar(100) NOT NULL default '',
  `mobile` varchar(14) NOT NULL default '',
  `ip` varchar(15) NOT NULL default '',
  `last_connect` varchar(10) NOT NULL default '',
  `admin` int(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`)
)";

    $sql_parametres="ALTER TABLE `parametres` ADD `fiches_clubs` ENUM( '0', '1' ) NOT NULL;
    ALTER TABLE `parametres` ADD `estimation` ENUM( '0', '1' ) NOT NULL;
    ALTER TABLE `parametres` CHANGE `id_champ` `id_champ` INT( 3 ) UNSIGNED DEFAULT '0' NOT NULL ,
CHANGE `pts_victoire` `pts_victoire` INT( 3 ) UNSIGNED DEFAULT '0' NOT NULL ,
CHANGE `pts_nul` `pts_nul` INT( 3 ) UNSIGNED DEFAULT '0' NOT NULL ,
CHANGE `pts_defaite` `pts_defaite` INT( 3 ) UNSIGNED DEFAULT '0' NOT NULL ,
CHANGE `accession` `accession` INT( 3 ) UNSIGNED DEFAULT '0' NOT NULL ,
CHANGE `barrage` `barrage` INT( 3 ) UNSIGNED DEFAULT '0' NOT NULL ,
CHANGE `relegation` `relegation` INT( 3 ) UNSIGNED DEFAULT '0' NOT NULL ,
CHANGE `id_equipe_fetiche` `id_equipe_fetiche` SMALLINT( 6 ) DEFAULT NULL ,
CHANGE `fiches_clubs` `fiches_clubs` INT( 1 ) UNSIGNED DEFAULT '1' NOT NULL ,
CHANGE `estimation` `estimation` INT( 1 ) UNSIGNED DEFAULT '1' NOT NULL";

$sql_pronostics="CREATE TABLE `pronostics` (
  `id_membre` varchar(4) NOT NULL default '',
  `id_champ` int(3) unsigned NOT NULL default '0',
  `id_match` int(8) unsigned NOT NULL default '0',
  `pronostic` char(1) default NULL,
  `buts_dom` int(3) unsigned default NULL,
  `buts_ext` int(3) unsigned default NULL,
  `points` int(2) unsigned NOT NULL default '0',
  `participation` int(1) unsigned NOT NULL default '0',
  KEY `id_champ` (`id_champ`)
)";

$sql_maj_1="
  RENAME TABLE buteurs TO phpl_buteurs,
  championnats TO phpl_championnats,
  classe TO phpl_classe,
  clmnt TO phpl_clmnt,
  clmnt_graph TO phpl_clmnt_graph,
  clmnt_pronos TO phpl_clmnt_pronos,
  clubs TO phpl_clubs,
  divisions TO phpl_divisions,
  donnee TO phpl_donnee,
  equipes TO phpl_equipes,
  joueurs TO phpl_joueurs,
  journees TO phpl_journees,
  logo TO phpl_logo,
  matchs TO phpl_matchs,
  membres TO phpl_membres,
  parametres TO phpl_parametres,
  pronostics TO phpl_pronostics,
  rens TO phpl_rens,
  saisons TO phpl_saisons,
  tapis_vert TO phpl_tapis_vert;
  ALTER TABLE `phpl_buteurs` CHANGE `id` `id` MEDIUMINT( 8 ) UNSIGNED NOT NULL AUTO_INCREMENT;
  ALTER TABLE `phpl_championnats` CHANGE `id` `id` MEDIUMINT( 8 ) UNSIGNED NOT NULL AUTO_INCREMENT ";


$sql_maj_2="
  ALTER TABLE `phpl_joueurs` CHANGE `photo` `photo` VARCHAR(250) NOT NULL,
  CHANGE `id` `id` INT( 5 ) NOT NULL AUTO_INCREMENT,
  CHANGE `nom` `nom` VARCHAR(100) NOT NULL,
  CHANGE `prenom` `prenom` VARCHAR(100) NOT NULL,
  CHANGE `id_club` `id_club` INT(4) NOT NULL,
  CHANGE `position_terrain` `position_terrain` VARCHAR( 50 ) NOT NULL;
  ALTER TABLE `phpl_buteurs` CHANGE `id` `id` INT( 10 ) UNSIGNED NOT NULL AUTO_INCREMENT ,
CHANGE `id_match` `id_match` INT( 10 ) UNSIGNED DEFAULT NULL ,
CHANGE `buts` `buts` TINYINT( 4 ) DEFAULT NULL;
ALTER TABLE `phpl_championnats` CHANGE `id` `id` TINYINT( 3 ) UNSIGNED NOT NULL AUTO_INCREMENT ,
CHANGE `id_division` `id_division` TINYINT( 3 ) UNSIGNED DEFAULT '0' NOT NULL ,
CHANGE `id_saison` `id_saison` TINYINT( 3 ) UNSIGNED DEFAULT '0' NOT NULL;
ALTER TABLE `phpl_classe` CHANGE `id` `id` TINYINT( 3 ) UNSIGNED NOT NULL AUTO_INCREMENT ,
CHANGE `nom` `nom` VARCHAR( 255 ) NOT NULL ,
CHANGE `rang` `rang` TINYINT( 3 ) DEFAULT '0' NOT NULL;
ALTER TABLE `phpl_clmnt` CHANGE `NOM` `NOM` VARCHAR( 255 ) DEFAULT NULL ,
CHANGE `POINTS` `POINTS` SMALLINT( 4 ) UNSIGNED DEFAULT NULL ,
CHANGE `JOUES` `JOUES` TINYINT( 3 ) UNSIGNED DEFAULT NULL ,
CHANGE `G` `G` TINYINT( 3 ) UNSIGNED DEFAULT NULL ,
CHANGE `N` `N` TINYINT( 3 ) UNSIGNED DEFAULT NULL ,
CHANGE `P` `P` TINYINT( 3 ) UNSIGNED DEFAULT NULL ,
CHANGE `BUTSPOUR` `BUTSPOUR` SMALLINT( 4 ) UNSIGNED DEFAULT NULL ,
CHANGE `BUTSCONTRE` `BUTSCONTRE` SMALLINT( 4 ) UNSIGNED DEFAULT NULL ,
CHANGE `DIFF` `DIFF` SMALLINT( 4 ) DEFAULT NULL ,
CHANGE `PEN` `PEN` TINYINT( 2 ) DEFAULT NULL ,
CHANGE `DOMPOINTS` `DOMPOINTS` SMALLINT( 4 ) UNSIGNED DEFAULT NULL ,
CHANGE `DOMJOUES` `DOMJOUES` TINYINT( 3 ) UNSIGNED DEFAULT NULL ,
CHANGE `DOMG` `DOMG` TINYINT( 3 ) UNSIGNED DEFAULT NULL ,
CHANGE `DOMN` `DOMN` TINYINT( 3 ) UNSIGNED DEFAULT NULL ,
CHANGE `DOMP` `DOMP` TINYINT( 3 ) UNSIGNED DEFAULT NULL ,
CHANGE `DOMBUTSPOUR` `DOMBUTSPOUR` SMALLINT( 4 ) UNSIGNED DEFAULT NULL ,
CHANGE `DOMBUTSCONTRE` `DOMBUTSCONTRE` SMALLINT( 4 ) UNSIGNED DEFAULT NULL ,
CHANGE `DOMDIFF` `DOMDIFF` SMALLINT( 4 ) DEFAULT NULL ,
CHANGE `EXTPOINTS` `EXTPOINTS` SMALLINT( 4 ) UNSIGNED DEFAULT NULL ,
CHANGE `EXTJOUES` `EXTJOUES` TINYINT( 3 ) UNSIGNED DEFAULT NULL ,
CHANGE `EXTG` `EXTG` TINYINT( 3 ) UNSIGNED DEFAULT NULL ,
CHANGE `EXTN` `EXTN` TINYINT( 3 ) UNSIGNED DEFAULT NULL ,
CHANGE `EXTP` `EXTP` TINYINT( 3 ) UNSIGNED DEFAULT NULL ,
CHANGE `EXTBUTSPOUR` `EXTBUTSPOUR` SMALLINT( 4 ) UNSIGNED DEFAULT NULL ,
CHANGE `EXTBUTSCONTRE` `EXTBUTSCONTRE` SMALLINT( 4 ) UNSIGNED DEFAULT NULL ,
CHANGE `EXTDIFF` `EXTDIFF` TINYINT( 4 ) DEFAULT NULL ,
CHANGE `ID_EQUIPE` `ID_EQUIPE` SMALLINT( 5 ) UNSIGNED DEFAULT '0' NOT NULL ,
CHANGE `ID_CHAMP` `ID_CHAMP` TINYINT( 3 ) UNSIGNED DEFAULT '0' NOT NULL;
ALTER TABLE `phpl_clmnt_graph` CHANGE `id_equipe` `id_equipe` SMALLINT( 5 ) UNSIGNED DEFAULT '0' NOT NULL ,
CHANGE `fin` `fin` TINYINT( 3 ) UNSIGNED DEFAULT '0' NOT NULL ,
CHANGE `classement` `classement` TINYINT( 3 ) UNSIGNED DEFAULT '0' NOT NULL;
ALTER TABLE `phpl_clmnt_pronos` CHANGE `id_champ` `id_champ` TINYINT( 3 ) UNSIGNED DEFAULT '0' NOT NULL ,
CHANGE `id_membre` `id_membre` SMALLINT( 5 ) UNSIGNED DEFAULT '0' NOT NULL ,
CHANGE `pseudo` `pseudo` VARCHAR( 255 ) NOT NULL ,
CHANGE `points` `points` SMALLINT( 5 ) UNSIGNED DEFAULT '0' NOT NULL ,
CHANGE `participation` `participation` SMALLINT( 3 ) UNSIGNED DEFAULT '0' NOT NULL ,
CHANGE `type` `type` ENUM( 'hebdo', 'mensuel_30_jours', 'mensuel_en_cours', 'general' ) NOT NULL;
ALTER TABLE `phpl_clmnt_graph` CHANGE `fin` `fin` TINYINT( 3 ) UNSIGNED DEFAULT '0' NOT NULL ,
CHANGE `classement` `classement` TINYINT( 3 ) UNSIGNED DEFAULT '0' NOT NULL;
ALTER TABLE `phpl_clubs` CHANGE `id` `id` SMALLINT( 5 ) UNSIGNED NOT NULL AUTO_INCREMENT ,
CHANGE `nom` `nom` VARCHAR( 255 ) NOT NULL;
ALTER TABLE `phpl_divisions` CHANGE `id` `id` TINYINT( 3 ) UNSIGNED NOT NULL AUTO_INCREMENT ,
CHANGE `nom` `nom` VARCHAR( 255 ) NOT NULL ;
ALTER TABLE `phpl_donnee` CHANGE `id` `id` SMALLINT( 5 ) UNSIGNED NOT NULL AUTO_INCREMENT ,
CHANGE `nom` `nom` TEXT NOT NULL ,
CHANGE `id_clubs` `id_clubs` SMALLINT( 5 ) UNSIGNED DEFAULT '0' NOT NULL ,
CHANGE `id_rens` `id_rens` SMALLINT( 5 ) UNSIGNED DEFAULT '0' NOT NULL ,
CHANGE `etat` `etat` ENUM( '0', '1' ) DEFAULT '0' NOT NULL ,
CHANGE `url` `url` VARCHAR( 200 ) NOT NULL ;
ALTER TABLE `phpl_equipes` CHANGE `id` `id` SMALLINT( 5 ) UNSIGNED NOT NULL AUTO_INCREMENT ,
CHANGE `id_champ` `id_champ` TINYINT( 3 ) UNSIGNED DEFAULT '0' NOT NULL ,
CHANGE `id_club` `id_club` SMALLINT( 5 ) UNSIGNED DEFAULT '0' NOT NULL ,
CHANGE `penalite` `penalite` TINYINT( 3 ) DEFAULT NULL;
ALTER TABLE `phpl_gr_championnats` CHANGE `id` `id` TINYINT( 3 ) UNSIGNED NOT NULL AUTO_INCREMENT ,
CHANGE `nom` `nom` VARCHAR( 255 ) NOT NULL ,
CHANGE `id_champ` `id_champ` TINYINT( 3 ) UNSIGNED DEFAULT '0' NOT NULL ,
CHANGE `activ_prono` `activ_prono` ENUM( '0', '1' ) DEFAULT '1' NOT NULL ,
CHANGE `pts_prono_exact` `pts_prono_exact` TINYINT( 3 ) UNSIGNED DEFAULT '0' NOT NULL ,
CHANGE `pts_prono_participation` `pts_prono_participation` TINYINT( 2 ) UNSIGNED DEFAULT '0' NOT NULL ,
CHANGE `id_master` `id_master` SMALLINT( 5 ) UNSIGNED DEFAULT '0',
CHANGE `tps_avant_prono` `tps_avant_prono` TINYINT( 2 ) UNSIGNED DEFAULT '2' NOT NULL;
ALTER TABLE `phpl_joueurs` CHANGE `nom` `nom` VARCHAR( 100 ) NOT NULL ,
CHANGE `prenom` `prenom` VARCHAR( 100 ) NOT NULL ,
CHANGE `date_naissance` `date_naissance` DATE DEFAULT '0000-00-00' NOT NULL ,
CHANGE `position_terrain` `position_terrain` VARCHAR( 50 ) NOT NULL ,
CHANGE `photo` `photo` VARCHAR( 250 ) NOT NULL ,
CHANGE `id` `id` SMALLINT( 5 ) NOT NULL AUTO_INCREMENT;
ALTER TABLE `phpl_journees` CHANGE `numero` `numero` TINYINT( 3 ) UNSIGNED NOT NULL ,
CHANGE `date_prevue` `date_prevue` DATE NOT NULL ,
CHANGE `id` `id` SMALLINT( 5 ) UNSIGNED NOT NULL AUTO_INCREMENT ,
CHANGE `id_champ` `id_champ` TINYINT( 3 ) UNSIGNED NOT NULL ;
ALTER TABLE `phpl_logo` CHANGE `id` `id` SMALLINT( 5 ) UNSIGNED NOT NULL AUTO_INCREMENT ,
CHANGE `id_club` `id_club` SMALLINT( 5 ) UNSIGNED DEFAULT '0' NOT NULL ,
CHANGE `url` `url` TINYTEXT NOT NULL;
ALTER TABLE `phpl_matchs` CHANGE `id` `id` MEDIUMINT( 6 ) UNSIGNED NOT NULL AUTO_INCREMENT ,
CHANGE `id_equipe_dom` `id_equipe_dom` SMALLINT( 5 ) UNSIGNED DEFAULT NULL ,
CHANGE `id_equipe_ext` `id_equipe_ext` SMALLINT( 5 ) UNSIGNED DEFAULT NULL ,
CHANGE `date_reelle` `date_reelle` DATETIME DEFAULT NULL ,
CHANGE `id_journee` `id_journee` SMALLINT( 5 ) UNSIGNED DEFAULT NULL ,
CHANGE `buts_dom` `buts_dom` SMALLINT( 4 ) UNSIGNED DEFAULT NULL ,
CHANGE `buts_ext` `buts_ext` SMALLINT( 4 ) UNSIGNED DEFAULT NULL ;
ALTER TABLE `phpl_membres` CHANGE `id` `id` SMALLINT( 5 ) UNSIGNED NOT NULL AUTO_INCREMENT ,
CHANGE `id_prono` `id_prono` VARCHAR( 19 ) NOT NULL ,
CHANGE `pseudo` `pseudo` VARCHAR( 20 ) NOT NULL ,
CHANGE `mot_de_passe` `mot_de_passe` VARCHAR( 250 ) NOT NULL ,
CHANGE `mail` `mail` VARCHAR( 40 ) NOT NULL ,
CHANGE `nom_site` `nom_site` VARCHAR( 50 ) NOT NULL ,
CHANGE `url_site` `url_site` VARCHAR( 100 ) NOT NULL ,
CHANGE `nom` `nom` VARCHAR( 50 ) NOT NULL ,
CHANGE `prenom` `prenom` VARCHAR( 50 ) NOT NULL ,
CHANGE `adresse` `adresse` VARCHAR( 100 ) NOT NULL ,
CHANGE `code_postal` `code_postal` MEDIUMINT( 5 ) DEFAULT '0' NOT NULL ,
CHANGE `ville` `ville` VARCHAR( 200 ) NOT NULL ,
CHANGE `pays` `pays` VARCHAR( 200 ) NOT NULL ,
CHANGE `date_naissance` `date_naissance` DATE DEFAULT '0000-00-00' NOT NULL ,
CHANGE `profession` `profession` VARCHAR( 200 ) NOT NULL ,
CHANGE `mobile` `mobile` VARCHAR( 14 ) NOT NULL ,
CHANGE `ip` `ip` VARCHAR( 15 ) NOT NULL ,
CHANGE `last_connect` `last_connect` VARCHAR( 10 ) NOT NULL ,
CHANGE `admin` `admin` ENUM( '0', '1' ) DEFAULT '0' NOT NULL;
ALTER TABLE `phpl_parametres` CHANGE `id_champ` `id_champ` TINYINT( 3 ) UNSIGNED DEFAULT '0' NOT NULL ,
CHANGE `pts_victoire` `pts_victoire` TINYINT( 3 ) UNSIGNED DEFAULT '0' NOT NULL ,
CHANGE `pts_nul` `pts_nul` TINYINT( 3 ) UNSIGNED DEFAULT '0' NOT NULL ,
CHANGE `pts_defaite` `pts_defaite` TINYINT( 3 ) UNSIGNED DEFAULT '0' NOT NULL ,
CHANGE `accession` `accession` TINYINT( 3 ) UNSIGNED DEFAULT '0' NOT NULL ,
CHANGE `barrage` `barrage` TINYINT( 3 ) UNSIGNED DEFAULT '0' NOT NULL ,
CHANGE `relegation` `relegation` TINYINT( 3 ) UNSIGNED DEFAULT '0' NOT NULL ,
CHANGE `id_equipe_fetiche` `id_equipe_fetiche` SMALLINT( 4 ) DEFAULT NULL ,
CHANGE `fiches_clubs` `fiches_clubs` ENUM( '0', '1' ) DEFAULT '1' NOT NULL ,
CHANGE `estimation` `estimation` ENUM( '0', '1' ) DEFAULT '1' NOT NULL;
ALTER TABLE `phpl_pronostics` CHANGE `id_membre` `id_membre` SMALLINT( 5 ) UNSIGNED DEFAULT '0' NOT NULL ,
CHANGE `id_champ` `id_champ` INT( 3 ) UNSIGNED DEFAULT '0' NOT NULL ,
CHANGE `id_match` `id_match` MEDIUMINT( 6 ) UNSIGNED DEFAULT '0' NOT NULL ,
CHANGE `pronostic` `pronostic` ENUM( '1', 'N', '2' ) DEFAULT NULL ,
CHANGE `buts_dom` `buts_dom` SMALLINT( 4 ) UNSIGNED DEFAULT NULL ,
CHANGE `buts_ext` `buts_ext` SMALLINT( 4 ) UNSIGNED DEFAULT NULL ,
CHANGE `points` `points` TINYINT( 2 ) UNSIGNED DEFAULT '0' NOT NULL ,
CHANGE `participation` `participation` ENUM( '0', '1' ) DEFAULT '0' NOT NULL ;
ALTER TABLE `phpl_rens` CHANGE `id` `id` SMALLINT( 5 ) UNSIGNED NOT NULL AUTO_INCREMENT ,
CHANGE `nom` `nom` VARCHAR( 50 ) NOT NULL ,
CHANGE `id_classe` `id_classe` TINYINT( 3 ) UNSIGNED DEFAULT '0' NOT NULL ,
CHANGE `rang` `rang` TINYINT( 3 ) UNSIGNED DEFAULT '0' NOT NULL ,
CHANGE `url` `url` VARCHAR( 200 ) NOT NULL;
ALTER TABLE `phpl_saisons` CHANGE `id` `id` TINYINT( 3 ) UNSIGNED NOT NULL AUTO_INCREMENT ,
CHANGE `annee` `annee` YEAR( 4 ) DEFAULT '0000' NOT NULL;
ALTER TABLE `phpl_buteurs` DROP `id_joueur`;
ALTER TABLE `phpl_joueurs` DROP `id_club`;
DROP TABLE `phpl_logo`";
  
$sql_maj_3 = "ALTER TABLE `phpl_clmnt` ADD `ID_CHAMP` INT( 3 ) UNSIGNED NOT NULL ;
ALTER TABLE `phpl_clubs` DROP INDEX `id`;
ALTER TABLE `phpl_clubs` DROP INDEX `nom`;
ALTER TABLE `phpl_championnats` ADD PRIMARY KEY ( `id` );
ALTER TABLE `phpl_championnats` DROP INDEX `id`;
ALTER TABLE `phpl_championnats` DROP INDEX `id_saison`;
ALTER TABLE `phpl_championnats` DROP INDEX `id_division`;
ALTER TABLE `phpl_classe` ADD PRIMARY KEY ( `id` );
ALTER TABLE `phpl_classe` DROP INDEX `id`;
ALTER TABLE `phpl_clmnt` ADD INDEX ( `ID_CHAMP` );
ALTER TABLE `phpl_clmnt_graph` DROP INDEX `nom`;
ALTER TABLE `phpl_clmnt_graph` ADD INDEX ( `id_equipe` );
ALTER TABLE `phpl_clmnt_pronos` DROP INDEX `pseudo`;
ALTER TABLE `phpl_clmnt_pronos` ADD INDEX ( `id_champ` );
ALTER TABLE `phpl_divisions` DROP PRIMARY KEY;
ALTER TABLE `phpl_divisions` DROP INDEX `nom_2`;
ALTER TABLE `phpl_divisions` ADD PRIMARY KEY ( `id` );
ALTER TABLE `phpl_divisions` DROP INDEX `id`;
ALTER TABLE `phpl_equipes` DROP INDEX `id`;
ALTER TABLE `phpl_journees` DROP INDEX `id`;
ALTER TABLE `phpl_journees` DROP INDEX `numero`;
ALTER TABLE `phpl_logo` DROP PRIMARY KEY;
ALTER TABLE `phpl_logo` ADD PRIMARY KEY ( `id_club` );
ALTER TABLE `phpl_logo` DROP INDEX `id_club`;
ALTER TABLE `phpl_parametres` ADD PRIMARY KEY ( `id_champ` );
ALTER TABLE `phpl_parametres` DROP INDEX `id_champ`;
ALTER TABLE `phpl_pronostics` ADD INDEX ( `id_match` );
ALTER TABLE `phpl_pronostics` DROP INDEX `id_champ`;
ALTER TABLE `phpl_pronostics` ADD INDEX ( `id_membre` ); 
ALTER TABLE `phpl_rens` ADD PRIMARY KEY ( `id` );
ALTER TABLE `phpl_rens` DROP INDEX `id`;
ALTER TABLE `phpl_saisons` DROP INDEX `id_2`;
ALTER TABLE `phpl_saisons` DROP PRIMARY KEY;
ALTER TABLE `phpl_saisons` ADD PRIMARY KEY ( `id` );
DROP TABLE `phpl_tapis_vert`;
ALTER TABLE `phpl_buteurs` ADD `id_effectif` SMALLINT( 5 ) UNSIGNED NOT NULL AFTER `id_match`;
ALTER TABLE `phpl_clubs` ADD `url_logo` TINYTEXT NOT NULL";

$sql_admin = "INSERT INTO `membres` (`id_prono` , `pseudo` , `mot_de_passe` , `mail` , `nom_site` , `url_site`, `admin` )
                             VALUES ('$id_prono', '$pseudo', '$mdp', '$mail', '$nom_site', '$url_site','1')";

$sql_gr_champ="CREATE TABLE phpl_gr_championnats (
  id int(10) unsigned NOT NULL auto_increment,
  nom varchar (50) NOT NULL,
  id_champ int(3) unsigned NOT NULL,
  activ_prono INT(1) unsigned NOT NULL default '1' ,
  pts_prono_exact int(3) unsigned NOT NULL default '0',
  pts_prono_participation int(3) unsigned NOT NULL default '0',
  id_master int(4) unsigned default '0',
  tps_avant_prono int(1) unsigned NOT NULL default '2',
  KEY id (id)
) ";

$sql_clmnt_cache="CREATE TABLE `phpl_clmnt_cache` (
  `NOM` varchar(255) default NULL,
  `POINTS` smallint(4) unsigned default NULL,
  `JOUES` tinyint(3) unsigned default NULL,
  `G` tinyint(3) unsigned default NULL,
  `N` tinyint(3) unsigned default NULL,
  `P` tinyint(3) unsigned default NULL,
  `BUTSPOUR` smallint(4) unsigned default NULL,
  `BUTSCONTRE` smallint(4) unsigned default NULL,
  `DIFF` smallint(4) default NULL,
  `PEN` tinyint(2) default NULL,
  `DOMPOINTS` smallint(4) unsigned default NULL,
  `DOMJOUES` tinyint(3) unsigned default NULL,
  `DOMG` tinyint(3) unsigned default NULL,
  `DOMN` tinyint(3) unsigned default NULL,
  `DOMP` tinyint(3) unsigned default NULL,
  `DOMBUTSPOUR` smallint(4) unsigned default NULL,
  `DOMBUTSCONTRE` smallint(4) unsigned default NULL,
  `DOMDIFF` smallint(4) default NULL,
  `EXTPOINTS` smallint(4) unsigned default NULL,
  `EXTJOUES` tinyint(3) unsigned default NULL,
  `EXTG` tinyint(3) unsigned default NULL,
  `EXTN` tinyint(3) unsigned default NULL,
  `EXTP` tinyint(3) unsigned default NULL,
  `EXTBUTSPOUR` smallint(4) unsigned default NULL,
  `EXTBUTSCONTRE` smallint(4) unsigned default NULL,
  `EXTDIFF` tinyint(4) default NULL,
  `ID_EQUIPE` smallint(5) unsigned NOT NULL default '0',
  `ID_CHAMP` tinyint(3) unsigned NOT NULL default '0',
  KEY `ID_CHAMP` (`ID_CHAMP`)
);
CREATE TABLE `phpl_effectif` (
`id` SMALLINT( 5 ) UNSIGNED NOT NULL AUTO_INCREMENT ,
`id_joueur` SMALLINT( 5 ) UNSIGNED NOT NULL ,
`id_equipe` SMALLINT( 5 ) UNSIGNED NOT NULL ,
PRIMARY KEY ( `id` )
)";



if ($res_clmnt_pronos = send_sql($sql_clmnt_pronos,SETUP_TABLE_3." clmnt_pronos"))
   { echo "<font color=\"#009900\" size=\"2\" face=\"Verdana\">".SETUP_TABLE." phpl_clmnt_pronos ".SETUP_TABLE_2."</font>\n<br />";  }
		
if ($res_membres = send_sql($sql_membres,SETUP_TABLE_3." membres"))
   { echo "<font color=\"#009900\" size=\"2\" face=\"Verdana\">".SETUP_TABLE." phpl_membres ".SETUP_TABLE_2."</font>\n<br />";}


if ($res_pronostics = send_sql($sql_pronostics,SETUP_TABLE_3." pronostics"))
   { echo "<font color=\"#009900\" size=\"2\" face=\"Verdana\">".SETUP_TABLE." phpl_pronostics ".SETUP_TABLE_2."</font>\n<br />";}

if ($res_admin = send_sql($sql_admin,SETUP_TABLE_4))
   { echo "<font color=\"#009900\" size=\"2\" face=\"Verdana\">".SETUP_TABLE_5."</font>\n<br />"; }
   
if ($res_parametres = send_sql($sql_parametres,SETUP_TABLE_3." parametres"))
   { echo "<font color=\"#009900\" size=\"2\" face=\"Verdana\">".SETUP_TABLE." phpl_parametres ".SETUP_TABLE_2."</font>\n<br />"; }

if ($res_maj_1 = send_sql($sql_maj_1,SETUP_TABLE_3." mises à jour (1)"))
   { echo "<font color=\"#009900\" size=\"2\" face=\"Verdana\">".SETUP_TABLE." mises à jour (1) ".SETUP_TABLE_2."</font>\n<br />";}
   
if ($res_maj_3 = send_sql($sql_maj_3,SETUP_TABLE_3." mises à jour (3)"))
   { echo "<font color=\"#009900\" size=\"2\" face=\"Verdana\">".SETUP_TABLE." mises à jour (3)".SETUP_TABLE_2."</font>\n<br />";}
   
if ($res_gr_champ = send_sql($sql_gr_champ,SETUP_TABLE_3." groupe championnats"))
   { echo "<font color=\"#009900\" size=\"2\" face=\"Verdana\">".SETUP_TABLE." phpl_gr_championnats ".SETUP_TABLE_2."</font>\n<br />"; }

if ($res_gr_champ = send_sql($sql_clmnt_cache,SETUP_TABLE_3." classement cache"))
   { echo "<font color=\"#009900\" size=\"2\" face=\"Verdana\">".SETUP_TABLE." phpl_clmnt_cache ".SETUP_TABLE_2."</font>\n<br />"; }


$requete="SELECT id_club, url FROM phpl_logo";
$resultat=mysql_query($requete) or die (mysql_error());

while($row = mysql_fetch_array($resultat))
{
  $url_logo=$row["url"];
  $id_club=$row["id_club"];
  $requete2="UPDATE phpl_clubs SET url_logo = '$url_logo'  WHERE id = '$id_club'";
  $resultat2=mysql_query($requete2) or die (mysql_error());

}

$requete="SELECT DISTINCT phpl_joueurs.id, phpl_equipes.id
          FROM phpl_joueurs, phpl_equipes, phpl_buteurs, phpl_matchs, phpl_clubs
          WHERE phpl_equipes.id_club=phpl_clubs.id
          AND (
           phpl_matchs.id_equipe_dom = phpl_equipes.id
           OR phpl_matchs.id_equipe_ext = phpl_equipes.id
              )
          AND phpl_matchs.id=phpl_buteurs.id_match
          AND phpl_joueurs.id_club=phpl_equipes.id_club
          AND phpl_buteurs.id_joueur=phpl_joueurs.id";

$resultat=mysql_query($requete) or die (mysql_error());

while($row = mysql_fetch_array($resultat))
{
 mysql_query ("INSERT INTO phpl_effectif (id_joueur, id_equipe) values ('$row[0]','$row[1]')") or die ("probleme " .mysql_error());
}

$requete="SELECT phpl_effectif.id, phpl_buteurs.id_match, phpl_joueurs.id
          FROM phpl_joueurs, phpl_equipes, phpl_buteurs, phpl_matchs, phpl_clubs, phpl_effectif
          WHERE phpl_equipes.id_club=phpl_clubs.id
          AND (
           phpl_matchs.id_equipe_dom = phpl_equipes.id
           OR phpl_matchs.id_equipe_ext = phpl_equipes.id
              )
          AND phpl_matchs.id=phpl_buteurs.id_match
          AND phpl_joueurs.id_club=phpl_equipes.id_club
          AND phpl_buteurs.id_joueur=phpl_joueurs.id
          AND phpl_effectif.id_joueur=phpl_joueurs.id
          AND phpl_effectif.id_equipe=phpl_equipes.id";
          
$resultat=mysql_query($requete) or die (mysql_error());

while($row = mysql_fetch_array($resultat))
{
 mysql_query ("UPDATE phpl_buteurs SET id_effectif='$row[0]' WHERE id_match='$row[1]' and id_joueur='$row[2]'") or die ("probleme " .mysql_error());
}


$requete="SELECT DISTINCT phpl_joueurs.nom, phpl_joueurs.prenom, phpl_buteurs.id, phpl_journees.numero, phpl_matchs.date_reelle, phpl_joueurs.id
FROM phpl_joueurs, phpl_buteurs, phpl_matchs, phpl_equipes, phpl_journees
WHERE phpl_matchs.id = phpl_buteurs.id_match
AND phpl_buteurs.id_joueur = phpl_joueurs.id
AND phpl_buteurs.id_effectif =0
AND (
phpl_matchs.id_equipe_dom = phpl_equipes.id
OR phpl_matchs.id_equipe_ext = phpl_equipes.id
    )
AND phpl_journees.id = phpl_matchs.id_journee";
          
$resultat=mysql_query($requete) or die (mysql_error());

if (!mysql_num_rows( $resultat )=='0')
{
echo "<form method=\"post\" action=\"\">";
$i=0;
while($row = mysql_fetch_array($resultat))
{  
  echo SETUP_MAJ_INCOH." : $row[0] $row[1] ".SETUP_MAJ_INCOH_2." : ";
  $requete1="SELECT phpl_clubs.nom, phpl_equipes.id, phpl_matchs.id
             FROM phpl_joueurs, phpl_equipes, phpl_buteurs, phpl_matchs, phpl_clubs, phpl_journees
             WHERE phpl_equipes.id_club = phpl_clubs.id
             AND (
             phpl_matchs.id_equipe_dom = phpl_equipes.id
             OR phpl_matchs.id_equipe_ext = phpl_equipes.id
                 )
             AND phpl_matchs.id = phpl_buteurs.id_match
             AND phpl_buteurs.id_joueur = phpl_joueurs.id
             AND phpl_buteurs.id_effectif =0
             AND phpl_journees.id = phpl_matchs.id_journee
             AND phpl_buteurs.id='$row[2]'";
             
 $resultat1=mysql_query($requete1) or die (mysql_error());
 
 while($row1 = mysql_fetch_array($resultat1))
 {
   echo "$row1[0]<input type=\"radio\" value=\"$row1[1]\" name=\"id_equipes-".$i."\">";
   echo "<input type=\"hidden\" value=\"$row[5]\" name=\"id_joueur-".$i."\">";
   echo "<input type=\"hidden\" value=\"$row1[2]\" name=\"id_match-".$i."\"><br>";
   
 }
 $i++;

}
echo "<input type=\"hidden\" value=\"1\" name=\"buteurs\">";
echo "<input type=\"hidden\" value=\"$i\" name=\"nb_joueur\">";
echo "<input type=\"submit\" value=".ENVOI."></form>";

}

if ($res_maj_2 = send_sql($sql_maj_2,SETUP_TABLE_3." mises à jour (2)"))
   { echo "<font color=\"#009900\" size=\"2\" face=\"Verdana\">".SETUP_TABLE." mises à jour (2)".SETUP_TABLE_2."</font>\n<br />";}




$installation_terminee=1;

}
elseif ($action2=="majv071" and $action=="2" and (!isset($_POST['nom_site']) or $_POST['nom_site']==""  or !isset($_POST['url_site']) or $_POST['url_site']=="" or !isset($_POST['repertoire_script']) or $_POST['repertoire_script']=="" or !isset($_POST['pseudo']) or $_POST['pseudo']=="" or !isset($_POST['mot_de_pass']) or $_POST['mot_de_pass']=="" or !isset($_POST['mail']) or $_POST['mail']=="" or $test=="erreur"))
 {echo SETUP_REMPLIR_CHAMP;}

 if ($action2=="majv081" and $action=="2" and isset($_POST['repertoire_script']) and !$_POST['repertoire_script']=="" and !$test=="erreur")
  {
  $nom_site = $_POST['nom_site'];
  $url_site = $_POST['url_site'];
  $repertoire_script = $_POST['repertoire_script'];
  $pseudo = $_POST['pseudo'];
  $mot_de_pass = $_POST['mot_de_pass'];
  $mail = $_POST['mail'];

     $taille = 19;
     $lettres = "abcdefghijklmnopqrstuvwxyz0123456789";
     srand(time());
     for ($i=0;$i<$taille;$i++)
     {
         if ($i=="0") {$id_prono = substr($lettres,(rand()%(strlen($lettres))),1);}
         else {$id_prono.= substr($lettres,(rand()%(strlen($lettres))),1);}
     }

     $mdp=md5($_POST['mot_de_pass']);

     // On modifie les tables



$sql_maj_1="ALTER TABLE `phpl_buteurs` CHANGE `id` `id` INT( 10 ) UNSIGNED NOT NULL AUTO_INCREMENT ,
CHANGE `id_match` `id_match` INT( 10 ) UNSIGNED DEFAULT NULL ,
CHANGE `buts` `buts` TINYINT( 4 ) DEFAULT NULL;
ALTER TABLE `phpl_championnats` CHANGE `id` `id` TINYINT( 3 ) UNSIGNED NOT NULL AUTO_INCREMENT ,
CHANGE `id_division` `id_division` TINYINT( 3 ) UNSIGNED DEFAULT '0' NOT NULL ,
CHANGE `id_saison` `id_saison` TINYINT( 3 ) UNSIGNED DEFAULT '0' NOT NULL;
ALTER TABLE `phpl_classe` CHANGE `id` `id` TINYINT( 3 ) UNSIGNED NOT NULL AUTO_INCREMENT ,
CHANGE `nom` `nom` VARCHAR( 255 ) NOT NULL ,
CHANGE `rang` `rang` TINYINT( 3 ) DEFAULT '0' NOT NULL;
ALTER TABLE `phpl_clmnt` CHANGE `NOM` `NOM` VARCHAR( 255 ) DEFAULT NULL ,
CHANGE `POINTS` `POINTS` SMALLINT( 4 ) UNSIGNED DEFAULT NULL ,
CHANGE `JOUES` `JOUES` TINYINT( 3 ) UNSIGNED DEFAULT NULL ,
CHANGE `G` `G` TINYINT( 3 ) UNSIGNED DEFAULT NULL ,
CHANGE `N` `N` TINYINT( 3 ) UNSIGNED DEFAULT NULL ,
CHANGE `P` `P` TINYINT( 3 ) UNSIGNED DEFAULT NULL ,
CHANGE `BUTSPOUR` `BUTSPOUR` SMALLINT( 4 ) UNSIGNED DEFAULT NULL ,
CHANGE `BUTSCONTRE` `BUTSCONTRE` SMALLINT( 4 ) UNSIGNED DEFAULT NULL ,
CHANGE `DIFF` `DIFF` SMALLINT( 4 ) DEFAULT NULL ,
CHANGE `PEN` `PEN` TINYINT( 2 ) DEFAULT NULL ,
CHANGE `DOMPOINTS` `DOMPOINTS` SMALLINT( 4 ) UNSIGNED DEFAULT NULL ,
CHANGE `DOMJOUES` `DOMJOUES` TINYINT( 3 ) UNSIGNED DEFAULT NULL ,
CHANGE `DOMG` `DOMG` TINYINT( 3 ) UNSIGNED DEFAULT NULL ,
CHANGE `DOMN` `DOMN` TINYINT( 3 ) UNSIGNED DEFAULT NULL ,
CHANGE `DOMP` `DOMP` TINYINT( 3 ) UNSIGNED DEFAULT NULL ,
CHANGE `DOMBUTSPOUR` `DOMBUTSPOUR` SMALLINT( 4 ) UNSIGNED DEFAULT NULL ,
CHANGE `DOMBUTSCONTRE` `DOMBUTSCONTRE` SMALLINT( 4 ) UNSIGNED DEFAULT NULL ,
CHANGE `DOMDIFF` `DOMDIFF` SMALLINT( 4 ) DEFAULT NULL ,
CHANGE `EXTPOINTS` `EXTPOINTS` SMALLINT( 4 ) UNSIGNED DEFAULT NULL ,
CHANGE `EXTJOUES` `EXTJOUES` TINYINT( 3 ) UNSIGNED DEFAULT NULL ,
CHANGE `EXTG` `EXTG` TINYINT( 3 ) UNSIGNED DEFAULT NULL ,
CHANGE `EXTN` `EXTN` TINYINT( 3 ) UNSIGNED DEFAULT NULL ,
CHANGE `EXTP` `EXTP` TINYINT( 3 ) UNSIGNED DEFAULT NULL ,
CHANGE `EXTBUTSPOUR` `EXTBUTSPOUR` SMALLINT( 4 ) UNSIGNED DEFAULT NULL ,
CHANGE `EXTBUTSCONTRE` `EXTBUTSCONTRE` SMALLINT( 4 ) UNSIGNED DEFAULT NULL ,
CHANGE `EXTDIFF` `EXTDIFF` TINYINT( 4 ) DEFAULT NULL ,
CHANGE `ID_EQUIPE` `ID_EQUIPE` SMALLINT( 5 ) UNSIGNED DEFAULT '0' NOT NULL ,
CHANGE `ID_CHAMP` `ID_CHAMP` TINYINT( 3 ) UNSIGNED DEFAULT '0' NOT NULL;
ALTER TABLE `phpl_clmnt_graph` CHANGE `id_equipe` `id_equipe` SMALLINT( 5 ) UNSIGNED DEFAULT '0' NOT NULL ,
CHANGE `fin` `fin` TINYINT( 3 ) UNSIGNED DEFAULT '0' NOT NULL ,
CHANGE `classement` `classement` TINYINT( 3 ) UNSIGNED DEFAULT '0' NOT NULL;
ALTER TABLE `phpl_clmnt_pronos` CHANGE `id_champ` `id_champ` TINYINT( 3 ) UNSIGNED DEFAULT '0' NOT NULL ,
CHANGE `id_membre` `id_membre` SMALLINT( 5 ) UNSIGNED DEFAULT '0' NOT NULL ,
CHANGE `pseudo` `pseudo` VARCHAR( 255 ) NOT NULL ,
CHANGE `points` `points` SMALLINT( 5 ) UNSIGNED DEFAULT '0' NOT NULL ,
CHANGE `participation` `participation` SMALLINT( 3 ) UNSIGNED DEFAULT '0' NOT NULL ,
CHANGE `type` `type` ENUM( 'hebdo', 'mensuel_30_jours', 'mensuel_en_cours', 'general' ) NOT NULL;
ALTER TABLE `phpl_clmnt_graph` CHANGE `fin` `fin` TINYINT( 3 ) UNSIGNED DEFAULT '0' NOT NULL ,
CHANGE `classement` `classement` TINYINT( 3 ) UNSIGNED DEFAULT '0' NOT NULL;
ALTER TABLE `phpl_clubs` CHANGE `id` `id` SMALLINT( 5 ) UNSIGNED NOT NULL AUTO_INCREMENT ,
CHANGE `nom` `nom` VARCHAR( 255 ) NOT NULL;
ALTER TABLE `phpl_divisions` CHANGE `id` `id` TINYINT( 3 ) UNSIGNED NOT NULL AUTO_INCREMENT ,
CHANGE `nom` `nom` VARCHAR( 255 ) NOT NULL ;
ALTER TABLE `phpl_donnee` CHANGE `id` `id` SMALLINT( 5 ) UNSIGNED NOT NULL AUTO_INCREMENT ,
CHANGE `nom` `nom` TEXT NOT NULL ,
CHANGE `id_clubs` `id_clubs` SMALLINT( 5 ) UNSIGNED DEFAULT '0' NOT NULL ,
CHANGE `id_rens` `id_rens` SMALLINT( 5 ) UNSIGNED DEFAULT '0' NOT NULL ,
CHANGE `etat` `etat` ENUM( '0', '1' ) DEFAULT '0' NOT NULL ,
CHANGE `url` `url` VARCHAR( 200 ) NOT NULL ;
ALTER TABLE `phpl_equipes` CHANGE `id` `id` SMALLINT( 5 ) UNSIGNED NOT NULL AUTO_INCREMENT ,
CHANGE `id_champ` `id_champ` TINYINT( 3 ) UNSIGNED DEFAULT '0' NOT NULL ,
CHANGE `id_club` `id_club` SMALLINT( 5 ) UNSIGNED DEFAULT '0' NOT NULL ,
CHANGE `penalite` `penalite` TINYINT( 3 ) DEFAULT NULL;
ALTER TABLE `phpl_gr_championnats` CHANGE `id` `id` TINYINT( 3 ) UNSIGNED NOT NULL AUTO_INCREMENT ,
CHANGE `nom` `nom` VARCHAR( 255 ) NOT NULL ,
CHANGE `id_champ` `id_champ` TINYINT( 3 ) UNSIGNED DEFAULT '0' NOT NULL ,
CHANGE `activ_prono` `activ_prono` ENUM( '0', '1' ) DEFAULT '1' NOT NULL ,
CHANGE `pts_prono_exact` `pts_prono_exact` TINYINT( 3 ) UNSIGNED DEFAULT '0' NOT NULL ,
CHANGE `pts_prono_participation` `pts_prono_participation` TINYINT( 2 ) UNSIGNED DEFAULT '0' NOT NULL ,
CHANGE `id_master` `id_master` SMALLINT( 5 ) UNSIGNED DEFAULT '0',
CHANGE `tps_avant_prono` `tps_avant_prono` TINYINT( 2 ) UNSIGNED DEFAULT '2' NOT NULL;
ALTER TABLE `phpl_joueurs` CHANGE `nom` `nom` VARCHAR( 100 ) NOT NULL ,
CHANGE `prenom` `prenom` VARCHAR( 100 ) NOT NULL ,
CHANGE `date_naissance` `date_naissance` DATE DEFAULT '0000-00-00' NOT NULL ,
CHANGE `position_terrain` `position_terrain` VARCHAR( 50 ) NOT NULL ,
CHANGE `photo` `photo` VARCHAR( 250 ) NOT NULL ,
CHANGE `id` `id` SMALLINT( 5 ) NOT NULL AUTO_INCREMENT;
ALTER TABLE `phpl_journees` CHANGE `numero` `numero` TINYINT( 3 ) UNSIGNED NOT NULL ,
CHANGE `date_prevue` `date_prevue` DATE NOT NULL ,
CHANGE `id` `id` SMALLINT( 5 ) UNSIGNED NOT NULL AUTO_INCREMENT ,
CHANGE `id_champ` `id_champ` TINYINT( 3 ) UNSIGNED NOT NULL ;
ALTER TABLE `phpl_logo` CHANGE `id` `id` SMALLINT( 5 ) UNSIGNED NOT NULL AUTO_INCREMENT ,
CHANGE `id_club` `id_club` SMALLINT( 5 ) UNSIGNED DEFAULT '0' NOT NULL ,
CHANGE `url` `url` TINYTEXT NOT NULL;
ALTER TABLE `phpl_matchs` CHANGE `id` `id` MEDIUMINT( 6 ) UNSIGNED NOT NULL AUTO_INCREMENT ,
CHANGE `id_equipe_dom` `id_equipe_dom` SMALLINT( 5 ) UNSIGNED DEFAULT NULL ,
CHANGE `id_equipe_ext` `id_equipe_ext` SMALLINT( 5 ) UNSIGNED DEFAULT NULL ,
CHANGE `date_reelle` `date_reelle` DATETIME DEFAULT NULL ,
CHANGE `id_journee` `id_journee` SMALLINT( 5 ) UNSIGNED DEFAULT NULL ,
CHANGE `buts_dom` `buts_dom` SMALLINT( 4 ) UNSIGNED DEFAULT NULL ,
CHANGE `buts_ext` `buts_ext` SMALLINT( 4 ) UNSIGNED DEFAULT NULL ;
ALTER TABLE `phpl_membres` CHANGE `id` `id` SMALLINT( 5 ) UNSIGNED NOT NULL AUTO_INCREMENT ,
CHANGE `id_prono` `id_prono` VARCHAR( 19 ) NOT NULL ,
CHANGE `pseudo` `pseudo` VARCHAR( 20 ) NOT NULL ,
CHANGE `mot_de_passe` `mot_de_passe` VARCHAR( 250 ) NOT NULL ,
CHANGE `mail` `mail` VARCHAR( 40 ) NOT NULL ,
CHANGE `nom_site` `nom_site` VARCHAR( 50 ) NOT NULL ,
CHANGE `url_site` `url_site` VARCHAR( 100 ) NOT NULL ,
CHANGE `nom` `nom` VARCHAR( 50 ) NOT NULL ,
CHANGE `prenom` `prenom` VARCHAR( 50 ) NOT NULL ,
CHANGE `adresse` `adresse` VARCHAR( 100 ) NOT NULL ,
CHANGE `code_postal` `code_postal` MEDIUMINT( 5 ) DEFAULT '0' NOT NULL ,
CHANGE `ville` `ville` VARCHAR( 200 ) NOT NULL ,
CHANGE `pays` `pays` VARCHAR( 200 ) NOT NULL ,
CHANGE `date_naissance` `date_naissance` DATE DEFAULT '0000-00-00' NOT NULL ,
CHANGE `profession` `profession` VARCHAR( 200 ) NOT NULL ,
CHANGE `mobile` `mobile` VARCHAR( 14 ) NOT NULL ,
CHANGE `ip` `ip` VARCHAR( 15 ) NOT NULL ,
CHANGE `last_connect` `last_connect` VARCHAR( 10 ) NOT NULL ,
CHANGE `admin` `admin` ENUM( '0', '1' ) DEFAULT '0' NOT NULL;
ALTER TABLE `phpl_parametres` CHANGE `id_champ` `id_champ` TINYINT( 3 ) UNSIGNED DEFAULT '0' NOT NULL ,
CHANGE `pts_victoire` `pts_victoire` TINYINT( 3 ) UNSIGNED DEFAULT '0' NOT NULL ,
CHANGE `pts_nul` `pts_nul` TINYINT( 3 ) UNSIGNED DEFAULT '0' NOT NULL ,
CHANGE `pts_defaite` `pts_defaite` TINYINT( 3 ) UNSIGNED DEFAULT '0' NOT NULL ,
CHANGE `accession` `accession` TINYINT( 3 ) UNSIGNED DEFAULT '0' NOT NULL ,
CHANGE `barrage` `barrage` TINYINT( 3 ) UNSIGNED DEFAULT '0' NOT NULL ,
CHANGE `relegation` `relegation` TINYINT( 3 ) UNSIGNED DEFAULT '0' NOT NULL ,
CHANGE `id_equipe_fetiche` `id_equipe_fetiche` SMALLINT( 4 ) DEFAULT NULL ,
CHANGE `fiches_clubs` `fiches_clubs` ENUM( '0', '1' ) DEFAULT '1' NOT NULL ,
CHANGE `estimation` `estimation` ENUM( '0', '1' ) DEFAULT '1' NOT NULL;
ALTER TABLE `phpl_pronostics` CHANGE `id_membre` `id_membre` SMALLINT( 5 ) UNSIGNED DEFAULT '0' NOT NULL ,
CHANGE `id_champ` `id_champ` INT( 3 ) UNSIGNED DEFAULT '0' NOT NULL ,
CHANGE `id_match` `id_match` MEDIUMINT( 6 ) UNSIGNED DEFAULT '0' NOT NULL ,
CHANGE `pronostic` `pronostic` ENUM( '1', 'N', '2' ) DEFAULT NULL ,
CHANGE `buts_dom` `buts_dom` SMALLINT( 4 ) UNSIGNED DEFAULT NULL ,
CHANGE `buts_ext` `buts_ext` SMALLINT( 4 ) UNSIGNED DEFAULT NULL ,
CHANGE `points` `points` TINYINT( 2 ) UNSIGNED DEFAULT '0' NOT NULL ,
CHANGE `participation` `participation` ENUM( '0', '1' ) DEFAULT '0' NOT NULL ;
ALTER TABLE `phpl_rens` CHANGE `id` `id` SMALLINT( 5 ) UNSIGNED NOT NULL AUTO_INCREMENT ,
CHANGE `nom` `nom` VARCHAR( 50 ) NOT NULL ,
CHANGE `id_classe` `id_classe` TINYINT( 3 ) UNSIGNED DEFAULT '0' NOT NULL ,
CHANGE `rang` `rang` TINYINT( 3 ) UNSIGNED DEFAULT '0' NOT NULL ,
CHANGE `url` `url` VARCHAR( 200 ) NOT NULL;
ALTER TABLE `phpl_saisons` CHANGE `id` `id` TINYINT( 3 ) UNSIGNED NOT NULL AUTO_INCREMENT ,
CHANGE `annee` `annee` YEAR( 4 ) DEFAULT '0000' NOT NULL;
ALTER TABLE `phpl_buteurs` DROP `id_joueur`;
ALTER TABLE `phpl_joueurs` DROP `id_club`;
DROP TABLE `phpl_logo`";

$sql_maj_2="ALTER TABLE `phpl_clubs` DROP INDEX `id`;
ALTER TABLE `phpl_clubs` DROP INDEX `nom`;
ALTER TABLE `phpl_championnats` ADD PRIMARY KEY ( `id` );
ALTER TABLE `phpl_championnats` DROP INDEX `id`;
ALTER TABLE `phpl_championnats` DROP INDEX `id_saison`;
ALTER TABLE `phpl_championnats` DROP INDEX `id_division`;
ALTER TABLE `phpl_classe` ADD PRIMARY KEY ( `id` );
ALTER TABLE `phpl_classe` DROP INDEX `id`;
ALTER TABLE `phpl_clmnt` ADD INDEX ( `ID_CHAMP` );
ALTER TABLE `phpl_clmnt_graph` DROP INDEX `nom`;
ALTER TABLE `phpl_clmnt_graph` ADD INDEX ( `id_equipe` );
ALTER TABLE `phpl_clmnt_pronos` DROP INDEX `pseudo`;
ALTER TABLE `phpl_clmnt_pronos` ADD INDEX ( `id_champ` );
ALTER TABLE `phpl_divisions` DROP PRIMARY KEY;
ALTER TABLE `phpl_divisions` DROP INDEX `nom_2`;
ALTER TABLE `phpl_divisions` ADD PRIMARY KEY ( `id` );
ALTER TABLE `phpl_divisions` DROP INDEX `id`;
ALTER TABLE `phpl_equipes` DROP INDEX `id`;
ALTER TABLE `phpl_journees` DROP INDEX `id`;
ALTER TABLE `phpl_journees` DROP INDEX `numero`;
ALTER TABLE `phpl_logo` DROP PRIMARY KEY;
ALTER TABLE `phpl_logo` ADD PRIMARY KEY ( `id_club` );
ALTER TABLE `phpl_logo` DROP INDEX `id_club`;
ALTER TABLE `phpl_parametres` ADD PRIMARY KEY ( `id_champ` );
ALTER TABLE `phpl_parametres` DROP INDEX `id_champ`;
ALTER TABLE `phpl_pronostics` ADD INDEX ( `id_match` );
ALTER TABLE `phpl_pronostics` DROP INDEX `id_champ`;
ALTER TABLE `phpl_pronostics` ADD INDEX ( `id_membre` ); 
ALTER TABLE `phpl_rens` ADD PRIMARY KEY ( `id` );
ALTER TABLE `phpl_rens` DROP INDEX `id`;
ALTER TABLE `phpl_saisons` DROP INDEX `id_2`;
ALTER TABLE `phpl_saisons` DROP PRIMARY KEY;
ALTER TABLE `phpl_saisons` ADD PRIMARY KEY ( `id` );
DROP TABLE `phpl_tapis_vert`;
ALTER TABLE `phpl_buteurs` ADD `id_effectif` SMALLINT( 5 ) UNSIGNED NOT NULL AFTER `id_match`;
ALTER TABLE `phpl_clubs` ADD `url_logo` TINYTEXT NOT NULL";

$sql_clmnt_cache = "CREATE TABLE `phpl_clmnt_cache` (
  `NOM` varchar(255) default NULL,
  `POINTS` smallint(4) unsigned default NULL,
  `JOUES` tinyint(3) unsigned default NULL,
  `G` tinyint(3) unsigned default NULL,
  `N` tinyint(3) unsigned default NULL,
  `P` tinyint(3) unsigned default NULL,
  `BUTSPOUR` smallint(4) unsigned default NULL,
  `BUTSCONTRE` smallint(4) unsigned default NULL,
  `DIFF` smallint(4) default NULL,
  `PEN` tinyint(2) default NULL,
  `DOMPOINTS` smallint(4) unsigned default NULL,
  `DOMJOUES` tinyint(3) unsigned default NULL,
  `DOMG` tinyint(3) unsigned default NULL,
  `DOMN` tinyint(3) unsigned default NULL,
  `DOMP` tinyint(3) unsigned default NULL,
  `DOMBUTSPOUR` smallint(4) unsigned default NULL,
  `DOMBUTSCONTRE` smallint(4) unsigned default NULL,
  `DOMDIFF` smallint(4) default NULL,
  `EXTPOINTS` smallint(4) unsigned default NULL,
  `EXTJOUES` tinyint(3) unsigned default NULL,
  `EXTG` tinyint(3) unsigned default NULL,
  `EXTN` tinyint(3) unsigned default NULL,
  `EXTP` tinyint(3) unsigned default NULL,
  `EXTBUTSPOUR` smallint(4) unsigned default NULL,
  `EXTBUTSCONTRE` smallint(4) unsigned default NULL,
  `EXTDIFF` tinyint(4) default NULL,
  `ID_EQUIPE` smallint(5) unsigned NOT NULL default '0',
  `ID_CHAMP` tinyint(3) unsigned NOT NULL default '0',
  KEY `ID_CHAMP` (`ID_CHAMP`)
);
CREATE TABLE `phpl_effectif` (
`id` SMALLINT( 5 ) UNSIGNED NOT NULL AUTO_INCREMENT ,
`id_joueur` SMALLINT( 5 ) UNSIGNED NOT NULL ,
`id_equipe` SMALLINT( 5 ) UNSIGNED NOT NULL ,
PRIMARY KEY ( `id` )
)";

$requete="SELECT id_equipe, pts FROM phpl_tapis_vert";
$resultat=mysql_query($requete) or die (mysql_error());

while($row = mysql_fetch_array($resultat))
{
  $pts=$row["pts"];
  $id_equipe=$row["id_equipe"];
  $requete2="UPDATE phpl_equipes SET penalite = '$pts'  WHERE id = '$id_equipe'";
  $resultat2=mysql_query($requete2) or die (mysql_error());
}



if ($res_clmnt_cache = send_sql($sql_clmnt_cache,SETUP_TABLE_3." classement cache") and $res_maj = send_sql($sql_maj_2,SETUP_TABLE_3." Mise à jour"))
   { echo "<font color=\"#009900\" size=\"2\" face=\"Verdana\">".SETUP_TABLE." phpl_clmnt_cache ".SETUP_TABLE_2."</font>\n<br />"; }

$requete="SELECT id_club, url FROM phpl_logo";
$resultat=mysql_query($requete) or die (mysql_error());

while($row = mysql_fetch_array($resultat))
{
  $url_logo=$row["url"];
  $id_club=$row["id_club"];
  $requete2="UPDATE phpl_clubs SET url_logo = '$url_logo'  WHERE id = '$id_club'";
  $resultat2=mysql_query($requete2) or die (mysql_error());

}

$requete="SELECT DISTINCT phpl_joueurs.id, phpl_equipes.id
          FROM phpl_joueurs, phpl_equipes, phpl_buteurs, phpl_matchs, phpl_clubs
          WHERE phpl_equipes.id_club=phpl_clubs.id
          AND (
           phpl_matchs.id_equipe_dom = phpl_equipes.id
           OR phpl_matchs.id_equipe_ext = phpl_equipes.id
              )
          AND phpl_matchs.id=phpl_buteurs.id_match
          AND phpl_joueurs.id_club=phpl_equipes.id_club
          AND phpl_buteurs.id_joueur=phpl_joueurs.id";

$resultat=mysql_query($requete) or die (mysql_error());

while($row = mysql_fetch_array($resultat))
{
 mysql_query ("INSERT INTO phpl_effectif (id_joueur, id_equipe) values ('$row[0]','$row[1]')") or die ("probleme " .mysql_error());
}

$requete="SELECT phpl_effectif.id, phpl_buteurs.id_match, phpl_joueurs.id
          FROM phpl_joueurs, phpl_equipes, phpl_buteurs, phpl_matchs, phpl_clubs, phpl_effectif
          WHERE phpl_equipes.id_club=phpl_clubs.id
          AND (
           phpl_matchs.id_equipe_dom = phpl_equipes.id
           OR phpl_matchs.id_equipe_ext = phpl_equipes.id
              )
          AND phpl_matchs.id=phpl_buteurs.id_match
          AND phpl_joueurs.id_club=phpl_equipes.id_club
          AND phpl_buteurs.id_joueur=phpl_joueurs.id
          AND phpl_effectif.id_joueur=phpl_joueurs.id
          AND phpl_effectif.id_equipe=phpl_equipes.id";
          
$resultat=mysql_query($requete) or die (mysql_error());

while($row = mysql_fetch_array($resultat))
{
 mysql_query ("UPDATE phpl_buteurs SET id_effectif='$row[0]' WHERE id_match='$row[1]' and id_joueur='$row[2]'") or die ("probleme " .mysql_error());
}


$requete="SELECT DISTINCT phpl_joueurs.nom, phpl_joueurs.prenom, phpl_buteurs.id, phpl_journees.numero, phpl_matchs.date_reelle, phpl_joueurs.id
FROM phpl_joueurs, phpl_buteurs, phpl_matchs, phpl_equipes, phpl_journees
WHERE phpl_matchs.id = phpl_buteurs.id_match
AND phpl_buteurs.id_joueur = phpl_joueurs.id
AND phpl_buteurs.id_effectif =0
AND (
phpl_matchs.id_equipe_dom = phpl_equipes.id
OR phpl_matchs.id_equipe_ext = phpl_equipes.id
    )
AND phpl_journees.id = phpl_matchs.id_journee";
          
$resultat=mysql_query($requete) or die (mysql_error());

if (!mysql_num_rows( $resultat )=='0')
{
echo "<form method=\"post\" action=\"\">";
$i=0;
while($row = mysql_fetch_array($resultat))
{  
  echo "Incohérence : $row[0] $row[1] a marqué sous le maillot de : ";
  $requete1="SELECT phpl_clubs.nom, phpl_equipes.id, phpl_matchs.id
             FROM phpl_joueurs, phpl_equipes, phpl_buteurs, phpl_matchs, phpl_clubs, phpl_journees
             WHERE phpl_equipes.id_club = phpl_clubs.id
             AND (
             phpl_matchs.id_equipe_dom = phpl_equipes.id
             OR phpl_matchs.id_equipe_ext = phpl_equipes.id
                 )
             AND phpl_matchs.id = phpl_buteurs.id_match
             AND phpl_buteurs.id_joueur = phpl_joueurs.id
             AND phpl_buteurs.id_effectif =0
             AND phpl_journees.id = phpl_matchs.id_journee
             AND phpl_buteurs.id='$row[2]'";
             
 $resultat1=mysql_query($requete1) or die (mysql_error());
 
 while($row1 = mysql_fetch_array($resultat1))
 {
   echo "$row1[0]<input type=\"radio\" value=\"$row1[1]\" name=\"id_equipes-".$i."\">";
   echo "<input type=\"hidden\" value=\"$row[5]\" name=\"id_joueur-".$i."\">";
   echo "<input type=\"hidden\" value=\"$row1[2]\" name=\"id_match-".$i."\"><br>";
   
 }
 $i++;

}
echo "<input type=\"hidden\" value=\"1\" name=\"buteurs\">";
echo "<input type=\"hidden\" value=\"$i\" name=\"nb_joueur\">";
echo "<input type=\"submit\" value=".ENVOI."></form>";

}

if ($res_maj = send_sql($sql_maj_1,SETUP_TABLE_3." mise à jour"))
   { echo "<font color=\"#009900\" size=\"2\" face=\"Verdana\">".SETUP_TABLE." Mises à jour ".SETUP_TABLE_2."</font>\n<br />"; }




$installation_terminee=1;



}
elseif ($action2=="majv081" and $action=="2" and (!isset($_POST['repertoire_script']) or $_POST['repertoire_script']=="" or $test=="erreur"))
 {echo SETUP_REMPLIR_CHAMP;}


if ($action2=="install" and $action=="2" and isset($_POST['nom_site']) and !$_POST['nom_site']=="" and isset($_POST['url_site']) and !$_POST['url_site']=="" and isset($_POST['repertoire_script']) and !$_POST['repertoire_script']=="" and isset($_POST['pseudo']) and !$_POST['pseudo']=="" and isset($_POST['mot_de_pass']) and !$_POST['mot_de_pass']=="" and isset($_POST['mail']) and !$_POST['mail']=="" and !$test=="erreur")
{
$nom_site = $_POST['nom_site'];
$url_site = $_POST['url_site'];
$repertoire_script = $_POST['repertoire_script'];
$pseudo = $_POST['pseudo'];
$mot_de_pass = $_POST['mot_de_pass'];
$mail = $_POST['mail'];

$taille = 19;
$lettres = "abcdefghijklmnopqrstuvwxyz0123456789";
srand(time());
    for ($i=0;$i<$taille;$i++)
    {
      if ($i=="0") {$id_prono = substr($lettres,(rand()%(strlen($lettres))),1);}
      else {$id_prono.= substr($lettres,(rand()%(strlen($lettres))),1);}
    }

$mdp=md5($_POST['mot_de_pass']);

// On crée les tables


$sql_admin = "
CREATE TABLE `phpl_buteurs` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `id_match` int(10) unsigned default NULL,
  `id_effectif` smallint(5) unsigned NOT NULL default '0',
  `buts` tinyint(4) default NULL,
  KEY `id` (`id`)
);
CREATE TABLE `phpl_championnats` (
  `id` tinyint(3) unsigned NOT NULL auto_increment,
  `id_division` tinyint(3) unsigned NOT NULL default '0',
  `id_saison` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ;
CREATE TABLE `phpl_classe` (
  `id` tinyint(3) unsigned NOT NULL auto_increment,
  `nom` varchar(255) NOT NULL default '',
  `rang` tinyint(3) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ;
CREATE TABLE `phpl_clmnt` (
  `NOM` varchar(255) default NULL,
  `POINTS` smallint(4) unsigned default NULL,
  `JOUES` tinyint(3) unsigned default NULL,
  `G` tinyint(3) unsigned default NULL,
  `N` tinyint(3) unsigned default NULL,
  `P` tinyint(3) unsigned default NULL,
  `BUTSPOUR` smallint(4) unsigned default NULL,
  `BUTSCONTRE` smallint(4) unsigned default NULL,
  `DIFF` smallint(4) default NULL,
  `PEN` tinyint(2) default NULL,
  `DOMPOINTS` smallint(4) unsigned default NULL,
  `DOMJOUES` tinyint(3) unsigned default NULL,
  `DOMG` tinyint(3) unsigned default NULL,
  `DOMN` tinyint(3) unsigned default NULL,
  `DOMP` tinyint(3) unsigned default NULL,
  `DOMBUTSPOUR` smallint(4) unsigned default NULL,
  `DOMBUTSCONTRE` smallint(4) unsigned default NULL,
  `DOMDIFF` smallint(4) default NULL,
  `EXTPOINTS` smallint(4) unsigned default NULL,
  `EXTJOUES` tinyint(3) unsigned default NULL,
  `EXTG` tinyint(3) unsigned default NULL,
  `EXTN` tinyint(3) unsigned default NULL,
  `EXTP` tinyint(3) unsigned default NULL,
  `EXTBUTSPOUR` smallint(4) unsigned default NULL,
  `EXTBUTSCONTRE` smallint(4) unsigned default NULL,
  `EXTDIFF` tinyint(4) default NULL,
  `ID_EQUIPE` smallint(5) unsigned NOT NULL default '0',
  `ID_CHAMP` tinyint(3) unsigned NOT NULL default '0',
  KEY `ID_CHAMP` (`ID_CHAMP`)
) ;
CREATE TABLE `phpl_clmnt_cache` (
  `NOM` varchar(255) default NULL,
  `POINTS` smallint(4) unsigned default NULL,
  `JOUES` tinyint(3) unsigned default NULL,
  `G` tinyint(3) unsigned default NULL,
  `N` tinyint(3) unsigned default NULL,
  `P` tinyint(3) unsigned default NULL,
  `BUTSPOUR` smallint(4) unsigned default NULL,
  `BUTSCONTRE` smallint(4) unsigned default NULL,
  `DIFF` smallint(4) default NULL,
  `PEN` tinyint(2) default NULL,
  `DOMPOINTS` smallint(4) unsigned default NULL,
  `DOMJOUES` tinyint(3) unsigned default NULL,
  `DOMG` tinyint(3) unsigned default NULL,
  `DOMN` tinyint(3) unsigned default NULL,
  `DOMP` tinyint(3) unsigned default NULL,
  `DOMBUTSPOUR` smallint(4) unsigned default NULL,
  `DOMBUTSCONTRE` smallint(4) unsigned default NULL,
  `DOMDIFF` smallint(4) default NULL,
  `EXTPOINTS` smallint(4) unsigned default NULL,
  `EXTJOUES` tinyint(3) unsigned default NULL,
  `EXTG` tinyint(3) unsigned default NULL,
  `EXTN` tinyint(3) unsigned default NULL,
  `EXTP` tinyint(3) unsigned default NULL,
  `EXTBUTSPOUR` smallint(4) unsigned default NULL,
  `EXTBUTSCONTRE` smallint(4) unsigned default NULL,
  `EXTDIFF` tinyint(4) default NULL,
  `ID_EQUIPE` smallint(5) unsigned NOT NULL default '0',
  `ID_CHAMP` tinyint(3) unsigned NOT NULL default '0',
  KEY `ID_CHAMP` (`ID_CHAMP`)
) ;
CREATE TABLE `phpl_clmnt_graph` (
  `id_equipe` smallint(5) unsigned NOT NULL default '0',
  `fin` tinyint(3) unsigned NOT NULL default '0',
  `classement` tinyint(3) unsigned NOT NULL default '0',
  KEY `id_equipe` (`id_equipe`)
) ;
CREATE TABLE `phpl_clmnt_pronos` (
  `id_champ` tinyint(3) unsigned NOT NULL default '0',
  `id_membre` smallint(5) unsigned NOT NULL default '0',
  `pseudo` varchar(255) NOT NULL default '',
  `points` smallint(5) unsigned NOT NULL default '0',
  `participation` smallint(3) unsigned NOT NULL default '0',
  `type` enum('hebdo','mensuel_30_jours','mensuel_en_cours','general') NOT NULL default 'hebdo',
  KEY `id_membre` (`id_membre`),
  KEY `id_champ` (`id_champ`)
) ;
CREATE TABLE `phpl_clubs` (
  `id` smallint(5) unsigned NOT NULL auto_increment,
  `nom` varchar(255) NOT NULL default '',
  `url_logo` tinytext NOT NULL,
  PRIMARY KEY  (`id`)
)  ;

CREATE TABLE `phpl_divisions` (
  `id` tinyint(3) unsigned NOT NULL auto_increment,
  `nom` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ;
CREATE TABLE `phpl_donnee` (
  `id` smallint(5) unsigned NOT NULL auto_increment,
  `nom` text NOT NULL,
  `id_clubs` smallint(5) unsigned NOT NULL default '0',
  `id_rens` smallint(5) unsigned NOT NULL default '0',
  `etat` enum('0','1') NOT NULL default '0',
  `url` varchar(200) NOT NULL default '',
  KEY `id` (`id`),
  KEY `id_rens` (`id_rens`),
  KEY `id_clubs` (`id_clubs`),
  KEY `etat` (`etat`)
) ;
CREATE TABLE `phpl_effectif` (
  `id` smallint(5) unsigned NOT NULL auto_increment,
  `id_joueur` smallint(5) unsigned NOT NULL default '0',
  `id_equipe` smallint(5) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ;
CREATE TABLE `phpl_equipes` (
  `id` smallint(5) unsigned NOT NULL auto_increment,
  `id_champ` tinyint(3) unsigned NOT NULL default '0',
  `id_club` smallint(5) unsigned NOT NULL default '0',
  `penalite` tinyint(3) default NULL,
  PRIMARY KEY  (`id`),
  KEY `id_champ` (`id_champ`),
  KEY `id_club` (`id_club`)
) ;
CREATE TABLE `phpl_gr_championnats` (
  `id` tinyint(3) unsigned NOT NULL auto_increment,
  `nom` varchar(255) NOT NULL default '',
  `id_champ` tinyint(3) unsigned NOT NULL default '0',
  `activ_prono` enum('0','1') NOT NULL default '1',
  `pts_prono_exact` tinyint(3) unsigned NOT NULL default '0',
  `pts_prono_participation` tinyint(2) unsigned NOT NULL default '0',
  `id_master` smallint(5) unsigned default '0',
  `tps_avant_prono` tinyint(2) unsigned NOT NULL default '2',
  KEY `id` (`id`)
) ;
CREATE TABLE `phpl_joueurs` (
  `nom` varchar(100) NOT NULL default '',
  `prenom` varchar(100) NOT NULL default '',
  `date_naissance` date NOT NULL default '0000-00-00',
  `position_terrain` varchar(50) NOT NULL default '',
  `photo` varchar(250) NOT NULL default '',
  `id` smallint(5) NOT NULL auto_increment,
  KEY `id` (`id`)
) ;
CREATE TABLE `phpl_journees` (
  `numero` tinyint(3) unsigned NOT NULL default '0',
  `date_prevue` date NOT NULL default '0000-00-00',
  `id` smallint(5) unsigned NOT NULL auto_increment,
  `id_champ` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `id_champ` (`id_champ`)
) ;
CREATE TABLE `phpl_matchs` (
  `id` mediumint(6) unsigned NOT NULL auto_increment,
  `id_equipe_dom` smallint(5) unsigned default NULL,
  `id_equipe_ext` smallint(5) unsigned default NULL,
  `date_reelle` datetime default NULL,
  `id_journee` smallint(5) unsigned default NULL,
  `buts_dom` smallint(4) unsigned default NULL,
  `buts_ext` smallint(4) unsigned default NULL,
  PRIMARY KEY  (`id`),
  KEY `id_equipe_dom` (`id_equipe_dom`),
  KEY `id_equipe_ext` (`id_equipe_ext`),
  KEY `buts_dom` (`buts_dom`),
  KEY `buts_ext` (`buts_ext`),
  KEY `id_journee` (`id_journee`)
) ;
CREATE TABLE `phpl_membres` (
  `id` smallint(5) unsigned NOT NULL auto_increment,
  `id_prono` varchar(19) NOT NULL default '',
  `pseudo` varchar(20) NOT NULL default '',
  `mot_de_passe` varchar(250) NOT NULL default '',
  `mail` varchar(40) NOT NULL default '',
  `nom_site` varchar(50) NOT NULL default '',
  `url_site` varchar(100) NOT NULL default '',
  `nom` varchar(50) NOT NULL default '',
  `prenom` varchar(50) NOT NULL default '',
  `adresse` varchar(100) NOT NULL default '',
  `code_postal` mediumint(5) NOT NULL default '0',
  `ville` varchar(200) NOT NULL default '',
  `pays` varchar(200) NOT NULL default '',
  `date_naissance` date NOT NULL default '0000-00-00',
  `profession` varchar(200) NOT NULL default '',
  `mobile` varchar(14) NOT NULL default '',
  `ip` varchar(15) NOT NULL default '',
  `last_connect` varchar(10) NOT NULL default '',
  `admin` enum('0','1') NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ;
CREATE TABLE `phpl_parametres` (
  `id_champ` tinyint(3) unsigned NOT NULL default '0',
  `pts_victoire` tinyint(3) unsigned NOT NULL default '0',
  `pts_nul` tinyint(3) unsigned NOT NULL default '0',
  `pts_defaite` tinyint(3) unsigned NOT NULL default '0',
  `accession` tinyint(3) unsigned NOT NULL default '0',
  `barrage` tinyint(3) unsigned NOT NULL default '0',
  `relegation` tinyint(3) unsigned NOT NULL default '0',
  `id_equipe_fetiche` smallint(4) default NULL,
  `fiches_clubs` enum('0','1') NOT NULL default '1',
  `estimation` enum('0','1') NOT NULL default '1',
  PRIMARY KEY  (`id_champ`)
) ;
CREATE TABLE `phpl_pronostics` (
  `id_membre` smallint(5) unsigned NOT NULL default '0',
  `id_champ` int(3) unsigned NOT NULL default '0',
  `id_match` mediumint(6) unsigned NOT NULL default '0',
  `pronostic` enum('1','N','2') default NULL,
  `buts_dom` smallint(4) unsigned default NULL,
  `buts_ext` smallint(4) unsigned default NULL,
  `points` tinyint(2) unsigned NOT NULL default '0',
  `participation` enum('0','1') NOT NULL default '0',
  KEY `id_match` (`id_match`),
  KEY `id_membre` (`id_membre`)
) ;
CREATE TABLE `phpl_rens` (
  `id` smallint(5) unsigned NOT NULL auto_increment,
  `nom` varchar(50) NOT NULL default '',
  `id_classe` tinyint(3) unsigned NOT NULL default '0',
  `rang` tinyint(3) unsigned NOT NULL default '0',
  `url` varchar(200) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ;
CREATE TABLE `phpl_saisons` (
  `id` tinyint(3) unsigned NOT NULL auto_increment,
  `annee` year(4) NOT NULL default '0000',
  PRIMARY KEY  (`id`),
  KEY `id` (`id`)
) ;
INSERT INTO `phpl_membres` (`id_prono` , `pseudo` , `mot_de_passe` , `mail` , `nom_site` , `url_site`, `admin` )
                    VALUES ('$id_prono', '$pseudo', '$mdp', '$mail', '$nom_site', '$url_site','1')";

if ($res_admin = send_sql($sql_admin,SETUP_TABLE_4))
   { echo "<font color=\"#009900\" size=\"2\" face=\"Verdana\">".SETUP_TABLE_5."</font>\n<br />";}


$installation_terminee = 1;


}
elseif ($action2=="install" and $action=="2" and (!isset($_POST['nom_site']) or $_POST['nom_site']=="" or !isset($_POST['url_site']) or $_POST['url_site']=="" or !isset($_POST['repertoire_script']) or $_POST['repertoire_script']=="" or !isset($_POST['pseudo']) or $_POST['pseudo']=="" or !isset($_POST['mot_de_pass']) or $_POST['mot_de_pass']=="" or !isset($_POST['mail']) or $_POST['mail']=="" or $test=="erreur"))
 {echo SETUP_REMPLIR_CHAMP;}


if (!isset($installation_terminee)) {$installation_terminee=0;}

if ($installation_terminee=="1")
{ 
$to="phpleague@univert.org<phpleague@univert.org>";

$sujet="Nouvel utilisateur";

$message="<html><head>
<title>PhpLeague</title>
</head>
<body>
<p align=\"center\"><u><font face=\"Verdana\" size=\"3\" color=\"#006A36\">Nouvel
utilisateur</font></u></p>
<p><font size=\"2\" face=\"Verdana\">Url : <a href=\"$url_site\">$url_site</a></font></p>
<p><font size=\"2\" face=\"Verdana\">Mail : $mail</font></p>
<p><font size=\"2\" face=\"Verdana\">Site : $nom_site</font></p>
</body>
</html>";

  $from="Content-Type: text/html; charset=\"iso-8859-1\"\nFrom: $mail\n";

  $email=@mail($to,$sujet,$message,$from);

    echo "<font size=\"2\" face=\"Verdana\">".SETUP_CONFIRMATION."<br /></font>";
    echo "<font color=\"#FF0000\" size=\"2\" face=\"Verdana\">".SETUP_FIN."</font>";
}
}
}
?>
</td>
    </tr>
  </table>
