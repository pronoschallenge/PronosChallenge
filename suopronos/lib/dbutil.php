<?php

require_once './data_connect.php';

function ouverture()
{
	global $hostname, $login, $password, $database, $idconnect;
	$_SERVER["DOCUMENT_ROOT"];
	if(($idconnect=@mysql_connect($hostname,$login,$password))==false) {
		$error="Impossible de creer une connexion persistante !";
		return(0);
	}
	if(@mysql_select_db($database,$idconnect)==false) {
		$error="Impossible de selectionner la base !";
		return(0);
	}

	return($idconnect);
	return($PHPLEAGUE_RACINE);
}

?>
