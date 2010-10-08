<?
$time_fin = microtime();
	$time_fin = explode(" ",$time_fin);
	$time_fin = $time_fin[0] + $time_fin[1];

	$time_search = $time_fin - $time_deb;
//$query="INSERT into chargement (id_page, temps) VALUES ($page, $time_search) " ;
//mysql_query($query) or die(mysql_error());
echo " ".ADMIN_GRAPH_6." ";
echo round($time_search,3);

echo " ".ADMIN_GRAPH_5;

?>
