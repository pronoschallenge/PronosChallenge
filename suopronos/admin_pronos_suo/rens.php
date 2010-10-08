<?php
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
<title>Edition renseignements</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>

<?php

/////////////////////////////////////////////////////////////////////////////////////////////////
// Titre       : Add-on Gestion des clubs (fiches clubs), mini-classement,                     //
//               statistiques, amélioration de la gestion des buteurs pour PhpLeague.          //
// Auteur      : Alexis MANGIN                                                                 //
// Email       : Alexis@univert.org                                                            //
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




print ("<SCRIPT type=\"text/javascript\">\n");
print ("<!--\n");
print ("function demander_confirmation()\n");
print ("{\n");
print ("var champ_select = document.getElementById('rens');\n");
$message=ADMIN_SECURITE_RENS;
print ("var message = \"$message \";\n");
print ("message = message + champ_select.options[champ_select.options.selectedIndex].text + \" ?\"; \n");

// confirm() fait apparaitre la boite de dialogue
print ("if (confirm(message))\n");
print ("{\n");
// action à faire si OK (soumettre le formulaire)
print ("return true;\n");
print ("}\n");
print ("else\n");
print ("{\n");
// action à faire si 'Annuler' (ici, rien)
print ("return false;\n");
print ("}\n");
print ("}\n");

print ("//-->\n");
print ("</SCRIPT>\n");

echo "<font class=phpl>".ADMIN_RENS_TITRE."</font><br /><br /><br />";

if (isset($_POST['rens'])) {$rens=$_POST['rens'];} else {$rens='';}
if (isset($_POST['url'])) {$url=$_POST['url'];} else {$url='';}
if (isset($_POST['nom'])) {$nom=$_POST['nom'];} else {$nom='';}
if (isset($_POST['classe'])) {$classe=$_POST['classe'];} else {$classe='';}
if (isset($_POST['rang'])) {$rang=$_POST['rang'];} else {$rang='';}
if (isset($_POST['data'])) {$data=$_POST['data'];} else {$data='';}


switch($go) // Ajout/ suppression
{
 	 case "supprens" :
	 {

     mysql_query (" DELETE FROM phpl_rens WHERE id='$rens' ") or die ("probleme " .mysql_error());
     mysql_query (" DELETE FROM phpl_donnee WHERE id_rens='$rens' ") or die ("probleme " .mysql_error());

	 ?>
    <table class=phpl width="80%">
            <tr>
              <td class=phpl2 align="center" colspan="2"><?php echo ADMIN_CLASSE_5; ?></td>
            </tr>
            <tr>
              <td align="center" colspan="2"><b><?php echo ADMIN_RENS_SUPP2; ?></b></td></tr></table><br />
              
              <?php

	 continue;
	 }
	 case "crerens":
	 {

           $rens=addslashes($rens);

	 mysql_query ("INSERT INTO phpl_rens (nom) VALUES ('$rens')") or die ("probleme " .mysql_error());
         $rens2=rens2($rens);

         $query="SELECT id FROM phpl_clubs";
         $result=mysql_query($query);
         while($row=mysql_fetch_array($result))
         {
         mysql_query ("INSERT INTO phpl_donnee (id_clubs, id_rens) VALUES ('$row[0]', '$rens2')") or die ("probleme " .mysql_error());
         }

         ?>
    <table class=phpl width="80%">
            <tr>
              <td class=phpl2 align="center" colspan="2"><?php echo ADMIN_CLASSE_5; ?></td>
            </tr>
            <tr>
              <td align="center" colspan="2"><b><?php echo ADMIN_CLUB_CREA2; ?></b></td></tr></table><br />

              <?php
	 
 	 continue;
	 }
	 default:
	 {}
}

 if ($actionc=="2")
     {
      reset ($url);
    reset ($nom);
    reset ($id);
	 while ( list ($cle, $val)= each ($url) and list ($cle, $val2)= each ($nom) and list ($cle, $val3)= each ($id))
         {
          $rens_nom = addslashes($val2);
          mysql_query ("UPDATE phpl_rens SET url='$val', nom='$rens_nom' WHERE id='$val3'") or die ("probleme " .mysql_error());
         }
               ?>
    <table class=phpl width="80%">
            <tr>
              <td class=phpl2 align="center" colspan="2"><?php echo ADMIN_CLASSE_5; ?></td>
            </tr>
            <tr>
              <td align="center" colspan="2"><b><?php echo ADMIN_CLASSE_2; ?></b></td></tr></table><br />
              
              <?php
               
               echo "</form>" ;
     }

