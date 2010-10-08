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


include ("avant.php");
require ("../config.php") ;
require ("../consult/fonctions.php");
ouverture ();
ENTETE2 ();

// Choix du championnat
if (!isset($_REQUEST['champ']))

{
   demande_champ ();
}

// Choix du club à consulter
else
{
    if (isset($_REQUEST['a'])) {$a = $_REQUEST['a'];} else {$a = "";}
    if (isset($_REQUEST['numero'])) {$numero = $_REQUEST['numero'];} else {$numero = "";}
    $champ = $_REQUEST['champ'];
    
    echo "<div align=\"center\">";
         
      if ($numero == "")
      {
      $query="SELECT max(phpl_journees.numero) FROM phpl_journees, phpl_matchs where phpl_journees.id=phpl_matchs.id_journee and buts_dom is not NULL and phpl_journees.id_champ='$champ'";
      }
      elseif (isset($numero))
      {
      $query="SELECT max(phpl_journees.numero) FROM phpl_journees, phpl_matchs where phpl_journees.id=phpl_matchs.id_journee and phpl_journees.id_champ='$champ' and phpl_journees.numero='$numero'";
      }
      $result=mysql_query($query);

      $row=mysql_fetch_array($result);

             $numero_suiv=$row[0]+1;
             $numero_prec=$row[0]-1;

             $numero=$row[0];
             $nb_journees=nb_journees($champ);

             if ($numero<1 or $numero>$nb_journees){echo CONSULT_CALENDAR_1;}

             else
             {               
             echo "<div align=\"center\"><table border=\"0\" width=\"90%\" cellspacing=\"0\">\n<tr>\n<td align=\"left\">\n";
             // On affiche le lien "précédent" seulement si la journée n'est pas la première
             if ($numero>1)
             {
             echo "<a href=\"calendrier_1.php?champ=$champ&amp;numero=$numero_prec\">".CONSULT_CALENDAR_2."</a>";
             }


             echo "</td>\n<td align=\"right\">";
             // On affiche le lien "suivant" si la journée n'est pas la dernière
             if ($numero<$nb_journees)
             {
             echo "<a href=\"calendrier_1.php?champ=$champ&amp;numero=$numero_suiv\">".CONSULT_CALENDAR_3."</a>";
             }
             echo "</td>\n</tr></table></div><br />";

             

             if ($a>0) { $legende= CONSULT_CALENDAR_4; }
             
             elseif ($a<0) { $legende= CONSULT_CALENDAR_5;}
             
             else { $legende=CONSULT_CLMNT_MSG6; }
             
             $nb_journees=nb_journees($champ);

             // SELECTION DES PARAMETRES
             $result=(mysql_query("SELECT fiches_clubs, id_equipe_fetiche
                               FROM phpl_parametres
                               WHERE phpl_parametres.id_champ='$champ'"));

             $row=mysql_fetch_array($result);

             $fiches_clubs=$row['fiches_clubs'];
             $id_equipe_fetiche=$row['id_equipe_fetiche'];


             aff_journee($champ, $numero, $legende, 0, $fiches_clubs, $id_equipe_fetiche);

echo"</div>";
}}
?>
<br />
<p align="right"><font face="Verdana" size="1">Powered by <a href="http://phpleague.univert.org" target="_blank">PhpLeague</a></font></p>
<?php
 include ("apres.php");
?>
