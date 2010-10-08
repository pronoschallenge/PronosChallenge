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

$action2 = isset($_POST['action2']) ? $_POST['action2'] : NULL;
$action3 = isset($_POST['action3']) ? $_POST['action3'] : NULL;
$confirm = isset($_GET['confirm']) ? $_GET['confirm'] : NULL;
$champ = isset($_GET['champ']) ? $_GET['champ'] : NULL;
$saison = isset($_POST['saison']) ? $_POST['saison'] : NULL;
$division = isset($_POST['division']) ? $_POST['division'] : NULL;
$nom_club = isset($_POST['nom_club']) ? $_POST['nom_club'] : NULL;
$club = isset($_POST['club']) ? $_POST['club'] : NULL;
$nb_equipes = isset($_POST['nb_equipes']) ? $_POST['nb_equipes'] : NULL;
$date = isset($_POST['date']) ? $_POST['date'] : NULL;
$heure = isset($_POST['heure']) ? $_POST['heure'] : NULL;
$minute = isset($_POST['minute']) ? $_POST['minute'] : NULL;
$boucle = isset($_POST['boucle']) ? $_POST['boucle'] : NULL;
$coherence = isset($_GET['coherence']) ? $_GET['coherence'] : NULL;
$miroir = isset($_POST['miroir']) ? $_POST['miroir'] : NULL;
$id_domicile = isset($_POST['id_domicile']) ? $_POST['id_domicile'] : NULL;
$id_exterieur = isset($_POST['id_exterieur']) ? $_POST['id_exterieur'] : NULL;

//paramètres
$pts_victoire = isset($_POST['pts_victoire']) ? $_POST['pts_victoire'] : NULL;
$pts_nul = isset($_POST['pts_nul']) ? $_POST['pts_nul'] : NULL;
$pts_defaite = isset($_POST['pts_defaite']) ? $_POST['pts_defaite'] : NULL;
$accession = isset($_POST['accession']) ? $_POST['accession'] : NULL;
$relegation = isset($_POST['relegation']) ? $_POST['relegation'] : NULL;
$id_equipe_fetiche = isset($_POST['id_equipe_fetiche']) ? $_POST['id_equipe_fetiche'] : NULL;
$estimation = isset($_POST['estimation']) ? $_POST['estimation'] : NULL;
$barrage = isset($_POST['barrage']) ? $_POST['barrage'] : NULL;
$fiches_clubs = isset($_POST['fiches_clubs']) ? $_POST['fiches_clubs'] : NULL;
$malus = isset($_POST['malus']) ? $_POST['malus'] : NULL;

$id_equipe = isset($_POST['id_equipe']) ? $_POST['id_equipe'] : NULL;
$id_journee = isset($_REQUEST['id_journee']) ? $_REQUEST['id_journee'] : NULL;


//Resultats
$date_reelle = isset($_POST['date_reelle']) ? $_POST['date_reelle'] : NULL;
$butd = isset($_POST['butd']) ? $_POST['butd'] : NULL;
$butv = isset($_POST['butv']) ? $_POST['butv'] : NULL;
$matchs_id = isset($_REQUEST['matchs_id']) ? $_REQUEST['matchs_id'] : NULL;
$numero = isset($_REQUEST['numero']) ? $_REQUEST['numero'] : NULL;


// Buteurs
$nb_matchs = isset($_POST['nb_matchs']) ? $_POST['nb_matchs'] : NULL;
$date_naissance = isset($_POST['date_naissance']) ? $_POST['date_naissance'] : NULL;
$nom = isset($_POST['nom']) ? $_POST['nom'] : NULL;
$prenom = isset($_POST['prenom']) ? $_POST['prenom'] : NULL;
$photo = isset($_POST['photo']) ? $_POST['photo'] : NULL;
$position = isset($_POST['position']) ? $_POST['position'] : NULL;
$data = isset($_POST['data']) ? $_POST['data'] : NULL;
$nbdom = isset($_POST['nbdom']) ? $_POST['nbdom'] : NULL;
$nbext = isset($_POST['nbext']) ? $_POST['nbext'] : NULL;
$joueursDom = isset($_POST['joueursDom']) ? $_POST['joueursDom'] : NULL;
$joueursExt = isset($_POST['joueursExt']) ? $_POST['joueursExt'] : NULL;
$nbdom = isset($_POST['nbdom']) ? $_POST['nbdom'] : NULL;
$action4 = isset($_GET['action4']) ? $_GET['action4'] : NULL;
$action_transfert = isset($_POST['action_transfert']) ? $_POST['action_transfert'] : NULL;
$id_buteur_supp = isset($_GET['id_buteur_supp']) ? $_GET['id_buteur_supp'] : NULL;
$joueurs_id = isset($_POST['joueurs_id']) ? $_POST['joueurs_id'] : NULL;
$equipe_id = isset($_POST['equipe_id']) ? $_POST['equipe_id'] : NULL;

