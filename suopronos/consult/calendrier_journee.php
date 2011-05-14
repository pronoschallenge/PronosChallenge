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


//include ("avant.php");
require ("../config.php") ;
require ("../consult/fonctions.php");
ouverture ();

// Choix du championnat
if (!isset($_REQUEST['champ']))
{
	echo "Impossible de déterminer le championnat !";
}
// Choix du club à consulter
else
{
	if (isset($_REQUEST['numero'])) {$numero = $_REQUEST['numero'];} else {$numero = "";}
	$champ = $_REQUEST['champ'];
         
	if ($numero == "")   
	{
    	$query="SELECT max(phpl_journees.numero) FROM phpl_journees, phpl_matchs where phpl_journees.id=phpl_matchs.id_journee and buts_dom is NOT NULL and phpl_journees.id_champ='$champ'";
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

    if ($numero<1 or $numero>$nb_journees) 
    {
		$numero=1;
	}            
     
    $nb_journees=nb_journees($champ);

    // SELECTION DES PARAMETRES
    $result=(mysql_query("SELECT fiches_clubs, id_equipe_fetiche
                       FROM phpl_parametres
                       WHERE phpl_parametres.id_champ='$champ'"));

    $row=mysql_fetch_array($result);

    $fiches_clubs=$row['fiches_clubs'];
    $id_equipe_fetiche=$row['id_equipe_fetiche'];
	$fiches_clubs=0;

    // cellule d'affichage des derniers résultats
    $color=0;
    $query1="SELECT cldom.nom as cldom, clext.nom as clext, phpl_matchs.buts_dom, phpl_matchs.buts_ext,
                        phpl_journees.date_prevue, cldom.id as cliddom, clext.id as clidext, date_reelle,
                        dom.id as eqdom, ext.id as eqext, phpl_matchs.id as id_match
                FROM phpl_equipes as dom, phpl_equipes as ext, phpl_matchs, phpl_journees,
                     phpl_clubs as cldom, phpl_clubs as clext
                WHERE phpl_matchs.id_equipe_dom=dom.id
                        AND phpl_matchs.id_equipe_ext=ext.id
                        AND phpl_journees.id_champ='$champ'
                        AND phpl_journees.numero='$numero'
                        AND dom.id_club=cldom.id
                        AND ext.id_club=clext.id
                        AND phpl_matchs.id_journee=phpl_journees.id
                        AND cldom.nom!='exempte'
                        AND clext.nom!='exempte'
                        ORDER BY date_reelle asc";
	$result=mysql_query($query1) or die (mysql_error());
        
    $x=1;
    $minute = 0;
    $heure = 0;
    $jour = 0;
    $mois = 0;
    $annee = 0;

	echo "<table id=\"tablePronosCalendrierL1\" cellspacing=\"0\" cellpadding=\"0\">";

    while ($row=mysql_fetch_array($result))
    {
    	$clubs_nom = stripslashes($row[0]);
        $clubs_nom1 = stripslashes($row[1]);
        $domproba= $row[2];
        $extproba= $row[3];

        if ($x==1)
        {
			echo "<caption>";

			if ($numero>1)
			{
				echo "<a onclick=\"javascript:$('#journee').load('http://".$_SERVER['SERVER_NAME'].substr($_SERVER['PHP_SELF'], 0, strrpos($_SERVER['PHP_SELF'],'/'))."/../consult/calendrier_journee.php?champ=$champ&amp;numero=$numero_prec');\" style=\"text-decoration:none;\"><<</a>";
			} 

			echo "&nbsp;&nbsp;<b>Journ&eacute;e n&deg; ". $numero."</b>&nbsp;&nbsp;";

   			if ($numero<$nb_journees)
			{
				echo "<a onclick=\"javascript:$('#journee').load('http://".$_SERVER['SERVER_NAME'].substr($_SERVER['PHP_SELF'], 0, strrpos($_SERVER['PHP_SELF'],'/'))."/../consult/calendrier_journee.php?champ=$champ&amp;numero=$numero_suiv');\" style=\"text-decoration:none;\">>></a>";
			}			     
            echo "</caption>";
       	}

        if (($color%2)==0) {$classe="l1LignePaire";} else {$classe="l1LigneImpaire";}

		//if (($minute==substr($row[7],14,2)) and ($heure==substr($row[7],11,2)) and ($jour==substr($row[7],8,2)) and ($mois==substr($row[7],5,2)) and ($annee==substr($row[7],0,4)))
		//if (!($annee==substr($row[7],0,4)) or !($mois==substr($row[7],5,2)) or !($jour==substr($row[7],8,2)) or !($minute==substr($row[7],14,2)) or !($heure==substr($row[7],11,2)))
		if (!($annee==substr($row[7],0,4)) or !($mois==substr($row[7],5,2)) or !($jour==substr($row[7],8,2)))
		{
			//$minute = substr($row[7],14,2); // on récupère la minute
			//$heure = substr($row[7],11,2); // on récupère l'heure
			$jour = substr($row[7],8,2); // on récupère le jour
			$mois = substr($row[7],5,2); // puis le mois
			$annee = substr($row[7],0,4); // et l'annee

			setlocale(LC_TIME, LEAGUE_LANGUAGE);
			//$t= mktime($heure,$minute,0,$mois,$jour,$annee);
			$t= mktime(0,0,0,$mois,$jour,$annee);

			echo "<tr class=\"date\"><td colspan=\"5\">";

			echo preg_replace('@é@','&eacute;',preg_replace('@û@','&ucirc;',strftime("%A %d %B ",$t)));
			//echo strftime("- %Hh%M",$t);
			echo "</td></tr>";
		}

		if ($row['eqdom']==$id_equipe_fetiche )
		{
			$DebMarqueur1 = "<b>";
			$FinMarqueur1 = "</b>";
		}
		else
		{
			$DebMarqueur1 = "";
			$FinMarqueur1 = "";
		}

		if ($row['eqext']==$id_equipe_fetiche )
		{
			$DebMarqueur2 = "<b>";
			$FinMarqueur2 = "</b>";
		}
		else
		{
			$DebMarqueur2 = "";
			$FinMarqueur2 = "";
		}

		echo "<tr class=\"$classe\">";

		$activ_prono=0;
		$fiche_match=0;
		
		if ($fiches_clubs=="1" and $activ_prono=="1")
		{
			echo "<td align=\"right\"><a href=\"club.php?id_clubs=$row[5]&amp;champ=$champ\">".$clubs_nom."</a></td><td align=\"center\" style=\"white-space: nowrap\"><a href=\"#\" onclick=\"window.open('match.php?id_match=$row[id_match]','Fichematch','toolbar=0,location=0,directories=0,status=0,scrollbars=1,resizable=0,copyhistory=0,menuBar=0,width=560,height=320');return false;\">".$domproba." - ".$extproba."</a></td><td align=\"left\" width=\"45%\"><a href=\"club.php?id_clubs=$row[6]&amp;champ=$champ\">".$DebMarqueur2.$clubs_nom1.$FinMarqueur2."</a></td>";
		}

		if ($fiches_clubs=="1" and $activ_prono=="0")
		{
			echo "<td align=\"right\"><a href=\"club.php?id_clubs=$row[5]&amp;champ=$champ\">".$clubs_nom."</a></td><td align=\"center\" style=\"white-space: nowrap\"><a href=\"#\" onclick=\"window.open('match.php?id_match=$row[id_match]','Fichematch','toolbar=0,location=0,directories=0,status=0,scrollbars=1,resizable=0,copyhistory=0,menuBar=0,width=560,height=320');return false;\">".$domproba." - ".$extproba."</a></td><td align=\"left\" width=\"45%\"><a href=\"club.php?id_clubs=$row[6]&amp;champ=$champ\">".$DebMarqueur2.$clubs_nom1.$FinMarqueur2."</a></td>";
		}
		elseif (!$fiches_clubs=="1" and $activ_prono=="1")
		{
			echo "<td align=\"right\">".$clubs_nom."</td><td align=\"center\" style=\"white-space: nowrap\"><a href=\"#\" onclick=\"window.open('match.php?id_match=$row[id_match]','Fichematch','toolbar=0,location=0,directories=0,status=0,scrollbars=1,resizable=0,copyhistory=0,menuBar=0,width=560,height=320');return false;\">".$domproba." - ".$extproba."</a></td><td align=\"left\" width=\"45%\">".$DebMarqueur2.$clubs_nom1.$FinMarqueur2."</td>";
		}
		elseif (!$fiches_clubs=="1" and $activ_prono=="0" and $fiche_match=="1")
		{
			echo "<td align=\"right\">".$clubs_nom."</td><td align=\"center\" style=\"white-space: nowrap\"><a href=\"#\" onclick=\"window.open('match.php?id_match=$row[id_match]','Fichematch','toolbar=0,location=0,directories=0,status=0,scrollbars=1,resizable=0,copyhistory=0,menuBar=0,width=560,height=320');return false;\">".$domproba." - ".$extproba."</a></td><td align=\"left\" width=\"45%\">".$DebMarqueur2.$clubs_nom1.$FinMarqueur2."</td>";
		}
		elseif (!$fiches_clubs=="1" and $activ_prono=="0" and $fiche_match=="0")
		{
			echo "<td align=\"right\">".$clubs_nom."</td><td align=\"center\" style=\"white-space: nowrap\">".$domproba." - ".$extproba."</td><td align=\"left\" width=\"45%\">".$DebMarqueur2.$clubs_nom1.$FinMarqueur2."</td>";
		}                
		
		echo "</tr>\n";
		$x++;
		$color+=1;

	}
              
  	$requete="SELECT phpl_clubs.nom, CLEXT.nom, phpl_matchs.buts_dom, phpl_matchs.buts_ext,
            phpl_matchs.id, phpl_matchs.date_reelle
            FROM phpl_clubs, phpl_clubs as CLEXT, phpl_matchs, phpl_journees, phpl_equipes, phpl_equipes as EXT
            WHERE phpl_clubs.id=phpl_equipes.id_club
            AND CLEXT.id=EXT.id_club
            AND phpl_equipes.id=phpl_matchs.id_equipe_dom
            AND EXT.id=phpl_matchs.id_equipe_ext
            AND phpl_matchs.id_journee=phpl_journees.id
            AND phpl_journees.numero='$numero'
            AND phpl_journees.id_champ='$champ'
            AND (CLEXT.nom='exempte' or phpl_clubs.nom='exempte')";
  	$resultats=mysql_query($requete) or die (mysql_error());

  	while ($row=mysql_fetch_array($resultats))
  	{   
   		$row[0] = stripslashes($row[0]);
   		$row[1]= stripslashes($row[1]);
    	if (($color%2)==0) {$bgcolor1="#e5e5e5";} else {$bgcolor1="#FFFFFF";}
    	if ($row[0]=='exempte') {echo "<tr bgcolor=\"$bgcolor1\" class=\"trphpl\"><td colspan=\"7\">".ADMIN_RESULTS_1." : $row[1]</td></tr>";}
    	if ($row[1]=='exempte') {echo "<tr bgcolor=\"$bgcolor1\" class=\"trphpl\"><td colspan=\"7\" >".ADMIN_RESULTS_1." : $row[0]</td></tr>";}
  	}
    
    echo "</table>";
}
?>

