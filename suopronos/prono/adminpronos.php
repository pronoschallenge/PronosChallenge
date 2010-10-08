<?php

//***********************************************************************/
// Phpleague : gestionnaire de championnat                              */
// ============================================                         */
//                                                                      */
// Version : 0.82b                                                      */
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
<SCRIPT type="text/JavaScript">
<!--
var PL = new InitTab(64);
var ChN="N";
var Ch1="1";
var Ch2="2";

ImgN=new Image(10,14); ImgN.src="N.gif";
Img1=new Image(10,14); Img1.src="1.gif";
Img2=new Image(10,14); Img2.src="2.gif";

ImgNR=new Image(10,14); ImgNR.src="barre.gif";
Img1R=new Image(10,14); Img1R.src="barre.gif";
Img2R=new Image(10,14); Img2R.src="barre.gif";

function Change(match, res) {
	if (res==1) {
		eval("document.matchid.m"+match+"_0.src = ImgN.src");
		eval("document.matchid.m"+match+"_1.src = Img1R.src");
		eval("document.matchid.m"+match+"_2.src = Img2.src");
		eval("PL["+match+"]=Ch1;");
	} else if (res==2) {
		eval("document.matchid.m"+match+"_0.src = ImgN.src");
		eval("document.matchid.m"+match+"_1.src = Img1.src");
		eval("document.matchid.m"+match+"_2.src = Img2R.src");
		eval("PL["+match+"]=Ch2;");
	} else {
		eval("document.matchid.m"+match+"_0.src = ImgNR.src");
		eval("document.matchid.m"+match+"_1.src = Img1.src");
		eval("document.matchid.m"+match+"_2.src = Img2.src");
		eval("PL["+match+"]=ChN;");
	}
}

function InitTab(length) {
	this.length = length;
	for(i=1; i<=length; i++) this[i] = "";
	return this;
}

function ValideGrille(tot) {
	for (i=1; i<=tot; i++) {
		if (PL[i]!="") { if (PL[i]!="undefined"){
			eval("document.matchid.r_"+i+".value=PL["+i+"];");
		} else {eval("document.matchid.r_"+i+".value=undefined;");}}
		else {eval("document.matchid.r_"+i+".value=undefined;");}
	}
	document.matchid.submit();
	return;
}

function ShowHide(id)
{
	 var element = document.getElementById(id);
	 if (element.style.display != "block") 
	 {
        element.style.display = "block";
     }
	 else 
	 {
        element.style.display = "none";
     }
}

function toggleSuiteDiv(id,flagit) {
	if (flagit=="1"){
		if (document.getElementById) {
			document.getElementById('eqDom'+id+'').style.display = "block";	
			document.getElementById('eqExt'+id+'').style.display = "block";	
		} else if (document.all) {
			document.all['eqDom'+id+''].style.display = "block";
			document.all['eqExt'+id+''].style.display = "block";
		} else if (document.layers) {
			document.layers['eqDom'+id+''].display = "block";
			document.layers['eqExt'+id+''].display = "block";
		}
	} else {
		if (document.getElementById) {
			document.getElementById('eqDom'+id+'').style.display = "none";
			document.getElementById('eqExt'+id+'').style.display = "none";
		} else if (document.all) {
			document.all['eqDom'+id+''].style.display = "none";
			document.all['eqExt'+id+''].style.display = "none";
		} else if (document.layers) {
			document.layers['eqDom'+id+''].display = "none";
			document.layers['eqExt'+id+''].display = "none";
		}
	}
}

// -->
</SCRIPT>
<style type="text/css">
	.divLeft {
		position:absolute; 
		top: 220; 
		left: 350; 
		width:200; 
		display:none;
	}
	.divRight {
		position:absolute; 
		top: 220; 
		left: 550; 
		width:200; 
		display:none;
	}
