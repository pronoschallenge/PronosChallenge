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

	// nombre de matchs à afficher
	$nb_matchs=10;
	// calcul du match de debut de fin
	if (isset($_REQUEST['debut'])) {$debut=$_REQUEST['debut'];} else {$debut='';}
	if (empty ($debut) or $debut=="0") $debut=0; $apres=1;
	$fin = $debut+$nb_matchs;
	
	// si l'action a effecuer est un reset...
	if ($action == "reset")
	{
		$requete="SELECT tps_avant_prono FROM phpl_gr_championnats WHERE id='$gr_champ'";
		$resultat=mysql_query($requete);
		while ($row = mysql_fetch_array($resultat))
		{
	    	$temps_avant_prono=$row[0];
	    }
		$date_actuelle=time();
		$requete = "SELECT id FROM phpl_membres WHERE id_prono='$user_id'";
		$resultat = mysql_query($requete);
		$fin = $debut+$nb_matchs;
        while ($row= mysql_fetch_array($resultat))
        {
          $id=$row["id"];
        }
		$requete="SELECT phpl_matchs.id
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
		          ORDER by phpl_matchs.date_reelle, phpl_clubs.nom
		          LIMIT $debut, $fin ";
	  	$resultat=mysql_query($requete) or die ("probleme " .mysql_error());
      	while ($row=mysql_fetch_array($resultat))
      	{
			$requete1="SELECT phpl_matchs.date_reelle FROM phpl_matchs WHERE phpl_matchs.id='$row[0]'";
        	$resultat1=mysql_query($requete1);
         	while ($row1= mysql_fetch_array($resultat1))
         	{
           		$date_relle=$row1[0];
         	}
        	$date_match_timestamp=format_date_timestamp($date_relle);

        	if ($date_actuelle<($date_match_timestamp+$temps_avant_prono*60))
        	{
           		mysql_query("UPDATE  phpl_pronostics SET pronostic='0' WHERE id_match='$row[0]' AND id_membre='$id'");
           	}
           	mysql_query("DELETE FROM phpl_pronostics WHERE pronostic='0'")or die ("probleme " .mysql_error());
        }
	}
	
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

	if ($debut=="0") 
	{
		$prec="index.php?page=derniers_pronos&amp;gr_champ=$gr_champ";
	}
	else 
	{
		$debut1=$debut-$nb_matchs; 
		$prec="index.php?page=pronos&amp;debut=$debut1&amp;gr_champ=$gr_champ"; 
	}
?>

<SCRIPT type="text/JavaScript">
<!--
var PL = new InitTab(64);
var ChN="N";
var Ch1="1";
var Ch2="2";
var TotPtsHourra = 0;

ImgN=new Image(10,14); ImgN.src="images/N-gris.gif";
Img1=new Image(10,14); Img1.src="images/1-gris.gif";
Img2=new Image(10,14); Img2.src="images/2-gris.gif";

ImgNR=new Image(10,14); ImgNR.src="images/N.gif";
Img1R=new Image(10,14); Img1R.src="images/1.gif";
Img2R=new Image(10,14); Img2R.src="images/2.gif";

