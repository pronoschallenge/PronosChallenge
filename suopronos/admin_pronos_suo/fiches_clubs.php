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
?>
<html>


<body class=phpl>



  <table border="0" width="90%">
    <tr>
      <td width="33%" align="center"><a href="?page=fiches_clubs&action=classes"><?php echo ADMIN_CLASSE_CLASSE; ?></a></td>
      <td width="33%" align="center"><a href="?page=fiches_clubs&action=rens"><?php echo ADMIN_CLASSE_RENS; ?></a></td>
      <td width="34%" align="center"><a href="?page=fiches_clubs&action=gest"><?php echo ADMIN_CLASSE_GEST; ?></a></td>
    </tr>
  </table><br />


</body>

</html>
<?php

if ($action=="classes"){ include ("classe.php"); }

if ($action=="rens") {include("rens.php");}

if ($action=="gest") {include("gestequipes.php");}

if ($action=="verif"){include("verif_clubs.php");}

?>
