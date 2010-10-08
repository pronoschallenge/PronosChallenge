<SCRIPT type="text/JavaScript">
<!--
// script pour les liens vers le forum
function openForum(action)
{
	document.getElementById("formForum").action = action;
	document.getElementById("formForum").submit();
}

// -->
</SCRIPT>

<?php
/*
PARTIE FORUM !!
*/
/*
$phpbb_root_path = '../../forum/'; //edit this to your phpBB root path
define('IN_PHPBB', true);
include($phpbb_root_path . 'extension.inc');
include($phpbb_root_path . 'common.'.$phpEx);

Function replacebbcode($text){
  
	$text = eregi_replace("\[b:.{0,10}\]", "<b>", $text); 
	$text = eregi_replace("\[\/b:.{0,10}\]", "</b>", $text);
	$text = eregi_replace("\[u:.{0,10}\]", "<u>", $text); 
	$text = eregi_replace("\[\/u:.{0,10}\]", "</u>", $text);
	$text = eregi_replace("\[i:.{0,10}\]", "<i>", $text); 
	$text = eregi_replace("\[\/i:.{0,10}\]", "</i>", $text);
	$text = eregi_replace("\[url=http:.{1,80}\]"," ",$text);
	$text = eregi_replace("\[\/url\]"," ",$text);
	$text = eregi_replace("\[url\]"," ",$text);
	$text = eregi_replace("\[.{1,10}:.{1,10}\]"," ",$text);
	$text = eregi_replace("\[\/.{1,10}:.{1,10}\]"," ",$text);
    $text = eregi_replace("\[\/.{1,20}\]"," ",$text);
    $text = eregi_replace("\[.{1,20}\]"," ",$text);
Return $text;
}
 
// various attributes - experiment!

$NUM_POSTS = 15;
$POST_IMAGE ="XP_NewFile.gif"; // icon next to each item
$TEXT_ON = FALSE; //display some of the text of the post?
$TEXT_LEN = 200;  //number of chars if above is true
$HIDE = true;//true or false - if true, do not show posts from certain forums - see below
$hide_level = 0;// display threshold 0=only show posts in forums open for guest reading,1= also registered, 2=also Mods only 3=show ALL posts even those froums for admins only
$fontheadersize="2";
$fontheadercolor="black";
$fontsize=1;
$fontcolor="#FF6A00";
$fontsizetext=1;
$fontcolortext="#FF6A00";
$fontheaderface="verdana";
$box_title = "<font size=\"$fontheadersize\" color=\"$fontheadercolor\" face=\"$fontheaderface\"><b> Derniers messages postés</b></font>";
$box_content = "";

$time=time();
$time=date("d M Y h:i a",$time);

$sqlxx="SELECT a1.post_id AS postid, a1.poster_id AS poster, a1.forum_id, a1.topic_id AS topic, a1.post_time AS time, a2.post_subject AS subject, a2.post_text AS text FROM phpbb_posts a1, phpbb_posts_text a2, phpbb_forums a3 WHERE a1.post_id = a2.post_id AND a1.forum_id = a3.forum_id";
  

if($HIDE) $sqlxx .= " AND a3.auth_view <= \"" . $hide_level . "\"";

$sqlxx .= " ORDER BY a1.post_time DESC";

$resultxx = mysql_query($sqlxx) or die("Cannot query database");

if($resultxx){
	
	$box_content .="<table cellpadding=\"0\" cellspacing = \"0\" width= \"100%\" border=\"0\">";
	$iColor=0;
	for($i=0;$i<$NUM_POSTS;$i++)
	{
		if($post = mysql_fetch_array($resultxx))
		{
		$color = $iColor % 2 ? '#000000' : '#000000';
		$result3=mysql_query("SELECT username FROM phpbb_users WHERE user_id =" . $post["poster"]);
		$author=mysql_fetch_array($result3);
		$result4 = mysql_query("SELECT forum_name FROM phpbb_forums WHERE forum_id =" . $post["forum_id"]);
		$forum=mysql_fetch_array($result4);
			if(!$post["subject"]){
				$result2=mysql_query("SELECT topic_title FROM phpbb_topics WHERE topic_id =" . $post["topic"]);
				$replyto = mysql_fetch_array($result2);
				$post["subject"]="RE: " . $replyto["topic_title"];
				mysql_free_result($result2);
			}
			$strSujetPost;
			if(strLen($post["subject"])>34)
			{
				$strSujetPost = substr($post["subject"],0, 34);
				$strSujetPost .= "...";
			}
			else
			{
				$strSujetPost = $post["subject"];
			}	
			//$box_content .="<tr><td bgcolor=\"" . $color . "\"><font size=\"$fontsize\" color=\"$fontcolor\" face=\"$fontheaderface\">&nbsp;" . date("d-m-y ", $post["time"]) . "&nbsp;<a href=\"" . $phpbb_root_path . "/viewtopic.php?t=" .$post["topic"] . "\" target=_blank title=\"Posté par : " . $author["username"]. " &nbsp;&nbsp; Dans : " .          $forum["forum_name"] . "\">" . $strSujetPost . "</a></font></td></tr>";
			$box_content .="<tr><td bgcolor=\"" . $color . "\"><font size=\"$fontsize\" color=\"$fontcolor\" face=\"$fontheaderface\">&nbsp;" . date("d-m-y ", $post["time"]) . "&nbsp;<a href=\"#\" onclick='openForum(\"".$phpbb_root_path . "/viewtopic.php?t=" .$post["topic"] . "\");' title=\"Posté par : " . $author["username"]. " &nbsp;&nbsp; Dans : " .          $forum["forum_name"] . "\">" . $strSujetPost . "</a></font></td></tr>";		
			if($TEXT_ON)
			{
			$post["text"] = replacebbcode($post["text"]);
			$post["text"] = substr($post["text"],0,$TEXT_LEN);
				$box_content .= "<tr><td cellpadding=\"0\" bgcolor=>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font color=\"$fontcolortext\" size=\"$fontsizetext\" face=\"$fontheaderface\">" . $post["text"] . "...</font></td></tr>";
			}
		 $iColor++;
		}
		
			
	}
$box_content .="</table>";
}
*/

