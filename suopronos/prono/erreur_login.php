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

if (isset($_REQUEST['t']) and $_REQUEST['t'] == "1")
{
  echo "<table><tr><td><div class=\"bleu\"><b>Identifiants erronés</b></div></td></tr></table>";
}

else
{
  echo "<table><tr><td><div class=\"bleu\"><b>Veuillez renseigner tous les champs</b></div></td></tr></table>";
}


echo "<br />";
?>
