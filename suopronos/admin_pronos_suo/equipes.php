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


if ($action2=="creer" and isset($nom_club))
{
  $nom_club = addslashes($nom_club);
  mysql_query ("INSERT INTO phpl_clubs (nom) values ('$nom_club')") or die ("probleme " .mysql_error());
}

if ($action2=="supp" and isset($club))
{  
   reset ($club);
	 while ( list($cle, $val)= each($club))
         {
	mysql_query ("DELETE FROM phpl_clubs WHERE id ='$val'") or die ("probleme " .mysql_error());
         }
}

if ($action3=="supp")
{
         reset ($club);
	 while ( list ($cle, $val)= each ($club))
         {
		 mysql_query ("DELETE FROM phpl_equipes WHERE id='$val'") or die ("probleme " .mysql_error());
         }
}

if ($action3=="creer")
{
         reset ($club);
	 while ( list($cle, $val)= each($club))
         {
	mysql_query ("INSERT INTO phpl_equipes (id_champ,id_club) values ('$champ','$val')") or die ("probleme " .mysql_error());
         }
}

?>

<table class=phpl width="80%">
            <tr>
              <td class=phpl2 align="center" colspan="3"><?php echo ADMIN_CLUBS_CREE; ?><td class=phpl2 align="right"><a href="#" onclick="window.open('Assistant_fr/equipes_1.htm','Assistant','toolbar=0,location=0,directories=0,status=0,scrollbars=1,resizable=0,copyhistory=0,menuBar=0,width=512,height=512');return false;"><img border="0" alt="Assistant" src="aide.gif"></a>
            </tr>

            <tr>
              <td align="center" class=phpl6 colspan="4"><b><?php echo ADMIN_RENS_7; ?></b></td>
            </tr>
            
            <tr>
              <td class=phpl3 colspan="2">
                <form method="post" action=""><?php echo ADMIN_CLUB_NOM." "; ?><input type="texte" name="nom_club">
              </td>
              
              <td class=phpl3 align=right colspan="2">
                <?php $value=ADMIN_CHAMP_CREER_3; echo "<input type=\"submit\" value=\"$value\">";?>
                <input type="hidden" name="action2" value="creer">
                <input type="hidden" name="action" value="equipes">
                <input type="hidden" name="page" value="championnat">
                <?php echo "<input type=\"hidden\" name=\"champ\" value=\"$champ\">";?>
              </td>
              
              </form>
            </tr>
            
            <tr>
              <td align="center" class=phpl6 colspan="4"><b><?php echo ADMIN_RENS_8; ?></b></td>
            </tr>
              <td class=phpl3 colspan="2"><form method="post" action=""><?php echo ADMIN_EQUIPE_1." "; clubs_menu (); ?></td>

              <td class=phpl3 align=right colspan="2">
                <?php $value=ADMIN_RENS_8; echo "<input type=\"submit\" value=\"$value\">";?>
                <input type="hidden" name="action2" value="supp">
                <input type="hidden" name="action" value="equipes">
                <input type="hidden" name="page" value="championnat">
                <?php echo "<input type=\"hidden\" name=\"champ\" value=\"$champ\">";?>
                </form>
              </td>
             </tr>

</table><br />



<table class=phpl width="80%">
            <tr>
              <td class=phpl2 align="center" colspan="4"><?php echo ADMIN_EQUIPE_TITRE." " ; affich_champ ($champ); ?></td><td class=phpl2 align="right"><a href="#" onclick="window.open('Assistant_fr/equipes_2.htm','Assistant','toolbar=0,location=0,directories=0,status=0,scrollbars=1,resizable=0,copyhistory=0,menuBar=0,width=512,height=512')"><img border="0" alt="Assistant" src="aide.gif"></a></td>
            </tr>

            <tr>
              <td align="center" class=phpl6 colspan="4"><b><?php echo ADMIN_RENS_7; ?></b></td>
            </tr>
            
            <tr>
              <td class=phpl3 colspan="3">
                <form method="post" action=""><?php echo ADMIN_GR_CHAMP_EDIT_1; ?> <b><?php affich_champ ($champ); ?></b> : <?php clubs_menu (); ?><br /><?php echo ADMIN_EQUIPE_3; ?>
              </td>
              
              <td class=phpl3 align=right colspan="2">
                <?php $value=ADMIN_RENS_7; echo "<input type=\"submit\" value=\"$value\">";?>
                <input type="hidden" name="action3" value="creer">
                <input type="hidden" name="action" value="equipes">
                <input type="hidden" name="page" value="championnat">
                <?php echo "<input type=\"hidden\" name=\"champ\" value=\"$champ\">";?>
              </td>
              
              </form>
            </tr>
            
            <tr>
              <td align="center" class=phpl6 colspan="4"><b><?php echo ADMIN_RENS_8; ?></b></td>
            </tr>
              <td class=phpl3 colspan="2"><form method="post" action=""><?php echo ADMIN_EQUIPE_4; ?> <b><?php affich_champ ($champ); ?></b> : <?php equipes_menu ($champ); print nb_equipes($champ);?> clubs</td>
                     
              <td class=phpl3 align=right colspan="3">
               <?php $value=ADMIN_RENS_8; echo "<input type=\"submit\" value=\"$value\">";?>
                <input type="hidden" name="action3" value="supp">
                <input type="hidden" name="action" value="equipes">
                <input type="hidden" name="page" value="championnat">
                <?php echo "<input type=\"hidden\" name=\"champ\" value=\"$champ\">";?>
                </form>
              </td>
             </tr>

</table><br />
