<?php
require_once("fonctions.php");
require_once("../config.php");

ouverture ();

header("Content-type: text/xml");
print("<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?>");
?>

<rss version="2.0">

<?php
$url = $_SERVER["REQUEST_URI"]; 
$urlElements = parse_url($url);
if($urlElements["query"] == "gazouillis")
{
?>

	<channel id="pronoschallenge">
		<description>Flux des gazouillis de PronosChallenge</description>
		<language>fr</language>
		<link>http://www.pronoschallenge.fr</link>
		<title>PronosChallenge - Gazouillis</title>

<?php
	$resultats=mysql_query("SELECT id_membre, pseudo, contenu, date_creation 
			FROM phpl_gazouillis, phpl_membres 
			WHERE phpl_gazouillis.id_membre=phpl_membres.id
			ORDER BY date_creation DESC
			LIMIT 0,50");
	while ($ligne=mysql_fetch_array($resultats))
	{
?>
		<item>
			<title><?php echo $ligne["pseudo"]?></title>
			<link>http://www.pronoschallenge.fr</link>
			<pubDate><?php echo date("D, d M Y H:i:s O", strtotime($ligne["date_creation"]));?></pubDate>
			<description><?php echo $ligne["contenu"]?></description>
			<author><?php echo $ligne["pseudo"]?></author>
		</item>
<?php
	}
?>
	</channel>
<?php
}
else if($urlElements["query"] == "news")
{
?>
	<channel id="pronoschallenge">
		<description>News de PronosChallenge</description>
		<language>fr</language>
		<link>http://www.pronoschallenge.fr</link>
		<title>PronosChallenge - News</title>

	</channel>
<?php
}
?>
</rss>
