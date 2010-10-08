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


if ($action2=="1")
{

reset ($date);
	 while ( list ($cle, $val)= each ($date))
         {
	$y=$cle+1;
	$dateUS=date_fr_vers_us($val);
	$query="UPDATE phpl_journees SET date_prevue='$dateUS' WHERE numero='$y' AND id_champ='$champ'";
	$result= mysql_query($query);

   $query2="SELECT phpl_journees.id FROM phpl_journees, phpl_matchs WHERE phpl_journees.id=phpl_matchs.id_journee and phpl_journees.numero='$y' AND id_champ='$champ'";
   $result2=mysql_query($query2) or die ("probleme " .mysql_error());
      while ($row2= mysql_fetch_array($result2))
      {
        $date_us_matchs="$dateUS $heure:$minute:00";
        $query="UPDATE phpl_matchs SET date_reelle='$date_us_matchs' WHERE phpl_matchs.id_journee=$row2[0]";
        $result= mysql_query($query);
      }

         }

 //$x=0;
 // while ($x <= ($nb_equipes*2)-2)
  //{
 //   $y=$x+1;
  //  $dateUS=date_fr_vers_us($date['x']);
  //  $query="UPDATE journees SET date_prevue='$dateUS' WHERE numero='$y' AND id_champ='$champ'";
  //  $result= mysql_query($query);

 //   $query2="SELECT journees.id FROM journees, matchs WHERE journees.id=matchs.id_journee and journees.numero='$y' AND id_champ='$champ'";
 //   $result2=mysql_query($query2) or die ("probleme " .mysql_error());
  //    while ($row2= mysql_fetch_array($result2))
  //    {
 //       $date_us_matchs="$dateUS $heure:$minute:00";
  //      $query="UPDATE matchs SET date_reelle='$date_us_matchs ' WHERE matchs.id_journee=$row2[0]";
  //      $result= mysql_query($query);
  //    }
   //   $x++;

  //}
}


?>
<table class=phpl width="80%">
            <tr>
              <td class=phpl2 align="center" colspan="3"><? echo ADMIN_DATES_TITRE." "; affich_champ ($champ); ?></td><td class=phpl2 align="right"><a href="#" onclick="window.open('Assistant_fr/dates.htm','Assistant','toolbar=0,location=0,directories=0,status=0,scrollbars=1,resizable=0,copyhistory=0,menuBar=0,width=512,height=512');return false;"><img border="0" alt="Assistant" src="aide.gif"></a></td>

            </tr>

  <form method="post" action="">
  <?
  $nb_equipes=nb_equipes ($champ);
  
  if ($nb_equipes==0)
  {
    echo "<tr><td align=\"center\">".ADMIN_DATES_5." <a href=\"?page=championnat&action=equipes&champ=$champ\">".TEAM."</td></tr></table>";
  }
  
  elseif (($nb_equipes%2)!==0)
  {
    echo "<tr><td align=\"center\">".ADMIN_DATES_3."</td></tr></table>";
  }

  else
  {

  $requete="SELECT * FROM phpl_journees WHERE id_champ='$champ'";
  $resultats=mysql_query($requete);
  $nb_equipes1=mysql_num_rows($resultats);

  
  
  if ($nb_equipes1!==(($nb_equipes)*2-2))
  {
    mysql_query("DELETE FROM phpl_journees WHERE id_champ='$champ'");
    $x=1;
    while ($x <= ($nb_equipes*2)-2)
		{
		$query="INSERT INTO phpl_journees (numero, id_champ) VALUES ('$x','$champ')";
		$result= mysql_query($query);
		$x++;
		}
  }





  $result=mysql_query("SELECT numero, date_prevue FROM phpl_journees WHERE id_champ='$champ' ORDER BY numero");
    $i=0;
    while ($row= mysql_fetch_array($result))
    {
      if (($i%2)==0) {$class="phpl3";}
      else {$class="phpl4";}
      $dateFR=@date_us_vers_fr($row[1]);
      echo "<tr><td class=$class colspan=2><br /><b>".ADMIN_JOURNEES_MSG9." $row[0]</b><br />".ADMIN_JOURNEES_MSG10."</td><td colspan=2 class=$class>";
      echo "<input type=\"text\" name=\"date[]\" size=8 maxlength=8 value=\"$dateFR\"></td></tr>";
      $i++;
    }

    echo "<tr><td width=80% colspan=2><b>".ADMIN_DATES_1."</b> : <br />".ADMIN_DATES_2." <a href=\"?page=championnat&action=resultats&champ=$champ\">".RESULT."</a>.</td>";
    echo "<td colspan=2><select size=\"1\" name=\"heure\">";
    for ($i="1"; $i<="24"; $i++)
    {
     if ($i=="20") {echo "<option value=\"$i\" selected>$i</option>";}
     else {echo "<option value=\"$i\">$i</option>";}
    }
  echo "</select>".ADMIN_DATES_HEURES." ";

  echo "<select size=\"1\" name=\"minute\">";
    for ($i="0"; $i<="60"; $i=$i+5)
    {
      if ($i<10) {$i="0$i";}
      if ($i=="0") {echo "<option value=\"$i\" selected>$i</option>";}
      else {echo "<option value=\"$i\">$i</option>";}
    }
  echo "</select>".ADMIN_DATES_MINUTES." </td></tr>";

    
  echo "<input type=\"hidden\" name=\"yes\" value=\"OUI\">";
  echo "<input type=\"hidden\" name=\"nb_equipes\" value=\"$nb_equipes\">";
  echo "<input type=\"hidden\" name=\"action\" value=\"dates\">";
  echo "<input type=\"hidden\" name=\"action2\" value=\"1\">";
  echo "<input type=\"hidden\" name=\"page\" value=\"championnat\">";
  echo "<input type=\"hidden\" name=\"champ\" value=\"$champ\">";
  echo "<input type=\"hidden\" name=\"nb_equipes\" value=\"$nb_equipes\">";

  $button=ENVOI;
  echo "<tr><td colspan=2 align=center><input type=\"submit\" value=$button></td></tr>";
  echo "</form></table><br /><br />";

  }




?>


