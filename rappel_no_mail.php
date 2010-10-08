<html>
<head>

</head>

<body>
<?
	$inDateAff = 0;
?>
<table class=phpl width="80%">
            <tr>
              <td class=phpl2 align="center" colspan="3">Matchs non pronostiqués</td>
            </tr>
            <tr>
            <td align="left">
            
            <b>Matchs du</b> 
            <select id="date" onchange="javascript:window.location='index.php?page=rappel_no_mail&date='+this.options[this.selectedIndex].value">
            	<?
            		$intDate = time();
           
            		for($i=0;$i<31;$i++) {
	            		echo "<option value=\"".date('d/m/Y', $intDate)."\"";
	            		if($date=="" && $i==1) {
		            		echo " selected";
	            		} else if($date!="" && $date==date('d/m/Y', $intDate)) {
		            		echo " selected";
		            		$intDateAff = $intDate;
	            		}
	            		echo ">";
	            		echo date('d/m/Y', $intDate);
	            		echo "</option>";
	            		$intDate += 86400;
            		}
            		echo "<br /><br />";
            	?>
            </select>
<?php
//include ("tps1.php3");   

// calcul de la date de demain
if($date=="") {
	// par défaut on prend la date de demain
	$intDateAff = time () + 86400;
}
//$date_demain=date('Y-m-d G:i:s', $intDemain);
$date_affichee=date('Y-m-d', $intDateAff);
$date_affichee_debut=date('Y-m-d 00:00:00', $intDateAff);
$date_affichee_fin=date('Y-m-d 23:59:59', $intDateAff);

// on vérifie qu'il y ait des pronos à faire pour le lendemain
			
$queryMatchs="SELECT phpl_matchs.id, phpl_clubs.nom, CLEXT.nom, phpl_matchs.date_reelle, phpl_journees.numero
		    FROM phpl_clubs, phpl_clubs as CLEXT, phpl_matchs, phpl_journees, phpl_equipes, phpl_equipes as EXT, phpl_gr_championnats
		    WHERE phpl_clubs.id=phpl_equipes.id_club
		    AND CLEXT.id=EXT.id_club
		    AND phpl_equipes.id=phpl_matchs.id_equipe_dom
		    AND EXT.id=phpl_matchs.id_equipe_ext
		    AND phpl_matchs.id_journee=phpl_journees.id
		    AND phpl_journees.id_champ=phpl_gr_championnats.id_champ
		    AND phpl_gr_championnats.id='7'
		    AND phpl_matchs.buts_dom is null
		    AND phpl_matchs.buts_ext is null
		    AND phpl_clubs.nom!='exempte'
		    AND CLEXT.nom!='exempte'
		    AND phpl_matchs.date_reelle>='$date_affichee_debut' 
		    AND phpl_matchs.date_reelle<='$date_affichee_fin'
		    ORDER by phpl_matchs.date_reelle, phpl_clubs.nom";
				    			
$resultMatchs=mysql_query ($queryMatchs);
if(mysql_num_rows($resultMatchs)==0)
{
	echo "aucun match ce jour là...";
}
else
{ 
	// si il y a des pronos demain...
	$tabRowsMatchs = array();
	while ($rowMatchs=mysql_fetch_array($resultMatchs))
	{	
		array_push($tabRowsMatchs, $rowMatchs);
	}
	
	// on récupère tous les pronostiqueurs
	$queryUsers="SELECT id, pseudo, mail, actif FROM phpl_membres";
	$resultUsers=mysql_query ($queryUsers);
	while ($rowUsers=mysql_fetch_array($resultUsers))
	{ 
		// si il est actif...
		if($rowUsers[3]=='1') 
		{
			// pour chaque utilisateur...
			$rappel = false;
			echo "<span style=\"font-family: Verdana; font-size: 10pt\">";
			echo "<br>Matchs non pronostiqués de <b>".$rowUsers[1]."</b> : <br>";
			echo "<ul>";
			for($i=0; $i<count($tabRowsMatchs); $i++)
			{
				$query3="SELECT * FROM phpl_pronostics WHERE id_match=".$tabRowsMatchs[$i][0]." AND id_membre=".$rowUsers[0]." AND pronostic IS NOT NULL";
				$result3=mysql_query ($query3);
				if(mysql_num_rows($result3)==0)
				{
					$rappel = true;
					echo "<li><span style=\"color:red\">".$tabRowsMatchs[$i][1]." - ".$tabRowsMatchs[$i][2]."</span></li>";
				}		
			}
						
			
			
			if($rappel==false) 
			{
				echo "<li>aucun</li>";		
			}
			
			echo "</ul></span>";
		}
	}
}



?>
</body>
</td></tr></table>
