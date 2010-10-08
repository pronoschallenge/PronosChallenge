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
?>
<html>
<head>
<meta http-equiv="Content-Language" content="fr">
<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
<meta name="GENERATOR" content="Microsoft FrontPage 4.0">
<meta name="ProgId" content="FrontPage.Editor.Document">
<title>Phpleague - Administration</title>
<link rel="stylesheet" type="text/css" href="style.css">

</head>

<body topmargin="0" leftmargin="0" class="phpl">

<?
require ("../config.php") ;
require ("fonctions.php");
ouverture ();
?>

<div align="center">
  <center>
  <table cellspacing="0" width="100%" height="100%" cellpadding="0">
    <tr>
      <td colspan="1" align="center" height="100"  bgcolor="#C0C0C0"></td>
      <td colspan="1" background="haut.gif" valign="middle" align="center" style="border-bottom-style: dashed;border-bottom-width : 1px; border-color:#3b487f"><font class="phpl">PhpLeague - Administration</font></td>
      <td bgcolor="#C0C0C0" style="border-bottom-style: dashed;border-bottom-width : 1px; border-color:#3b487f"></td>
    </tr>
    <tr>
      <td class="phpl3" width="12%" valign="top" style="border-right-style: dashed;border-right-width : 1px; border-color:#3b487f">
      <a href="?page=championnat"><? echo LEAGUE; ?></a><br /><br />
      <a href="?page=groupes_championnats"><? echo "Groupes championnats"; ?></a><br /><br />
      <a href="?page=fiches_clubs"><? echo MENU_FICHES_CLUBS; ?></a><br /><br />
      <a href="?page=mini_classement"><? echo ADMIN_MINI_1; ?></a><br /><br />
      <a href="?page=membres"><? echo MENU_MEMBRES; ?></a><br /><br />
      <a href="?page=credits"><? echo MENU_CREDITS; ?></a><br /><br />
      <a href="logout.php"><? echo "Logout"; ?></a><br />
      </td>
      <td valign="top" colspan="2"><div align="center">

     <?
     if (isset($_COOKIE['user'])) {$user_pseudo=$_COOKIE['user'];} else {$user_pseudo='';}
     if (isset($_COOKIE['mot_de_passe'])) {$user_mdp=$_COOKIE['mot_de_passe'];} else {$user_mdp='';}
     if (isset($_GET['identification'])) {$identification=$_GET['identification'];} else {$identification='';}
     if (isset($_POST['action'])) {$action=$_POST['action'];} 
     elseif (isset($_GET['action'])) {$action=$_GET['action'];}
	 else {$action='';}
     //echo "\nACTION GET : ".$action;
     if (isset($_REQUEST['actionc'])) {$actionc=$_REQUEST['actionc'];} else {$actionc='';}
     if (isset($_REQUEST['go'])) {$go=$_REQUEST['go'];} else {$go='';}
     if (isset($_REQUEST['ga'])) {$ga=$_REQUEST['ga'];} else {$ga='';}
     if (isset($_REQUEST['id'])) {$id=$_REQUEST['id'];} else {$id='';}
     if (isset($_GET['page'])) {$page=$_GET['page'];} else {$page='';}


     if (isset($_POST['page'])){$page=$_POST['page'];}
     else if (isset($_GET['page'])) {$page=$_GET['page'];}
     else {$page="accueil";}


     if (VerifSession ($user_pseudo,$user_mdp)=="1")
                {
       if ($page=="championnat") {include ("championnat.php");}
       elseif ($page=="groupes_championnats") {include ("groupes_championnats.php");}
       elseif ($page=="mini_classement") {include ("mini_classement.php");}
       elseif ($page=="fiches_clubs") {include ("fiches_clubs.php");}
       elseif ($page=="rappel_no_mail") {include ("rappel_no_mail.php");}
       elseif ($page=="ajout_membre") {include ("ajout_membre.php");}      
       elseif ($page=="membres") {include ("membres.php");}
       elseif ($page=="credits") {include ("credits.htm");}
       elseif ($page=="accueil") {include ("accueil.htm");}
       else {include ("accueil.htm");}
                }
       elseif ($identification == "non") {echo "Veuillez renseigner tous les champs !";}
       elseif ($identification == "non2") {echo "Identifiants erronés !";}
    
       else {echo "<br /><br /><br /><br />";include ("menu.non-identifie.htm");}
     
     ?>
      </div></td>
      <td  width="1%" style="border-left-style: dashed;border-left-width : 1px; border-color:#3b487f">&nbsp;</td>
    </tr>
    <tr>
    <td bgcolor="C0C0C0"></td>
      <td width="100%" colspan="2" height="10" style="border-top-style: dashed;border-top-width : 1px; border-color:#3b487f">&nbsp;</td>
      <td></td>
    </tr>
    <tr>
      <td class="phpl" height="10" colspan="1"><img border="0" src="bouton.gif" width="111" height="25">
        </td>
      <td class="phpl" colspan="3" width="70%" height="10" colspan="2" align="right"><a target="_blank" href="http://phpleague.univert.org">PHPLeague</a>
        - Copyright © 2004-2005 - Tous droits réservés - <a href="mailto:Alexis@univert.org">Alexis
        MANGIN</a><br />
        </td>
    </tr>
  </table>
  </center>
</div>

</body>

</html>
