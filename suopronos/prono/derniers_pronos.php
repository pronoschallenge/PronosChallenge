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

if (isset($_GET['debut']))
{
	$debut=$_GET['debut'];
} 
else 
{
	$debut='';
}

if (empty ($debut) or $debut=="0") 
{
	$debut=0; 
	$apres=1;
}
$fin = $debut+10;
if ($debut=="0") 
{
	$suiv="?page=pronos&amp;gr_champ=$gr_champ";
}
else
{
	$debut1=($debut-"10"); 
	$suiv="?page=derniers_pronos&amp;debut=$debut1&amp;gr_champ=$gr_champ";
}

?>

<div class="bloc bloc_derniers_pronos">
	<div class="rounded-block-top-left"></div>
	<div class="rounded-block-top-right"></div>
	<div class="rounded-outside">
		<div class="rounded-inside">
			<div class="bloc_entete">
				<div class="bloc_icone"></div>
				<div class="bloc_titre">Derniers pronos</div>
			</div>
			<div class="bloc_contenu">

				<div id="pronos_pagination">
					<div id="matchs_precedents">
						<a href="?page=derniers_pronos&amp;debut=<?php echo "$fin&amp;gr_champ=$gr_champ";?>"><img src="ico/g.png" border="0" align="absmiddle"/></a>
						<a href="?page=derniers_pronos&amp;debut=<?php echo "$fin&amp;gr_champ=$gr_champ";?>"><?php echo PRONO_GRILLE_PRECEDENT; ?></a>
					</div>
					<div id="matchs_suivants">
						<a href="<?php print $suiv; ?>"><?php echo PRONO_GRILLE_SUIVANT; ?></a>
						<a href="<?php print $suiv; ?>"><img src="ico/d.png" border="0" align="absmiddle"/></a>
					</div>
				</div>

				<div id="pronos">
					<table cellspacing="0">
						<tr>
							<th class="derniers_pronos_journee"><?php echo ADMIN_COHERENCE_MSG2; ?></th>
							<th class="derniers_pronos_date"><?php echo DATE; ?></th>
							<th class="derniers_pronos_clubdom">&nbsp;</th>
							<th class="derniers_pronos_prono"><?php echo PRONO_GRILLE_PRONO; ?></th>
							<th class="derniers_pronos_clubext">&nbsp;</th>
							<th class="derniers_pronos_score"><?php echo PRONO_GRILLE_SCORE; ?></th>
							<th class="derniers_pronos_picto">&nbsp;</th>
							<th class="derniers_pronos_points"><?php echo PRONO_POINTS_HOURRA; ?></th>
					  	</tr>

<?php


$query="SELECT phpl_clubs.nom, CLEXT.nom, phpl_matchs.buts_dom, phpl_matchs.buts_ext, phpl_matchs.id, phpl_matchs.date_reelle, phpl_journees.numero, pts_prono_exact, pts_prono_participation
	FROM phpl_clubs, phpl_clubs as CLEXT, phpl_matchs, phpl_journees, phpl_equipes, phpl_equipes as EXT, phpl_gr_championnats
	WHERE phpl_clubs.id=phpl_equipes.id_club
	AND CLEXT.id=EXT.id_club 
	AND phpl_equipes.id=phpl_matchs.id_equipe_dom
	AND EXT.id=phpl_matchs.id_equipe_ext
	AND phpl_matchs.id_journee=phpl_journees.id
	AND phpl_journees.id_champ=phpl_gr_championnats.id_champ
	AND phpl_gr_championnats.id='$gr_champ'
	AND phpl_matchs.buts_dom is not null
	AND phpl_matchs.buts_ext is not null
	AND phpl_clubs.nom!='exempte'
	AND CLEXT.nom!='exempte'
	ORDER by phpl_matchs.date_reelle desc, phpl_clubs.nom desc
	LIMIT $debut, $fin ";
