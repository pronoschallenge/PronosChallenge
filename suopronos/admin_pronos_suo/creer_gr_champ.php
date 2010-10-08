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
<table class=phpl width="80%">
            <tr>
              <td class=phpl2 align="center" colspan="3"><? echo ADMIN_GR_CHAMPIONNATS_CREA; ?></td><td class=phpl2 align="right"><a href="#" onclick="window.open('Assistant_fr/creer_gr_champ.htm','Assistant','toolbar=0,location=0,directories=0,status=0,scrollbars=1,resizable=0,copyhistory=0,menuBar=0,width=512,height=512');return false;"><img border="0" alt="Assistant" src="aide.gif"></a></td>
            </tr>
            
            <tr>
              <td align="center" class=phpl6 colspan="4"><b><? echo ADMIN_CHAMP_CREER_3; ?></b></td>
            </tr>
              <td class=phpl3 align=right><form method="post"  action=""><? echo ADMIN_GR_CHAMP_CREER_1; ?> :</td>
              <td class=phpl3 align=left>
                <input type="texte" name="nom_group" size="25" maxlength="50">
              </td>
              <td class=phpl3 align=right colspan="2">
                <? $value=ADMIN_CHAMP_CREER_3; echo "<input type=\"submit\" value=\"$value\">";?>
                <input type="hidden" name="action2" value="creer">
                <input type="hidden" name="action" value="creer">
                <input type="hidden" name="page" value="groupes_championnats">
                </form>
              </td>
             </tr>



          </table><br /><br />
          

