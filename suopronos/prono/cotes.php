<?php

//***********************************************************************/
// Phpleague : gestionnaire de championnat                              */
// ============================================                         */
//                                                                      */
// Version : 0.82                                                       */
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
$nbPts = 0;
$nb_matchs=10;
if (isset($_REQUEST['debut']))
{
	$debut=$_REQUEST['debut'];
}
else
{
	$debut='';
}
if (empty ($debut) or $debut=="0") $debut=0; $apres=1;
$fin = $debut+$nb_matchs;
   
if ($debut=="0") 
{
	$prec=$debut;
}
else 
{
	$debut1=$debut-$nb_matchs;
	if ($debut1 < 0) 
	{
		$debut1="0";
	}	    	
	$prec=$debut1; 
}
$prec=$debut-$nb_matchs;
?>

<div class="bloc bloc_cotes">
	<div class="rounded-block-top-left"></div>
	<div class="rounded-block-top-right"></div>
	<div class="rounded-outside">
		<div class="rounded-inside">
			<div class="bloc_entete">
				<div class="bloc_icone"></div>
				<div class="bloc_titre">Cotes</div>
			</div>
			<div class="bloc_contenu">

				<div id="pronos_pagination">
					<div id="matchs_precedents">
						<? if ($debut!="0") { ?>
						<a href="index.php?page=cotes&amp;debut=<?PHP print $prec; ?>&amp;gr_champ=<?php print $gr_champ;?>"><img src="ico/g.png" border="0" align="absmiddle"/></a>
						<a href="index.php?page=cotes&amp;debut=<?PHP print $prec; ?>&amp;gr_champ=<?php print $gr_champ;?>"><?php echo PRONO_GRILLE_PRECEDENT; ?></a>
						<? } ?>
					</div>
					<div id="matchs_suivants">
						<a href="index.php?page=cotes&amp;debut=<? print $fin; ?>&amp;gr_champ=<?php print $gr_champ;?>"><?php echo PRONO_GRILLE_SUIVANT; ?></a>
						<a href="index.php?page=cotes&amp;debut=<? print $fin; ?>&amp;gr_champ=<?php print $gr_champ;?>"><img src="ico/d.png" border="0" align="absmiddle"/></a>
					</div>
				</div>

				<div id="cotes_pronos">	
					<table cellspacing="0">				
						<tr>
							<th class="cotes_journee">Journée</th>
							<th class="cotes_clubdom">&nbsp;</th>
							<th class="cotes_prono1"><img src="images/1-transparent.gif" /></th>
							<th class="cotes_pronoN"><img src="images/N-transparent.gif" /></th>
							<th class="cotes_prono2"><img src="images/2-transparent.gif" /></th>
							<th class="cotes_clubext">&nbsp;</th>
							<th class="cotes_parieurs">Parieurs</th>
					  	</tr>		
<!--
<tr>
	<td colspan="7" align="center"><br>Nb de points gagnés si le pari est bon<td>
</tr>
-->

