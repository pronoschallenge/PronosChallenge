<?
	define('IN_PHPBB', true);
	$phpbb_root_path = '../../forum/';
	include($phpbb_root_path . 'extension.inc');
	include($phpbb_root_path . 'common.'.$phpEx);

	if (isset($_POST['action'])) {$action=$_POST['action'];} else {$action='';}
	
	if (isset($_POST['pseudo'])) {$pseudo=$_POST['pseudo'];} else {$pseudo='';}
	if (isset($_POST['mot_de_passe'])) {$mot_de_passe=md5($_POST['mot_de_passe']);} else {$mot_de_passe='';}
	if (isset($_POST['nom'])) {$nom=$_POST['nom'];} else {$nom='';}
	if (isset($_POST['prenom'])) {$prenom=$_POST['prenom'];} else {$prenom='';}
	if (isset($_POST['mail'])) {$mail=$_POST['mail'];} else {$mail='';}
	if (isset($_POST['adresse'])) {$adresse=$_POST['adresse'];} else {$adresse='';}
	if (isset($_POST['code_postal'])) {$code_postal=$_POST['code_postal'];} else {$code_postal='';}
	if (isset($_POST['ville'])) {$nom=$_POST['nom'];} else {$ville='';}
	if (isset($_POST['departement'])) {$departement=$_POST['departement'];} else {$departement='';}
	if (isset($_POST['pays'])) {$pays=$_POST['pays'];} else {$pays='';}
	if (isset($_POST['date_naissance'])) {$date_naissance=$_POST['date_naissance'];} else {$date_naissance='';}
	if (isset($_POST['admin'])) {$admin=$_POST['admin'];} else {$admin='';}
	if (isset($_POST['suo'])) {$suo=$_POST['suo'];} else {$suo='';}
	if (isset($_POST['actif'])) {$actif=$_POST['actif'];} else {$actif='';}
	if (isset($_POST['regle'])) {$regle=$_POST['regle'];} else {$regle='';}	
	
	if($action=='ajout') {
		// on vérifie que le pseudo n'est pas déjà utilisé
		$requete="SELECT * FROM phpl_membres where pseudo='$pseudo'";
		$resultat=mysql_query($requete);
		if ($row=mysql_fetch_array($resultat)) {
			echo "<div style=\"color: red; font-size:10pt; font-family: verdana;\">Ce pseudo est déjà utilisé !</div><br />";
		} else {			
			$requete="SELECT * FROM phpl_membres where mail='$mail' or mail like '$mail,%' or mail like '%,$mail' or mail like '%,$mail,%'";
			$resultat=mysql_query($requete);
			if ($row=mysql_fetch_array($resultat)) {
				echo "<div style=\"color: red; font-size:10pt; font-family: verdana;\">Cette adresse email est déjà utilisée !</div><br />";
			} else {		
				//		
				// ajout de l'utilisateur dans la base des pronos
				//
				$queryInsert="INSERT INTO phpl_membres (pseudo,mot_de_passe,nom,prenom,mail,adresse,";
				if($code_postal!='') {
					$queryInsert .= "code_postal,";
				}
				$queryInsert .= "ville,";
				if($departement!='') {
					$queryInsert .= "departement,";
				}
				$queryInsert .= "pays,date_naissance,admin,suo,actif,regle) VALUES ('$pseudo','$mot_de_passe','$nom','$prenom','$mail','$adresse',";				
				if($code_postal!='') {
					$queryInsert .= "$code_postal,";
				}
				$queryInsert .= "'$ville',";
				if($departement!='') {
					$queryInsert .= "$departement,";
				}
				$queryInsert .= "'$pays','$date_naissance','$admin','$suo','$actif','$regle')";

				mysql_query($queryInsert) or die ("probleme " .mysql_error());
		
				// rrécupèration de l'id du membre crée pour le copier dans le champ id_prono (car id_prono=id)
				$requete="SELECT * FROM phpl_membres where pseudo='$pseudo' and nom='$nom'";
				$resultat=mysql_query($requete);
				while ($row=mysql_fetch_array($resultat)) {
					$id = $row["id"];
				}
				
				// mise à jour du champ id_prono avec l'id récupéré
				mysql_query("UPDATE phpl_membres SET id_prono='$id' WHERE id=$id") or die ("probleme " .mysql_error());
				
				//
				// ajout du nouveau membre dans le forum
				// 
				
				$query1 = 'SELECT MAX(user_id) AS total FROM '.USERS_TABLE.';';
				$result1 = mysql_query($query1);
				$row1 = mysql_fetch_array($result1);

				$id_user_phpbb = $row1['total'] + 1;        

				$registred = time();
				   
				$query2 = "INSERT INTO ".USERS_TABLE."
				  (
				    user_id,
				    user_active,
				    user_actkey,
				    username,
				    user_password,
				    user_session_time,
				    user_session_page,
				    user_lastvisit,
				    user_regdate,
				    user_level,
				    user_posts,
				    user_style,
				    user_lang,
				    user_dateformat,
				    user_new_privmsg,
				    user_unread_privmsg,
				    user_last_privmsg,
				    user_viewemail,
				    user_attachsig,
				    user_allowhtml,
				    user_allowbbcode,
				    user_allowsmile,
				    user_allowavatar,
				    user_allow_pm,
				    user_allow_viewonline,
				    user_notify,
				    user_notify_pm,
				    user_popup_pm,
				    user_rank,
				    user_avatar_type,
				    user_email
				  )
				  VALUES
				  (
				    '".$id_user_phpbb."',
				    '1',
				    '',
				    '".$pseudo."',
				    '".$mot_de_passe."', 
				    '0',
				    '0',
				    '0',
				    '".$registred."', 
				    '0',
				    '0',
				    '1',
				    'french',
				    'd M Y h:i a',
				    '0',
				    '0',
				    '0',
				    '0',
				    '1',
				    '0',
				    '1',
				    '1',
				    '1',
				    '1',
				    '1',
				    '0',
				    '1',
				    '1',
				    '0',
				    '0',
				    '".$mail."'
				  );";
				$result2 = mysql_query($query2) or die ("probleme " .mysql_error());
				 
				$query3 = "INSERT INTO ".GROUPS_TABLE." (group_name, group_description, group_single_user, group_moderator) VALUES ('".$pseudo."', 'Personal User', 1, 0)";
				$result3 = mysql_query($query3) or die ("probleme " .mysql_error());
				 
				$group_id = mysql_insert_id();
				 
				$query4 = "INSERT INTO ".USER_GROUP_TABLE."
				  (
				    group_id,
				    user_id,
				    user_pending
				  )
				  VALUES
				  (
				    '".$group_id."',
				    '".$id_user_phpbb."',
				    '0'
				  );";
				$result4 = mysql_query($query4) or die ("probleme " .mysql_error());							
						
				echo "<div style=\"color: black; font-size:10pt; font-family: verdana;\"><img src=\"images/success.gif\" />&nbsp;Membre ajouté !</div><br />";
			}
		}				
		
	}