switch($ga)
{
 	 case "supprens":
            {
            reset ($data);
         while ( list($key, $val)= each($data))
	 mysql_query (" UPDATE phpl_rens SET id_classe='0' WHERE phpl_rens.id='$val'") or die ("probleme " .mysql_error());
	 ?>
    <table class=phpl width="80%">
            <tr>
              <td class=phpl2 align="center" colspan="2"><?php echo ADMIN_CLASSE_5; ?></td>
            </tr>
            <tr>
              <td align="center" colspan="2"><b><?php echo ADMIN_RENS_SUPP2; ?></b></td></tr></table><br />

              <?php
	 continue;
	 }
	 default:
	 {}

	 case "crerens":
	 {
       	reset ($classe);
	while ( list($val, $value)= each($classe))
	 mysql_query ("update phpl_rens SET id_classe='$value' WHERE id='$rens'") or die ("probleme " .mysql_error());
         
         ?>
    <table class=phpl width="80%">
            <tr>
              <td class=phpl2 align="center" colspan="2"><?php echo ADMIN_CLASSE_5; ?></td>
            </tr>
            <tr>
              <td align="center" colspan="2"><b><?php echo ADMIN_CLUB_CREA2; ?></b></td></tr></table><br />
              
              <?php

 	 continue;
	 }
	 default:
	 {}
}

if ($actionc=="1")
   {
      reset ($rang);
    reset ($id);
	 while ( list ($cle, $val)= each ($rang) and list ($cle, $val2)= each ($id))
         {
                 mysql_query ("UPDATE phpl_rens SET rang='$val' WHERE id='$val2'") or die ("probleme " .mysql_error());
                 
        }

        ?>
    <table class=phpl width="80%">
            <tr>
              <td class=phpl2 align="center" colspan="2"><?php echo ADMIN_CLASSE_5; ?></td>
            </tr>
            <tr>
              <td align="center" colspan="2"><b><?php echo ADMIN_CLASSE_2; ?></b></td></tr></table><br />
              
              <?php
      echo "</form>" ;
                      
   }



?>
<table class=phpl width="80%">
            <tr>
              <td class=phpl2 align="center" colspan="2"><?php echo ADMIN_RENS_TITRE; ?></td>
            </tr>
            <tr>
              <td align="center"  colspan="2"><b><?php echo ADMIN_RENS_8; ?></b></td></tr>
<?php 

// Suppression d'un renseignement

echo "<form method=\"post\" action=\"\">";
echo "<tr><td>".ADMIN_RENS_SUPP1;
echo "<select name=\"rens\">";
echo "<option value=\"0\"> </option>";
$result = mysql_query("SELECT id, nom FROM phpl_rens ORDER BY nom");

        while($row = mysql_fetch_array($result))
	{
                   $a=$row[1]+1;
                   $rens_nom = stripslashes($row[1]);
	           echo (" <option value=\"$row[0]\">$rens_nom");
	           echo ("</option>\n");
	}

echo "</select></td><td align=right>";
$button=ADMIN_RENS_BUTTON_SUPP;
echo "<input type=\"hidden\" name=\"go\" value=\"supprens\">";
echo "<input type=\"hidden\" name=\"page\" value=\"fiches_clubs\">";
echo "<input type=\"hidden\" name=\"action\" value=\"rens\">";
echo "<input type=\"submit\" name=\"envoi\" value=$button onclick=\"return demander_confirmation()\">";
echo "</form></td></tr>";

// Ajout d'un renseignement

echo "<tr><td align=\"center\"  colspan=\"2\"><b>".ADMIN_RENS_7."</b></td></tr>";
echo "<tr><td>";
echo "<form method=\"post\" action=\"\">";
echo ADMIN_RENS_NOM;
echo "<input type=\"text\" size=\"30\" name=\"rens\" maxlength=\"50\">";
echo "<input type=\"hidden\" name=\"page\" value=\"fiches_clubs\">";
echo "<input type=\"hidden\" name=\"action\" value=\"rens\">";
echo "<input type=\"hidden\" name=\"go\" value=\"crerens\">";
$button=ADMIN_RENS_BUTTON_CREA;
echo "</td><td align=right><input type=\"submit\" value=$button></form></td></tr></table><br />";


// Editer les renseignements

     
echo "<br />";
echo ADMIN_RENS_10;
echo "<form method=\"post\" action=\"\">";
echo "<table class=phpl cellspacing=\"0\" align=center border=\"0\" width=\"80%\"><center>";
echo "<tr class=phpl3><td>".ADMIN_RENS_12."</td><td>".ADMIN_RENS_13."</td></tr>";
$query="SELECT id, nom, url FROM phpl_rens";
$result = mysql_query($query);

        while($row = mysql_fetch_array($result))
        {
           $rens_nom = stripslashes($row[1]);
           echo "<tr>";
           echo "<td><center><input type=\"text\" name=\"nom[]\"  value=\"$rens_nom\" size=40 maxlength=50></td>";
           echo "<td><center><input type=\"text\" name=\"url[]\"  value=\"$row[2]\" size=40 maxlength=200></td>";
           echo "<input type=\"hidden\" name=\"id[]\"  value=\"$row[0]\">";
           echo"</tr>";
        }
        
echo "<input type=\"hidden\" name=\"actionc\"  value=\"2\">";
echo "<input type=\"hidden\" name=\"page\" value=\"fiches_clubs\">";
echo "<input type=\"hidden\" name=\"action\" value=\"rens\">";
echo "<tr><td colspan=\"2\"><br /></tr>";
echo "<tr><td colspan=\"2\"><center><input type=\"submit\" value=".ADMIN_RENS_11."></tr>";
echo "</table></center></form><br />";
echo "<hr>";

