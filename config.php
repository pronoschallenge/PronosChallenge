<?php
function ouverture()
{
  $_SERVER["DOCUMENT_ROOT"];
require("data_connect.php");
if(($idconnect=@mysql_connect($hostname,$login,$password))==false){
$error="Impossible de creer une connexion persistante !";
return(0);
}
if(@mysql_select_db($database,$idconnect)==false){
$error="Impossible de selectionner la base !";
return(0);
}
return($idconnect);
return($PHPLEAGUE_RACINE);
}
include("data_connect.php");
include("../lang/lang_".$lang.".php");
?>
