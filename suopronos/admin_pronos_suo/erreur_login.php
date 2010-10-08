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


<body>

    <table width="100%" border="0" align="center">
      <tr>
        <td colspan="2" align="center">
            <?
include("haut.inc.php3");
?>
            
          </td>
        </tr>
      <tr>
        <td width="22%" align="center" valign="top"  height="70">
          <font face="Verdana" size="2" color="#3b487f"><b>PHPleague</b></font>
        </td>
        <td width="77%" align="center" valign="top">
<?
include("pronos.inc.htm");
?>
        </td>
      </tr>
      <tr>
        <td width="22%" rowspan="3" align="center" valign="top">

            <?
include("menu.inc.php3");
?>
         </td>


         <td align="center" valign="top" height="100%">
          <?
if (!$t=="1") echo "<font face=\"Verdana\" color=\"#3b487f\" size=\"1\"><b>Veuillez renseigner tous les champs</b></font>";
if ($t=="1") echo "<font face=\"Verdana\" color=\"#3b487f\" size=\"1\"><b>Identifiants erronés</b></font>";
echo "<br /></td></tr></table>";
include("bas.inc.php3");
?>
    
</body>

</html>
