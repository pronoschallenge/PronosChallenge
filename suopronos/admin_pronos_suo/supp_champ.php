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

if ($champ and !$confirm=="ok")
{
?>
<table class=phpl width="80%">
            <tr>
              <td class=phpl2 align="center"><? echo ADMIN_CHAMPIONNATS_SUPP; ?></td>
            </tr>
            <tr>
              <td class=phpl3 align=center><? echo ADMIN_CHAMPIONNATS_SUPP1; ?> <b><? affich_champ ($champ); ?></b> <? echo ADMIN_CHAMPIONNATS_SUPP2; ?></td>
            </tr>
            <tr>
              <td class=phpl3 align=center><a href="?page=championnat&action=supp&champ=<? print $champ; ?>&confirm=ok"><? echo ADMIN_RENS_17; ?></a> - <a href="?page=championnat"><? echo ADMIN_RENS_18; ?></a></td>
            </tr>


          </table>
<?
}

?>
