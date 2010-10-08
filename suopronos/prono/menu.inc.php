<?
	// Calcul de l'onglet actif
	
	$pages_rubrique = array();
	$pages_rubrique["accueil"] = array("accueil"); 
	$pages_rubrique["pronos"] = array("pronos", "derniers_pronos", "cotes", "statistiques", "stats");
	$pages_rubrique["classements"] = array("classement");
	$pages_rubrique["ligue1"] = array("classement_pc");
	$pages_rubrique["options"] = array("compte", "param_filtre_classement", "strategie");
	$pages_rubrique["reglementlots"] = array("reglement", "lots");

	if (isset($_GET['page'])) {
		$page = $_GET['page'];
	} else {
		$page = "accueil";
	}

	$onglet_actif = array("accueil" => "", "pronos" => "", "classements" => "", "ligue1" => "", "options" => "", "reglementlots" => "");
	foreach($onglet_actif as $onglet => $actif) {
		if(in_array($page, $pages_rubrique[$onglet])) {
			$onglet_actif[$onglet] = "active";
			break;
		}
	}
?>

<script type="text/javascript">
	jQuery(document).ready(function(){
	jQuery(" .menu ul li ul ").css({display: "none"}); // Opera Fix
	jQuery(" .menu ul li ul li:has(ul)").addClass("liWithChildren");
	jQuery(" .menu ul li").hover(function(){
		//jQuery(this).find('ul:first').css({visibility: "visible",display: "none"}).fadeIn(500);
		jQuery(this).find('ul:first').css({visibility: "visible",display: "none"}).fadeIn(500);
	},function(){
		jQuery(this).find('ul:first').css({visibility: "hidden"});
	});
	jQuery(" .menu ul li").hover(function(){
		jQuery(this).addClass("selected");
	},function(){
		jQuery(this).removeClass("selected");		
	});
});

function viderMotDePasse(value) {
	if(value=='Mot de passe') {
		jQuery('#divMotDePasse').html('<input type="password" name="pass" id="pass" class="inputFlat" value="" onBlur="reinitialiserMotDePasse(this.value);" />');
		// On appelle 2 fois le focus à cause d'un bug IE...
		jQuery('#pass').focus();
		jQuery('#pass').focus();
	}
}

function reinitialiserMotDePasse(value) {
	if(value=='') {
		jQuery('#divMotDePasse').html('<input type="text" name="pass" id="pass" class="inputFlat" value="Mot de passe" onFocus="viderMotDePasse(this.value);" onBlur="reinitialiserMotDePasse(this.value);" />');
	}
}
</script>

<div class="menu" align="center">
	<ul>
		<li class="<? print $onglet_actif["accueil"] ?>">
			<a href="index.php?champ=<?php echo $champ; ?>"><span>ACCUEIL</span></a>
		</li>				

		<li class="<? print $onglet_actif["pronos"] ?>">
			<a href="#"><span>PRONOS</span></a>

			<ul>
				<li style="width:140px;">
					<a href="index.php?page=pronos&amp;gr_champ=<?php print $gr_champ;?>">Pronos à valider</a>
				</li>
				<li style="width:140px;">
					<a href="index.php?page=derniers_pronos&amp;gr_champ=<?php print $gr_champ;?>">Derniers pronos</a>
				</li>
				<li style="width:140px;">
					<a href="index.php?page=cotes&amp;gr_champ=<?php print $gr_champ;?>">Cotes</a>
				</li>
				<li style="width:140px;">
					<a href="index.php?page=statistiques&amp;gr_champ=<?php print $gr_champ;?>">Tendances</a>
				</li>
				<li style="width:140px;">
					<!--<a href="index.php?page=stats&amp;gr_champ=<?php print $gr_champ;?>"><img src="ico/onebit_16.png" />Statistiques</a>-->
					<a href="index.php?page=stats&amp;gr_champ=<?php print $gr_champ;?>">Statistiques</a>
				</li>						
			</ul>

		</li>
		
		<li class="<? print $onglet_actif["classements"] ?>">
			<a href="#"><span>CLASSEMENTS</span></a>
			<ul>
				<li style="width:230px;">
					<a href="index.php?page=classement&amp;type=derniere_journee&amp;gr_champ=<?php print $gr_champ; ?>&amp;filtre=0">Général - Derni&egrave;re journ&eacute;e</a>						
				</li>
				<li style="width:230px;">
					<a href="index.php?page=classement&amp;gr_champ=<?php print $gr_champ; ?>&amp;filtre=0">Général - Complet</a>						
				</li>
				<li style="width:230px;">
					<a href="index.php?page=classement&amp;type=hourra_derniere_journee&amp;gr_champ=<?php print $gr_champ; ?>&amp;filtre=0">Hourra - Derni&egrave;re journ&eacute;e</a>						
				</li>
				<li style="width:230px;">
					<a href="index.php?page=classement&amp;type=hourra&amp;gr_champ=<?php print $gr_champ; ?>&amp;filtre=0">Hourra - Complet</a>						
				</li>
				<li style="width:230px;">
					<a href="index.php?page=classement&amp;type=mixte&amp;gr_champ=<?php print $gr_champ; ?>&amp;filtre=0">Mixte</a>
				</li>
			</ul>

			<!--
			<ul>
				<li>
					<a href="#">Général</a>
					<ul>
						<li><a href="index.php?page=classement&amp;type=derniere_journee&amp;gr_champ=<?php print $gr_champ; ?>&amp;filtre=0">Dernière journée</a></li>
						<li><a href="index.php?page=classement&amp;gr_champ=<?php print $gr_champ; ?>&amp;filtre=0">Complet</a></li>
					</ul>							
				</li>
				<li>
					<a href="#">Hourra</a>
					<ul>
						<li><a href="index.php?page=classement&amp;type=hourra_derniere_journee&amp;gr_champ=<?php print $gr_champ; ?>&amp;filtre=0">Dernière journée</a></li>
						<li><a href="index.php?page=classement&amp;type=hourra&amp;gr_champ=<?php print $gr_champ; ?>&amp;filtre=0">Complet</a></li>
					</ul>
				</li>
				<li>
					<a href="index.php?page=classement&amp;type=mixte&amp;gr_champ=<?php print $gr_champ; ?>&amp;filtre=0">Mixte</a>
				</li>
			</ul>
			-->
		</li>
		
		<li class="<? print $onglet_actif["ligue1"] ?>">
			<a href="index.php?page=classement_pc&champ=<?php echo $champ; ?>"><span>LIGUE 1</span></a>
		</li>
		
		<li class="<? print $onglet_actif["reglementlots"] ?>">
			<a href="#"><span>REGLES</span></a>

			<ul>
				<li style="width:100px;">
					<a href="index.php?page=reglement">Réglement</a>					
				</li>
				<li style="width:100px;">
					<a href="index.php?page=lots">Lots</a>
				</li>
			</ul>
		</li>					
	</ul>