/*
PARTIE NEWS
*/

// Lecture d'un fichier XML
function lit_rss($fichier,$champs) {
   // on lit le fichier
   if($chaine = @implode("",@file($fichier))) {
      // on explode sur <item>
      $tmp = preg_split("/<\/?"."item".">/",$chaine);
      // pour chaque <item>
      for($i=1;$i<sizeof($tmp)-1 && $i<30;$i+=2)
         // on lit les champs demand? <champ>
         foreach($champs as $champ) {
            $tmp2 = preg_split("/<\/?".$champ.">/",$tmp[$i]);
            // on ajoute au tableau
            $tmp3[$i-1][] = @$tmp2[1];
         }
      // et on retourne le tableau
      return $tmp3;
   }
}
$rss = lit_rss("http://www.lequipe.fr/Xml/Football/Titres/actu_rss.xml",array("title","link","description","pubDate",));

$rssPC = lit_rss("xml/news.xml",array("title","link","description","pubDate",));
?>

    <div id="colonne1">
    	<div class="bloc accueil_news">
    		<div class="rounded-block-top-left"></div>
    		<div class="rounded-block-top-right"></div>
    		<div class="rounded-outside">
				<div class="rounded-inside">
					<div class="bloc_entete">
						<div class="bloc_icone"><img src="images/icon-rss.png"/></div>
						<div class="bloc_titre">Les news</div>
					</div>
					<div class="bloc_contenu">
						<table border="0" align="center" cellpadding="1" cellspacing="0">
							  <?php
								$iStyleClass=0;
								foreach($rssPC as $tab) 
								{
									setlocale (LC_ALL, "fr_FR");
									$mois = strftime("%b",strtotime($tab[3]));
									//$mois = substr($mois, 0, strlen($mois)-1);

									$styleClass = $iStyleClass % 2 ? 'ligne_paire' : 'ligne_impaire';
									echo '<tr class="'.$styleClass.'">
											<td class="news_date">';
									echo '<div class="news_date_jour">'.strftime("%d",strtotime($tab[3])).'</div>';
									echo '<div class="news_date_mois">'.strtoupper($mois).'</div>';
									//echo '<div class="news_date_annee">'.strftime("%Y",strtotime($tab[3])).'</div>';
									echo '</td>
											<td class="news_texte">
												<b>'.$tab[0].'&nbsp;</b>
												<br/>'.$tab[2].'
											</td>
										</tr>';
								   $iStyleClass++;
								}
							  ?>
						</table>				
					</div>
				</div>
			</div>
			<div class="rounded-block-bottom-left"></div>
			<div class="rounded-block-bottom-right"></div>
		</div>

    	<div class="bloc accueil_gazouillis">
    		<div class="rounded-block-top-left"></div>
    		<div class="rounded-block-top-right"></div>
    		<div class="rounded-outside">
				<div class="rounded-inside">
					<div class="bloc_entete">
						<div class="bloc_icone"><img src="images/icon-rss.png"/></div>
						<div class="bloc_titre">Les gazouillis</div>
					</div>
					<div class="bloc_contenu">
						<?php include("gazouillis_home.php");?>
					</div>
				</div>
			</div>
			<div class="rounded-block-bottom-left"></div>
			<div class="rounded-block-bottom-right"></div>
		</div>
	</div>      

