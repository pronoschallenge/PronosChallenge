<?
     if (isset($_POST['action'])) {$action=$_POST['action'];} 
     elseif (isset($_GET['action'])) {$action=$_GET['action'];}
	 else {$action='';}

	if($action=='updatefilter') 
	{
?>
		<script type="text/javascript">
			/* on sélectionne l'onglet sur lequel on validé le formulaire */
			$(function() {
				$('#tabs').tabs('option', 'selected', 1);
			});
		</script>
<?php		
		if (isset($_POST['idsMembres'])) 
		{
			$idsMembres = $_POST['idsMembres'];
			// réinitialisation du filtre de l'utilisateur
			mysql_query("DELETE FROM phpl_clmnt_filtre WHERE id ='$user_id'") or die (mysql_error());
			foreach ($idsMembres as $id)
			{
			 	mysql_query("INSERT INTO phpl_clmnt_filtre SET id ='$user_id', idMembre ='$id'") or die (mysql_error());				
			}
		}
	}


//$requete="SELECT * FROM phpl_membres, phpl_clmnt_filtre where phpl_membres.id=phpl_clmnt_filtre.idMembre and phpl_clmnt_filtre.id='$user_id' and actif='1' order by phpl_membres.pseudo";
$requete="SELECT * FROM phpl_membres where actif='1' order by phpl_membres.pseudo";
$membres=mysql_query($requete);

$requete="SELECT * FROM phpl_clmnt_filtre where phpl_clmnt_filtre.id='$user_id'";
$membresSelectionnes=mysql_query($requete);
$idMembresSelectionnes = Array();
$i =0; 
while ($rowMembresSelectionnes=mysql_fetch_array($membresSelectionnes))
{
	$idMembresSelectionnes[$i] = $rowMembresSelectionnes["idMembre"];
	$i++;
}

?>

<script>
	function toggleAllCheckboxes(cb)
	{
		var inputsTab = document.getElementsByTagName('input');
		
		for(var i=0; i<inputsTab.length; i++)
		{
			if(inputsTab[i].name != 'toggle' && inputsTab[i].type == 'checkbox')
			{
				if(cb.checked)
				{
					inputsTab[i].checked = true;
				}
				else
				{
					inputsTab[i].checked = false;
				}
			}
		}
	}
</script>

<br />
<div>Sélectionnez les pronostiqueurs à afficher lors de l'activation du filtre</div>
<br />

<form name="formFiltre" method="POST" action="index.php?page=compte">
	<input type="hidden" name="action" value="updatefilter" />

	<div align="center">
		<input type="submit" value="Valider" class="bouton"/>
	</div>
	<br />
	<table id="tableFiltre" border="0" cellpadding="0" cellspacing="0">
	  <tr>
		<th><input type="checkbox" name="toggle" onclick="toggleAllCheckboxes(this)" title="Sélectionner/Désélectionner tout"></th>
		<th>PSEUDO</th>
		<th>NOM</th>
		<th>PRENOM</th>
		<th>VILLE</th>
	  </tr>
<?php
	$i=0;
	while ($rowMembres=mysql_fetch_array($membres))
	{
		if (($i%2)==0) 
		{
			$styleClass="filtreLignePaire";
		}
		else
		{
			$styleClass="filtreLigneImpaire";
		}
?>
	  <tr class="<?php echo $styleClass; ?>">
		<td>
			<?php
			$estSelectionne = false;
			foreach($idMembresSelectionnes as $idMembreSelectionne)
			{
				if($rowMembres["id"] == $idMembreSelectionne)
				{
					$estSelectionne = true;
					break;
				}
			}
			?> 
			<input type="checkbox" name="idsMembres[]" value="<?php print $rowMembres["id"] ?>" <?php if($estSelectionne){?>checked<?php } ?> />
		</td>
		<td><?php print $rowMembres["pseudo"] ?></td>
		<td><?php print $rowMembres["nom"] ?></td>
		<td><?php print $rowMembres["prenom"] ?></td>
		<td><?php print $rowMembres["ville"] ?></td>
	  </tr>
<?php
		$i++;
	}
?>
	</table>

	<br />

	<div align="center">
		<input type="submit" value="Valider" class="bouton"/>
	</div>
</form>
