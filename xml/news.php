<html>
<head>

<title>News !</title>
<link rel="stylesheet" href="news.css" type="text/css"/>
</head>

<body>

<?
// Lecture d'un fichier XML
function lit_rss($fichier,$champs) {
   // on lit le fichier
   if($chaine = @implode("",@file($fichier))) {
      // on explode sur <item>
      $tmp = preg_split("/<\/?"."item".">/",$chaine);
      // pour chaque <item>
      for($i=1;$i<sizeof($tmp)-1;$i+=2)
         // on lit les champs demand? <champ>
         foreach($champs as $champ) {
            $tmp2 = preg_split("/<\/?".$champ.">/",$tmp[$i]);
            // on ajoute au tableau
            $tmp3[$i-1][] = @$tmp2[1];
         }
      // et on retourne le tableau
      return $tmp3;
   }
}

$rss = lit_rss("http://www.lequipe.fr/Xml/Football/Titres/actu_rss.xml",array("title","link","description","pubDate",));
// et on affiche...
foreach($rss as $tab) {
  echo '<div class="news_box">
           <div class="news_box_title">'.$tab[0].'</div>
           <div class="news_box_date">posté le '.date("d/m/Y",strtotime($tab[3])).'</div>
           '.$tab[2].'
        </div>';
}
?>
</body>
</html>