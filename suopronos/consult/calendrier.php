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

include ("avant.php");

require ("../config.php") ;
require ("../consult/fonctions.php");



ouverture ();
ENTETE2 ();


if (!isset($_REQUEST['champ']))
{
    demande_champ ();
}
else
{
$champ=$_REQUEST['champ'];


$result=(mysql_query("SELECT id_equipe_fetiche, phpl_divisions.nom, phpl_saisons.annee, (phpl_saisons.annee)+1
                      FROM phpl_parametres, phpl_championnats, phpl_divisions, phpl_saisons 
                      WHERE id_champ='$champ'
                      AND phpl_championnats.id='$champ'
                      AND phpl_divisions.id=phpl_championnats.id_division
                      AND phpl_saisons.id=phpl_championnats.id_saison"));

while ($row=mysql_fetch_array($result))
  {
   $id_equipe_fetiche=$row['id_equipe_fetiche'];
   echo "<div align=\"center\"><h4>".$row[1]."  ".$row[2]."/".$row[3]."</h4></div>";
  }
$color=0;

$nb_equipe=nb_equipes($champ);

$requete2="SELECT phpl_clubs.nom, CLEXT.nom, phpl_matchs.buts_dom, phpl_matchs.buts_ext, phpl_matchs.id, phpl_matchs.date_reelle
             FROM phpl_clubs, phpl_clubs as CLEXT, phpl_matchs, phpl_journees, phpl_equipes, phpl_equipes as EXT 
             WHERE phpl_clubs.id=phpl_equipes.id_club 
             AND CLEXT.id=EXT.id_club
             AND phpl_equipes.id=phpl_matchs.id_equipe_dom 
             AND EXT.id=phpl_matchs.id_equipe_ext 
             AND phpl_matchs.id_journee=phpl_journees.id
             AND phpl_journees.id_champ='$champ'
             AND (CLEXT.nom='exempte' or phpl_clubs.nom='exempte')
             ORDER by phpl_journees.numero";
             
             
$resultats2=mysql_query($requete2) or die (mysql_error());    
$i=0;
while ($row2=mysql_fetch_array($resultats2))
  {
   $row2[0] = stripslashes($row2[0]);
   $row2[1] = stripslashes($row2[1]);
   $resultats_0[$i] = $row2[0];  $resultats_1[$i] = $row2[1];
   $i++;
  }

$query="SELECT phpl_journees.numero, phpl_journees.date_prevue, cldom.nom, clext.nom, phpl_matchs.buts_dom, phpl_matchs.buts_ext, dom.id, ext.id
        FROM phpl_journees, phpl_equipes as dom, phpl_equipes as ext, phpl_matchs, phpl_clubs as cldom, phpl_clubs as clext
        WHERE phpl_journees.id_champ='$champ'
        AND phpl_matchs.id_equipe_dom=dom.id
        AND phpl_matchs.id_equipe_ext=ext.id
        AND dom.id_club=cldom.id
        AND ext.id_club=clext.id
        AND phpl_matchs.id_journee=phpl_journees.id
        AND cldom.nom!='exempte'
        AND clext.nom!='exempte'
        ORDER BY phpl_journees.numero";
        
$result=mysql_query($query);

echo "<table width=\"80%\" align=\"center\"><tr><td>";
$journee_milieu=(nb_journees($champ)/2);
$journee=0;
$x=2;
$i=0;

while ($row=mysql_fetch_array($result))
{
   $row[2] = stripslashes($row[2]);
   $row[3] = stripslashes($row[3]);

        if (($journee=="0"))
            { 
              $date = ereg_replace('^([0-9]{2,4})-([0-9]{1,2})-([0-9]{1,2})$','\\3/\\2/\\1', $row[1]);
              ?>
<br><table class="tablephpl2" cellspacing="0" width="80%" align="center">
              
<tr class="trphpl3"><td colspan="3" align="center"><b>
              
<?php echo ADMIN_COHERENCE_MSG2." ".$row[0].CONSULT_MATCHS_MSG2.$date."</b></td></tr>";
            }
        elseif (!($journee==$row[0]))
            {   
                echo "</table><br>";
                
                if ($journee==$journee_milieu) {echo "</td><td align=\"center\">";}
                $date = ereg_replace('^([0-9]{2,4})-([0-9]{1,2})-([0-9]{1,2})$','\\3/\\2/\\1', $row[1]);
                ?>
                
<table class="tablephpl2" cellspacing="0" width="80%" align="center">
<tr class="trphpl3"><td colspan="3" align="center"><b>
<?php echo ADMIN_COHERENCE_MSG2." ".$row[0].CONSULT_MATCHS_MSG2.$date."</b></td></tr>";
                $x=2;
            }


        $classe="ligne2";
        if (($color%2)==0) $classe="ligne1";
        
echo "<tr class=\"$classe\">";

        if ($row[6]==$id_equipe_fetiche) 
        {
          ?>
 <td class="cld1"><b><?php print $row[2]; ?></b></td>
          <?php
        }
        else
        {
          ?>
 <td class="cld1"><?php print $row[2]; ?></td>
          <?php
        }

        ?>
 <td align="center">
<?php echo $row[4]." - ".$row[5]."</td>";

        if ($row[7]==$id_equipe_fetiche)
        { 
          ?>
 <td class="cld2"><b>
<?php echo "$row[3]</b></td></tr>";
        }
else { echo "<td class=\"cld2\">".$row[3]."</td></tr>";  }


        if ($x==($nb_equipe/2))
         {
           if (($color%2)==0) {$classe="ligne2";} else {$classe="ligne1";}
           if (isset($resultats_0[$i]) and $resultats_0[$i]=='exempte') {echo "<tr class=$classe><td colspan=3>".ADMIN_RESULTS_1." : $resultats_1[$i]</td></tr>";}
           if (isset($resultats_1[$i]) and $resultats_1[$i]=='exempte') {echo "<tr class=$classe><td colspan=3>".ADMIN_RESULTS_1." : $resultats_0[$i]</td></tr>";}
          $i++;
         }
        
        $color++;
        $journee=$row[0];
        $x++;
} 
    
echo "</table></td></tr></table>";
}

?>
<br />
<p align="right"><font face="Verdana" size="1">Powered by <a href="http://phpleague.univert.org" target="_blank">PhpLeague</a></font></p>
<?

include ("apres.php");

?>