</style>
<?php
	// nombre de matchs à afficher
	$nb_matchs=10;
	// calcul du match de debut de fin
	if (isset($_REQUEST['debut'])) {$debut=$_REQUEST['debut'];} else {$debut='';}
	if (empty ($debut) or $debut=="0") $debut=0; $apres=1;
	$fin = $debut+$nb_matchs;
		
    /*
	$id_last_journee="";
	$requete="SELECT phpl_matchs.id_journee  
				FROM phpl_matchs, phpl_journees, phpl_gr_championnats
				WHERE phpl_gr_championnats.id='$gr_champ'   
				AND phpl_matchs.buts_dom is not null
				AND phpl_matchs.buts_ext is not null	
				ORDER by phpl_matchs.date_reelle DESC LIMIT 0, 1";
	$resultat=mysql_query($requete) or die ("probleme " .mysql_error());
	while ($row=mysql_fetch_array($resultat))
    {
	    $id_last_journee=$row[0];
	}
	*/
	$id_last_journee="1";
	  
	// si l'action a effectuer est la validation des pronos...
	if ($action == "valid_pronos")
	{
	      
		for($i=1;$i<=$_REQUEST['nb_fiche'];$i++)
	    {   
	     	$nom_f_prono = "r_$i";
	    	$nom_id_match = "id_match_$i";
	
	     	if ($_REQUEST[$nom_f_prono]) {$f_prono[$i]=$_REQUEST[$nom_f_prono];}
	     	if ($_REQUEST[$nom_id_match]) {$id_match[$i]=$_REQUEST[$nom_id_match];}
	
	     	$requete="SELECT phpl_matchs.date_reelle FROM phpl_matchs WHERE phpl_matchs.id='$id_match[$i]'";
	     	$resultat=mysql_query($requete);
	
	       	while ($row= mysql_fetch_array($resultat))
	       	{      
	         	$date_relle=$row[0];
	       	}
	
	     	$requete="SELECT tps_avant_prono FROM phpl_gr_championnats WHERE id='$gr_champ'";
	     	$resultat=mysql_query($requete);
	
	       	while ($row= mysql_fetch_array($resultat))
	       	{
	         	$temps_avant_prono=$row[0];
	       	}
	
	     	$date_match_timestamp=format_date_timestamp($date_relle);
	     	$date_actuelle=time();
	
	     	if ($f_prono[$i] !== "undefined")
	     	{
	       		mysql_query("DELETE FROM phpl_pronostics WHERE pronostic=' '")or die ("probleme " .mysql_error());
	       		$requete = "SELECT * FROM phpl_matchs, phpl_pronostics, phpl_membres WHERE phpl_membres.id_prono='$user_id'
			                   AND phpl_membres.id=phpl_pronostics.id_membre
			                   AND phpl_pronostics.id_match=phpl_matchs.id
			                   AND phpl_pronostics.id_match='$id_match[$i]'";
	       		$resultat=mysql_query($requete);
	       		$nb_prono=mysql_num_rows($resultat);
	
	       		$requete = "SELECT id FROM phpl_membres WHERE id_prono='$user_id'";
	       		$resultat = mysql_query($requete);
	
	         	while ($row= mysql_fetch_array($resultat))
	         	{
	           		$id=$row["id"];
	         	}
	
	       		if ($nb_prono == "1")
	       		{
	         		if ($date_actuelle<($date_match_timestamp+$temps_avant_prono*60))
	         		{
	           			mysql_query("UPDATE phpl_pronostics SET pronostic='$f_prono[$i]'
				                        WHERE phpl_pronostics.id_membre='$id'
				                        AND phpl_pronostics.id_match='$id_match[$i]'") or die ("probleme " .mysql_error());
	         		}
	       		}
		       	if ($nb_prono == "0")
		       	{
		         	if ($date_actuelle<($date_match_timestamp+$temps_avant_prono*60))
		         	{
		           		mysql_query("INSERT INTO phpl_pronostics (id_membre, pronostic, id_match, id_champ) VALUES ('$id','$f_prono[$i]','$id_match[$i]', '$gr_champ')") or die ("probleme " .mysql_error());
		         	}
		       	}
		       	elseif ($nb_prono!= "1" and $nb_prono != "0") 
		       	{
			       	echo "erreur !<br />";
			    }
	     	}
	    }
	   
		// mise à jour du classement des pronos
		// effacement de tous les classements
		mysql_query("DELETE FROM phpl_clmnt_pronos WHERE id_champ='$gr_champ' AND type='general'") or die (mysql_error());
		
		// reconstruction du classmenet général
		$query="SELECT id_membre, pseudo, sum(points) as total, sum(participation) as participations FROM phpl_membres, phpl_pronostics
		WHERE id_champ='$gr_champ' AND id_membre=phpl_membres.id
		GROUP by pseudo
		ORDER by total, participations";
		
		$result=mysql_query ($query);
		while ($row=mysql_fetch_array($result))
		{
		
		mysql_query("INSERT INTO phpl_clmnt_pronos (id_champ, id_membre, pseudo, points, participation, type) values ('$gr_champ', '$row[0]', '$row[1]', '$row[2]', '$row[3]', 'general')") or die (mysql_error());
		}
		
		// reconstruction du classmenet du mois en cours
		/*$query="SELECT id_membre, pseudo, sum(points) as total, sum(participation) as participations
		FROM phpl_membres, phpl_pronostics, phpl_matchs
		WHERE id_champ='$gr_champ'
		AND id_membre=phpl_membres.id
		AND phpl_matchs.id=id_match
		AND MONTH (date_reelle) = MONTH (NOW())
		AND YEAR (date_reelle) = YEAR (NOW())
		GROUP by pseudo
		ORDER by total, participations";
		
		$result=mysql_query ($query) or die ("probleme " .mysql_error());
		while ($row=mysql_fetch_array($result))
		{
		mysql_query("INSERT INTO phpl_clmnt_pronos (id_champ, id_membre, pseudo, points, participation, type) values ('$gr_champ', '$row[0]', '$row[1]', '$row[2]', '$row[3]', 'mensuel_en_cours')") or die (mysql_error());
		}
		
		// reconstruction du classmenet des 30 derniers jours
		$query="SELECT id_membre, pseudo, sum(points) as total, sum(participation) as participations
		FROM phpl_membres, phpl_pronostics, phpl_matchs
		WHERE id_champ='$gr_champ'
		AND id_membre=phpl_membres.id
		AND phpl_matchs.id=id_match
		AND DATE_ADD(date_reelle, INTERVAL 30 DAY) >= NOW()
		GROUP by pseudo
		ORDER by total, participations";
		
		$result=mysql_query ($query) or die ("probleme " .mysql_error());
		while ($row=mysql_fetch_array($result))
		{
		mysql_query("INSERT INTO phpl_clmnt_pronos (id_champ, id_membre, pseudo, points, participation, type) values ('$gr_champ', '$row[0]', '$row[1]', '$row[2]', '$row[3]', 'mensuel_30_jours')") or die (mysql_error());
		}
		
		// reconstruction du classmenet hebdo
		$query="SELECT id_membre, pseudo, sum(points) as total, sum(participation) as participations
		FROM phpl_membres, phpl_pronostics, phpl_matchs
		WHERE id_champ='$gr_champ'
		AND id_membre=phpl_membres.id
		AND phpl_matchs.id=id_match
		AND DATE_ADD(date_reelle, INTERVAL 7 DAY) >= NOW()
		GROUP by pseudo
		ORDER by total, participations";
		
		$result=mysql_query ($query) or die ("probleme " .mysql_error());
		while ($row=mysql_fetch_array($result))
		{
		mysql_query("INSERT INTO phpl_clmnt_pronos (id_champ, id_membre, pseudo, points, participation, type) values ('$gr_champ', '$row[0]', '$row[1]', '$row[2]', '$row[3]', 'hebdo')") or die (mysql_error());
		}
		
		// reconstruction du classmenet de la derniere journee
		$query="SELECT id_membre, pseudo, sum(points) as total, sum(participation) as participations
		FROM phpl_membres, phpl_pronostics, phpl_matchs
		WHERE id_champ='$gr_champ'
		AND id_membre=phpl_membres.id
		AND phpl_matchs.id=id_match
		AND phpl_matchs.id_journee=$id_last_journee
		GROUP by pseudo
		ORDER by total, participations";
		
		$result=mysql_query ($query) or die ("probleme " .mysql_error());
		while ($row=mysql_fetch_array($result))
		{
		mysql_query("INSERT INTO phpl_clmnt_pronos (id_champ, id_membre, pseudo, points, participation, type) values ('$gr_champ', '$row[0]', '$row[1]', '$row[2]', '$row[3]', 'derniere_journee')") or die (mysql_error());
		}*/	
	    // fin mise à jour des pronos
		
	  	//echo "<table><tr><td align=\"center\"><div class=\"bleu\">".PRONO_GRILLE_CONFIRME."<br /><a href=\"index.php?page=pronos&amp;gr_champ=$gr_champ&amp;debut=$debut\">".RETOUR."</a> - <a href=\"index.php?page=pronos&amp;debut=$fin&amp;gr_champ=$gr_champ\">".PRONO_GRILLE_PROCHAINE."</a></div></td></tr></table>";
	  	echo "<table><tr><td align=\"center\"><div style=\"color: red; font-size:10pt; font-family: verdana;\"><b>".PRONO_GRILLE_CONFIRME."</b></div></td></tr></table>";
	}
	
	// si l'action a effectuer est l'affichage des pronos...
	//elseif ($action !== "valid_pronos")
	//{    
		/*if ($debut=="0") 
		{
			$prec="index.php?page=adminpronos&amp;gr_champ=$gr_champ";
		}
	    else 
	    {*/
		    $debut1=$debut-$nb_matchs; 
		    $prec="index.php?page=adminpronos&amp;debut=$debut1&amp;gr_champ=$gr_champ"; 
		//}
	    include("adminpronos.htm");
		
	    // requete pour récupérer les matchs à pronostiquer
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
	    $x=0;
	    $resultat=mysql_query($requete);
	    
	    if (mysql_num_rows($resultat)=="0") 
	    {
		    echo "<tr><td colspan=6 align=center><div class=\"blanc\">Journée Inexistante</div></td></tr>";
		}	
		
		
	    while ($row=mysql_fetch_array($resultat) and $i<$nb_matchs)
	    {
		    // nom du club domicile et du club exterieur
	    	$clubs_nom = stripslashes($row[0]);
	       	$clubs_nom1 = stripslashes($row[1]);
	       		       	
	       	// on regarde si le prono a déjà été pronostiqué
	       	$requete2= "SELECT pronostic FROM phpl_pronostics, phpl_membres WHERE phpl_pronostics.id_match='$row[2]' AND phpl_membres.id=phpl_pronostics.id_membre AND phpl_membres.id_prono='$user_id'";
	       	$resultat2=mysql_query($requete2) or die ("probleme " .mysql_error());
	       	$nb_pronos= mysql_num_rows($resultat2);
		
	       	if ($nb_pronos == "0") 
	       	{
		       	$prono="0";
		    }
		    
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

			// requete pour recuperer la serie de l'equipe a domicile
			$querySerie="SELECT phpl_journees.numero, cldom.nom, clext.nom, phpl_matchs.buts_dom, phpl_matchs.buts_ext, phpl_matchs.date_reelle, phpl_matchs.id
					        FROM phpl_equipes as dom, phpl_equipes as ext, phpl_matchs, phpl_journees, phpl_clubs as cldom  , phpl_clubs as clext, phpl_gr_championnats
					        WHERE phpl_matchs.id_equipe_dom=dom.id
					        AND phpl_matchs.id_equipe_ext=ext.id
					        AND (cldom.nom='$clubs_nom'
					        OR clext.nom='$clubs_nom')
					        AND phpl_journees.id_champ=phpl_gr_championnats.id_champ
					        AND phpl_gr_championnats.id='$gr_champ'
					        AND dom.id_club=cldom.id
					        AND ext.id_club=clext.id
					        AND phpl_matchs.id_journee=phpl_journees.id
					        AND phpl_matchs.buts_dom is not null
					        AND phpl_matchs.buts_ext is not null
					        ORDER BY phpl_journees.numero DESC
					        LIMIT 0,5";
					        				        
			$resultSerie=mysql_query($querySerie);
		        	
			$serieDom = "";
	        while ($rowSerie=mysql_fetch_array($resultSerie))
	        {
	        	$nbjournee=$rowSerie[0];
	        	$clubdom=$rowSerie[1];
	        	$clubext=$rowSerie[2];
	        	$nbbutsdom=$rowSerie[3];
	        	$nbbutsext=$rowSerie[4];
	        	
	        	
	            if ($nbbutsdom<>'' and $clubdom==$clubs_nom)
                {
                  if ($nbbutsdom>$nbbutsext) $serieDom = "V".$serieDom;
                  if ($nbbutsdom<$nbbutsext) $serieDom = "D".$serieDom;
                  if ($nbbutsdom==$nbbutsext) $serieDom = "N".$serieDom;
                }
                elseif($nbbutsext<>'' and $clubext==$clubs_nom)
                {
                  if ($nbbutsdom>$nbbutsext) $serieDom = "D".$serieDom;
                  if ($nbbutsdom<$nbbutsext) $serieDom = "V".$serieDom;
                  if ($nbbutsdom==$nbbutsext) $serieDom = "N".$serieDom;               
            	}
            	
	        }	
			
			while (strlen($serieDom)<5)
	        {
				$serieDom = $serieDom."-";
			}
	        //echo "<div id=\"eqDom$row[2]\" class=\"divLeft\">$serieDom</div>";
	        
			// requete pour recuperer la serie de l'equipe a l'exterieur
			$querySerie="SELECT phpl_journees.numero, cldom.nom, clext.nom, phpl_matchs.buts_dom, phpl_matchs.buts_ext, phpl_matchs.date_reelle, phpl_matchs.id
					        FROM phpl_equipes as dom, phpl_equipes as ext, phpl_matchs, phpl_journees, phpl_clubs as cldom  , phpl_clubs as clext, phpl_gr_championnats
					        WHERE phpl_matchs.id_equipe_dom=dom.id
					        AND phpl_matchs.id_equipe_ext=ext.id
					        AND (cldom.nom='$clubs_nom1'
					        OR clext.nom='$clubs_nom1')
					        AND phpl_journees.id_champ=phpl_gr_championnats.id_champ
					        AND phpl_gr_championnats.id='$gr_champ'
					        AND dom.id_club=cldom.id
					        AND ext.id_club=clext.id
					        AND phpl_matchs.id_journee=phpl_journees.id
					        AND phpl_matchs.buts_dom is not null
					        AND phpl_matchs.buts_ext is not null
					        ORDER BY phpl_journees.numero DESC
					        LIMIT 0,5";
					        				        
			$resultSerie=mysql_query($querySerie);
		        	
			$serieExt = "";
	        while ($rowSerie=mysql_fetch_array($resultSerie))
	        {
	        	$nbjournee=$rowSerie[0];
	        	$clubdom=$rowSerie[1];
	        	$clubext=$rowSerie[2];
	        	$nbbutsdom=$rowSerie[3];
	        	$nbbutsext=$rowSerie[4];
	        	
	        	
	            if ($nbbutsdom<>'' and $clubdom==$clubs_nom1)
                {
                  if ($nbbutsdom>$nbbutsext) $serieExt = "V".$serieExt;
                  if ($nbbutsdom<$nbbutsext) $serieExt = "D".$serieExt;
                  if ($nbbutsdom==$nbbutsext) $serieExt = "N".$serieExt;
                }
                elseif($nbbutsext<>'' and $clubext==$clubs_nom1)
                {
                  if ($nbbutsdom>$nbbutsext) $serieExt = "D".$serieExt;
                  if ($nbbutsdom<$nbbutsext) $serieExt = "V".$serieExt;
                  if ($nbbutsdom==$nbbutsext) $serieExt = "N".$serieExt;               
            	}
            	
	        }
			
			while (strlen($serieExt)<5)
	        {
				$serieExt = $serieExt."-";
			}
	        //$serieExt = "<table><tr>".$serieExt."</tr></table>";	
	        //echo "<div id=\"eqExt$row[2]\" class=\"divRight\">$serieExt</div>";		
			
			
			// requete pour recuperer le temps avant la fin du prono
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
	  	   	$class = "noir";		
		   
	  	   	// debut d'affichage de la ligne du prono (numero de la journee, date, club receveur)
	       	echo "<tr onMouseOver=\"toggleSuiteDiv('$row[2]',1)\" onMouseOut=\"toggleSuiteDiv('$row[2]',0)\">";
	       	echo "<td align=\"center\"><div class=$class>$row[4]</div></td>";
	       	echo "<td align=\"center\"><div class=$class>$date</div></td>";
	       	echo "<td align=\"center\" font=\"verdana\" size=\"1\"><div >$serieDom</div></td>"; 
	       	echo "<td align=\"right\"><div class=$class>$clubs_nom</div></td>";
	
	       	if ($ecart_heures>="0")
	       	{    
		       	$x++;
	         	echo"<td>";
	         	echo "<input type=\"hidden\" name=\"id_match_$x\" value=\"$row[2]\">";
?>

<style type="text/css">
<!--
.Style1 {
	font-size: 9px;
	font-family: Verdana, Arial, Helvetica, sans-serif;
}
-->
</style>
<INPUT type="hidden" value="1" name="r_<?php print $x;?>">
         <table  border="0" cellpadding="0" cellspacing="0" align="center" width="100%">
         <tr>
         <td height="22" align="center" class="<?php print $class; ?>">
<?php

             if ($prono=="0")
           {
             ?>
             <a href="javascript:Change(<?php print $x; ?>,1);"><img src="1.gif" border="no" name="m<?php print $x; ?>_1" alt=""></a>
             <a href="javascript:Change(<?php print $x; ?>,0);"><img src="N.gif" border="no" name="m<?php print $x; ?>_0" alt=""></a>
             <a href="javascript:Change(<?php print $x; ?>,2);"><img src="2.gif"  border="no" name="m<?php print $x;?>_2" alt=""></a>
             <?
           }

         if ($prono=="1")
           {
             ?>
             <a href="javascript:Change(<?php print $x; ?>,1);"><img src="barre.gif" border="no" name="m<?php print $x; ?>_1" alt=""></a>
             <a href="javascript:Change(<?php print $x; ?>,0);"><img src="N.gif" border="no" name="m<?php print $x; ?>_0" alt=""></a>
             <a href="javascript:Change(<?php print $x; ?>,2);"><img src="2.gif"  border="no" name="m<?php print $x;?>_2" alt=""></a>
             <?
           }

         if ($prono=="N")
           {
             ?>    
             <a href="javascript:Change(<?php print $x; ?>,1);"><img src="1.gif" border="no" name="m<?php print $x; ?>_1" alt=""></a>
             <a href="javascript:Change(<?php print $x; ?>,0);"><img src="barre.gif" border="no" name="m<?php print $x; ?>_0" alt=""></a>
             <a href="javascript:Change(<?php print $x; ?>,2);"><img src="2.gif"  border="no" name="m<?php print $x;?>_2" alt=""></a>
             <?
           }

         if ($prono=="2")
           {
             ?>
             <a href="javascript:Change(<?php print $x; ?>,1);"><img src="1.gif" border="no" name="m<?php print $x; ?>_1" alt=""></a>
             <a href="javascript:Change(<?php print $x; ?>,0);"><img src="N.gif" border="no" name="m<?php print $x; ?>_0" alt=""></a>
             <a href="javascript:Change(<?php print $x; ?>,2);"><img src="barre.gif"  border="no" name="m<?php print $x;?>_2" alt=""></a>
             <?
           }
         echo "</td></tr></table></td>";
               
       }

       else
       {
         echo "<td><table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" align=\"center\">\n";
         echo "<tr>\n";
         echo "<td width=\"45\" height=\"10\" valign=\"middle\" align=\"center\">\n";
         echo "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" align=\"center\" width=\"50\">\n<tr>\n<td>\n";

         if ($prono=="1")
           {
           ?>
             <img src="barre.gif" border="no" alt=""> <img src="N.gif" border="no" alt=""> <img src="2.gif"  border="no" alt="">
           <?php
           }

         if ($prono=="N")
           {
           ?>
             <img src="1.gif" border="no" alt=""> <img src="barre.gif" border="no" alt=""> <img src="2.gif"  border="no" alt="">
           <?php
           }

         if ($prono=="2")
         {
         ?>
             <img src="1.gif" border="no" alt=""> <img src="N.gif" border="no" alt=""> <img src="barre.gif"  border="no" alt="">
         <?php
         }
  
         if ($prono=="0")
         {
          ?> 
             <img src="1.gif" border="no" alt=""> <img src="N.gif" border="no" alt=""> <img src="2.gif"  border="no" alt="">
          <?php
         }
         echo "</td></tr></table>";
         echo "</td></tr></table></td>";
       }
  
       echo "<td><div class=\"blanc\">$clubs_nom1</div></td><td align=\"center\" font=\"verdana\" size=\"1\"><div >$serieExt</div></td><td align=center>"; 
  
       if ($ecart_heures>48) echo "<div class=\"blanc\">$ecart_jours jours</div>";
       elseif ($ecart_heures>0) echo "<div class=\"blanc\">$ecart_heures h</div>";
       elseif ($ecart_heures == 0) echo "<div class=\"blanc\">$ecart_minutes min</div>";
       else {echo"<div class=\"blanc\">".PRONO_GRILLE_EXPIRE."</div>";}
       echo "</td>";
       echo "</tr>";
       $i++;
      }
?>

<tr><td colspan="8" align="center">
<input type="hidden" name="action" value="valid_pronos">
<input type="hidden" name="nb_fiche" value="<?php print $x; ?>">


<input type="hidden" name="debut" value="<?php print $debut; ?>">
<a href="javascript:ValideGrille(<?php print $x; ?>);"><img src="images/ok.gif" alt="" width="25" height="23" border="0"></a>
          
         
</td>
</tr>
</table>
</form>
</td>
</tr>
</table>

<?php

	//} // fin de l'action d'affichage des pronos

?>
