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
?>
<html>
<head>
<title>Fiches clubs</title>
<link rel= "stylesheet" type= "text/css" href="../league.css"/>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
<?php

/////////////////////////////////////////////////////////////////////////////////////////////////
// Titre       : Add-on Gestion des clubs (fiches clubs), mini-classement,                     //
//               statistiques, amélioration de la gestion des buteurs pour PhpLeague.          //
// Auteur      : Alexis MANGIN                                                                 //
// Email       : PhpLeagueunivert.org                                                            //
// Url         : http://www.univert.org                                                        //
// Démo        : http://univert42.free.fr/adversaire/classement/consult/classement.php?champ=2 //
// Description : Edition, gestion, fiches phpl_clubs, statistiques, mini-classement...              //
// Version     : 0.71 (29/03/2003)                                                             //
//                                                                                             //
//                                                                                             //
// L'Univert   : Retrouvez quotidiennement l'actualité des Verts ainsi que de                  //
//               nombreuses autres rubriques consacrées à l'AS Saint-Etienne. Mais             //
//               L'Univert c'est avant tout la présentation d'un club devenu légende.          //
//                                                                                             //
/////////////////////////////////////////////////////////////////////////////////////////////////

if (isset($_POST['urllogo'])) {$urllogo=$_POST['urllogo'];} else {$urllogo='';}
if (isset($_POST['nom'])) {$nom=$_POST['nom'];} else {$nom='';}
if (isset($_POST['etat'])) {$etat=$_POST['etat'];} else {$etat='';}
if (isset($_POST['url'])) {$url=$_POST['url'];} else {$url='';}
if (isset($_POST['idd'])) {$idd=$_POST['idd'];} else {$idd='';}

if(!isset($_POST['id']))
{
       $result=mysql_query("SELECT id, nom FROM phpl_clubs ORDER BY nom");
       echo "<form method=\"post\" action=\"\">";
       echo "<h3>".ADMIN_GESTEQUIPE_2."</h3>";
       echo "<select name=\"id\">";
       echo "<option value=\"0\"> </option>";

            while($row = mysql_fetch_array($result))
            {
               $row[1] = stripslashes($row[1]);
	       echo (" <option value=\"$row[0]\">$row[1]");
               echo ("</option>\n");
            }

       echo "</select>";
       $button=ENVOI;
       echo "<input type=\"submit\" value=$button>";
       echo "<input type=\"hidden\" name=\"page\" value=\"fiches_clubs\">";
       echo "<input type=\"hidden\" name=\"action\" value=\"gest\">";
       echo "</form>";
      
}



