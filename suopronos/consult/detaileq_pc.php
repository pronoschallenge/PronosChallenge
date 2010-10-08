<?
//***********************************************************************/
// Phpleague : gestionnaire de championnat                              */
// ============================================                         */
//                                                                      */
// Version : 0.82b                                                       */
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

require ("../consult/fonctions.php");
ouverture ();
?>

<div class="bloc bloc_detail_ligue1">
	<div class="rounded-block-top-left"></div>
	<div class="rounded-block-top-right"></div>
	<div class="rounded-outside">
		<div class="rounded-inside">
			<div class="bloc_entete">
				<div class="bloc_icone"></div>
				<div class="bloc_titre">Ligue 1</div>
			</div>
			<div class="bloc_contenu">

<?
if (!isset($_REQUEST['champ']))
{
   demande_champ ();
}
// Choix du club
elseif (!isset($_REQUEST['id_equipe']))
{
$champ = $_REQUEST['champ'];

echo "<div align=\"center\"><font size=\"3\">".DETAILEQ_TITRE."</font><br /><br />";

demande_equipe($champ);
echo "</div>";
}

else
{
$champ = $_REQUEST['champ'];
$id_equipe = $_REQUEST['id_equipe'];

$query="SELECT id FROM phpl_equipes WHERE id_champ='$champ' AND id='$id_equipe'";
$result = mysql_query($query);
while ($row=mysql_fetch_array($result))
    {
    $id_equipe=$row[0];
    }
$query="SELECT phpl_clubs.nom FROM phpl_clubs, phpl_equipes WHERE phpl_equipes.id='$id_equipe' AND phpl_equipes.id_club=phpl_clubs.id";
$result = mysql_query($query);
while ($row=mysql_fetch_array($result))
    {
    $club = stripslashes($row[0]);

    }
$color=0;
$query="SELECT phpl_divisions.nom, phpl_saisons.annee, (phpl_saisons.annee)+1 
        FROM phpl_championnats, phpl_divisions, phpl_saisons 
        WHERE phpl_championnats.id='$champ' 
        AND phpl_divisions.id=phpl_championnats.id_division 
        AND phpl_saisons.id=phpl_championnats.id_saison";
$result = mysql_query($query);
    while ($row=mysql_fetch_array($result))
    {
          echo "<div align=\"center\"><h5>";
          echo $row[0];
          echo "  ";
          echo $row[1];
          echo "/";
          echo $row[2];
          echo "</h5></div>";
    }
echo "<div align=\"center\"><b>[ <font class=\"victoire\">".VICTOIRE."</font> | <font class=\"nul\">".NUL."</font> | <font class=\"defaite\">".DEFAITE."</font> ]</b></div><br />";
echo "<table id=\"tableDetailEquipeL1\" cellspacing=\"0\">";
$query1="SELECT phpl_journees.numero, cldom.nom, clext.nom, phpl_matchs.buts_dom, phpl_matchs.buts_ext, phpl_matchs.date_reelle, phpl_matchs.id, dom.id, ext.id
        FROM phpl_equipes as dom, phpl_equipes as ext, phpl_matchs, phpl_journees, phpl_clubs as cldom  , phpl_clubs as clext
        WHERE phpl_matchs.id_equipe_dom=dom.id
        AND phpl_matchs.id_equipe_ext=ext.id
        AND (phpl_matchs.id_equipe_ext='$id_equipe'
        OR phpl_matchs.id_equipe_dom='$id_equipe')
        AND phpl_journees.id_champ='$champ'
        AND dom.id_club=cldom.id
        AND ext.id_club=clext.id
        AND phpl_matchs.id_journee=phpl_journees.id
        ORDER BY phpl_journees.numero";
$result=mysql_query($query1);

	echo "<tr><th>".JOURNEE."</th><th></th><th >".DATE."</th><th></th><th></th><th></th></tr>";
    
        while ($row=mysql_fetch_array($result))
        {
      $row[1] = stripslashes($row[1]);
      $row[2] = stripslashes($row[2]);
	
	 if (($color%2)==0) {$classe="l1LignePaire";} else {$classe="l1LigneImpaire";}

	 $color+=1;
         echo "<tr class=\"$classe\">\n";
         echo "<td align=\"center\"><b>$row[0]</b></td>\n";
         echo "<td></td>";
         echo "<td>";

                $minute = substr($row[5],14,2); // on r�cup�re la minute
                $heure = substr($row[5],11,2); // on r�cup�re l'heure
                $jour = substr($row[5],8,2); // on r�cup�re le jour
                $mois = substr($row[5],5,2); // puis le mois
                $annee = substr($row[5],0,4); // et l'annee



                setlocale(LC_TIME, LEAGUE_LANGUAGE);
                $t= mktime($heure,$minute,0,$mois,$jour,$annee);
                echo strftime("%A %d %B ",$t);
                echo strftime("- %Hh%M",$t);

                                
         echo "</td>";

                if ($row[1]=='exempte' or $row[2]=='exempte'){echo "<td align=\"right\">".ADMIN_RESULTS_1."</td><td colspan=\"5\"></td>";}
                else
                {

                echo "<td align=\"right\">";
                if ($row[3]<>'' and $row[1]==$club)
                {
                  if ($row[3]>$row[4]) echo "<font class=\"victoire\">";
                  if ($row[3]<$row[4]) echo "<font class=\"defaite\">";
                  if ($row[3]==$row[4]) echo "<font class=\"nul\">";
                  echo "<b>"; 
                }
                else if($row[1]<>$club)
                {
                	echo "<a href=\"index.php?page=detaileq_pc&champ=$champ&amp;id_equipe=".$row[7]."\" style=\"text-decoration:none\">";
                }
                echo $row[1];
                
                if ($row[3]<>'' and $row[1]==$club) 
                {
                	echo "</b></font>";
                }
                else if($row[1]<>$club)
                {
                	echo "</a>";
                }
                
                echo "</td>";
                echo "<td align=\"center\">$row[3] - $row[4]";
                echo "</td><td align=\"left\">";
                if ($row[4]<>'' and $row[2]==$club)
                {
                 if ($row[3]<$row[4]) echo "<font class=\"victoire\">";
                 if ($row[3]>$row[4]) echo "<font class=\"defaite\">";
                 if ($row[3]==$row[4]) echo "<font class=\"nul\">";
                 echo "<b>"; 
                }
                else if($row[2]<>$club)
                {
                	echo "<a href=\"index.php?page=detaileq_pc&champ=$champ&amp;id_equipe=".$row[8]."\" style=\"text-decoration:none\">";
                }                
                
                echo $row[2];
                if ($row[4]<>'' and $row[2]==$club) 
                {
	                echo "</b></font>";
	            }
                else if($row[2]<>$club)
                {
                	echo "</a>";
                }
                	            
                echo "</td></tr>";
                }
        }
echo "</table><br />";
if (isset($_SERVER["HTTP_REFERER"])) {$referer=$_SERVER["HTTP_REFERER"];} else {$referer="classement.php?champ=$champ&amp;type=G�n�ral";}
echo "<div align=\"center\"><a href=\"$referer\"><b>".RETOUR."</b></a></div><br />";
}
?>
<br />

			</div>
		</div>
	</div>
	<div class="rounded-block-bottom-left"></div>
	<div class="rounded-block-bottom-right"></div>
</div>	
