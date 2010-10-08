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

if ($gr_champ and !$confirm=="ok")
{
?>
<table  class=phpl width="80%">
            <tr>
              <td class=phpl2 align="center"><?php echo ADMIN_GR_CHAMPIONNATS_SUPP; ?></td>
            </tr>
            <tr>
              <td class=phpl3 align=center><?php echo ADMIN_GR_CHAMPIONNATS_SUPP1; ?> <b><?php affich_gr_champ ($gr_champ); ?></b> <?php echo ADMIN_GR_CHAMPIONNATS_SUPP2; ?></td>
            </tr>
            <tr>
              <td class=phpl3 align=center><a href="?page=groupes_championnats&action=supp&gr_champ=<?php print $gr_champ; ?>&confirm=ok"><?php echo ADMIN_RENS_17; ?></a> - <a href="?page=groupes_championnats"><?php echo ADMIN_RENS_18; ?></a></td>
            </tr>


          </table>
<?php
}

?>
