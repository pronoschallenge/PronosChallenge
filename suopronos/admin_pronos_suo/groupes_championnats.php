<?php
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

if (isset($_GET['gr_champ'])) {$gr_champ=$_GET['gr_champ'];} else {$gr_champ='';}
if (isset($_POST['action2'])) {$action2=$_POST['action2'];} else {$action2='';}
if (isset($_POST['nom_group'])) {$nom_group=$_POST['nom_group'];} else {$nom_group='';}
if (isset($_GET['confirm'])) {$confirm=$_GET['confirm'];} else {$confirm='';}
if (isset($_POST['action3'])) {$action3=$_POST['action3'];} else {$action3='';}
if (isset($_POST['champ'])) {$champ=$_POST['champ'];} else {$champ='';}

if (isset($_POST['pts_prono_exact'])) {$pts_prono_exact=$_POST['pts_prono_exact'];} else {$pts_prono_exact='';}
if (isset($_POST['pts_prono_participation'])) {$pts_prono_participation=$_POST['pts_prono_participation'];} else {$pts_prono_participation='';}
if (isset($_POST['id_master'])) {$id_master=$_POST['id_master'];} else {$id_master='';}
if (isset($_POST['tps_avant_prono'])) {$tps_avant_prono=$_POST['tps_avant_prono'];} else {$tps_avant_prono='';}
if (isset($_POST['activ_prono'])) {$activ_prono=$_POST['activ_prono'];} else {$activ_prono='';}



if ($action2=="creer" and isset($nom_group) and $action=="creer")
{   
  mysql_query ("INSERT INTO phpl_gr_championnats (nom) VALUES ('$nom_group')") or die ("probleme " .mysql_error());
}


if ($confirm=="ok" and $gr_champ and $action=="supp")
{
      mysql_query("DELETE FROM phpl_gr_championnats WHERE id ='$gr_champ' ")or die ("probleme " .mysql_error());
}
?>

<br /><font class=phpl><?php echo GR_LEAGUE; ?></font><br /><br />



<table class=phpl width="90%">
            <tr>
              <td class=phpl2><?php echo MENU_ID; ?></td>
              <td class=phpl2><?php echo MENU_NOM; ?></td>
              <td class=phpl2></td>
            </tr>
            <?php affich_gr_championnats ($gr_champ, $action); ?>
            <tr>
              <td class=phpl5 align="right" colspan="4"><a href="?page=groupes_championnats&action=creer">> <?php echo ADMIN_GR_CHAMP_CREER;?></a></td>
            </tr>

          </table>
<br /><br />
<?php

if ($action=="supp" and $gr_champ){ include ("supp_gr_champ.php"); }

if ($action=="creer") {include("creer_gr_champ.php");}

if ($action=="editer") {include("editer_gr_champ.php");}

if ($action=="generer") {include("generer_gr.php");}

?>
<br />
<br />
<table>
	<tr>
		<td class=phpl2>
			Un clic sur le lien "G&eacute;n&eacute;rer" effectue les actions suivantes :
			<ul>
				<li>reg&eacute;n&eacute;ration complete de la table des graphes (phpl_clmnt_graph) pour tous les types de classement</li>
				<li>reg&eacute;n&eacute;ration de la table des classements (phpl_clmnt_pronos) pour tous les types de classement</li>
			</ul>
		</td>
	</tr>
</table>