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

require ("../config.php") ;
require ("fonctions.php");
ouverture ();
include ("avant.php");
ENTETE2();
?>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<div align="center"><h1> PHPLEAGUE</h1>
<p> (script réalisé par <a href="http://phpleague.univert.org">http://phpleague.univert.org</a>)</p>
<h2> <? echo MENU_UTILISATEUR; ?> </h2></div>
<p align="center"><img src="http://univert42.free.fr/images/logo20.gif"></p><? 
include ("apres.php");

?>