// joueurs
$id_joueur = isset($_POST['id_joueur']) ? $_POST['id_joueur'] : NULL;


if ($action2=="creer" and $saison and $action=="creer")
{
  mysql_query ("INSERT INTO phpl_saisons (annee) values ('$saison')") or die ("probleme " .mysql_error());
}

if ($action2=="creer" and $division and $action=="creer")
{
  mysql_query ("INSERT INTO phpl_divisions (nom) values ('$division')") or die ("probleme " .mysql_error());
}

if ($action3=="creer" and $action=="creer")
{
  mysql_query ("INSERT INTO phpl_championnats (id_division, id_saison) values ('$division','$saison')") or die ("probleme " .mysql_error());
}


if ($action2=="supp" and $saison and $action=="creer")
{
  mysql_query ("DELETE FROM phpl_saisons WHERE id ='$saison'") or die ("probleme " .mysql_error());
}

if ($action2=="supp" and $division and $action=="creer")
{
  mysql_query ("DELETE FROM phpl_divisions WHERE id ='$division'") or die ("probleme " .mysql_error());
}

if ($confirm=="ok" and $champ and $action=="supp")
{
  $requete="SELECT phpl_equipes.id FROM phpl_equipes, phpl_championnats WHERE id_champ='$champ'";
  $resultats=mysql_query($requete);
     while($row = mysql_fetch_array($resultats))
      {
        mysql_query ("DELETE FROM phpl_matchs where id_equipe_dom='$row[0]' ") or die ("probleme" .mysql_error());
        mysql_query ("DELETE FROM phpl_matchs where id_equipe_ext='$row[0]' ") or die ("probleme" .mysql_error());
      }
    mysql_query("DELETE FROM phpl_championnats where id='$champ' ") or die ("probleme " .mysql_error());
    mysql_query("DELETE FROM phpl_journees WHERE  id_champ = '$champ'")or die ("probleme " .mysql_error());
    mysql_query("DELETE FROM phpl_equipes WHERE id_champ ='$champ' ")or die ("probleme " .mysql_error());
    mysql_query("DELETE FROM phpl_parametres WHERE id_champ ='$champ' ")or die ("probleme " .mysql_error());

}
?>

<br /><font class=phpl><? echo LEAGUE; ?></font><br /><br />



<table class=phpl width="90%">
            <tr>
              <td class=phpl2><? echo MENU_ID; ?></td>
              <td class=phpl2><? echo MENU_NOM; ?></td>
              <td class=phpl2><? echo ADMIN_JOURNEES_MSG3; ?></td>
              <td class=phpl2></td>
            </tr>
            <? affich_championnats ($champ, $action); ?>
            <tr>
              <td class=phpl5 align="right" colspan="4"><a href="?page=championnat&action=creer">> <? echo ADMIN_CHAMP_CREER;?></a></td>
            </tr>

          </table>
<br /><br />
<?

if ($action=="supp" and $champ){ include ("supp_champ.php"); }

if ($action=="creer") {include("creer_champ.php");}

if ($action=="resultats") {include("resultats.php");}

if ($action=="equipes" and $champ){include("equipes.php");}

if ($action=="dates" and $champ){include("dates.php");}

if ($action=="matchs" and $champ){include("matchs.php");}
                                                
if ($action=="parametres" and $champ){include("parametres.php");}

if ($action=="joueurs" and $champ){include("joueurs.php");}

if ($action=="buteurs" and $champ){include("buteurs.php");}

if ($action=="generer" and $champ){include("generer.php");}





?>