<?php

/* EVOLUTIONS DES PARIEURS*/
$box_evol = "";
$type_mixte = "mixte";
$type_gen = "general";
$type_hourra = "hourra";
$nbPlus = 3;
$nbMoins = 3;
$iStyleClass=0;

// Liste des membres
$queryMembres  =  "SELECT id, pseudo FROM phpl_membres;";
$resultMembres = mysql_query($queryMembres) or die ("probleme " .mysql_error());

while ($rowEvol=mysql_fetch_array($resultMembres))
{					 
	$membres[$rowEvol[0]] = ($rowEvol[1]);
}

// Dernière journée de championnat pronostiquée
$queryMaxJournee  =  "SELECT max(fin) FROM phpl_pronos_graph
           		     WHERE type='$type_gen' AND id_gr_champ='$gr_champ'";
$resultMaxJournee = mysql_query($queryMaxJournee) or die ("probleme " .mysql_error());
while ($rowMaxJournee=mysql_fetch_array($resultMaxJournee))
{
   $journeemax = $rowMaxJournee[0];
}
?>      
      
	<div id="colonne2">
		<div class="bloc accueil_topflop">
    		<div class="rounded-block-top-left"></div>
    		<div class="rounded-block-top-right"></div>
    		<div class="rounded-outside">
				<div class="rounded-inside">
					<div class="bloc_entete">
						<div class="bloc_icone"><img src="images/icon-top-flop.png" /></div>
						<div class="bloc_titre">Les Top et les Flop</div>
					</div>
					<div class="bloc_contenu">

<table cellpadding="0" cellspacing = "0" width="100%" border="0">
	<tr>
		<td colspan=2 style="text-align:center;padding:0px 0px 1px 0px;"><b>CLASSEMENT MIXTE</b></td>
	</tr>
<?
// Evolution de chaques pronostiqueurs au classement general
$queryEvol = "SELECT P1.id_membre, (P1.classement - P2.classement) as evolution
                       	FROM phpl_pronos_graph P1
                       	JOIN phpl_pronos_graph P2 ON P1.id_membre = P2.id_membre
                       	WHERE P1.id_gr_champ='$gr_champ'
			AND P2 .id_gr_champ='$gr_champ'
			AND P1.fin = '".($journeemax-1)."'
                       	AND P2.fin = '$journeemax'
                       	AND P1.type = '$type_mixte'
			AND P2.type = '$type_mixte'
                       	ORDER BY evolution DESC";
					   
$resultEvol = mysql_query($queryEvol) or die ("probleme " .mysql_error());

$evolPlus=0;
$evolMoins=0;
$idPlus = 0;
$idMoins = 0;
while ($rowEvol=mysql_fetch_array($resultEvol))
{
   $styleClass = $iStyleClass % 2 ? 'ligne_paire' : 'ligne_impaire';
   $idMembre = $rowEvol[0];
   $evol = $rowEvol[1];
   $idMoins++;
	
   if($evol>0)
   {
	   if($idPlus<$nbPlus)
	   {
		   echo "<tr class=\"".$styleClass."\"><td style=\"width:25%;\">&nbsp;<img src=\"images/icon-top.png\">&nbsp;&nbsp;+" . $evol . "&nbsp;</td>";
		   echo "<td style=\"width:75%;\">&nbsp;" . $membres[$idMembre] . "&nbsp;</td></tr>";
		   
		   if($evolPlus!=$evol)
		   {
				$idPlus++;
		   }
		    $iStyleClass++;
		}
   }
   else
   {
   	   if((mysql_num_rows($resultEvol)-$idMoins)<$nbMoins)
	   {
		   echo "<tr class=\"".$styleClass."\"><td style=\"width:25%;\">&nbsp;<img src=\"images/icon-flop.png\">&nbsp;&nbsp;" . $evol . "&nbsp;</td>";
		   echo "<td style=\"width:75%;\">&nbsp;" . $membres[$idMembre] . "&nbsp;</td></tr>";
		   $iStyleClass++;
		}
   }
}
?>

