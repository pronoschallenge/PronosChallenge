<style type="text/css">
<!--
.Style14 {color: #339900}
.Style2 {font-weight: bold}
.Style3 {font-size: 9px}
.Style4 {	font-size: 9px;
	font-weight: bold;
}
.Style5 {color: #FFFFFF}
.Style7 {font-size: 9px; font-weight: bold; color: #FFFFFF; }
-->
</style>

<?php
/*
PARTIE FORUM !!
*/
$phpbb_root_path = '../../forum/'; //edit this to your phpBB root path
define('IN_PHPBB', true);
include($phpbb_root_path . 'extension.inc');
include($phpbb_root_path . 'common.'.$phpEx);

Function replacebbcode($text){
  
	$text = eregi_replace("\[b:.{0,10}\]", "<b>", $text); 
	$text = eregi_replace("\[\/b:.{0,10}\]", "</b>", $text);
	$text = eregi_replace("\[u:.{0,10}\]", "<u>", $text); 
	$text = eregi_replace("\[\/u:.{0,10}\]", "</u>", $text);
	$text = eregi_replace("\[i:.{0,10}\]", "<i>", $text); 
	$text = eregi_replace("\[\/i:.{0,10}\]", "</i>", $text);
	$text = eregi_replace("\[url=http:.{1,80}\]"," ",$text);
	$text = eregi_replace("\[\/url\]"," ",$text);
	$text = eregi_replace("\[url\]"," ",$text);
	$text = eregi_replace("\[.{1,10}:.{1,10}\]"," ",$text);
	$text = eregi_replace("\[\/.{1,10}:.{1,10}\]"," ",$text);
    $text = eregi_replace("\[\/.{1,20}\]"," ",$text);
    $text = eregi_replace("\[.{1,20}\]"," ",$text);
Return $text;
}
 
// various attributes - experiment!

$NUM_POSTS = 30;
$POST_IMAGE ="XP_NewFile.gif"; // icon next to each item
$TEXT_ON = FALSE; //display some of the text of the post?
$TEXT_LEN = 200;  //number of chars if above is true
$HIDE = true;//true or false - if true, do not show posts from certain forums - see below
$hide_level = 0;// display threshold 0=only show posts in forums open for guest reading,1= also registered, 2=also Mods only 3=show ALL posts even those froums for admins only
$fontheadersize="2";
$fontheadercolor="black";
$fontsize=1;
$fontcolor="#5695BA";
$fontsizetext=1;
$fontcolortext="#5695BA";
$fontheaderface="verdana";
$box_title = "<font size=\"$fontheadersize\" color=\"$fontheadercolor\" face=\"$fontheaderface\"><b> Derniers messages postés</b></font>";
$box_content = "";

$time=time();
$time=date("d M Y h:i a",$time);

$sqlxx="SELECT a1.post_id AS postid, a1.poster_id AS poster, a1.forum_id, a1.topic_id AS topic, a1.post_time AS time, a2.post_subject AS subject, a2.post_text AS text FROM phpbb_posts a1, phpbb_posts_text a2, phpbb_forums a3 WHERE a1.post_id = a2.post_id AND a1.forum_id = a3.forum_id";
  

if($HIDE) $sqlxx .= " AND a3.auth_view <= \"" . $hide_level . "\"";

$sqlxx .= " ORDER BY a1.post_time DESC";

$resultxx = mysql_query($sqlxx) or die("Cannot query database");

if($resultxx){
	
	$box_content .="<table  cellpadding=\"0\" cellspacing = \"0\" width= \"100%\" border=\"0\">";
	for($i=0;$i<$NUM_POSTS;$i++){
		if($post = mysql_fetch_array($resultxx)){
		$result3=mysql_query("SELECT username FROM phpbb_users WHERE user_id =" . $post["poster"]);
		$author=mysql_fetch_array($result3);
		$result4 = mysql_query("SELECT forum_name FROM phpbb_forums WHERE forum_id =" . $post["forum_id"]);
		$forum=mysql_fetch_array($result4);
			if(!$post["subject"]){
				$result2=mysql_query("SELECT topic_title FROM phpbb_topics WHERE topic_id =" . $post["topic"]);
				$replyto = mysql_fetch_array($result2);
				$post["subject"]="RE: " . $replyto["topic_title"];
				mysql_free_result($result2);
			}
			$box_content .="<tr><td bgcolor=\"#FFFFFF\"><font size=\"$fontsize\" color=\"$fontcolor\" face=\"$fontheaderface\">&nbsp;" . date("d-m-y ", $post["time"]) . "&nbsp;<a href=\"" . $phpbb_root_path . "/viewtopic.php?t=" .$post["topic"] . "\" title=\"Posté par : " . $author["username"]. " &nbsp;&nbsp; Dans : " .          $forum["forum_name"] . "\">" . $post["subject"] . "</a></font></td></tr>";		
			if($TEXT_ON){
        $post["text"] = replacebbcode($post["text"]);
        $post["text"] = substr($post["text"],0,$TEXT_LEN);
				$box_content .= "<tr><td cellpadding=\"0\" bgcolor=\"#FFFFFF\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<font color=\"$fontcolortext\" size=\"$fontsizetext\" face=\"$fontheaderface\">" . $post["text"] . "...</font></td></tr>";
			}

		}
		
			
	}
$box_content .="</table>";
}


/*
PARTIE NEWS
*/

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

$rssPC = lit_rss("flux.xml",array("title","link","description","pubDate",));
?>
<table width="100%" align="center" cellspacing="5">
  <tbody>
    <tr>
      <td width="65%" valign="top" class="prono1"><table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" bordercolor="#000000" bgcolor="#FFFFFF">
          <tbody>
            <tr>
              <td width="19%" align="right"><div align="left"><img src="images/la_biscotte.png" width="71" height="50"></div></td>
              <td width="81%" align="right"><p align="left" class="Style2">Et tes pronos, tu y penses ?! </p></td>
            </tr>
          </tbody>
        </table>
          <table width="100%"  border="0" cellspacing="0" cellpadding="2">
            <tr>
              <td height="158" bgcolor="#333333"><table width="100%" border="0" align="center" cellpadding="1" cellspacing="0" bordercolor="#000000" bgcolor="#FFFFFF">
                  <tbody>
                    <tr bordercolor="#333333" bgcolor="#333333">
                      <td align="right"><div align="center" class="Style5"><span class="Style5"></span></div></td>
                      <td align="right"><span class="Style5"></span></td>
                      <td align="right"><div align="center" class="Style5"><span class="Style4">Matchs</span></div></td>
                      <td align="right"><span class="Style5"></span></td>
                      <td align="center"><span class="Style4 Style5">Tps</span></td>
                    </tr>
                    <tr bordercolor="#333333">
                      <td width="14%" align="right"><div class="noir Style3">
                          <div align="center" class="Style14">OK</div>
                      </div></td>
                      <td width="14%" align="right"><span class="noir Style3">Lorient</span></td>
                      <td width="24%"><div align="center" class="Style3">-</div></td>
                      <td width="26%"><div class="blanc Style3">Bordeaux</div></td>
                      <td width="22%" align="center"><div class="blanc Style3">4 jours</div></td>
                    </tr>
                    <tr bordercolor="#333333" bgcolor="#F0F0F0">
                      <td align="right"><div class="blanc Style3 Style12">
                          <div align="center">KO</div>
                      </div></td>
                      <td align="right"><span class="blanc Style3">Lyon</span></td>
                      <td><div align="center" class="Style3">-</div></td>
                      <td><div class="blanc Style3">Toulouse</div></td>
                      <td align="center"><div class="blanc Style3">4 jours</div></td>
                    </tr>
                    <tr bordercolor="#333333">
                      <td align="right"><div class="blanc Style3 Style12">
                          <div align="center">KO</div>
                      </div></td>
                      <td align="right"><span class="blanc Style3">Monaco</span></td>
                      <td><div align="center" class="Style3">-</div></td>
                      <td><div class="blanc Style3">Saint-Etienne</div></td>
                      <td align="center"><div class="blanc Style3">4 jours</div></td>
                    </tr>
                    <tr bordercolor="#333333" bgcolor="#F0F0F0">
                      <td align="right"><div class="blanc Style3 Style12">
                          <div align="center">KO</div>
                      </div></td>
                      <td align="right"><span class="blanc Style3">Nancy</span></td>
                      <td><div align="center" class="Style3">-</div></td>
                      <td><div class="blanc Style3">Sedan</div></td>
                      <td align="center"><div class="blanc Style3">4 jours</div></td>
                    </tr>
                    <tr bordercolor="#333333">
                      <td align="right"><div class="blanc Style3 Style12">
                          <div align="center">KO</div>
                      </div></td>
                      <td align="right"><span class="blanc Style3">Nice</span></td>
                      <td><div align="center" class="Style3">-</div></td>
                      <td><div class="blanc Style3">Nantes</div></td>
                      <td align="center"><div class="blanc Style3">4 jours</div></td>
                    </tr>
                    <tr bordercolor="#333333" bgcolor="#F0F0F0">
                      <td align="right"><div class="blanc Style3 Style12">
                          <div align="center">KO</div>
                      </div></td>
                      <td align="right"><span class="blanc Style3">Sochaux</span></td>
                      <td><div align="center" class="Style3">-</div></td>
                      <td><div class="blanc Style3">Auxerre</div></td>
                      <td align="center"><div class="blanc Style3">4 jours</div></td>
                    </tr>
                    <tr bordercolor="#333333">
                      <td align="right"><div class="blanc Style3 Style12">
                          <div align="center"><span class="Style14">OK</span></div>
                      </div></td>
                      <td align="right"><span class="blanc Style3">Troyes</span></td>
                      <td><div align="center" class="Style3">-</div></td>
                      <td><div class="blanc Style3">Le Mans</div></td>
                      <td align="center"><div class="blanc Style3">4 jours</div></td>
                    </tr>
                    <tr bordercolor="#333333" bgcolor="#F0F0F0">
                      <td align="right"><div class="blanc Style3 Style12">
                          <div align="center">KO</div>
                      </div></td>
                      <td align="right"><span class="blanc Style3">Valenciennes</span></td>
                      <td><div align="center" class="Style3">-</div></td>
                      <td><div class="blanc Style3">PSG</div></td>
                      <td align="center"><div class="blanc Style3">4 jours</div></td>
                    </tr>
                    <tr bordercolor="#333333">
                      <td align="right" bordercolor="#FFFFFF" bgcolor="#FFFFFF"><div class="blanc Style3 Style12">
                          <div align="center">KO</div>
                      </div></td>
                      <td align="right"><span class="blanc Style3">Lille</span></td>
                      <td><div align="center" class="Style3">-</div></td>
                      <td><div class="blanc Style3">Lens</div></td>
                      <td align="center"><div class="blanc Style3">5 jours</div></td>
                    </tr>
                    <tr bordercolor="#333333" bgcolor="#F0F0F0">
                      <td align="right"><div class="blanc Style3 Style12">
                          <div align="center">KO</div>
                      </div></td>
                      <td align="right"><span class="blanc Style3">Marseille</span></td>
                      <td><div align="center" class="Style3">-</div></td>
                      <td><div class="blanc Style3">Rennes</div></td>
                      <td align="center"><div class="blanc Style3">6 jours</div></td>
                    </tr>
                  </tbody>
              </table></td>
            </tr>
        </table></td>
      <td width="35%" rowspan="3" valign="top" class="prono1"><table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
          <tbody>
            <tr>
              <td width="20%" align="right"><div align="center"><img src="images/biere.jpg" width="38" height="50"></div></td>
              <td width="80%" align="right"><p align="left" class="Style2">Derniers posts sur le forum </p></td>
            </tr>
          </tbody>
        </table>
          <table width="100%" height="100%"  border="0" cellpadding="2" cellspacing="0">
            <tr>
              <td valign="top" bgcolor="#333333"><table width="100%" border="0" align="center" cellpadding="1" cellspacing="0" bordercolor="#000000" bgcolor="#FFFFFF">
                  <tbody>
                    <tr bordercolor="#333333" bgcolor="#333333">
                      <td align="right"><div align="center" class="Style5"><span class="Style4">Date</span></div></td>
                      <td align="right"><div align="center"><span class="Style7">Rubrique</span></div></td>
                    </tr>
					<?php print($box_content);?>
                  </tbody>
              </table></td>
            </tr>
        </table></td>
    </tr>
    <tr>
      <td class="prono1"><br>
          <img src="images/news.png" width="37" height="35" align="absmiddle"> <strong> &nbsp;&nbsp;L'info en continue ! </strong></td>
    </tr>
    <tr>
      <td class="prono1"><table width="100%"  border="0" cellspacing="0" cellpadding="2">
          <tr>
            <td bgcolor="#333333">
			<table width="100%" border="0" align="center" cellpadding="1" cellspacing="0" bordercolor="#000000" bgcolor="#FFFFFF">
                  <tr bordercolor="#333333" bgcolor="#333333">
                    <td width="100%" align="right"><div align="center" class="Style5"><span class="Style4">Actualit&eacute;s</span></div></td>
                  </tr>
				  <tr>
				  <td>
				  <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
				  <?php
					foreach($rssPC as $tab) 
					{
						if(date("d/m/Y",strtotime($tab[3]))-date("d/m/Y",strtotime("now"))==0)
						{
							echo '<tr>
				           <td bgcolor="#FFFFFF"><font face=verdana size=1 color=5695BA><b>&nbsp;'.date("d-m-y",strtotime($tab[3])).'&nbsp;</b></font></td>
				           <td bgcolor="#FFFFFF"><font face=verdana size=1><b>&nbsp;Pronos&nbsp;</b></font></td>
				           <td bgcolor="#FFFFFF"><font face=verdana size=1><b>&nbsp;'.$tab[0].'&nbsp;</b></font></td>
				           <!--<td bgcolor="#FFFFFF"><font face=verdana size=1><b>'.$tab[2].'</b></font></td>-->
						   <td>&nbsp;</td>
					       </tr>';
						}
					}
				  ?>
				  <?php
					foreach($rss as $tab) 
					{
						if(date("d/m/Y",strtotime($tab[3]))-date("d/m/Y",strtotime("now"))==0)
						{
							echo '<tr>
				           <td bgcolor="#FFFFFF"><font face=verdana size=1 color=5695BA>&nbsp;'.date("d-m-y",strtotime($tab[3])).'&nbsp;</font></td>
   				           <td bgcolor="#FFFFFF"><font face=verdana size=1><b>&nbsp;Foot&nbsp;</b></font></td>
				           <td bgcolor="#FFFFFF"><font face=verdana size=1>&nbsp;'.substr(strrchr($tab[0],"- "),1).'&nbsp;</font></td>
				           <!--<td bgcolor="#FFFFFF"><font face=verdana size=1>'.$tab[2].'</font></td>-->
						   <td><img src="images/lien.png">123</td>
					       </tr>';
						}
					}
				  ?>
				  </table>
				  </td>
				  </tr>
				  </table>
            </table>
			</td>
          </tr>
      </table>
	  </td>
    </tr>
  </tbody>
</table>