function Change(match, res, pts1, ptsN, pts2) {
	var ptsHourra = 0;
	var ptsAncProno = 0

	if(eval("document.matchid.m"+match+"_1.src") == Img1R.src)
	{
		ptsAncProno=pts1;
	}else if (eval("document.matchid.m"+match+"_2.src") == Img2R.src){
		ptsAncProno=pts2;
	}else if (eval("document.matchid.m"+match+"_0.src") == ImgNR.src){
		ptsAncProno=ptsN;
	}	
	
	if (res==1) {
		eval("document.matchid.m"+match+"_0.src = ImgN.src");
		eval("document.matchid.m"+match+"_1.src = Img1R.src");
		eval("document.matchid.m"+match+"_2.src = Img2.src");
		eval("PL["+match+"]=Ch1;");
		ptsHourra=pts1;

	} else if (res==2) {
		eval("document.matchid.m"+match+"_0.src = ImgN.src");
		eval("document.matchid.m"+match+"_1.src = Img1.src");
		eval("document.matchid.m"+match+"_2.src = Img2R.src");
		eval("PL["+match+"]=Ch2;");
		ptsHourra=pts2;
	} else{
		eval("document.matchid.m"+match+"_0.src = ImgNR.src");
		eval("document.matchid.m"+match+"_1.src = Img1.src");
		eval("document.matchid.m"+match+"_2.src = Img2.src");
		eval("PL["+match+"]=ChN;");
		ptsHourra=ptsN;
	}

	TotPtsHourra = TotPtsHourra  + ptsHourra - ptsAncProno
	document.getElementById("nbPtsHourra").innerHTML = "<font face=verdana size=1><b>AU TOTAL VOUS POUVEZ GAGNER " + TotPtsHourra + " POINTS AU CLASSEMENT HOURRA !</b></font>";
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

<div class="bloc bloc_pronos_classement">
	<div class="rounded-block-top-left"></div>
	<div class="rounded-block-top-right"></div>
	<div class="rounded-outside">
		<div class="rounded-inside">
			<div class="bloc_entete">
				<div class="bloc_icone"></div>
				<div class="bloc_titre">Classement</div>
			</div>
			<div class="bloc_contenu">
				<div id="pronos_classement" name="pronos_classement" style="text-align:center"></div>
			</div>
		</div>
	</div>
	<div class="rounded-block-bottom-left"></div>
	<div class="rounded-block-bottom-right"></div>
</div>			

<div class="bloc bloc_pronos_journees">
	<div class="rounded-block-top-left"></div>
	<div class="rounded-block-top-right"></div>
	<div class="rounded-outside">
		<div class="rounded-inside">
			<div class="bloc_entete">
				<div class="bloc_icone"></div>
				<div class="bloc_titre">Résultats</div>
			</div>
			<div class="bloc_contenu">
				<div id="journee" class="journee"></div>
			</div>
		</div>
	</div>
	<div class="rounded-block-bottom-left"></div>
	<div class="rounded-block-bottom-right"></div>
</div>	

<div class="bloc bloc_pronos">
	<div class="rounded-block-top-left"></div>
	<div class="rounded-block-top-right"></div>
	<div class="rounded-outside">
		<div class="rounded-inside">
			<div class="bloc_entete">
				<div class="bloc_icone"></div>
				<div class="bloc_titre">Pronostics</div>
			</div>
			<div class="bloc_contenu">

				<div id="pronos_pagination">
					<div id="matchs_precedents">
						<a href="<?php print $prec; ?>"><img src="ico/g.png" border="0" align="absmiddle"/></a>
						<a href="<?php print $prec; ?>"><?php echo PRONO_GRILLE_PRECEDENT; ?></a>
					</div>
					<div id="matchs_suivants">
						<a href="index.php?page=pronos&amp;debut=<?php print $fin; ?>&amp;gr_champ=<?php print $gr_champ;?>"><?php echo PRONO_GRILLE_SUIVANT; ?></a>
						<a href="index.php?page=pronos&amp;debut=<?php print $fin; ?>&amp;gr_champ=<?php print $gr_champ;?>"><img src="ico/d.png" border="0" align="absmiddle"/></a>
					</div>
				</div>

<?php
	      
	// si l'action a effectuer est la validation des pronos...
	if ($action == "valid_pronos")
	{
	      	// pour chaque match pronostiqué...
		for($i=1;$i<=$_REQUEST['nb_fiche'];$i++)
	    	{   
	     	$nom_f_prono = "r_$i";
	    	$nom_id_match = "id_match_$i";
	
	     	if ($_REQUEST[$nom_f_prono]) {$f_prono[$i]=$_REQUEST[$nom_f_prono];}
	     	if ($_REQUEST[$nom_id_match]) {$id_match[$i]=$_REQUEST[$nom_id_match];}
	
		// on récupère la date du match
	     	$requete="SELECT phpl_matchs.date_reelle FROM phpl_matchs WHERE phpl_matchs.id='$id_match[$i]'";
	     	$resultat=mysql_query($requete);
	
	       	while ($row= mysql_fetch_array($resultat))
	       	{      
	         	$date_relle=$row[0];
	       	}
	
		// on récupère le temps avant l'expiration des pronos
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
	
	       		// on prend en compte le prono si la date d'expiration n'est pas passée
	         	if ($date_actuelle<($date_match_timestamp+$temps_avant_prono*60))
	         	{	
				// si l'utilisateur avait déjà pronostiqué ce match...
		       		if ($nb_prono == "1")
		       		{
	           			mysql_query("UPDATE phpl_pronostics SET pronostic='$f_prono[$i]'
				                        WHERE phpl_pronostics.id_membre='$id'
				                        AND phpl_pronostics.id_match='$id_match[$i]'") or die ("probleme " .mysql_error());
		       		}
			       	if ($nb_prono == "0")
			       	{
		           		mysql_query("INSERT INTO phpl_pronostics (id_membre, pronostic, id_match, id_champ) VALUES ('$id','$f_prono[$i]','$id_match[$i]', '$gr_champ')") or die ("probleme " .mysql_error());
			       	}
			       	elseif ($nb_prono!= "1" and $nb_prono != "0") 
			       	{
				       	echo "erreur !<br />";
			    	}
			}
	     	}
	    }
	   
	   	//************************************//
		// mise à jour du classement des pronos //
		//************************************//
		
		// effacement du classement de l'utilisateur
		//mysql_query("DELETE FROM phpl_clmnt_pronos WHERE id_champ='$gr_champ' AND type='general' AND id_membre='$id'") or die (mysql_error());
		
		// reconstruction du classement général
		/*
		$query="SELECT id_membre, pseudo, sum(points) as total, sum(participation) as participations FROM phpl_membres, phpl_pronostics
		WHERE id_champ='$gr_champ' AND id_membre=phpl_membres.id AND id_membre='$id' 
		GROUP by pseudo
		ORDER by total, participations";
		
		$result=mysql_query ($query);
		while ($row=mysql_fetch_array($result))
		{	
			mysql_query("INSERT INTO phpl_clmnt_pronos (id_champ, id_membre, pseudo, points, participation, type) values ('$gr_champ', '$row[0]', '$row[1]', '$row[2]', '$row[3]', 'general')") or die (mysql_error());
		}
		*/
		
		// récupération du nombre de participation du user pour les classements "non dernière journée"
		$query="SELECT id_membre, pseudo, sum(participation) as participations FROM phpl_membres, phpl_pronostics
		WHERE id_champ='$gr_champ' AND id_membre=phpl_membres.id AND id_membre='$id' 
		GROUP by pseudo";
		
		$result=mysql_query ($query);
		if ($row=mysql_fetch_array($result))
		{	
			mysql_query("UPDATE phpl_clmnt_pronos SET participation='$row[2]' WHERE id_champ='$gr_champ' AND id_membre='$id' AND (type!='derniere_journee' AND type!='hourra_derniere_journee')") or die (mysql_error());
		}
		/* A FINIR !!! -> ce cas est assez rare finalement :)
		else // -> l'utilisateur n'avait encore jamais pronostiqué sur ce championnat
		{
			// insertion de l'utilisateur dans le classement général
		
			// récupération du dernier du classement général
			$query="SELECT id_membre, pseudo, place, points FROM phpl_clmnt_pronos
			WHERE id_champ='$gr_champ' AND type='general' AND points=(SELECT MIN(points) FROM phpl_clmnt_pronos WHERE id_champ='$gr_champ' AND type='general')";
		
			$result=mysql_query ($query);
			if ($row=mysql_fetch_array($result))
			{
				$place = 0;
				if($row[3]==0)
				{
					$place = $row[2];
				}		
				else
				{
					$place = $row[2]+1;
				}
				
				mysql_query("INSERT INTO phpl_clmnt_pronos (id_champ, id_membre, pseudo, place, points, participation, type) values ('$gr_champ', '$id', '$row[1]', '$row[2]', '$row[3]', 'general')") or die (mysql_error());
			}
		}	
		*/

		// récupération du nombre de participation du user pour les classements "dernière journée"
		$query="SELECT id_membre, pseudo, sum(participation) as participations FROM phpl_membres, phpl_pronostics
		WHERE id_champ='$gr_champ' AND id_membre=phpl_membres.id AND id_membre='$id' 
		AND phpl_matchs.id=id_match
		AND phpl_matchs.id_journee=$id_last_journee		
		GROUP by pseudo";
		
		$result=mysql_query ($query);
		if ($result != null && $row=mysql_fetch_array($result))
		{	
			mysql_query("UPDATE phpl_clmnt_pronos SET participation='$row[2]' WHERE id_champ='$gr_champ' AND id_membre='$id' AND (type='derniere_journee' OR type='hourra_derniere_journee')") or die (mysql_error());
		}			
		
		// Pas de mise à jour de la colonne 'place' car le classement ne peut pas changer par rapport au nombre de pronos

		
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
		}*/
		
	    // fin mise à jour des pronos
		
	  	//echo "<table><tr><td align=\"center\"><div class=\"bleu\">".PRONO_GRILLE_CONFIRME."<br /><a href=\"index.php?page=pronos&amp;gr_champ=$gr_champ&amp;debut=$debut\">".RETOUR."</a> - <a href=\"index.php?page=pronos&amp;debut=$fin&amp;gr_champ=$gr_champ\">".PRONO_GRILLE_PROCHAINE."</a></div></td></tr></table>";
	  	echo "<div id=\"pronos_message_confirmation\">";
	  	echo "<div><img src=\"ico/top.png\" align=\"absmiddle\"/>&nbsp;<b>".PRONO_GRILLE_CONFIRME."</b></div>";
	  	echo "<div>".PRONO_GRILLE_CONFIRME_SUITE."</div>";
	  	echo "</div>";
	}