</table>

<table cellpadding="0" cellspacing = "0" width="100%" border="0">
	<tr>
		<td colspan=2 style="text-align:center;padding:3px 0px 1px 0px;"><b>CLASSEMENT GENERAL</b></td>
	</tr>
<?
// Evolution de chaques pronostiqueurs au classement general
$queryEvol = "SELECT P1.id_membre, (P1.classement - P2.classement) as evolution
                       	FROM phpl_pronos_graph P1
                       	JOIN phpl_pronos_graph P2 ON P1.id_membre = P2.id_membre
                       	WHERE P1.id_gr_champ='$gr_champ'
			AND P2 .id_gr_champ='$gr_champ'
			AND P1.fin = '".($journeemax-1)."'
                       	AND P2.fin = '$journeemax'
                       	AND P1.type = '$type_gen'
			AND P2.type = '$type_gen'
                       	ORDER BY evolution DESC";
					   
$resultEvol = mysql_query($queryEvol) or die ("probleme " .mysql_error());

$evolPlus=0;
$evolMoins=0;
$idPlus = 0;
$idMoins = 0;
while ($rowEvol=mysql_fetch_array($resultEvol))
{
   $styleClass = $iStyleClass % 2 ? 'ligne_paire' : 'ligne_impaire';
   $idMembre = $rowEvol[0];
   $evol = $rowEvol[1];
   $idMoins++;
	
   if($evol>0)
   {
	   if($idPlus<$nbPlus)
	   {
		   echo "<tr class=\"".$styleClass."\"><td style=\"width:25%;\">&nbsp;<img src=\"images/icon-top.png\">&nbsp;&nbsp;+" . $evol . "&nbsp;</td>";
		   echo "<td style=\"width:75%;\">&nbsp;" . $membres[$idMembre] . "&nbsp;</td></tr>";
		   
		   if($evolPlus!=$evol)
		   {
				$idPlus++;
		   }
		    $iStyleClass++;
		}
   }
   else
   {
   	   if((mysql_num_rows($resultEvol)-$idMoins)<$nbMoins)
	   {
		   echo "<tr class=\"".$styleClass."\"><td style=\"width:25%;\">&nbsp;<img src=\"images/icon-flop.png\">&nbsp;&nbsp;" . $evol . "&nbsp;</td>";
		   echo "<td style=\"width:75%;\">&nbsp;" . $membres[$idMembre] . "&nbsp;</td></tr>";
		   $iStyleClass++;
		}
   }
}
?>

</table>

<table cellpadding="0" cellspacing = "0" width= "100%" border="0">
	<tr>
		<td colspan=2 style="text-align:center;padding:3px 0px 1px 0px;"><b>CLASSEMENT HOURRA</b></td>
	</tr>

<?
// Evolution de chaques pronostiqueurs au classement hourra
$queryEvol = "SELECT P1.id_membre, (P1.classement - P2.classement) as evolution
                       FROM phpl_pronos_graph P1
                       JOIN phpl_pronos_graph P2 ON P1.id_membre = P2.id_membre
                       WHERE P1.id_gr_champ='$gr_champ'
			AND P2 .id_gr_champ='$gr_champ'
			AND P1.fin = '".($journeemax-1)."'
                       AND P2.fin = '$journeemax'
                       AND P1.type = '$type_hourra'
                       AND P2.type = '$type_hourra'
                       ORDER BY evolution DESC";
					   
$resultEvol = mysql_query($queryEvol) or die ("probleme " .mysql_error());

$evolPlus=0;
$evolMoins=0;
$idPlus = 0;
$idMoins = 0;
while ($rowEvol=mysql_fetch_array($resultEvol))
{
   $styleClass = $iStyleClass % 2 ? 'ligne_paire' : 'ligne_impaire';
   $idMembre = $rowEvol[0];
   $evol = $rowEvol[1];
   $idMoins++;
   
   if($evol>0)
   {
	   if($idPlus<$nbPlus)
	   {
		   	echo "<tr class=\"".$styleClass."\"><td style=\"width:25%;\">&nbsp;<img src=\"images/icon-top.png\">&nbsp;&nbsp;+" . $evol . "&nbsp;</td>";
		   	echo "<td style=\"width:75%;\">&nbsp;" . $membres[$idMembre] . "&nbsp;</td></tr>";
		   	if($evolPlus!=$evol)
		   	{
				$idPlus++;
		   	}
		   	$iStyleClass++;
		}
   }
   else
   {
   	   if((mysql_num_rows($resultEvol)-$idMoins)<$nbMoins)
	   {
		  	echo "<tr class=\"".$styleClass."\"><td style=\"width:25%;\">&nbsp;<img src=\"images/icon-flop.png\">&nbsp;&nbsp;" . $evol . "&nbsp;</td>";
		   	echo "<td style=\"width:75%;\">&nbsp;" . $membres[$idMembre] . "&nbsp;</td></tr>";
		    $iStyleClass++;
		}
   }
}
?>

