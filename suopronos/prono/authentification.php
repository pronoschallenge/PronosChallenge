<?php
if (isset($_REQUEST['code_erreur']))
{
	echo "<div id=\"erreur_login\"><img src=\"images/erreur-login.png\" />";
	if($_REQUEST['code_erreur'] == "0")
	{
		echo "Veuillez renseigner tous les champs";
	}
	else if($_REQUEST['code_erreur'] == "1")
	{
		echo "Identifiants erron&eacute;s"; 		
	}
	echo "</div>";
}
?>

<div class="bloc login">
	<div class="rounded-block-top-left"></div>
	<div class="rounded-block-top-right"></div>
	<div class="rounded-outside">
		<div class="rounded-inside">
			<!--<div class="bloc_entete">
				<div class="bloc_titre">Login</div>
			</div>-->
			<div class="bloc_contenu">
				<div id="login_icone">
					<img src="images/cles.png" />
				</div>
				<div id="login_form">
					<form action="login.php" method="post">
						<input type="hidden" name="autoidentification" value="1">	
						<table>
							<tr>
								<td id="libelle_identifiant">Identifiant :</td>
								<td id="input_identifiant"><input id="user" name="user" class="" onFocus="this.className=''" onBlur="this.className=''" /></td>
							</tr>
							<tr>
								<td id="libelle_motdepasse">Mot de passe :</td>
								<td id="input_motdepasse">
									<input id="pass" type="password" name="pass" class="" onFocus="this.className=''" onBlur="this.className=''" />
									<a href="?page=perdu_mdp">Mot de passe oubli&eacute; ?</a>
								</td>
							</tr>							
						</table>
						<div><input name="bouton-connexion" type="image" src="images/bouton-simple-connexion.png" id="bouton-connexion" class="bouton_connexion" /></div>
					</form>
				</div>
			</div>
		</div>
	</div>
	<div class="rounded-block-bottom-left"></div>
	<div class="rounded-block-bottom-right"></div>
</div>

<!--
<TABLE border="0" cellpadding="0" cellspacing="0" height="100%" width="100%" align="center" valign="center">
      <TR>
       <TD>
         <FORM action="login.php" method="post">
           <TABLE width="50%" border=0 align="center" cellpadding="0" cellSpacing=0>
             <TR>
		   <TD rowspan="3" width="20" align="center"><img src="images/barre_grde.png"></TD>
               <TD><div style="font-face:verdana;font-size:12pt;color:#FFFFFF"><B>&nbsp;<?php echo PRONO_MENU_LOGIN; ?></B></div></TD>
               <TD><div style="font-face:verdana;font-size:12pt;color:#FFFFFF"><B>&nbsp;<?php echo PRONO_MENU_MDP; ?></B></div></TD>
		   <TD>&nbsp;</TD>
             </TR>
             <TR>
		   <TD>
			<div class="menu2">
			 <INPUT size="20" name="user" class="inputFlat" onFocus="this.className='inputFlatUpd'" onBlur="this.className='inputFlat'"> 
			</div>
		   </TD>
               <TD><div class="menu2"><INPUT size="20" type="password" name="pass" class="inputFlat" onFocus="this.className='inputFlatUpd'" onBlur="this.className='inputFlat'"></div></TD>
		   <TD>
			 <input type="hidden" name="autoidentification" value="1">			     
			 <input type="image" src="images/valider.png" border="0"> 
		   </TD>
             </TR>
	       <TR>
		   <TD>
			<span align="left" style="font-size:6pt;color:#FFFFFF"><a href="mailto:pronoschallenge.info@online.fr" style="color:#FFFFFF">S'INSCRIRE</a></span>
		   </TD>
		   <TD>
			<span align="left" style="font-size:6pt;color:#FFFFFF"><a href="?page=perdu_mdp" style="color:#FFFFFF">MOT DE PASSE OUBLIE ?</a></span>
		   </TD>
		   <TD>&nbsp;</TD>
	     </TR>            
           </TABLE>
         </FORM>
         <br><br><br>
       </TD>
      </TR>
</TABLE>
-->
