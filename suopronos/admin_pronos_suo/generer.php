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
<table class=phpl width="80%">
            <tr>
              <td class=phpl2 align="center" colspan="3"><?php echo ADMIN_GRAPH_TITRE." "; affich_champ ($champ); ?></td>
            </tr>
            <tr>
            <td align="center">
<?php
include ("tps1.php3");   


$query1="SELECT id FROM phpl_equipes WHERE id_champ='$champ'";
$result1=mysql_query ($query1);
while ($row1=mysql_fetch_array($result1))
{ 
$query="DELETE FROM phpl_clmnt_graph WHERE phpl_clmnt_graph.id_equipe='$row1[0]'" ;
        mysql_query($query) or die (mysql_error());
}
$debut=0;
$fin=1;

$result=mysql_query("SELECT accession, barrage, relegation FROM phpl_parametres WHERE id_champ='$champ'");
 while ($row=mysql_fetch_array($result))
  {
    $accession = $row['accession'];
    $barrage = $row['barrage'] + $accession;
    $relegation = nb_equipes($champ)- $row['relegation'];
  }
$legende=CONSULT_CLMNT_MSG4.$debut.CONSULT_CLMNT_MSG5.$fin;
$query="SELECT max(phpl_journees.numero) FROM phpl_journees, phpl_matchs WHERE phpl_journees.id=phpl_matchs.id_journee AND buts_dom is not NULL and phpl_journees.id_champ='$champ'";
$result=mysql_query ($query);
$row=mysql_fetch_array($result);
$max=$row[0];
                                
while ($fin<=$max)
{                 
@db_clmnt($champ, $debut, $fin, 0);

             
$query="SELECT * FROM phpl_clmnt ORDER BY POINTS DESC, DIFF DESC, BUTSPOUR DESC, BUTSCONTRE ASC, NOM";
$result=mysql_query($query) or die (mysql_error());
$pl=1;

      while ($row=mysql_fetch_array($result))
      {   
        $x=0;
        $id_equipe=$row["ID_EQUIPE"];

        $query="INSERT INTO phpl_clmnt_graph (id_equipe, fin, classement) VALUES ('$id_equipe','$fin', '$pl')" ;
        mysql_query($query);
        $pl++;                    
      }
$fin++;
      }     

$query="SELECT phpl_clmnt_graph.id_equipe FROM phpl_clmnt_graph, phpl_equipes WHERE phpl_equipes.id=phpl_clmnt_graph.id_equipe
                                                 and phpl_equipes.id_champ=$champ";
$result=mysql_query($query);
$nb_saving=mysql_num_rows($result);

$query="SELECT * FROM phpl_equipes WHERE id_champ=$champ";
$result=mysql_query($query);
$nb_equipes=mysql_num_rows($result);
             
@db_clmnt($champ, $debut, $fin, 1);


if ($nb_saving=$max*$nb_equipes){

echo ADMIN_GRAPH; include ("tps2.php3");}
else {echo ADMIN_GRAPH_4;}

?>
</td></tr></table>
