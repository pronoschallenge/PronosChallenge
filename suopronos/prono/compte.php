<script type="text/javascript">
	$(function() {
		$("#tabs").tabs();
	});
</script>

<div class="bloc bloc_compte">
	<div class="rounded-block-top-left"></div>
	<div class="rounded-block-top-right"></div>
	<div class="rounded-outside">
		<div class="rounded-inside">
			<div class="bloc_entete">
				<div class="bloc_icone"></div>
				<div class="bloc_titre">Mon compte</div>
			</div>
			<div class="bloc_contenu">

<div id="tabs">
	<ul>
		<li><a href="#tabs-1">Profil</a></li>
		<li><a href="#tabs-2">Filtre</a></li>
		<li><a href="#tabs-3">Stratégie</a></li>
	</ul>
	<div id="tabs-1">
		<?php include("profil.php");?>
	</div>
	<div id="tabs-2">
		<?php include("param_filtre_classement.php");?>
	</div>
	<div id="tabs-3">
		<?php include ("strategie.php");?>
	</div>
</div>	
	
			</div>
		</div>
	</div>
	<div class="rounded-block-bottom-left"></div>
	<div class="rounded-block-bottom-right"></div>
</div>