$i=9;
$result=mysql_query($query);
if (mysql_num_rows( $result )=="0") 
{
	echo "<tr><td colspan=6>Journée Inexistante</td></tr>";
}
while ($row=mysql_fetch_array($result) and $i>=0)
{
	$clubs_nom = stripslashes($row[0]);
	$clubs_nom1 = stripslashes($row[1]);
	$query2= "SELECT pronostic FROM phpl_pronostics, phpl_membres WHERE phpl_pronostics.id_match='$row[4]' AND phpl_membres.id=phpl_pronostics.id_membre AND phpl_membres.id_prono='$user_id'";
	$result2=mysql_query($query2) or die ("probleme " .mysql_error());
	$nb_pronos= mysql_num_rows($result2 );

	if ($nb_pronos == "0") {$prono="0";}
	{
		while ($row2=mysql_fetch_array($result2))
	  	{
			$prono=$row2["0"];
			if ($row2["0"] == "")
			{
				$prono="0";
			}
	  	}
	}

	// requete pour récupérer les cotes
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
	$nombre_paris=mysql_query("SELECT COUNT(*) AS parieurs FROM phpl_pronostics WHERE phpl_pronostics.id_match='$row[4]'"); 
	$nb_paris=mysql_fetch_array($nombre_paris); 
	$nb_parieurs=$nb_paris['parieurs']; 
          
	//On compte le nombre de parieurs sur une victoire de l'equipe à domicile 
	$nombre_1=mysql_query("SELECT COUNT(*) AS domicile FROM phpl_pronostics WHERE phpl_pronostics.id_match='$row[4]' AND pronostic='1'"); 
	$nb_1=mysql_fetch_array($nombre_1); 
	$nb_parieurs1=$nb_1['domicile']; 
          
	//On compte le nombre de parieurs sur un match nul 
	$nombre_N=mysql_query("SELECT COUNT(*) AS nul FROM phpl_pronostics WHERE phpl_pronostics.id_match='$row[4]' AND pronostic='N'"); 
	$nb_N=mysql_fetch_array($nombre_N); 
	$nb_parieursN=$nb_N['nul']; 
          
    //On compte le nombre de parieurs sur une victoire de l'equipe à l'exterieur 
    $nombre_2=mysql_query("SELECT COUNT(*) AS visiteur FROM phpl_pronostics WHERE phpl_pronostics.id_match='$row[4]' AND pronostic='2'"); 
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

  	$date=format_date_fr_red($row[5]);
  
	echo "<tr>";
  	echo "<td class=\"derniers_pronos_journee\">$row[6]</td>";
  	echo "<td class=\"derniers_pronos_date\">$date</td>";
  	echo "<td class=\"derniers_pronos_clubdom\">$clubs_nom</td>";
  	echo "<td class=\"derniers_pronos_prono\">";
  
	$nb_points = 0; 
  
  	if ($prono=="1")
	{ 
		$nb_points = $points_prono_domicile;
?>
             <img src="images/1.gif" alt="" title="<?php print "Points Hourra : ".$points_prono_domicile; ?>">
             <img src="images/N-gris.gif" alt="" title="<?php print "Points Hourra : ".$points_prono_nul; ?>">
             <img src="images/2-gris.gif" alt="" title="<?php print "Points Hourra : ".$points_prono_visiteur; ?>">
<?php
	}
 	if ($prono=="N")
	{
		$nb_points = $points_prono_nul;
?>
             <img src="images/1-gris.gif" alt="" title="<?php print "Points Hourra : ".$points_prono_domicile; ?>">
             <img src="images/N.gif" alt="" title="<?php print "Points Hourra : ".$points_prono_nul; ?>">
             <img src="images/2-gris.gif" alt="" title="<?php print "Points Hourra : ".$points_prono_visiteur; ?>">
<?php
	}
  	if ($prono=="2")
	{
		$nb_points = $points_prono_visiteur;
?>
             <img src="images/1-gris.gif" alt="" title="<?php print "Points Hourra : ".$points_prono_domicile; ?>">
             <img src="images/N-gris.gif" alt="" title="<?php print "Points Hourra : ".$points_prono_nul; ?>">
             <img src="images/2.gif" alt="" title="<?php print "Points Hourra : ".$points_prono_visiteur; ?>">
<?php
	}
  	if ($prono=="0")
	{
?>
             <img src="images/1-gris.gif" alt="" title="<?php print "Points Hourra : ".$points_prono_domicile; ?>">
             <img src="images/N-gris.gif" alt="" title="<?php print "Points Hourra : ".$points_prono_nul; ?>">
             <img src="images/2-gris.gif" alt="" title="<?php print "Points Hourra : ".$points_prono_visiteur; ?>">
<?php
	}
  	echo "</td>";
  
  	$pronos_exact=$row['pts_prono_exact']+$row['pts_prono_participation'];

  	echo "<td class=\"derniers_pronos_clubext\">$clubs_nom1</td>";
  	echo "<td class=\"derniers_pronos_score\">$row[2]-$row[3]</td>";
  	echo "<td class=\"derniers_pronos_picto\">";
  	if (($row[2]>$row[3] and $prono=="1") || ($row[2]==$row[3] and $prono=="N") || ($row[2]<$row[3] and $prono=="2"))
  	{
		echo "<img src=\"ico/top.png\" alt=\"\"></td><td class=\"derniers_pronos_points\">".$nb_points."/".$nb_points;
	}
  	elseif ($prono=='0')
  	{
		echo "</td><td class=\"derniers_pronos_points\">0";
	}
  	else 
  	{
		echo "<img src=\"ico/flop.png\" alt=\"\"></td><td>0/".$nb_points;
	}
  	echo "</td>";
  	echo "</tr>";
  	$i--;
}  
?>
					</table>
				</div>

			</div>
		</div>
	</div>
	<div class="rounded-block-bottom-left"></div>
	<div class="rounded-block-bottom-right"></div>
</div>

<script>
$(document).ready(function() {
	$('.derniers_pronos_prono img').tooltip({
		track: true,
		delay: 0,
		showURL: false
	});
});
</script>
