
<div class="bloc bloc_tendances">
	<div class="rounded-block-top-left"></div>
	<div class="rounded-block-top-right"></div>
	<div class="rounded-outside">
		<div class="rounded-inside">
			<div class="bloc_entete">
				<div class="bloc_icone"></div>
				<div class="bloc_titre">Tendances</div>
			</div>
			<div class="bloc_contenu">
			
  				<table border="0" cellpadding="0" cellspacing="0" width="92%" align="center">
    				<tr>
    					<td class="compclair">
    						<BR>
    							<table border="0" cellpadding="0" cellspacing="0" width="100%" align="center">
                              		<tr>
                              			<td width=22%>&nbsp;</td>
                              			<td width=55% style="text-align: center;font-size: 11pt;font-weight: bold;padding-bottom: 10px;">
                                    		Répartition des pronostics
                              			</td>
                              			<td width=22%>&nbsp;</td>
                              			<td width=5%></td>
                              		</tr>
                              		<tr>
                              			<td width=22%>&nbsp;</td>
                              			<td width=45%>
											<table border=0 width=100%>
												<tr>
													<td class="tendance1" width=33% align=center>1</td>
													<td class="tendanceN" width=33% align=center>N</td>
													<td class="tendance2" width=33% align=center>2</td>
												</tr>
											</table>
                              			</td>
                              			<td width=22%>&nbsp;</td>
                              			<td width=5% style="text-align: center;font-weight: bold;">Parieurs</td>
                              		</tr>
<?php
//Général
$query="SELECT DISTINCT C.nom, D.nom, P.pronostic, count( * ) , id_journee
FROM phpl_pronostics P, phpl_matchs M, phpl_equipes E, phpl_equipes F, phpl_clubs C, phpl_clubs D
WHERE M.buts_dom IS NULL
AND M.buts_ext IS NULL
AND P.id_champ !=0
AND P.id_match !=0
AND M.id = P.id_match
AND M.id_equipe_dom = E.id
AND M.id_equipe_ext = F.id
AND E.id_club = C.id
AND F.id_club = D.id
GROUP BY P.id_match, P.pronostic
ORDER BY M.date_reelle, P.id_match, P.pronostic";

$result=mysql_query($query) or die ("probleme " .mysql_error());
$eqid1="xxx";
$eqid2="YYY";
$totalparieur=0;
$totalparieur1=0;
$totalparieurN=0;
$totalparieur2=0;
$premiertour=0;
$stopjournee=0;

while ($row=mysql_fetch_array($result))
{
	if ($premiertour=="0") 
   	{
		$stopjournee=$row[4];
	}
 	if ($row[4]==$stopjournee)
	{
 		if ($eqid1==$row[0] and $eqid2==$row[1])
   		{
     		if ($row[2]=="1")
        	{
				$totalparieur1=$row[3];
			}
     		else if ($row[2]=="N")
        	{
				$totalparieurN=$row[3];
			}
     		else 
     		{
				$totalparieur2=$row[3];
			}
     		$totalparieur=$totalparieur+$row[3];
   		}
 		else 
   		{
     		if ($premiertour==1) 
     		{  
				if($totalparieur > 0) 
				{
					$longueur1=round($totalparieur1/$totalparieur*100, 2);
         			$longueurN=round($totalparieurN/$totalparieur*100, 2);
         			$longueur2=round($totalparieur2/$totalparieur*100, 2);			
				} 
				else 
				{
					$longueur1 = 0;
					$longueurN = 0;
					$longueur2 = 0;
				} 	        

         		echo "<tr><td style=\"text-align:right;\"> $eqid1 &nbsp;&nbsp;</td><td><center><table border=0 width=100%><tr> ";
         		if ($longueur1!="0")
         		{
					echo "<td class=\"tendance1\" width=\"$longueur1\%\" title=\"$longueur1%\"></td>";
				}
         		if ($longueurN!="0")
         		{
					echo "<td class=\"tendanceN\" width=\"$longueurN\%\" title=\"$longueurN%\"></td>";
				}
         		if ($longueur2!="0")
         		{
					echo "<td class=\"tendance2\" width=\"$longueur2\%\" title=\"$longueur2%\"></td>";
				}
         		echo "</tr></table></td><td style=\"text-align:left;\">&nbsp;&nbsp; $eqid2 </td><td align=center>&nbsp;&nbsp;$totalparieur</td></tr>";
     		}
     		$eqid1=$row[0];
     		$eqid2=$row[1];
     		$totalparieur=0;
     		$totalparieur1=0;
     		$totalparieurN=0;
     		$totalparieur2=0;
     		if ($row[2]=="1")
        	{
				$totalparieur1=$row[3];
			}
     		else if ($row[2]=="N")
        	{
				$totalparieurN=$row[3];
			}
     		else 
     		{
				$totalparieur2=$row[3];
			}
     		$totalparieur=$totalparieur+$row[3];
   		}
   		$premiertour=1;   
	}
}

if($totalparieur > 0) 
{
	$longueur1=round($totalparieur1/$totalparieur*100, 2);
    $longueurN=round($totalparieurN/$totalparieur*100, 2);
    $longueur2=round($totalparieur2/$totalparieur*100, 2);	
    
    echo "<tr><td style=\"text-align:right;\"> $eqid1 &nbsp;&nbsp;</td><td><center><table border=0 width=100%><tr> ";
    if ($longueur1!="0")
    {
		echo "<td class=\"tendance1\" width=\"$longueur1\%\" title=\"$longueur1%\"></td>";
	}
    if ($longueurN!="0")
    {
		echo "<td class=\"tendanceN\" width=\"$longueurN\%\" title=\"$longueurN%\"></td>";
	}
    if ($longueur2!="0")
    {
		echo "<td class=\"tendance2\" width=\"$longueur2\%\" title=\"$longueur2%\"></td>";
	}
    echo "</tr></table></td><td style=\"text-align:left;\">&nbsp;&nbsp; $eqid2 </td><td align=center>&nbsp;&nbsp;$totalparieur</td></tr></table><br>";
}
else
{
	echo "<tr><td></td><td align=\"center\"<br /><br /><i>Aucun pronostic n'a été effectué...</i><br /><br /><br /><br /></td><td></td><td></td></tr>";
} 
		
?>

</td></tr></table>

			</div>
		</div>
	</div>
	<div class="rounded-block-bottom-left"></div>
	<div class="rounded-block-bottom-right"></div>
</div>	
<script>
$(document).ready(function() {
	$('.tendance1, .tendanceN, .tendance2').tooltip({
		track: true,
		delay: 0,
		showURL: false
	});
});
</script>
