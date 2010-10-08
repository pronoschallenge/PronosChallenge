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

	// si l'action a effecuer est l'ajout d'un message...
	if ($_POST['action_gazouilli'] == "post")
	{
		if (isset($_POST['gazouilli']))
		{
			$contenu=$_POST['gazouilli'];
		}
		else
		{
			$contenu='';
		}
		
		mysql_query("INSERT INTO phpl_gazouillis (id_membre, contenu, reponse_a) VALUES ('$user_id','$contenu',null)") or die ("probleme " .mysql_error());
	}
?>

<div class="bloc bloc_gazouillis">
	<div class="rounded-block-top-left"></div>
	<div class="rounded-block-top-right"></div>
	<div class="rounded-outside">
		<div class="rounded-inside">
			<div class="bloc_entete">
				<div class="bloc_icone"></div>
				<div class="bloc_titre">Gazouillis</div>
			</div>
			<div class="bloc_contenu">

<? if ($connecte) { ?>
<div class="gazouilli_form">
	<form method="POST">
		<input type="hidden" name="action_gazouilli" value="post" />
		<textarea id="gazouilli" name="gazouilli" rows="1" cols="79"></textarea>
		<div class="gazouilli_cars_restants">140</div>
		<div class="gazouilli_bouton_submit"><input type="submit" value="Gazouiller !" class="bouton"/></div>
	</form>
</div>	
<? } ?>
<div class="liste_gazouillis">

</div>
<div class="bouton bouton_plus">Plus</div>		

			</div>
		</div>
	</div>
	<div class="rounded-block-bottom-left"></div>
	<div class="rounded-block-bottom-right"></div>
</div>

<script>
	function limitChars(textid, limit)
	{
		var text = $('#'+textid).val(); 
		var textlength = text.length;
		if(textlength > limit)
		{
			$('#'+textid).val(text.substr(0,limit));
			return false;
		}
		else
		{
			$('.gazouilli_cars_restants').text(limit - textlength);
			return true;
		}
	}
	
	var debut = 0;
	
	$(function(){
		$.get('gazouillis_liste.php', 
			{ debut: debut },
			function(data) {
				$('.liste_gazouillis').append(data);
			}
		);
		$('#gazouilli').keyup(function(){
			limitChars('gazouilli', 140);
		});
		$('.bouton_plus').click(function(){
			$('.bouton_plus').html('<img src="images/ajax-loader.gif"/>');
			debut = debut + 10;
			$.get('gazouillis_liste.php', 
				{ debut: debut },
				function(data) {
					$('.liste_gazouillis').append(data);
				}
			);
			$('.bouton_plus').html('Plus');
		});
	});
</script>