?>

				<div id="pronos">
					<form id="matchid" name="matchid" action="index.php?page=pronos&amp;gr_champ=<?php print $gr_champ;?>" method="post">
						<table cellspacing="0">
							<tr>
								<th>
									<?php echo ADMIN_COHERENCE_MSG2; ?>
								</th>
								<th>
									<?php echo DATE; ?>
								</th>
								<th></th>
								<th></th>
								<th>
									<?php echo PRONO_GRILLE_PRONO; ?>
								</th>
								<th></th>
								<th></th>
								<th>
									<?php echo PRONO_GRILLE_TEMPS; ?>
								</th>
							</tr>

<?php	
	// Affichage des pronos 
		
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
	        //echo "<div id=\"eqDom$row[2]\">$serieDom</div>";
	        
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
		 $nb_pts_1 = 0;
		 $nb_pts_N = 0;
		 $nb_pts_2 = 0; 
		 $nb_pts_1_prono_nok = 0;
		 $nb_pts_1_prono_ok  = 0;
		 $nb_pts_N_prono_nok = 0;
		 $nb_pts_N_prono_ok  = 0;
		 $nb_pts_2_prono_nok = 0;
		 $nb_pts_2_prono_ok  = 0;

         if ($nb_parieurs1=="0")
		 {
			$points_prono_domicile="---";
			$nb_pts_1_prono_nok=floor(($points_prono_exact*$nb_parieurs)/1);
			
		 }
		 else
		 {
			//$points_prono_domicile=floor(($points_prono_exact*$nb_parieurs)/($nb_parieurs1));
			$nb_pts_1_prono_ok = floor(($points_prono_exact*$nb_parieurs)/($nb_parieurs1));
			$nb_pts_1_prono_nok = floor(($points_prono_exact*$nb_parieurs)/($nb_parieurs1+1));
		  }
		 
		 if ($nb_parieursN=="0")
		 {
			$points_prono_nul="---";
			$nb_pts_N_prono_nok=floor(($points_prono_exact*$nb_parieurs)/1);
		 }
		 else
		 {
			//$points_prono_nul=floor(($points_prono_exact*$nb_parieurs)/($nb_parieursN));
		  	$nb_pts_N_prono_ok = floor(($points_prono_exact*$nb_parieurs)/($nb_parieursN));
			$nb_pts_N_prono_nok = floor(($points_prono_exact*$nb_parieurs)/($nb_parieursN+1));
		 }
		 
		 if ($nb_parieurs2=="0")
		 {
			$points_prono_visiteur="---";
			$nb_pts_2_prono_nok=floor(($points_prono_exact*$nb_parieurs)/1);
		 }
		 else
         {
			//$points_prono_visiteur=floor(($points_prono_exact*$nb_parieurs)/($nb_parieurs2));
		    $nb_pts_2_prono_ok = floor(($points_prono_exact*$nb_parieurs)/($nb_parieurs2));
			$nb_pts_2_prono_nok = floor(($points_prono_exact*$nb_parieurs)/($nb_parieurs2+1));
  		 }
	  	   	
	  	   			   
	  	   	// debut d'affichage de la ligne du prono (numero de la journee, date, club receveur)
	       	//echo "<tr onMouseOver=\"toggleSuiteDiv('$row[2]',1)\" onMouseOut=\"toggleSuiteDiv('$row[2]',0)\">";
	       	echo "<tr>";
	       	echo "<td class=\"pronos_journee\">$row[4]</td>";
	       	echo "<td class=\"pronos_date\">$date</td>";
	       	echo "<td class=\"pronos_seriedom\">$serieDom</td>"; 
	       	echo "<td class=\"pronos_clubdom\">$clubs_nom</td>";
	
	       	if ($ecart_heures>="0")
	       	{    
		       	$x++;
?>

<td class="pronos_prono">
	<input type="hidden" name="id_match_<?php print $x?>" value="<?php print $row[2]?>">
	<INPUT type="hidden" value="1" name="r_<?php print $x;?>">

<?php
	// le pronostic n'a pas été fait
	if ($prono=="0")
    {
		$nb_pts_1=$nb_pts_1_prono_nok;
		$nb_pts_N=$nb_pts_N_prono_nok;
		$nb_pts_2=$nb_pts_2_prono_nok;
?>
             	<a href="javascript:Change(<?php print $x; ?>,1,<?php print $nb_pts_1; ?>,<?php print $nb_pts_N; ?>,<?php print $nb_pts_2; ?>);"><img src="images/1-gris.gif" border="no" name="m<?php print $x; ?>_1" alt="" title="<?php print "Points Hourra : ".$nb_pts_1; ?>"></a>
             	<a href="javascript:Change(<?php print $x; ?>,0,<?php print $nb_pts_1; ?>,<?php print $nb_pts_N; ?>,<?php print $nb_pts_2; ?>);"><img src="images/N-gris.gif" border="no" name="m<?php print $x; ?>_0" alt="" title="<?php print "Points Hourra : ".$nb_pts_N; ?>"></a>
             	<a href="javascript:Change(<?php print $x; ?>,2,<?php print $nb_pts_1; ?>,<?php print $nb_pts_N; ?>,<?php print $nb_pts_2; ?>);"><img src="images/2-gris.gif"  border="no" name="m<?php print $x;?>_2" alt="" title="<?php print "Points Hourra : ".$nb_pts_2; ?>"></a>
<?php
	}
	// victoire à domicile
    else if ($prono=="1")
    {
		$nb_pts_1=$nb_pts_1_prono_ok;
		$nb_pts_N=$nb_pts_N_prono_nok;
		$nb_pts_2=$nb_pts_2_prono_nok;
?>
			 	<script>
             		TotPtsHourra = TotPtsHourra + <?php print $nb_pts_1; ?>
			 	</script>	
             	<a href="javascript:Change(<?php print $x; ?>,1,<?php print $nb_pts_1; ?>,<?php print $nb_pts_N; ?>,<?php print $nb_pts_2; ?>);"><img src="images/1.gif" border="no" name="m<?php print $x; ?>_1" alt="" title="<?php print "Points Hourra : ".$nb_pts_1; ?>"></a>
             	<a href="javascript:Change(<?php print $x; ?>,0,<?php print $nb_pts_1; ?>,<?php print $nb_pts_N; ?>,<?php print $nb_pts_2; ?>);"><img src="images/N-gris.gif" border="no" name="m<?php print $x; ?>_0" alt="" title="<?php print "Points Hourra : ".$nb_pts_N; ?>"></a>
             	<a href="javascript:Change(<?php print $x; ?>,2,<?php print $nb_pts_1; ?>,<?php print $nb_pts_N; ?>,<?php print $nb_pts_2; ?>);"><img src="images/2-gris.gif"  border="no" name="m<?php print $x;?>_2" alt="" title="<?php print "Points Hourra : ".$nb_pts_2; ?>"></a>
<?php
	}
	// match nul
    else if ($prono=="N")
    {
		$nb_pts_1=$nb_pts_1_prono_nok;
		$nb_pts_N=$nb_pts_N_prono_ok;
		$nb_pts_2=$nb_pts_2_prono_nok;
?>
			 	<script>
             		TotPtsHourra = TotPtsHourra + <?php print $nb_pts_N; ?>
             	</script>	     
             	<a href="javascript:Change(<?php print $x; ?>,1,<?php print $nb_pts_1; ?>,<?php print $nb_pts_N; ?>,<?php print $nb_pts_2; ?>);"><img src="images/1-gris.gif" border="no" name="m<?php print $x; ?>_1" alt="" title="<?php print "Points Hourra : ".$nb_pts_1; ?>"></a>
             	<a href="javascript:Change(<?php print $x; ?>,0,<?php print $nb_pts_1; ?>,<?php print $nb_pts_N; ?>,<?php print $nb_pts_2; ?>);"><img src="images/N.gif" border="no" name="m<?php print $x; ?>_0" alt="" title="<?php print "Points Hourra : ".$nb_pts_N; ?>"></a>
             	<a href="javascript:Change(<?php print $x; ?>,2,<?php print $nb_pts_1; ?>,<?php print $nb_pts_N; ?>,<?php print $nb_pts_2; ?>);"><img src="images/2-gris.gif"  border="no" name="m<?php print $x;?>_2" alt="" title="<?php print "Points Hourra : ".$nb_pts_2; ?>"></a>
<?php
	}
	// victoire à l'extérieur
    else if ($prono=="2")
    {
		$nb_pts_1=$nb_pts_1_prono_nok;
		$nb_pts_N=$nb_pts_N_prono_nok;
		$nb_pts_2=$nb_pts_2_prono_ok;
?>
			 	<script>
             		TotPtsHourra = TotPtsHourra + <?php print $nb_pts_2; ?>
             	</script>
             	<a href="javascript:Change(<?php print $x; ?>,1,<?php print $nb_pts_1; ?>,<?php print $nb_pts_N; ?>,<?php print $nb_pts_2; ?>);"><img src="images/1-gris.gif" border="no" name="m<?php print $x; ?>_1" alt="" title="<?php print "Points Hourra : ".$nb_pts_1; ?>"></a>
             	<a href="javascript:Change(<?php print $x; ?>,0,<?php print $nb_pts_1; ?>,<?php print $nb_pts_N; ?>,<?php print $nb_pts_2; ?>);"><img src="images/N-gris.gif" border="no" name="m<?php print $x; ?>_0" alt="" title="<?php print "Points Hourra : ".$nb_pts_N; ?>"></a>
             	<a href="javascript:Change(<?php print $x; ?>,2,<?php print $nb_pts_1; ?>,<?php print $nb_pts_N; ?>,<?php print $nb_pts_2; ?>);"><img src="images/2.gif"  border="no" name="m<?php print $x;?>_2" alt="" title="<?php print "Points Hourra : ".$nb_pts_2; ?>"></a>
<?php
	}
?>
</td>
<?php               
	}
    else
    {
?>
<td class="pronos_prono">
<?php
         if ($prono=="1")
           {
           ?>
             <img src="images/1.gif" border="no" alt="" title="<?php print "Points Hourra : ".$points_prono_domicile; ?>"> <img src="images/N-gris.gif" border="no" alt="" title="<?php print "Points Hourra : ".$points_prono_nul; ?>"> <img src="images/2-gris.gif"  border="no" alt="" title="<?php print "Cote : ".$points_prono_visiteur; ?>">
           <?php
           }

         if ($prono=="N")
           {
           ?>
             <img src="images/1-gris.gif" border="no" alt="" title="<?php print "Points Hourra : ".$points_prono_domicile; ?>"> <img src="images/N.gif" border="no" alt="" title="<?php print "Points Hourra : ".$points_prono_nul; ?>"> <img src="images/2-gris.gif"  border="no" alt="" title="<?php print "Cote : ".$points_prono_visiteur; ?>">
           <?php
           }

         if ($prono=="2")
         {
         ?>
             <img src="images/1-gris.gif" border="no" alt="" title="<?php print "Points Hourra : ".$points_prono_domicile; ?>"> <img src="images/N-gris.gif" border="no" alt="" title="<?php print "Points Hourra : ".$points_prono_nul; ?>"> <img src="images/2.gif"  border="no" alt="" title="<?php print "Cote : ".$points_prono_visiteur; ?>">
         <?php
         }
  
         if ($prono=="0")
         {
          ?> 
             <img src="images/1-gris.gif" border="no" alt="" title="<?php print "Points Hourra : ".$points_prono_domicile; ?>"> <img src="images/N-gris.gif" border="no" alt="" title="<?php print "Points Hourra : ".$points_prono_nul; ?>"> <img src="images/2-gris.gif"  border="no" alt="" title="<?php print "Cote : ".$points_prono_visiteur; ?>">
          <?php
         }
         echo "</td>";
       }
  
       echo "<td class=\"pronos_clubext\">$clubs_nom1</td><td class=\"pronos_serieext\">$serieExt</td><td class=\"pronos_expire\">"; 
  
       if ($ecart_heures>48) echo $ecart_jours." jours";
       elseif ($ecart_heures>0) echo $ecart_heures." h";
       elseif ($ecart_heures == 0) echo $ecart_minutes." min";
       else {echo PRONO_GRILLE_EXPIRE;}
       echo "</td>";
       echo "</tr>";
       $i++;
      }