</table>

					</div>
				</div>
			</div>
			<div class="rounded-block-bottom-left"></div>
			<div class="rounded-block-bottom-right"></div>
		</div>
	</div>      
      
	<div id="colonne3">
		<div class="bloc accueil_top5">
    		<div class="rounded-block-top-left"></div>
    		<div class="rounded-block-top-right"></div>
    		<div class="rounded-outside">
				<div class="rounded-inside">
					<div class="bloc_entete">
						<div class="bloc_icone"><img src="images/icon-coupe.png" /></div>
						<div class="bloc_titre">Top 5</div>
					</div>
					<div class="bloc_contenu">
						<div id="top5_general">
							<table>
								<caption>G&eacute;n&eacute;ral</caption>
<?
	$queryTop5="SELECT clmnt.id_membre, membre.pseudo, clmnt.place, clmnt.points, clmnt.participation,
		membre.nom,  membre.prenom,  membre.ville, membre.departement, membre.id_club_favori, 
		club.nom, club.url_logo, membre.avatar, membre.champ_gen, membre.champ_hourra, 
		membre.nb_champ_gen, membre.nb_champ_hourra 
		FROM phpl_clmnt_pronos as clmnt, phpl_membres as membre, phpl_clubs as club
		WHERE clmnt.id_champ='$gr_champ' AND clmnt.type='general'
		AND membre.id=clmnt.id_membre
		AND membre.actif='1'
		AND (membre.id_club_favori IS NULL OR club.id = membre.id_club_favori)
		GROUP by clmnt.pseudo
		ORDER by  clmnt.points desc, clmnt.participation asc, membre.pseudo
		LIMIT 0, 5";	
	$resultTop5=mysql_query($queryTop5) or die ("probleme " .mysql_error());
	while ($rowTop5=mysql_fetch_array($resultTop5))
	{
?>								
								<tr>
									<td><?print $rowTop5["place"]?>. <?print $rowTop5["pseudo"]?></td>
								</tr>
<?
	}
?>					
							</table>
						</div>
						<div id="top5_hourra">
							<table>
								<caption>Hourra</caption>
<?
	$queryTop5="SELECT clmnt.id_membre, membre.pseudo, clmnt.place, clmnt.points, clmnt.participation,
		membre.nom,  membre.prenom,  membre.ville, membre.departement, membre.id_club_favori, 
		club.nom, club.url_logo, membre.avatar, membre.champ_gen, membre.champ_hourra, 
		membre.nb_champ_gen, membre.nb_champ_hourra 
		FROM phpl_clmnt_pronos as clmnt, phpl_membres as membre, phpl_clubs as club
		WHERE clmnt.id_champ='$gr_champ' AND clmnt.type='hourra'
		AND membre.id=clmnt.id_membre
		AND membre.actif='1'
		AND (membre.id_club_favori IS NULL OR club.id = membre.id_club_favori)
		GROUP by clmnt.pseudo
		ORDER by  clmnt.points desc, clmnt.participation asc, membre.pseudo
		LIMIT 0, 5";	
	$resultTop5=mysql_query($queryTop5) or die ("probleme " .mysql_error());
	while ($rowTop5=mysql_fetch_array($resultTop5))
	{
?>								
								<tr>
									<td><?print $rowTop5["place"]?>. <?print $rowTop5["pseudo"]?></td>
								</tr>
<?
	}
?>							
							</table>
						</div>
						<div id="top5_mixte">
							<table>
								<caption>Mixte</caption>