// Classer les renseignements


// Entrer un renseignement dans une classe
echo "<br />";
echo "</center>";
$nb_rens2=nb_rens2($id);
$nb_rens2=$nb_rens2+1;
echo ADMIN_RENS_4;
echo "<form method=\"post\" action=\"\">";
echo "<select name=\"rens\">";
echo "<option value=\"0\"> </option>";
$result = mysql_query("SELECT id, nom, id_classe FROM phpl_rens ORDER BY nom");
        while($row = mysql_fetch_array($result))
        {
           $a=$row[1]+1;
           $rens_nom = stripslashes($row[1]);
           echo (" <option value=\"$row[0]\">$rens_nom");
           echo ("</option>\n");
        }

echo "</select>";
echo ADMIN_RENS_1;
echo "<select name=\"classe[]\">";
$result = mysql_query("select id, nom FROM phpl_classe ");
        while($row = mysql_fetch_array($result))
        {
           echo ("<option value=\"$row[0]\">$row[1]");
           echo ("</option>\n");
        }

echo "</select>";
$button=ADMIN_RENS_7;
echo "<input type=\"submit\" value=$button>";
echo "<input type=\"hidden\" name=\"ga\" value=\"crerens\">";
echo "<input type=\"hidden\" name=\"page\" value=\"fiches_clubs\">";
echo "<input type=\"hidden\" name=\"action\" value=\"rens\">";
echo "</form>";


// Enlever un renseignement d'une classe
echo "<br />";
echo "</center>";
$nb_rens=nb_rens($id);
$nb_rens=$nb_rens+1;
echo "<form method=\"post\" action=\"\">";
echo ADMIN_RENS_5;
echo "<br />";
$query="SELECT phpl_rens.id, phpl_rens.id_classe, phpl_rens.nom, phpl_classe.id, phpl_classe.nom
       FROM phpl_rens, phpl_classe
       WHERE phpl_rens.id_classe=phpl_classe.id";
$result = mysql_query($query) or die (mysql_error());
echo "<select name=\"data[]\"  multiple size=$nb_rens>";
           
           while($row = mysql_fetch_array($result))
	   {
              $rens_nom = stripslashes($row[2]);
              echo (" <option value=\"$row[0]\">$rens_nom" .ADMIN_RENS_6." $row[4]");
	      echo ("</option>\n");
	   }
	   
echo "</select>";
$button=ADMIN_RENS_8;
echo "<input type=\"submit\" value=$button>";
echo "<input type=\"hidden\" name=\"ga\" value=\"supprens\">";
echo "<input type=\"hidden\" name=\"id\" value=\"$id\">";
echo "<input type=\"hidden\" name=\"page\" value=\"fiches_clubs\">";
echo "<input type=\"hidden\" name=\"action\" value=\"rens\">";
echo "</form>";

// Quels renseignements ne sont pas classés ?
echo ADMIN_RENS_14;
$query="SELECT phpl_rens.nom FROM phpl_rens where id_classe='0'";
$result = mysql_query($query);
$nb=mysql_num_rows($result);
        
        if ($nb=="0")
           {
           echo "<br /><center>".ADMIN_RENS_15."</center>";
           }

             while($row = mysql_fetch_array($result))
             {
               $rens_nom = stripslashes($row[0]);
               echo"<br /><center>$rens_nom</center>";
             }
             
echo "<hr>";

// Ordonner les renseignements


echo "<br />";
echo ADMIN_RENS_9;
$query = "SELECT id, nom, rang from phpl_classe ORDER by rang";
echo "<form method=\"post\" action=\"\">";
$result = mysql_query($query);
     
     while($row = mysql_fetch_array($result))
     {
        echo "<table class=phpl border=\"0\" cellpadding=\"2\" cellspacing=\"0\" valign=\"bottom\" align=\"center\" width=\"90%\"><tr class=phpl3><td><b> $row[1] </b></td></tr>";
        $query2="SELECT id, nom, rang, id_classe FROM phpl_rens WHERE id_classe='$row[0]' ORDER by rang";
        $result2=mysql_query($query2);
             
             while($row = mysql_fetch_array($result2))
             {  $rens_nom = stripslashes($row[1]);
                echo "<tr>
                <td><input type=\"text\" name=\"rang[]\" value=\"$row[2]\" size=2 maxlength=2> $rens_nom</td>
                <input type=\"hidden\" name=\"id[]\" value=\"$row[0]\">
                </tr>";
             }
     echo "<br /></table>";
     }
     echo "<input type=\"hidden\" name=\"page\" value=\"fiches_clubs\">";
echo "<input type=\"hidden\" name=\"action\" value=\"rens\">";
echo "<br /><input type=\"hidden\" name=\"actionc\" value=\"1\">

<center><input type=\"submit\" value=".ENVOI."></center></form>";




?>
</body>
</html>