elseif ($go<>"1")
{
       // On met a jour la bd
       //$query= "SELECT * FROM phpl_logo WHERE id_club='$id'";
       //$result = mysql_query ($query);
       //$nb=mysql_num_rows($result);

       // Si pas d'URL logo renseignée, on l'insère
       //if ($nb=='0')
       //{
         //mysql_query ("INSERT INTO phpl_logo (id_club) VALUES ('$id')") or die ("probleme " .mysql_error());
         //mysql_query ("UPDATE phpl_clubs SET url_logo = '$pts'  WHERE id = '$id_equipe'") or die ("probleme " .mysql_error());

         //}

       $query2= "SELECT * FROM phpl_donnee WHERE id_clubs='$id'";
       $result2 = mysql_query ($query2);
       $nb2=mysql_num_rows($result2);
       $nb_rens=nb_rens2();
       
       // Si pas de donnée pour les renseignements, on les crée
       if (!$nb2==$nb_rens)
       {
         $query="SELECT id FROM phpl_rens";
         $result=mysql_query($query);
         while($row=mysql_fetch_array($result))
         {
         mysql_query ("INSERT INTO phpl_donnee (id_clubs, id_rens) VALUES ('$id', '$row[0]')") or die ("probleme " .mysql_error());
         }
         }
       
       echo "<h3>";
       $query="SELECT id, nom FROM phpl_clubs WHERE id='$id'";
       $result = mysql_query ($query);

               While($row = mysql_fetch_array($result))
               {
                 $row[1] = stripslashes($row[1]);
                 echo "<h3 align=\"center\">";
		 echo ADMIN_GESTEQUIPE_1." ";
		 echo $row[1];
               }

       echo "</h3><br /><br />";
       echo "<table class=tablephpl2 border=\"0\" cellpadding=\"2\" cellspacing=\"0\" valign=\"bottom\" align=\"center\" width=\"90%\"><form method=\"post\" action=\"\">";
       echo "<input type=\"hidden\" name=\"id\" value=\"$id\">";
       echo "<input type=\"hidden\" name=\"page\" value=\"fiches_clubs\">";
       echo "<input type=\"hidden\" name=\"action\" value=\"gest\">";
       $query="SELECT phpl_clubs.id, url_logo FROM phpl_clubs WHERE phpl_clubs.id='$id'";
       $result = mysql_query ($query) or die(mysql_error());;

               While($row = mysql_fetch_array($result))
               {
                          echo "<tr class=phpl3>
                          <td align=\"left\"><b>Classe</b></td>
                          <td align=\"left\"><b>".ADMIN_GESTEQUIPE_3."</b></td>
                          <td align=\"left\"><b>".ADMIN_GESTEQUIPE_4."</b></td>
                          <td align=\"left\"><b>".ADMIN_EQUIPE_5."</b></td>
                          <td align=\"left\"><b>".ADMIN_EQUIPE_6."</b></td></tr>";
                          echo "<tr class=phpl2><td></td><td align=\"left\">".ADMIN_EQUIPE_7."</td>";
                          echo "<td align=\"left\"><input type=\"text\" name=\"urllogo\" value=\"$row[1]\" size=25 maxlength=200><td></td><td></td></td></tr>";
               }

        $query2="SELECT phpl_classe.nom, phpl_classe.id FROM phpl_classe order by rang";
        $result2 = mysql_query ($query2);

                 while($row2 = mysql_fetch_array($result2))
                 {
                             $query="SELECT phpl_rens.nom, phpl_donnee.nom, phpl_donnee.id, phpl_donnee.etat, phpl_donnee.url, phpl_classe.nom
                             FROM phpl_rens, phpl_classe, phpl_clubs, phpl_donnee
                             WHERE phpl_clubs.id='$id'
                                   AND id_clubs='$id'
                                   AND id_classe='$row2[1]'
                                   AND id_classe=phpl_classe.id
                                   AND phpl_rens.id=id_rens
                             ORDER by phpl_rens.rang";
                             $result=mysql_query ($query) or die (mysql_error());

                                     While($row=mysql_fetch_array($result))
                                     {         
                                                $donnee_nom=stripslashes($row[1]);
                                                $rens_nom=stripslashes($row[0]);
                                                echo "<tr><td><b>$row[5]</b></td>";
                                                echo "<td>$rens_nom : </td>";
                                                echo "<td><input type=\"text\" name=\"nom[]\" value=\"$donnee_nom\" size=25></td>";
                                                echo "<td><input type=\"text\" name=\"url[]\" value=\"$row[4]\" size=25></td>";
                                                echo "<td><center><input type=\"text\" name=\"etat[]\"  value=\"$row[3]\" size=1 maxlength=1></center></td>";
                                                echo "<input type=\"hidden\" name=\"idd[]\" value=\"$row[2]\" size=40 maxlength=99>";
                                                echo "</tr>";
                                     }

                 }

         echo "<tr><td colspan=\"8\"><br /><input type=\"hidden\" name=\"go\" value=\"1\">
         <center><input type=\"submit\" value=".ENVOI."></td></tr></table>";
         echo "<br /><br />";
         $query="SELECT url_logo FROM phpl_clubs WHERE id='$id'";
         $result = mysql_query($query);

                 while($row = mysql_fetch_array($result))
                 {
                            echo "<center><img src=\"$row[0]\"><br /><br /><br /><br />";
                 }
                 
         $query="SELECT phpl_classe.nom, phpl_classe.id FROM phpl_classe order by rang";
         $result = mysql_query ($query);

                 while($row = mysql_fetch_array($result))
                 {
                            echo "<table class=tablephpl2 cellspacing=\"0\" align=center width=\"90%\">";
                            echo "<tr class=phpl3><td><b><font color=\"#FFFFFF\">$row[0]</font></b></td></tr>";
                            $id_classe=$row[1];
                            echo "<td><table cellspacing=\"0\"><tr><td><font face=\"arial\" size=\"2\">";
                            $aff_rens=aff_rens ($id_classe, $id);
                            echo "$aff_rens</font>";
                            echo "</tr></td>";
                            echo "</table></td></table><br /><br />";
                 }

         echo "<table class=tablephpl2 cellspacing=\"0\" align=center width=\"90%\"><tr class=phpl3><td><b><font color=\"#FFFFFF\">".CONSULT_CLUB_3."</font></b></td></tr>";
         $query="SELECT annee, phpl_divisions.nom, phpl_championnats.id, phpl_equipes.id
         FROM phpl_saisons, phpl_championnats, phpl_divisions, phpl_clubs, phpl_equipes
         WHERE phpl_equipes.id_champ=phpl_championnats.id
               AND id_division=phpl_divisions.id
               AND phpl_clubs.id=id_club
               AND phpl_equipes.id_club='$id'
               AND phpl_saisons.id=phpl_championnats.id_saison order by annee desc";
         $result = mysql_query($query);
         
                 while($row = mysql_fetch_array($result))
                 {
                            echo "<tr><td>";
                            echo "<tr class=phpl2><td><center>$row[0]/". ($row[0]+1)." ($row[1])</td></tr>";
                            echo "<tr><td><a href=\"../consult/classement.php?champ=$row[2]&type=G%E9n%E9ral\">".CONSULT_CLUB_1."</a></td></tr>";
                            echo "<tr><td><a href=\"../consult/detaileq.php?champ=$row[2]&equipe=$row[3]\">".CONSULT_CLUB_2."</a><br /><br /></td></tr>";
                            echo "</td></tr>";
                 }
         echo"</table><br /><br />";
}

elseif ($go=="1")
{
    reset ($url);
    reset ($nom);
    reset ($etat);
    reset ($idd);
	 while ( list ($cle, $val)= each ($url) and list ($cle, $val1)= each ($nom) and list ($cle, $val2)= each ($etat) and list ($cle, $val3)= each ($idd))
         {   
         $val_1=addslashes($val1);
         mysql_query ("UPDATE phpl_clubs SET url_logo='$urllogo' WHERE id='$id'") or die ("probleme " .mysql_error());
         mysql_query ("UPDATE phpl_donnee SET nom='$val_1', etat='$val2', url='$val' WHERE id='$val3'") or die ("probleme " .mysql_error());
         }
     echo "<font color=\"#008000\">".ADMIN_CLASSE_2."</font>";
     echo "</form>" ;

}

?>
</body>
</html>