?>

<script>
	function trim(str) {
	   return str.replace(/^\s*|\s*$/g,"");
	}

	function valider() {
        var pseudo = document.getElementById('pseudo').value;
        if(pseudo == null || trim(pseudo) == '') {
	        alert('Le pseudo doit être renseigné !');
	        return false;
        }
        var mot_de_passe = document.getElementById('mot_de_passe').value;
        if(mot_de_passe == null || trim(mot_de_passe) == '') {
	        alert('Le mot de passe doit être renseigné !');
	        return false;
        }
        var mail = document.getElementById('mail').value;
        if(mail == null || trim(mail) == '') {
	        alert('Le mail doit être renseigné !');
	        return false;
        }       
                       
        /*
        var modele = /^[a-zA-Z0-9\.\-_]+@[a-zA-Z0-9]+\.[a-zA-Z]{2,5}$/i;
        if (modele.test(email))
          alert("Votre adresse email est valide !")
        else
          alert("Votre adresse email est invalide !");
        return false;		
        */
	}
</script>

<form name="formAjout" action="index.php" method="POST" onsubmit="return valider()">
	<input type="hidden" name="page" value="ajout_membre" />
	<input type="hidden" name="action" value="ajout" />
	<table class=phpl width="80%">
            <tr>
              <td class=phpl2 align="center" colspan="3">Ajout d'un nouveau pronostiqueur</td>
            </tr>
            <tr class="ligne1">
              <td width="15%">&nbsp;</td>
              <td width="40%" align="left">Login *</td>
              <td width="45%" align="left"><input type="text" id="pseudo" name="pseudo" value="<? print $pseudo ?>" /></td>
            </tr>
            <tr class="ligne2">
              <td>&nbsp;</td>
              <td align="left">Mot de passe *</td>
              <td align="left"><input type="text" id="mot_de_passe" name="mot_de_passe" value="<? print $mot_de_passe ?>" /></td>
            </tr>
            <tr class="ligne1">
              <td>&nbsp;</td>
              <td align="left">Nom</td>
              <td align="left"><input type="text" id="nom" name="nom" value="<? print $nom ?>" /></td>
            </tr>
            <tr>
              <td>&nbsp;</td>
              <td align="left">Prénom</td>
              <td align="left"><input type="text" id="prenom" name="prenom" value="<? print $prenom ?>" /></td>
            </tr>
            <tr>
              <td>&nbsp;</td>
              <td align="left">Mail *</td>
              <td align="left"><input type="text" id="mail" name="mail" value="<? print $mail ?>" /></td>
            </tr>
            <tr>
              <td>&nbsp;</td>
              <td align="left">Adresse</td>
              <td align="left"><input type="text" id="adresse" name="adresse" value="<? print $adresse ?>" /></td>
            </tr>
            <tr>
              <td>&nbsp;</td>
              <td align="left">Code postal</td>
              <td align="left"><input type="text" id="code_postal" name="code_postal" value="<? print $code_postal ?>" /></td>
            </tr>
            <tr>
              <td>&nbsp;</td>
              <td align="left">Ville</td>
              <td align="left"><input type="text" id="ville" name="ville" value="<? print $ville ?>" /></td>
            </tr>            
            <tr>
              <td>&nbsp;</td>
              <td align="left">Département</td>
              <td align="left"><input type="text" id="departement" name="departement" value="<? print $departement ?>" /></td>
            </tr>
            <tr>
              <td>&nbsp;</td>
              <td align="left">Pays</td>
              <td align="left"><input type="text" id="pays" name="pays" value="<? print $pays ?>" /></td>
            </tr>
            <tr>
              <td>&nbsp;</td>
              <td align="left">Date de naissance</td>
              <td align="left"><input type="text" id="date_naissance" name="date_naissance" value="<? print $date_naissance ?>" /></td>
            </tr>
            <tr>
              <td>&nbsp;</td>
              <td align="left">Administrateur ?</td>
              <td align="left">
              	<input type="radio" name="admin" value="1" <? if($admin=='1') {echo 'checked';} ?> />oui&nbsp;&nbsp;&nbsp;
              	<input type="radio" name="admin" value="0" <? if($admin == '' || $admin=='0') {echo 'checked';} ?> />non
              </td>
            </tr> 
            <tr>
              <td>&nbsp;</td>
              <td align="left">SU ?</td>
              <td align="left">
              	<input type="radio" name="suo" value="1" <? if($suo=='1') {echo 'checked';} ?> />oui&nbsp;&nbsp;&nbsp;
              	<input type="radio" name="suo" value="0" <? if($suo == '' || $suo=='0') {echo 'checked';} ?> />non
              </td>
            </tr> 
            <tr>
              <td>&nbsp;</td>
              <td align="left">Actif ?</td>
              <td align="left">
              	<input type="radio" name="actif" value="1" <? if($actif == '' || $actif=='1') {echo 'checked';} ?> />oui&nbsp;&nbsp;&nbsp;
              	<input type="radio" name="actif" value="0" <? if($actif=='0') {echo 'checked';} ?> />non
              </td>
            </tr> 
            <tr>
              <td>&nbsp;</td>
              <td align="left">A réglé ?</td>
              <td align="left">
              	<input type="radio" name="regle" value="1" <? if($regle=='1') {echo 'checked';} ?> />oui&nbsp;&nbsp;&nbsp;
              	<input type="radio" name="regle" value="0" <? if($regle == '' || $regle=='0') {echo 'checked';} ?> />non
              </td>
            </tr>                                                                                          
            <tr>
              <td align="center" colspan="3">
              	<input type="submit" value="Ajouter" />
              	<input type="reset" value="Réinitialiser" />
              </td>
            </tr>            
	</table>
</form>