<?
	$queryTop5="SELECT clmnt.id_membre, membre.pseudo, clmnt.place, clmnt.points, clmnt.participation,
		membre.nom,  membre.prenom,  membre.ville, membre.departement, membre.id_club_favori, 
		club.nom, club.url_logo, membre.avatar, membre.champ_gen, membre.champ_hourra, 
		membre.nb_champ_gen, membre.nb_champ_hourra 
		FROM phpl_clmnt_pronos as clmnt, phpl_membres as membre, phpl_clubs as club
		WHERE clmnt.id_champ='$gr_champ' AND clmnt.type='mixte'
		AND membre.id=clmnt.id_membre
		AND membre.actif='1'
		AND (membre.id_club_favori IS NULL OR club.id = membre.id_club_favori)
		GROUP by clmnt.pseudo
		ORDER by  clmnt.points desc, clmnt.participation asc, membre.pseudo
		LIMIT 0, 5";	
	$resultTop5=mysql_query($queryTop5) or die ("probleme " .mysql_error());
	while ($rowTop5=mysql_fetch_array($resultTop5))
	{
?>								
								<tr>
									<td><?print $rowTop5["place"]?>. <?print $rowTop5["pseudo"]?></td>
								</tr>
<?
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
		
		<div class="bloc accueil_pronos">
			<div class="rounded-block-top-left"></div>
			<div class="rounded-block-top-right"></div>
			<div class="rounded-outside">
				<div class="rounded-inside">		
					<div class="bloc_entete"
						onClick="javascript:document.location='index.php?page=pronos&amp;gr_champ=<?php print $gr_champ;?>'">
						<!--<img id="imgpronos" src="ico/ko.png" style="cursor:pointer;vertical-align:middle;" />-->
						<div class="bloc_icone"><img id="imgpronos" src="images/icon-drapeau.png" /></div>
						<div class="bloc_titre"><span id="titrepronos" style="cursor:pointer">Vos pronostics</span></div>
					</div>
								
					<div class="bloc_contenu">
					  <table width="100%" border="0" align="center" cellpadding="1" cellspacing="0" bordercolor="#FF6A00" bgcolor="#FF6A00">

							<?php
								$bolOk=1;
								// nombre de matchs à afficher
								$nb_matchs=10;
								// calcul du match de debut de fin
								if (isset($_REQUEST['debut'])) {$debut=$_REQUEST['debut'];} else {$debut='';}
								if (empty ($debut) or $debut=="0") $debut=0; $apres=1;
								$fin = $debut+$nb_matchs;
																						
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
										echo "<tr><td align=center><div class=\"blanc\">Journée Inexistante</div></td></tr>";
									}	
									
									$iStyleClass=0;								
									while ($row=mysql_fetch_array($resultat) and $i<$nb_matchs)
									{
										$styleClass = $iStyleClass % 2 ? 'ligne_paire' : 'ligne_impaire';
										$iStyleClass++;
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
										echo "<tr class=\"".$styleClass."\">";
										if ($prono=="0")
										{
											echo "<td id=\"imgok_prono_accueil\"><img src='ico/ko_pt.png'></td>";
											$bolOk=0;
										}
										else
										{
											echo "<td id=\"imgok_prono_accueil\"><img src='ico/ok_pt.png'></td>";
										}
										echo "<td id=\"clubdom_prono_accueil\">$clubs_nom</td>";
								 
										$x++;
										echo"<td id=\"img1n2_prono_accueil\">";
							
									   if ($prono=="0")
									   {
										 ?>
										 -
										 <?
									   }
							
									 if ($prono=="1")
									   {
										?>
										<img src="images/1_home.gif" border="no" name="m<?php print $x; ?>_1" alt="">
										<?
									   }
							
									 if ($prono=="N")
									   {
										 ?>    
										 <img src="images/N_home.gif" border="no" name="m<?php print $x; ?>_0" alt="">
										 <?
									   }
							
									 if ($prono=="2")
									   {
										 ?>
										 <img src="images/2_home.gif"  border="no" name="m<?php print $x;?>_2" alt=""></a>
										 <?
									   }
									 echo "</td>";
													  
								   echo "<td id=\"clubext_prono_accueil\">$clubs_nom1</td><td>";

								   if ($ecart_heures>48) echo $ecart_jours." jours";
								   elseif ($ecart_heures>0) echo $ecart_heures." h";
								   elseif ($ecart_heures == 0) echo $ecart_minutes." min";
								   else {echo PRONO_GRILLE_EXPIRE;}
								   echo "</td>";
								   echo "</tr>";
								   $i++;
								  }
								  if($bolOk==1)
								  {
								  ?>
								  <script>
									document.getElementById("imgpronos").src = "ico/ok.png";
									//document.getElementById("titrepronos").innerHTML = "Bien joué, tous vos pronos sont à jour !";
								</script>
								  <?
								  }
							?>
							</table>
						</div>
					</div>
				</div>
				<div class="rounded-block-bottom-left"></div>
				<div class="rounded-block-bottom-right"></div>
			</div>
		</div>
