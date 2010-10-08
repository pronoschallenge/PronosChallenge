<style type="text/css">
<!--
.Style2 {font-weight: bold}
.Style4 {font-size: 9px;
	font-weight: bold;
}
.Style5 {color: #FFFFFF}
.Style7 {font-size: 9px; font-weight: bold; color: #FFFFFF; }
-->
</style>

<?php

$queryMaxJournee  =  "SELECT max(fin) FROM phpl_pronos_graph
           		     WHERE type='$type'";
					 
$resultMaxJournee = mysql_query($queryMaxJournee) or die ("probleme " .mysql_error());

while ($rowMaxJournee=mysql_fetch_array($resultMaxJournee))
{
   $journeemax = $rowMaxJournee[0];
}


$queryEvol = "SELECT P1.id_membre, (P1.classement - P2.classement) as evolution
                       FROM phpl_pronos_graph P1
                       JOIN phpl_pronos_graph P2 ON P1.id_membre = P2.id_membre
                       WHERE P1.fin = '".($journeemax-1)."'
                       AND P2.fin = '$journeemax'
                       AND P1.type = '$type'
                       AND P2.type = '$type'
                       ORDER BY evolution";
					   
$resultEvol = mysql_query($queryEvol) or die ("probleme " .mysql_error());

?>

<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td width="20%" align="right"><div align="center"><img src="images/biere.jpg" width="38" height="50"></div></td>
    <td width="80%" align="right"><p align="left" class="Style2">Derniers posts sur le forum </p></td>
  </tr>
</table>
<table width="100%" height="100%"  border="0" cellpadding="2" cellspacing="0">
  <tr>
    <td valign="top" bgcolor="#333333"><table width="100%" border="0" align="center" cellpadding="1" cellspacing="0" bordercolor="#000000" bgcolor="#FFFFFF">
        <tr bordercolor="#333333" bgcolor="#333333">
          <td><table  border="0" cellpadding="0" cellspacing="0" width="100%">
              <tr>
                <td align="center" width="20%"><div align="center" class="Style5"><span class="Style4">Date</span></div></td>
                <td align="center" width="80%"><div align="center"><span class="Style7">Rubrique</span></div></td>
              </tr>
          </table></td>
        </tr>
        <tr>
          <td>
		  <?php	
		  	while ($rowEvol=mysql_fetch_array($resultEvol))
			{
			   $idMembre = $rowEvol[0];
   			   $evol = $rowEvol[1];
			   echo ($idMembre) ;
   			   echo ($evol) ;
			}
		   ?>
		</td>
        </tr>
    </table></td>
  </tr>
</table>