<?
    $requete="SELECT phpl_clubs.nom, CLEXT.nom, phpl_matchs.id, phpl_matchs.date_reelle, phpl_journees.numero
		FROM phpl_clubs, phpl_clubs as CLEXT, phpl_matchs, phpl_journees, phpl_equipes, phpl_equipes as EXT, phpl_gr_championnats
		WHERE phpl_clubs.id=phpl_equipes.id_club
		AND CLEXT.id=EXT.id_club
		AND phpl_equipes.id=phpl_matchs.id_equipe_dom
		AND EXT.id=phpl_matchs.id_equipe_ext
		AND phpl_matchs.id_journee=phpl_journees.id
		AND phpl_journees.id_champ=phpl_gr_championnats.id_champ
		AND phpl_gr_championnats.id='$gr_champ'
		AND phpl_matchs.buts_dom is null
		AND phpl_matchs.buts_ext is null
		AND phpl_clubs.nom!='exempte'
		AND CLEXT.nom!='exempte'
		ORDER by phpl_matchs.date_reelle, phpl_clubs.nom
		LIMIT $debut, $fin ";

    $i=0;
    $resultat=mysql_query($requete);
    
    // Journée inexistante
    if (mysql_num_rows($resultat)=="0")
    {
		echo "<tr><td colspan=6 align=center><div class=\"blanc\">Journée Inexistante</div></td></tr>";
	}

    // boucle sur les matchs
    while ($row=mysql_fetch_array($resultat) and $i<$nb_matchs)
    {
		$club_dom = stripslashes($row[0]);
		$club_ext = stripslashes($row[1]);
		
		if($connecte) 
		{ 
       		$requete2= "SELECT pronostic FROM phpl_pronostics, phpl_membres WHERE phpl_pronostics.id_match='$row[2]' AND phpl_membres.id=phpl_pronostics.id_membre AND phpl_membres.id_prono='$user_id'";
       		$resultat2=mysql_query($requete2) or die ("probleme " .mysql_error());
       		$nb_pronos= mysql_num_rows($resultat2 );



       		if ($nb_pronos == "0")
       		{
				$prono="0";
			}
			else
        	{
          		while ($row2=mysql_fetch_array($resultat2))
           		{
            		$prono=$row2["0"];

            		if ($row2["0"] == "")
            		{
						$prono="0";
					}
           		}
        	}
        }
        else
        {
			$prono="0";
		}


       	$requete2="SELECT tps_avant_prono FROM phpl_gr_championnats WHERE id='$gr_champ'";
       	$resultat2=mysql_query($requete2) or die ("probleme " .mysql_error());

        while ($row2=mysql_fetch_array($resultat2))
        {
        	$temps_avantmatch=$row2[0];
        }

       	$date_match_timestamp=format_date_timestamp($row[3]);
       	$date_actuelle=time();
       	$ecart_secondes=$date_match_timestamp-$date_actuelle;
       	$ecart_heures = floor($ecart_secondes / (60*60))-$temps_avantmatch;
       	$ecart_minutes = floor($ecart_secondes / 60)-$temps_avantmatch*60;
       	$ecart_jours = floor($ecart_secondes / (60*60*24)-$temps_avantmatch/60);
       	$date=format_date_fr_red($row[3]);
	   
	   	$requete5="SELECT pts_prono_exact, pts_prono_participation FROM phpl_gr_championnats WHERE phpl_gr_championnats.id='$gr_champ'"; 
        $resultats5=mysql_query($requete5) or die (mysql_error()); 

        while ($row5=mysql_fetch_array($resultats5)) 
        { 
        	$pts_prono_exact=$row5[0]; 
            $pts_prono_participation=$row5[1]; 
            // avant, le point de participation etait inclus dans les points mis en jeu
            //$points_prono_exact=$pts_prono_exact + $pts_prono_participation ; 
            $points_prono_exact=$pts_prono_exact ; 
        }
          
        //On compte le nombre de parieurs sur le match 
        $nombre_paris=mysql_query("SELECT COUNT( *) AS parieurs FROM phpl_pronostics WHERE phpl_pronostics.id_match='$row[2]'"); 
        $nb_paris=mysql_fetch_array($nombre_paris); 
        $nb_parieurs=$nb_paris['parieurs']; 
          
        //On compte le nombre de parieurs sur une victoire de l'equipe à domicile 
        $nombre_1=mysql_query("SELECT COUNT( *) AS domicile FROM phpl_pronostics WHERE phpl_pronostics.id_match='$row[2]' AND pronostic='1'"); 
        $nb_1=mysql_fetch_array($nombre_1); 
        $nb_parieurs1=$nb_1['domicile']; 
          
        //On compte le nombre de parieurs sur un match nul 
        $nombre_N=mysql_query("SELECT COUNT( *) AS nul FROM phpl_pronostics WHERE phpl_pronostics.id_match='$row[2]' AND pronostic='N'"); 
        $nb_N=mysql_fetch_array($nombre_N); 
        $nb_parieursN=$nb_N['nul']; 
          
        //On compte le nombre de parieurs sur une victoire de l'equipe à l'exterieur 
        $nombre_2=mysql_query("SELECT COUNT( *) AS visiteur FROM phpl_pronostics WHERE phpl_pronostics.id_match='$row[2]' AND pronostic='2'"); 
        $nb_2=mysql_fetch_array($nombre_2); 
        $nb_parieurs2=$nb_2['visiteur'];
		           
        //On attribue les points 
        if ($nb_parieurs1=="0")
		{
			$points_prono_domicile="---";
		}
		else
		{
			$points_prono_domicile=floor(($points_prono_exact*$nb_parieurs)/$nb_parieurs1);
		}
		 
		if ($nb_parieursN=="0")
		{
			$points_prono_nul="---";
		}
		else
		{
			$points_prono_nul=floor(($points_prono_exact*$nb_parieurs)/$nb_parieursN);
		}
		 
		if ($nb_parieurs2=="0")
		{
			$points_prono_visiteur="---";
		}
		else
        {
			$points_prono_visiteur=floor(($points_prono_exact*$nb_parieurs)/$nb_parieurs2);
		}
		 
		echo "<tr>";
	   	echo "<td class=\"cotes_journee\">$row[4]</td>";
       	echo "<td class=\"cotes_clubdom\">$club_dom</td>";
       	echo "<td class=\"cotes_prono1\">";
        if($prono == "1") 
		{
			echo "<div class=\"cotes_pronoutilisateur\">".$points_prono_domicile."</div>";
			$nbPts=$nbPts+$points_prono_domicile;
   		}
   		else
   		{
	   		echo $points_prono_domicile;
   		}
       	echo "</td>";
	   	echo "<td class=\"cotes_pronoN\">";
        if($prono == "N")
        {
			echo "<div class=\"cotes_pronoutilisateur\">".$points_prono_nul."</div>";
			$nbPts=$nbPts+$points_prono_nul;
   		}
   		else
   		{
	   		echo $points_prono_nul;
   		}	   
	   	echo "</td>";
	   	echo "<td class=\"cotes_prono2\">";
        if($prono == "2")
        {
			echo "<div class=\"cotes_pronoutilisateur\">".$points_prono_visiteur."</div>";
			$nbPts=$nbPts+$points_prono_visiteur;
   		}
   		else
   		{
	   		echo $points_prono_visiteur;
   		}	   
	   	echo "</td>";
	   	echo "<td class=\"cotes_clubext\">$club_ext";
	   	echo "<td class=\"cotes_parieurs\">$nb_parieurs";
	   	echo "</td>";
	   	echo "</tr>";
	   	$i++;
	}
?>

</table>

				</div>

<?php
if($connecte)
{
?>
<div>
<br/>
NOMBRE DE POINTS HOURRA POTENTIELS : <b><?echo $nbPts?></b>
<br/>
<br/>
<table cellpadding="0" cellspacing="0" border="0">
	<tr>
		<td style="text-align: center;"><div class="cotes_pronoutilisateur">X</div></td>
		<td>&nbsp;: correspond à votre prono, <b>X</b> étant le nombre de points que vous allez remporter si votre prono est bon.</td>
	</tr>
</table>
</div>
<?php
}	   
?>
<br/>
			</div>
		</div>
	</div>
	<div class="rounded-block-bottom-left"></div>
	<div class="rounded-block-bottom-right"></div>
</div>	
