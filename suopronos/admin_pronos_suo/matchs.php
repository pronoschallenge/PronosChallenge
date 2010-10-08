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

if (empty ($numero))
  {
    $requete="SELECT max(phpl_journees.numero) FROM phpl_journees, phpl_matchs where phpl_journees.id=phpl_matchs.id_journee and buts_dom is not NULL and phpl_journees.id_champ='$champ'";
    $resultats=mysql_query($requete);
     while ($row=mysql_fetch_array($resultats))
       {
         $numero=$row[0];
       }
       if ($numero=="") {$numero="1";}
  }

if ($boucle==1)
{ 
  $boucle=0;
  global $boucle;
  // recherche de id_journee de la journee miroir si existe
    if ($miroir<>"none")
     {     
       $query = "SELECT id FROM phpl_journees WHERE  id_champ='$champ' AND numero='$miroir'";
       $result=mysql_query ($query);
          while ($row=mysql_fetch_array($result))
          {
           $id_journee_miroir=$row[0] ;
          }
       $query="DELETE FROM phpl_matchs WHERE id_journee='$id_journee_miroir'";
       mysql_query($query) or die(mysql_error());

     }
  // effacer les anciennes données
  $x=$numero-1;
  $query = "SELECT id FROM phpl_journees WHERE id_champ='$champ' AND numero='$x'";
  $result=mysql_query ($query);
       while ($row=mysql_fetch_array($result))
       {
         $id_journee=$row[0] ;
       }
  $query="DELETE FROM phpl_matchs WHERE id_journee='$id_journee'";
  mysql_query($query) or die(mysql_error());

  // insertion des nouvelles données
  for ( $counter=0; $counter<((nb_equipes($champ))/2); $counter++ )
   {
     //insertion
     //echo "<br />ALLER=".$id_journee."N°:".$x;
     mysql_query("INSERT INTO phpl_matchs (id_journee, id_equipe_dom, id_equipe_ext) VALUES ('$id_journee','$id_domicile[$counter]','$id_exterieur[$counter]') ") or die(mysql_error());

    //insertion journée miroir, si existe
	if ($miroir<>"none")
	{
          //echo "<br />MIROIR=".$id_journee_miroir."N°:".$miroir;
	  $query="INSERT INTO phpl_matchs (id_journee, id_equipe_dom, id_equipe_ext) VALUES ('$id_journee_miroir','$id_exterieur[$counter]','$id_domicile[$counter]') ";
          mysql_query ($query) or die(mysql_error());
        }        

   }
}

?>

