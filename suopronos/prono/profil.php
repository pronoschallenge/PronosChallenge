<?php
	require_once("fonctions.php");

	if (isset($_POST['action'])) {$action=$_POST['action'];} else {$action='';}
	
	if($action=='valide_profil') 
	{
?>
		<script type="text/javascript">
			/* on sélectionne l'onglet sur lequel on validé le formulaire */
			$(function() {
				$('#tabs').tabs('option', 'selected', 0);
			});
		</script>
<?php
		// on enregistre les infos modifiées du user
		if (isset($_POST['nom'])) {$nom=$_POST['nom'];} else {$nom='';}
		if (isset($_POST['prenom'])) {$prenom=$_POST['prenom'];} else {$prenom='';}
		if (isset($_POST['ville'])) {$ville=$_POST['ville'];} else {$ville='';}
		if (isset($_POST['ancien_mot_de_passe'])) {$ancien_mot_de_passe=$_POST['ancien_mot_de_passe'];} else {$ancien_mot_de_passe='';}
		if (isset($_POST['nouveau_mot_de_passe'])) {$nouveau_mot_de_passe=$_POST['nouveau_mot_de_passe'];} else {$nouveau_mot_de_passe='';}
		if (isset($_POST['nouveau_mot_de_passe_confirm'])) {$nouveau_mot_de_passe_confirm=$_POST['nouveau_mot_de_passe_confirm'];} else {$nouveau_mot_de_passe_confirm='';}
		if (isset($_POST['departement'])) {$departement=$_POST['departement'];} else {$departement='';}
		if (isset($_POST['id_club_favori'])) {$id_club_favori=$_POST['id_club_favori'];} else {$id_club_favori='';}
		
		if($ancien_mot_de_passe != '' || $nouveau_mot_de_passe != '' || $nouveau_mot_de_passe_confirm != '') {
			// on va rechercher le mot de passe actuel du user
			$requete = "SELECT mot_de_passe FROM phpl_membres WHERE id_prono='$user_id'";
			$resultat = mysql_query($requete);
		    while ($row= mysql_fetch_array($resultat))
		    {
		      $mot_de_passe_actuel=$row[0];
		    }
		    
		    if($mot_de_passe_actuel != md5($ancien_mot_de_passe)) {
			    echo "<div style=\"color: red; font-size:10pt; font-family: verdana;\"><img src=\"images/error.gif\" />&nbsp;Attention : votre ancien mot de passe est incorrect !</div><br />";				
		    } else if($nouveau_mot_de_passe != $nouveau_mot_de_passe_confirm) {
				echo "<div style=\"color: red; font-size:10pt; font-family: verdana;\"><img src=\"images/error.gif\" />&nbsp;Attention : les nouveaux mots de passe saisis sont différents !</div><br />";				
		    } else if($nouveau_mot_de_passe == '' && $nouveau_mot_de_passe_confirm == '') {
			    echo "<div style=\"color: red; font-size:10pt; font-family: verdana;\"><img src=\"images/error.gif\" />&nbsp;Attention : le nouveau mot de passe ne doit pas être vide !</div><br />";				
		    } else {
				// on met à jour les infos, avec le mot de passe			
				$strQuery = "UPDATE phpl_membres SET nom='$nom', prenom='$prenom', ville='$ville' ";	
		
				if($departement!='')
				{
					$strQuery .= ", departement='$departement' ";
				}
				else
				{
					$strQuery .= ", departement=null";
				}
		
				if($id_club_favori!='')
				{
					$strQuery .= ", id_club_favori='$id_club_favori' ";
				}
				else
				{
					$strQuery .= ", id_club_favori=null";
				}
				
				$strQuery .= ", mot_de_passe='".md5($nouveau_mot_de_passe)."' ";
		
				$strQuery .= " WHERE id_prono='$user_id'";		
		
				mysql_query($strQuery) or die ("probleme " .mysql_error());
				
				//
				// Mise à jour des infos dans le forum
				//
		
				$strQueryForum = "UPDATE ".USERS_TABLE." SET user_password='".md5($nouveau_mot_de_passe)."' WHERE username='".$user_pseudo."'";
						 
				mysql_query($strQueryForum) or die ("probleme " .mysql_error());
						                        
				echo "<div style=\"color: black; font-size:10pt; font-family: verdana;\"><img src=\"images/success.gif\" />&nbsp;Votre compte a été mis à jour !</div><br />";				
			    
		    }

		} else {
			// AVATAR                       
			if(!empty($_FILES["fichier_choisi"]["name"]))
			{
				//nom du fichier choisi:
				$nomFichier       = $user_id;
				// Extension du fichier
				$extensionFichier = strrchr($_FILES["fichier_choisi"]["name"], ".");
				//nom temporaire sur le serveur:
				$nomTemporaire    = $_FILES["fichier_choisi"]["tmp_name"] ;
				//type du fichier choisi:
				$typeFichier      = $_FILES["fichier_choisi"]["type"] ;
				//poids en octets du fichier choisit:
				$poidsFichier     = $_FILES["fichier_choisi"]["size"] ;
				//code de l'erreur si jamais il y en a une:
				$codeErreur       = $_FILES["fichier_choisi"]["error"] ;
				
				//chemin qui mène au dossier qui va contenir les fichiers uplaod:
				$chemin = "images/avatars/" ;
				
				if(strtolower($extensionFichier)==".gif" || strtolower($extensionFichier)==".GIF")
				{
					if($poidsFichier<=70000)
					{
						if(copy($nomTemporaire, $chemin.$nomFichier.$extensionFichier))
						{
							mysql_query("UPDATE phpl_membres SET avatar='$bolavatar' WHERE id_prono='$user_id'") or die ("probleme " .mysql_error());
							//echo("<div style=\"color: red; font-size:10pt; font-family: verdana;\">L'upload a réussi</div><br>") ;
						}
						else
						{
							echo("<div style=\"color: red; font-size:10pt; font-family: verdana;\">L'upload a échoué</div><br>") ;
						}
					}
					else
					{
						echo("<div style=\"color: red; font-size:10pt; font-family: verdana;\">Les fichiers pesant plus de 70 Ko ne sont pas acceptés !</div><br>") ;
					}
				}
				else
				{
					echo("<div style=\"color: red; font-size:10pt; font-family: verdana;\">Seules les images en .gif sont acceptées !</div><br>") ;
				}
			}//fin if			
			
			// on met à jour les autres infos, sauf le mot de passe			
			$strQuery = "UPDATE phpl_membres SET nom='$nom', prenom='$prenom', ville='$ville' ";	
	
			if($departement!='')
			{
				$strQuery .= ", departement='$departement' ";
			}
			else
			{
				$strQuery .= ", departement=null";
			}
	
			if($id_club_favori!='')
			{
				$strQuery .= ", id_club_favori='$id_club_favori' ";
			}
			else
			{
				$strQuery .= ", id_club_favori=null";
			}
	
			$strQuery .= " WHERE id_prono='$user_id'";		
	
			mysql_query($strQuery) or die ("probleme " .mysql_error());
					                        				                        
			echo "<div style=\"color: black; font-size:10pt; font-family: verdana;\"><img src=\"images/success.gif\" />&nbsp;Votre compte a &eacute;t&eacute; mis &agrave; jour !</div><br />";				
		}
	}
	
	/*
	if($action=='dl_avatar') 
	{
		// on enregistre les infos modifiées du user
		if (isset($_POST['blavatar'])) {$bolavatar=$_POST['blavatar'];} else {$bolavatar=0;}
	
		else
		{
			if($bolavatar==1)
			{
				if(remote_file_exists("images/avatars/".$user_id.".jpg")==true || remote_file_exists("images/avatars/".$user_id.".gif")==true)
				{
					mysql_query("UPDATE phpl_membres SET avatar='$bolavatar' WHERE id_prono='$user_id'") or die ("probleme " .mysql_error());
					echo("<div style=\"color: red; font-size:10pt; font-family: verdana;\">Avatar activé !!</div><br>");
				}
				else
				{
					echo("<div style=\"color: red; font-size:10pt; font-family: verdana;\">Vous n'avez pas encore uploadé d'avatar, vous ne pouvez donc pas l'activer.</div><br>");
				}
			}
			else
			{
				mysql_query("UPDATE phpl_membres SET avatar='$bolavatar' WHERE id_prono='$user_id'") or die ("probleme " .mysql_error());
				echo("<div style=\"color: red; font-size:10pt; font-family: verdana;\">Avatar désactivé !!</div><br>");
			}
		}//fin else
	}
	*/
	
	// on va rechercher les infos du user
	$requete = "SELECT * FROM phpl_membres WHERE id_prono='$user_id'";
	$resultat = mysql_query($requete);
    while ($row= mysql_fetch_array($resultat))
    {
      $nom=$row["nom"];
      $prenom=$row["prenom"];
      $ville=$row["ville"];
      $departement=$row["departement"];
      $id_club_favori=$row["id_club_favori"];
	  $avatar=$row["avatar"];
    }	
    
    // recuperation des clubs

	//$requeteClubs = "SELECT clubs.id, clubs.nom 
	//				FROM phpl_clubs as clubs, phpl_equipes as equipes
	//				WHERE equipes.id_club = clubs.id
	//				AND equipes.id_champ=8
	//				ORDER BY clubs.nom";
	$requeteClubs = "SELECT clubs.id, clubs.nom 
					FROM phpl_clubs as clubs
					WHERE url_logo is not null
					AND url_logo != '' 
					ORDER BY clubs.nom";
	$resultatClubs = mysql_query($requeteClubs);	