</div>

<!--
<form id="formForum" action="../../forum/index.php" method="POST" target="_blank">
<input type="HIDDEN" name="username" value="<?php echo $user_pseudo ?>">
<input type="HIDDEN" name="password" value="<?php echo $mot_de_passe ?>">
</form>
-->   

<? if ($connecte) { ?>

	<div id="utilisateur">
		<div id="clmnt_utilisateur">
			<table cellpadding="0" cellspacing="1">
				<tr>
					<td id="utilisateur_col_gauche">
						<div id="pseudo"><?php echo $user_pseudo ?></div>
						<a href="?page=compte"><img src="images/bouton-options.png" class="bouton_option" /></a>
					</td>
					<td>
						<table cellpadding="0" cellspacing="0">
							<tr id="clmnt_utilisateur_mixte" onClick="javascript:document.location='index.php?page=classement&amp;type=mixte&amp;gr_champ=<?php print $gr_champ;?>'">
								<td class="nom_clmnt_utilisateur">Mixte</td>
								<td class="place_clmnt_utilisateur"><?php echo getClmntUtilisateur($user_id, $gr_champ, 'mixte');?></td>
							</tr>
							<tr id="clmnt_utilisateur_general" onClick="javascript:document.location='index.php?page=classement&amp;type=general&amp;gr_champ=<?php print $gr_champ;?>'">
								<td class="nom_clmnt_utilisateur">G&eacute;n&eacute;ral</td>
								<td class="place_clmnt_utilisateur"><?php echo getClmntUtilisateur($user_id, $gr_champ, 'general');?></td>
							</tr>
							<tr id="clmnt_utilisateur_mixte" onClick="javascript:document.location='index.php?page=classement&amp;type=hourra&amp;gr_champ=<?php print $gr_champ;?>'">
								<td class="nom_clmnt_utilisateur">Hourra</td>
								<td class="place_clmnt_utilisateur"><?php echo getClmntUtilisateur($user_id, $gr_champ, 'hourra');?></td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
				</tr>
			</table>
		</div>
		
		<a href="mailto:pronoschallenge@pronoschallenge.fr">
			<img name="bouton-email" id="bouton-email" src="images/email.png" class="bouton_email" />
		</a>
		
		<a href="logout.php">
			<img name="bouton-deconnexion" id="bouton-deconnexion" src="images/bouton-deconnexion.png" class="bouton_deconnexion" />
		</a>
	</div>
	
<? } else { ?>

	<div id="connexion">
		<form action="login.php" method="post">
			<input type="text" name="user" id="user" class="inputFlat"
				value="Identifiant"
				onFocus="if(this.value=='Identifiant'){this.value=''};this.className='inputFlatUpd';this.style.color='#000000';" 
				onBlur="this.className='inputFlat';if(this.value==''){this.style.color='#707070';this.value='Identifiant';}" /> 
			<div id="divMotDePasse" style="display: inline;">
				<input type="text" name="pass" id="pass" class="inputFlat" 
					value="Mot de passe"
					onFocus="viderMotDePasse(this.value);" 
					onBlur="reinitialiserMotDePasse(this.value);" />
			</div>
			<a href="?page=perdu_mdp"><img src="images/bouton-aide.png" class="bouton_aide" title="Mot de pass oublié" alt="Mot de pass oublié"/></a>
			
			<a href="mailto:pronoschallenge@pronoschallenge.fr">
				<img name="bouton-email" id="bouton-email" src="images/email.png" class="bouton_email" />
			</a>
				
			<input name="bouton-connexion" type="image" src="images/bouton-connexion.png" id="bouton-connexion" class="bouton_connexion" />
		</form>
	</div>

<? } ?>