<table class=phpl width="80%">
            <tr>
              <td class=phpl2 align="center" colspan="3" width="100%"><? echo ADMIN_MATCHS_TITRE." "; affich_champ ($champ); ?></td><td class=phpl2 align="right"><a href="#" onclick="window.open('Assistant_fr/matchs.htm','Assistant','toolbar=0,location=0,directories=0,status=0,scrollbars=1,resizable=0,copyhistory=0,menuBar=0,width=512,height=512');return false;"><img border="0" alt="Assistant" src="aide.gif"></a></td>
            </tr>
            <tr>
            
            <?
  $requete="SELECT id FROM phpl_journees WHERE id_champ='$champ'";
  $resultats=mysql_query($requete);
  $nb_equipes1=mysql_num_rows($resultats);
  $nb_equipes=nb_equipes ($champ);


  
  if ($nb_equipes1!==(($nb_equipes)*2-2))
  {
    echo "<td colspan=\"2\">".ADMIN_MATCHS_1." <a href=\"?page=championnat&action=dates&champ=$champ\">".DATE."</a></td></tr>";
  }
  else
  {       
            echo "<td align=\"center\" colspan=\"4\">";journees ($champ, $numero, $action); ?></td></tr>
            <tr><td align="center" colspan="4">

<?
// TEST EXISTENCE DE LA JOURNEE DANS LA TABLE matchs

$query = "SELECT id FROM phpl_journees WHERE id_champ='$champ' AND numero='$numero'";
$result=mysql_query ($query);
          while ($row=mysql_fetch_array($result))
          {
          $id_journee=$row[0] ;
          }

echo ADMIN_JOURNEES_MSG9;
echo " $numero</b> ";
$nb_rencontres = (nb_equipes ($champ))/2;



echo "<table width=100% align=center>";
echo "<form method=\"post\" action=\"\">" ;
echo "<TR valign=\"top\"><TD align=center><b>";
echo DOMICILE;
echo "</b><TD align=center><b>";
echo EXTERIEUR;
echo "</b>";

$i=0;
for ($counter=$nb_rencontres; $counter>0 ; $counter=$counter-1 ) // Nb de rencontres dans la journée

        {
           if (($i%2)==0) {$class="phpl3";}
           else {$class="phpl4";}
        // saisie des rencontres DOMICILE

		echo "<TR valign=\"top\"><TD class=$class align=center>";
		$counter0=$counter-1;
		$query=" SELECT * FROM phpl_matchs WHERE id_journee='$id_journee' LIMIT $counter0,1";
		$res1=mysql_query($query) or die(mysql_error());;
			while ($result_res1 = mysql_fetch_array($res1))
			{
			$existant_dom=$result_res1['id_equipe_dom'];
			$existant_ext=$result_res1['id_equipe_ext'];
			}
        echo "<select name=\"id_domicile[]\">";
        echo "<option value=\"id[$counter]\"$club[$counter]></option> " ;
        $query = "SELECT DISTINCT phpl_clubs.nom, phpl_equipes.id FROM phpl_clubs, phpl_equipes
                  WHERE phpl_equipes.id_champ='$champ' AND phpl_clubs.id=phpl_equipes.id_club ORDER BY phpl_clubs.nom";

        $result=mysql_query($query) or die(mysql_error());
		
        while ($row = mysql_fetch_array($result))
                {
                  $row[0] = stripslashes($row[0]);
		  if ($existant_dom==$row[1]) echo (" <option value=\"$row[1]\" selected>$row[0]");
		  else echo (" <option value=\"$row[1]\">$row[0]");
                  echo ("</option>\n");
                }
        echo "</select>";

        // saisie des rencontres EXTERIEUR
        echo "<TD class=$class align=center>";
        echo "<select name=\"id_exterieur[]\">";
        echo "<option value=\"0\"> </option> " ;
        $query = "SELECT DISTINCT phpl_clubs.nom, phpl_equipes.id 
        FROM phpl_clubs, phpl_equipes WHERE phpl_equipes.id_champ='$champ' AND phpl_clubs.id=phpl_equipes.id_club ORDER BY phpl_clubs.nom";

        $result=mysql_query($query) or die(mysql_error());
        while ($row = mysql_fetch_array($result))
                {
                 $row[0] = stripslashes($row[0]);
				if ($existant_ext==$row[1]) echo (" <option value=\"$row[1]\" selected>$row[0]");
                else echo (" <option value=\"$row[1]\">$row[0]");
                echo ("</option>\n");
                }
                $i++;
        }
        echo "</select><TR><TD colspan=\"2\" align=center>";
//JOURNEE MIROIR ?


		echo "<b>".JOURNEE_MIROIR." </b>";
		$miroir="none";
                echo" <select name=\"miroir\">";
                echo "<option value=\"none\" selected>".ADMIN_DATES_4."</option>\n";
                $query = "SELECT numero, id FROM phpl_journees WHERE id_champ='$champ'  ORDER BY phpl_journees.numero";
                $result=mysql_query($query);
                while ($row = mysql_fetch_array($result)) {
                echo (" <option value=\"$row[0]\">$row[0]");
                echo ("</option>\n");
                $id_journee=$row[1];
                }

                echo "</select>";
                $numero++;



echo "</select>";

// VALIDATION DU FORMULAIRE
        echo "<input type=\"hidden\" name=\"champ\" value=\"$champ\">";
        echo "<input type=\"hidden\" name=\"boucle\" value=\"1\">";
	echo "<input type=\"hidden\" name=\"go\" value=\"3\">";
        echo "<input type=\"hidden\" name=\"id_journee\" value=\"$id_journee\">";
        echo "<input type=\"hidden\" name=\"page\" value=\"championnat\">";
        echo "<input type=\"hidden\" name=\"action\" value=\"matchs\">";
        echo "<input type=\"hidden\" name=\"numero\" value=\"$numero\">";
      $button=ENVOI;
        echo "<input type=\"submit\" value=$button>" ;
        echo "</form>";




?>
</td></tr></table></table><br /><br />
<table class=phpl width="80%">
  <tr> 
    <td class=phpl2 align=center><? echo ADMIN_COHERENCE_TITRE; ?>
    </td>
  </tr>
  <tr>
    <td>

<?
if ($coherence=="1")
{


// CHECK SUM DU CHAMPIONNAT
  $query=" SELECT sum(id) FROM phpl_equipes WHERE id_champ='$champ' ";
$check_sum=mysql_fetch_array($result=mysql_query($query));
$sumsum=$check_sum[0];
echo "CHEKSUM= ".$sumsum."<br />";

	
$nb_journees=(nb_equipes($champ)*2)-2;
$x=1;
while($x<=$nb_journees)
    {
$query=" SELECT sum(phpl_matchs.id_equipe_dom), sum(phpl_matchs.id_equipe_ext) FROM phpl_matchs, phpl_journees WHERE phpl_matchs.id_journee=phpl_journees.id AND phpl_journees.id_champ='$champ'   AND phpl_journees.numero='$x' ";
$result=mysql_query($query);
$sum=mysql_fetch_array($result);
$sum_day=$sum[0]+$sum[1];
echo "<small>MATCH_SUM = ".$sum_day."  </small>";
    $query="SELECT count(DISTINCT phpl_matchs.id_equipe_dom), count(DISTINCT phpl_matchs.id_equipe_ext) FROM phpl_matchs, phpl_journees   WHERE phpl_matchs.id_journee=phpl_journees.id    AND phpl_journees.id_champ='$champ'    AND phpl_journees.numero='$x' ";
    $result=mysql_query($query);

    while( $row = (mysql_fetch_array($result)) )
        {
        if($row[0]==$row[1] and $row[0]==nb_equipes($champ)/2 and  ($sum[0]+$sum[1]==$sumsum) )
         					{
							
							echo ADMIN_COHERENCE_MSG2." ";
							echo "$x";
							echo ADMIN_COHERENCE_MSG3;
							echo "<br />";
							$incoherent="0";
							}
        else 
			 {
			 echo ADMIN_COHERENCE_MSG2." ";
			 echo "$x ";
			 echo ADMIN_COHERENCE_MSG4;
			 $incoherent="1";
			 echo "<br />";
			 }
        }
    $x++;
    }

$query=" SELECT sum(phpl_matchs.id_equipe_dom), sum(phpl_matchs.id_equipe_ext) FROM phpl_matchs, phpl_journees WHERE phpl_matchs.id_journee=phpl_journees.id AND phpl_journees.id_champ='$champ'  ";
$result=mysql_query($query);
$sum=mysql_fetch_array($result);
$sumsum=$sum[0];
 $query="SELECT sum(phpl_matchs.id_equipe_dom), sum(phpl_matchs.id_equipe_ext) FROM phpl_matchs, phpl_journees WHERE  phpl_journees.id_champ='$champ' AND phpl_matchs.id_journee=phpl_journees.id  ";
$result=mysql_query($query);
while($row=mysql_fetch_array($result))
    {
    if( $row[0]==$row[1] and $sumsum*2==$row[0]+$row[1] and !$incoherent=="1")
		{
		echo ADMIN_COHERENCE_MSG5;
		echo "<br />"; 
		}
    else 
		 {
		 echo "<small>CHECKSUM = $sumsum - ALLER $row[0] RETOUR $row[1]  </small>";
		 echo "<big>".ADMIN_COHERENCE_MSG6."</big>";
		 }

 	}
 $query="SELECT   phpl_matchs.id_equipe_dom as DOM,  count(*) as ct FROM phpl_matchs, phpl_journees WHERE  phpl_journees.id_champ='$champ' AND phpl_matchs.id_journee=phpl_journees.id GROUP BY DOM";
$result=mysql_query($query); 
while($row=mysql_fetch_array($result))
		{
		if ($row[1]<>$nb_journees/2) 
			{
			$nom_club=nom_club($row[0]);
			echo "<br /> $nom_club  joue $row[1] fois à domicile ?";
			}
		}
 $query="SELECT phpl_matchs.id_equipe_ext as DOM,  count(*) as ct FROM phpl_matchs, phpl_journees WHERE  phpl_journees.id_champ='$champ' AND phpl_matchs.id_journee=phpl_journees.id GROUP BY DOM";
$result=mysql_query($query); 
while($row=mysql_fetch_array($result))
		{
		if ($row[1]<>$nb_journees/2)
			{
			$nom_club=nom_club($row[0]);
			echo "<br /> $nom_club  joue $row[1] fois à l'extérieur ?";
			}
		}

}
else 
{
  echo "<tr><td align=center><a href=\"?page=championnat&action=matchs&champ=$champ&coherence=1\">".ADMIN_COHERENCE_MSG7."</a></td></tr>";
}
}// fin else de vérif si journées existent 
?>
</td></tr></table><br /><br />