?>
			
				<br />
				<form id="formProfil" action="index.php?page=compte" method="post" enctype="multipart/form-data">
					<input type="hidden" name="action" value="valide_profil" />
					<table>
						<tr>
							<td>Avatar :</td>
							<td>
								<img src="./images/avatars/<?php echo $user_id?>.gif" />
								<input type="file" name="fichier_choisi" />				
							</td>
						</tr>					
						<tr>
							<td>Nom :</td>
							<td><input type="text" id="nom" name="nom" value="<? print $nom ?>" /></td>
						</tr>
						<tr>
							<td>Pr&eacute;nom :</td>
							<td><input type="text" id="prenom" name="prenom" value="<? print $prenom ?>" /></td>
						</tr>
						<tr>
							<td>Ancien mot de passe :</td>
							<td><input type="password" id="ancien_mot_de_passe" name="ancien_mot_de_passe" value="" /></td>
						</tr>
						<tr>
							<td>Nouveau mot de passe :</td>
							<td><input type="password" id="nouveau_mot_de_passe" name="nouveau_mot_de_passe" value="" /></td>
						</tr>
						<tr>
							<td>Confirmez le nouveau mot de passe :</td>
							<td><input type="password" id="nouveau_mot_de_passe_confirm" name="nouveau_mot_de_passe_confirm" value="" /></td>
						</tr>						
						<tr>
							<td>Ville :</td>
							<td><input type="text" id="ville" name="ville" value="<? print $ville ?>" /></td>
						</tr>
						<tr>
							<td>D&eacute;partement :</td>
							<td><input type="text" id="departement" name="departement" value="<? print $departement ?>" /></td>
						</tr>			
						<tr>
							<td>Club favori :</td>
							<td>
								<select name="id_club_favori" id="id_club_favori">
									<?
										if($id_club_favori == null)					
										{
									?>
										<option value="" selected>Aucun</option>
									<?
										}
										else
										{
									?>
										<option value="">Aucun</option>
									<?
										}
										
										while ($rowClubs= mysql_fetch_array($resultatClubs))
										{
											if($id_club_favori == $rowClubs[0])
											{
									?>
											<option value="<? print $rowClubs[0] ?>" selected><? print $rowClubs[1] ?></option>
									<?
											}
											else
											{
									?>
											<option value="<? print $rowClubs[0] ?>"><? print $rowClubs[1] ?></option>
									<?
											}
										}
									?>
								</select>
							</td>
						</tr>							
						<tr>
							<td colspan="2" style="text-align: center;"><br/><input type="submit" value="Enregistrer" class="bouton" /></td>
						</tr>			
					</table>
				</form>	

<!--
				<form name="formulaire_envoi_fichier" enctype="multipart/form-data" method="post" action="index.php?page=compte">
				  <input type="hidden" name="action" value="dl_avatar" />
					<table width="60%" class="tablephpl2">
						<tr bordercolor="#000000" bgcolor="#FF6600">
							<td colspan="2" align="center">VOTRE AVATAR</td>
						</tr>
						<tr class="ligne2">
							<td width="50%">Télécharger son avatar : <b><font size=1></font></b>
							</td>
							<td width="50%"><input type="file" name="fichier_choisi">
							</td>
						</tr>
						<tr class="ligne2">
							<td width="50%">Avatar activé :<b><font size=1></font></b>
							</td>
							<td width="50%">
								<select name="blavatar" id="blavatar">
									<? 
									if($avatar==1){
									?>
										<option value="1" selected>oui</option>
										<option value="0">non</option>
									<? 
									}else{
									?>
										<option value="1">oui</option>
										<option value="0" selected>non</option>
									<?
									}
									?>
								</select>
							</td>
						</tr>
						<tr>
							<td colspan="2" align="center">  <input type="submit" name="bouton_submit" value="Envoyer le fichier"></td>
						</tr>	
					</table>
				</form>	
-->