?>

						</table>
						
						<input type="hidden" name="action" value="valid_pronos">
						<input type="hidden" name="nb_fiche" value="<?php print $x; ?>">
						<br />
						<div id="nbPtsHourra">
							<font face=verdana size=1>
								<b>AU TOTAL VOUS POUVEZ GAGNER <script>document.write(TotPtsHourra)</script> POINTS AU CLASSEMENT HOURRA !</b>
							</font>
						</div>
						<br>
						<input type="hidden" name="debut" value="<?php print $debut; ?>">
						<a href="javascript:ValideGrille(<?php print $x; ?>);"><img src="images/valider.png" alt="" border="0"></a>						
					</form>
				</div>

			</div>
		</div>
	</div>
	<div class="rounded-block-bottom-left"></div>
	<div class="rounded-block-bottom-right"></div>
</div>

<script>
$(document).ready(function() {
	$('#pronos_classement').load('http://<?php echo $_SERVER['SERVER_NAME'].substr($_SERVER['PHP_SELF'], 0, strrpos($_SERVER['PHP_SELF'],'/')); ?>/../consult/miniseul.php?champmini=<?php echo $champ; ?>&typemini=General&presentationmini=1&lienmini=non&classmini=1');
	$('#journee').load('http://<?php echo $_SERVER['SERVER_NAME'].substr($_SERVER['PHP_SELF'], 0, strrpos($_SERVER['PHP_SELF'],'/')); ?>/../consult/calendrier_journee.php?champ=<?php echo $champ; ?>');
	$('.pronos_prono img').tooltip({
		track: true,
		delay: 0,
		showURL: false
	});
});
</script>
