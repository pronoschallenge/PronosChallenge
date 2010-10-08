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
<link rel="stylesheet" type="text/css" href="../league.css">
<?php
require ("../config.php") ;
require ("fonctions.php");
ouverture ();

echo "<STYLE>";
require ("../league.css");
echo "</STYLE>";
?>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
        <body class=phpl>
<?php

if (!isset($_REQUEST['champ']))
       {
         demande_champ ();
       }
else
{
$champ = $_REQUEST['champ'];
// Nom du champ
$query2="SELECT nom FROM phpl_divisions, phpl_championnats where id_division=phpl_divisions.id";
$result2=(mysql_query($query2));
while ($row=mysql_fetch_array($result2))
       {
       $nom=$row[0];
       }

// SELECTION DES PARAMETRES
$query="select * FROM phpl_parametres where id_champ='$champ' ";
$result=(mysql_query($query));
while ($row=mysql_fetch_array($result))
       {
       $id_equipe_fetiche=$row['id_equipe_fetiche'];
       }

// NOM de EQUIPE FAVORITE a partir de son id
$result=(mysql_query("SELECT nom FROM phpl_clubs, phpl_equipes WHERE phpl_equipes.id='$id_equipe_fetiche' AND phpl_clubs.id=phpl_equipes.id_club"));
while ($row=mysql_fetch_array($result))
       {
       $equipe_fetiche= stripslashes($row[0]);

       }

$query="SELECT max(phpl_journees.numero) FROM phpl_journees, phpl_matchs where phpl_journees.id=phpl_matchs.id_journee and buts_dom is not NULL and phpl_journees.id_champ='$champ' and (id_equipe_ext='$id_equipe_fetiche' or id_equipe_dom='$id_equipe_fetiche')";
                      $result=mysql_query($query);

                               while ($row=mysql_fetch_array($result))
                               { $numero=$row[0];
                                 
$query1="SELECT logodom.url_logo as logodom, logoext.url_logo as logoext, phpl_matchs.buts_dom, phpl_matchs.buts_ext, phpl_journees.date_prevue, logodom.id as logoiddom, logoext.id as logoidext
                FROM phpl_equipes as dom, phpl_equipes as ext, phpl_matchs, phpl_journees, phpl_clubs as logodom, phpl_clubs as logoext
                WHERE phpl_matchs.id_equipe_dom=dom.id
                        AND phpl_matchs.id_equipe_ext=ext.id
                        AND phpl_journees.id_champ='$champ'
                        AND phpl_journees.numero='$numero'
                        AND dom.id_club=logodom.id
                        AND ext.id_club=logoext.id
                        AND phpl_matchs.id_journee=phpl_journees.id
                        AND (phpl_matchs.id_equipe_ext='$id_equipe_fetiche'
                        OR phpl_matchs.id_equipe_dom='$id_equipe_fetiche' )";
        $result=mysql_query($query1) or die ("probleme " .mysql_error());
         echo "<TABLE class=phpl cellspacing=\"0\" align=\"center\" >";
        $legende="ème journée de $nom";

             while ($row=mysql_fetch_array($result))
             {
             $domproba= $row[2];
             $extproba= $row[3];

                $date = ereg_replace('^([0-9]{2,4})-([0-9]{1,2})-([0-9]{1,2})$','\\3/\\2/\\1', $row[4]);
                echo "<TR class=phpl><TH class=phpl colspan=5 text-align=\"center\"><b>". $numero."".$legende." le ".$date."</b></th></tr>";
                


                $bgcolor="#FFFFFF";

                
                echo "<TR class=phpl bgcolor=$bgcolor width=\"100%\">";
                echo "<TD class=phpl align=\"right\" width=\"41%\">";
                echo "<a href=\"club.php?id_clubs=$row[5]&champ=$champ\"><img border=0 src=\"$row[0]\" width=40 height=50></a>";
                echo "</td><TD class=phpl align=\"center\">".$domproba."</td><TD class=phpl>-</td><TD class=phpl>".$extproba."</td>";
                echo "<TD class=phpl align=\"left\" width=\"41%\">";
                echo "<a href=\"club.php?id_clubs=$row[5]&champ=$champ\"><img border=0 src=\"$row[1]\" width=40 height=50></a>";
                echo "</td></tr>";
             }
        echo "</table>";
                               }
echo "<br /><br />";
$query="SELECT max(phpl_journees.numero) from phpl_journees, phpl_matchs where phpl_journees.id=phpl_matchs.id_journee and buts_dom is not NULL and phpl_journees.id_champ='$champ' and (id_equipe_ext='$id_equipe_fetiche' or id_equipe_dom='$id_equipe_fetiche')";
                      $result=mysql_query($query);

                               while ($row=mysql_fetch_array($result))
                               { $numero=$row[0]+1;
                                 
$query1="SELECT logodom.url_logo as logodom, logoext.url_logo as logoext, phpl_matchs.buts_dom, phpl_matchs.buts_ext, phpl_journees.date_prevue, logodom.id as logoiddom, logoext.id as logoidext
                FROM phpl_equipes as dom, phpl_equipes as ext, phpl_matchs, phpl_journees, phpl_clubs as logodom, phpl_clubs as logoext
                WHERE phpl_matchs.id_equipe_dom=dom.id
                        AND phpl_matchs.id_equipe_ext=ext.id
                        AND phpl_journees.id_champ='$champ'
                        AND phpl_journees.numero='$numero'
                        AND dom.id_club=logodom.id
                        AND ext.id_club=logoext.id
                        AND phpl_matchs.id_journee=phpl_journees.id
                        AND (phpl_matchs.id_equipe_ext='$id_equipe_fetiche'
                        OR phpl_matchs.id_equipe_dom='$id_equipe_fetiche' )";
        $result=mysql_query($query1) or die (mysql_error()) ;
         echo "<TABLE class=phpl cellspacing=\"0\" align=\"center\" >";
        $x=1;
        $legende="ème journée de $nom";
        
             while ($row=mysql_fetch_array($result))
             {
             $domproba= $row[2];
             $extproba= $row[3];


             if ($x==1)
                {
                $date = ereg_replace('^([0-9]{2,4})-([0-9]{1,2})-([0-9]{1,2})$','\\3/\\2/\\1', $row[4]);
                echo "<TR class=phpl ><TH class=phpl colspan=5 text-align=\"center\"><b> ". $numero."".$legende." le ".$date."</b></th></tr>";
                }
                
                if ($row[0]==$equipe_fetiche )
                {
                $DebMarqueur1 = "<b>";
                $FinMarqueur1 = "</b>";
                }
                        
                else
                {
                $DebMarqueur1 = "";
                $FinMarqueur1 = "";
                }
                
                if ($row[1]==$equipe_fetiche )
                {
                $DebMarqueur2 = "<b>";
                $FinMarqueur2 = "</b>";
                }

                else
                {
                $DebMarqueur2 = "";
                $FinMarqueur2 = "";
                }

                $bgcolor="#FFFFFF";
                        

                echo "<TR class=phpl bgcolor=$bgcolor width=\"100%\">";
                echo "<TD class=phpl align=\"right\" width=\"41%\">";
                echo "<a href=\"club.php?id_clubs=$row[5]&champ=$champ\"><img border=0 src=\"$row[0]\" width=40 height=50></a>";
                echo "</td><TD class=phpl align=\"center\">".$domproba."</td><TD class=phpl>-</td><TD class=phpl>".$extproba."</td>";
                echo "<TD class=phpl align=\"left\" width=\"41%\">";
                echo "<a href=\"club.php?id_clubs=$row[5]&champ=$champ\"><img border=0 src=\"$row[1]\" width=40 height=50></a>";
                echo "</td></tr>";                
                $x++;
             }

        echo "</table>";
 }                              }
?>
