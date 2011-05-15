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


function affich_championnats ($champ, $action)
{
  $requete="SELECT phpl_championnats.id, phpl_divisions.nom, phpl_saisons.annee 
            FROM phpl_championnats, phpl_divisions, phpl_saisons 
            WHERE phpl_championnats.id_division=phpl_divisions.id 
            AND phpl_championnats.id_saison=phpl_saisons.id ORDER by annee desc, nom";
  $resultats=mysql_query($requete) or die (mysql_error());
  $i=0;
    while ($row = mysql_fetch_array($resultats))
      {
        $saison=$row[2]+1;
        $gras_fin="</b>";

        for($i=1;$i<=9;$i++)
        {
          //echo "sg";
          $gras = "gras_$i";
          $$gras="</b>";
        }

        if ($action=="equipes" and $champ=="$row[0]"){$gras_1="<b>";}
        elseif ($action=="dates" and $champ==$row[0]){$gras_2="<b>";}
        elseif ($action=="matchs" and $champ==$row[0]){$gras_3="<b>";}
        elseif ($action=="parametres" and $champ==$row[0]){$gras_4="<b>";}
        elseif ($action=="resultats" and $champ==$row[0]){$gras_5="<b>";}
        elseif ($action=="joueurs" and $champ==$row[0]){$gras_9="<b>";}
        elseif ($action=="buteurs" and $champ==$row[0]){$gras_6="<b>";}
        elseif ($action=="generer" and $champ==$row[0]){$gras_7="<b>";}
        elseif ($action=="supp" and $champ==$row[0]){$gras_8="<b>";}

        if ($champ=="$row[0]") {$class="phpl7";}
        elseif (($i%2)==0) {$class="phpl3";}
        else {$class="phpl4";}

        echo "<tr>";
        echo "<td class='$class'>$row[0]</td>";
        echo "<td class='$class'>$row[1]</td>";
        echo "<td class='$class'>$row[2]/$saison</td>";
        echo "<td class='$class' align=\"right\" width=\"75%\">";

        echo " $gras_1<a href=\"?page=championnat&action=equipes&champ=$row[0]\">[".EQUIPE."]</a>$gras_fin";
        echo " $gras_2<a href=\"?page=championnat&action=dates&champ=$row[0]\">[".DATE."]</a>$gras_fin";
        echo " $gras_3<a href=\"?page=championnat&action=matchs&champ=$row[0]\">[".MATCH."]$gras_fin</a>";
        echo " $gras_4<a href=\"?page=championnat&action=parametres&champ=$row[0]\">[".PARAMETRE."]$gras_fin</a>";
        echo " $gras_5<a href=\"?page=championnat&action=resultats&champ=$row[0]\">[".RESULT."]$gras_fin</a>";
        echo " $gras_9<a href=\"?page=championnat&action=joueurs&champ=$row[0]\">[".JOUEURS."]$gras_fin</a>";
        echo " $gras_6<a href=\"?page=championnat&action=buteurs&champ=$row[0]\">[".BUTEUR."]$gras_fin</a>";
        echo " $gras_7<a href=\"?page=championnat&action=generer&champ=$row[0]\">[".GENERER."]$gras_fin</a>";
        echo " $gras_8<a href=\"?page=championnat&action=supp&champ=$row[0]\">[".ADMIN_RENS_8."]$gras_fin</a></td>";
        echo "</tr>";
        $i++;


      }
}

function affich_gr_championnats ($gr_champ, $action)
{
  $requete="SELECT DISTINCT id, nom FROM phpl_gr_championnats ORDER by id desc";
  $resultats=mysql_query($requete) or die (mysql_error());
  $i=0;
    while ($row = mysql_fetch_array($resultats))
      {
        $gras_fin="</b>";

        for($i=1;$i<=8;$i++)
        {
          $gras = "gras_$i";
          $$gras="</b>";
        }

        if ($action=="editer" and $gr_champ=="$row[0]"){$gras_1="<b>";}


        if ($gr_champ=="$row[0]") {$class="phpl7";}
        elseif (($i%2)==0) {$class="phpl3";}
        else {$class="phpl4";}

        echo "<tr>";
        echo "<td class='$class'>$row[0]</td>";
        echo "<td class='$class'>$row[1]</td>";
        echo "<td class='$class' align=\"right\" width=\"75%\">";

        echo " $gras_1<a href=\"?page=groupes_championnats&action=editer&gr_champ=$row[0]\">[".EDITER."]</a>$gras_fin";
        echo " $gras_2<a href=\"?page=groupes_championnats&action=generer&gr_champ=$row[0]\">[Tout ".ADMIN_GR_CHAMP_GENERER."]</a>$gras_fin";
        echo " $gras_2<a href=\"?page=groupes_championnats&action=generer&gr_champ=$row[0]&mode=delta\">[".ADMIN_GR_CHAMP_GENERER."]</a>$gras_fin";
        echo " $gras_8<a href=\"?page=groupes_championnats&action=supp&gr_champ=$row[0]\">[".ADMIN_RENS_8."]$gras_fin</a></td>";

        echo "</tr>";
        $i++;


      }
}



function affich_champ ($champ)
{
    $requete="SELECT phpl_divisions.nom, phpl_saisons.annee 
              FROM phpl_championnats, phpl_divisions, phpl_saisons
              WHERE phpl_championnats.id_division=phpl_divisions.id 
              AND phpl_championnats.id_saison=phpl_saisons.id 
              AND phpl_championnats.id='$champ' ORDER by annee desc";
    $resultats=mysql_query($requete) or die (mysql_error());

    
    while ($row = mysql_fetch_array($resultats))
      {
        $saison=$row[1]+1;
        echo "$row[0] $row[1]/$saison";
      }
}

function affich_gr_champ ($gr_champ)
{
    $requete="SELECT nom FROM phpl_gr_championnats WHERE id='$gr_champ'";
    $resultats=mysql_query($requete) or die (mysql_error());
    $row = mysql_fetch_array($resultats);
    
        echo "$row[0]";
}

function divisions_menu ()
{
$requete ="SELECT phpl_divisions.id, phpl_divisions.nom FROM phpl_divisions ORDER by nom";
$result = mysql_query($requete);
echo "<select name=division>";
echo "<option></option>";
while($row = mysql_fetch_array($result))
                {
                echo ("<option value=\"$row[0]\">$row[1]\n");
                echo ("</option>\n");
                }
echo "</select>";
}

function saisons_menu()
{
 $requete ="SELECT phpl_saisons.id, phpl_saisons.annee FROM phpl_saisons ORDER by annee";
$result = mysql_query($requete);
echo "<select name=saison>";
echo "<option></option>";
while($row = mysql_fetch_array($result))
                {
                $saison1=$row[1]+1;
                echo ("<option value=\"$row[0]\">$row[1]/$saison1\n");
                echo ("</option>\n");
                }
echo "</select>";
}

function clubs_menu()
{    
    $requete ="SELECT phpl_clubs.id, phpl_clubs.nom FROM phpl_clubs ORDER by nom";
    $result = mysql_query($requete);
    echo "<select name=\"club[]\" multiple size=\"8\">";
      while($row = mysql_fetch_array($result))
      {
        $row[1] = stripslashes($row[1]);
        echo ("<option value=\"$row[0]\">$row[1]\n");
        echo ("</option>\n");
      }
echo "</select>";
}

function champ_menu()
{
    $requete = "SELECT phpl_championnats.id, phpl_divisions.nom, phpl_saisons.annee, (phpl_saisons.annee)+1 FROM phpl_championnats, phpl_divisions, phpl_saisons WHERE phpl_divisions.id=phpl_championnats.id_division AND phpl_saisons.id=phpl_championnats.id_saison ORDER by annee desc, phpl_divisions.nom";
    $result = mysql_query($requete);
    echo "<select name=\"champ[]\" multiple size=\"8\">";
      while($row = mysql_fetch_array($result))
      {
        echo ("<option value=\"$row[0]\">$row[1] $row[2]/$row[3]\n");
        echo ("</option>\n");
      }
echo "</select>";
}

function equipes_menu($champ)
{
    $requete ="SELECT phpl_equipes.id, phpl_clubs.nom FROM phpl_clubs, phpl_equipes WHERE id_champ='$champ' AND phpl_clubs.id=phpl_equipes.id_club ORDER by nom";
    $result = mysql_query($requete);
    echo "<select name=\"club[]\" multiple size=\"8\">";
      while($row = mysql_fetch_array($result))
      {
        $row[1] = stripslashes($row[1]);
        echo ("<option value=\"$row[0]\">$row[1]\n");
        echo ("</option>\n");
      }
echo "</select>";
}

function champ_gr_menu($gr_champ)
{

   echo "<select name=\"champ[]\" multiple size=\"8\">";
    $requete="SELECT id_champ FROM phpl_gr_championnats WHERE id = '$gr_champ'";
    $result = mysql_query($requete) or die (mysql_error());
    
    while ($row=mysql_fetch_array($result))
    {
         $requete2 = "SELECT phpl_championnats.id, phpl_divisions.nom, phpl_saisons.annee, (phpl_saisons.annee)+1 FROM phpl_championnats, phpl_divisions, phpl_saisons WHERE phpl_divisions.id=phpl_championnats.id_division AND phpl_saisons.id=phpl_championnats.id_saison AND phpl_championnats.id='$row[0]'  ORDER by annee desc, phpl_divisions.nom";
         $result2 = mysql_query($requete2) or die (mysql_error());
         while($row2 = mysql_fetch_array($result2))
         {
            echo ("<option value=\"$row2[0]\">$row2[1] $row2[2]/$row2[3]\n");
            echo ("</option>\n");
         }
    }

echo "</select>";


}

function journees ($champ, $numero, $action)
{

        
  $requete="SELECT numero, id FROM phpl_journees WHERE id_champ='$champ' ORDER BY numero";
  $resultats=mysql_query($requete);
  while ($row=mysql_fetch_array($resultats))
     {
       if ($numero==$row[0]) {echo "<b><a href=\"?page=championnat&action=$action&champ=$champ&id_journee=$row[1]&numero=$row[0]\">$row[0]</a></b> ";}
       else {echo "<a href=\"?page=championnat&action=$action&id_journee=$row[1]&champ=$champ&numero=$row[0]\">$row[0]</a> ";}
     }

}

function format_date_fr($date){
  list($annee,$mois,$jour) = explode("-",substr($date,0,10));
  list($heure,$minute,$seconde) = explode(":",substr($date,11,7));
  return $jour."/".$mois."/".$annee." ".$heure.":".$minute;
}

function resultats ($champ, $numero)
{
  $requete="SELECT phpl_clubs.nom, CLEXT.nom, phpl_matchs.buts_dom, phpl_matchs.buts_ext, phpl_matchs.id, phpl_matchs.date_reelle
            FROM phpl_clubs, phpl_clubs as CLEXT, phpl_matchs, phpl_journees, phpl_equipes, phpl_equipes as EXT
            WHERE phpl_clubs.id=phpl_equipes.id_club
                  AND CLEXT.id=EXT.id_club
                  AND phpl_equipes.id=phpl_matchs.id_equipe_dom
                  AND EXT.id=phpl_matchs.id_equipe_ext
                  AND phpl_matchs.id_journee=phpl_journees.id
                  AND phpl_journees.numero='$numero'
                  AND phpl_journees.id_champ='$champ'
                  AND CLEXT.nom!='exempte'
                  AND phpl_clubs.nom!='exempte'
                  ORDER BY date_reelle asc";
  $resultats=mysql_query($requete) or die (mysql_error());

  $i=0;
  while ($row=mysql_fetch_array($resultats))
  {
    $row[0] = stripslashes($row[0]);
    $row[1] = stripslashes($row[1]);
    if (($i%2)==0) {$class="phpl3";}
    else {$class="phpl4";}
    $date_fr=format_date_fr($row[5]);
    echo "<tr><td class=$class>$row[0]";
    echo "<td class=$class><input type=\"text\" size=\"3\" name=\"butd[]\" value=\"$row[2]\"></td>";
    echo "<td class=$class><input type=\"text\" size=\"3\" name=\"butv[]\" value=\"$row[3]\">";
    echo "<input type=\"hidden\" name=\"matchs_id[]\" value=\"$row[4]\"></td>";
    echo "<td class=$class>$row[1]</td>";
    echo "<td class=$class><input type=\"text\" size=\"16\" name=\"date_reelle[]\" value=\"$date_fr\" maxlength=\"16\"></td>";
    $matchs_id[]=$row[4];
    $i++;
  }

  $requete3="SELECT phpl_clubs.nom, CLEXT.nom, phpl_matchs.buts_dom, phpl_matchs.buts_ext, phpl_matchs.id, phpl_matchs.date_reelle FROM phpl_clubs, phpl_clubs as CLEXT, phpl_matchs, phpl_journees, phpl_equipes, phpl_equipes as EXT WHERE phpl_clubs.id=phpl_equipes.id_club AND CLEXT.id=EXT.id_club AND phpl_equipes.id=phpl_matchs.id_equipe_dom AND EXT.id=phpl_matchs.id_equipe_ext AND phpl_matchs.id_journee=phpl_journees.id AND phpl_journees.numero='$numero' AND phpl_journees.id_champ='$champ' AND (CLEXT.nom='exempte' or phpl_clubs.nom='exempte')";
  $resultats3=mysql_query($requete3) or die (mysql_error());

  while ($row3=mysql_fetch_array($resultats3))
  {
    $row3[0] = stripslashes($row3[0]);
    $row3[1] = stripslashes($row3[1]);
    if (($i%2)==0) {$class="phpl3";}
    else {$class="phpl4";}
    if ($row3[0]=='exempte') {echo "<tr><td colspan=6  class=$class>".ADMIN_RESULTS_1." : $row3[1]</td></tr>";}
    if ($row3[1]=='exempte') {echo "<tr><td colspan=6  class=$class>".ADMIN_RESULTS_1." : $row3[0]</td></tr>";}
  }


  echo "</tr><td colspan=\"5\" align=\"center\">";
  echo "<input type=\"hidden\" name=\"champ\" value=\"$champ\">";

  $numero=$numero+1;
  echo "<input type=\"hidden\" name=\"numero\" value=\"$numero\">";
  echo "<input type=\"hidden\" name=\"action\" value=\"resultats\">";
  echo "<input type=\"hidden\" name=\"action2\" value=\"1\">";
  $button=ENVOI;
  echo "<input type=\"submit\" value=$button>";
  echo "</td></tr></table>";

}

function nb_equipes($id_champ)
{
$query="SELECT id FROM phpl_equipes WHERE id_champ='$id_champ'";
$result=mysql_query($query);
//if (!$result) die mysql_error();
$nb_equipes=mysql_num_rows( $result );
return("$nb_equipes");
}

function format_date_us($date){
    list($jour,$mois,$annee) = explode("/",substr($date,0,10));
    list($heure,$minute) = explode(":",substr($date,10,22));

  $seconde="00";
  return $annee."-".$mois."-".$jour." ".$heure.":".$minute.":".$seconde;
}

function date_us_vers_fr($dateUS) // $dateUS=AAAA-MM-JJ
{
//$elementsdate=chunk_split($dateUS , 2 , "-");
$elementsdate=explode("-",$dateUS);
$jour=$elementsdate[2];
$mois= $elementsdate[1];
$annee=$elementsdate[0];
return $dateFR=$jour.$mois.$annee;
}

function date_fr_vers_us($dateFR)
{
if ($dateFR)
{
$elementsdate=chunk_split($dateFR , 2 , "-");
$elementsdate=explode("-",$elementsdate);

$annee=$elementsdate[2].$elementsdate[3];
$mois=$elementsdate[1];
$jour=$elementsdate[0];
$dateUS=$annee."-".$mois."-".$jour;
return $dateUS;
}
else return "00000000";
}

function nom_club($id_equipe)
{
$query="SELECT nom FROM phpl_clubs, phpl_equipes WHERE phpl_clubs.id=phpl_equipes.id_club and phpl_equipes.id='$id_equipe'";
$result=mysql_query($query) or die(mysql_error());
$row=mysql_fetch_array( $result );
$nom_club=stripslashes($row[0]);
return("$nom_club");
}

function nb_journees($id_champ)
{
$query="SELECT id FROM phpl_equipes WHERE id_champ='$id_champ'";
$result=mysql_query($query);
$nb_equipes=mysql_num_rows( $result );
$nb_journees=((($nb_equipes)*2)-2) ;
return("$nb_journees");
}

function nb_matchs ($numero, $champ)
{
$query="select * from phpl_matchs, phpl_journees where phpl_matchs.id_journee=phpl_journees.id and phpl_journees.numero=$numero and phpl_journees.id_champ=$champ";
$result=mysql_query($query);
$nb_matchs=mysql_num_rows( $result );
return("$nb_matchs");
}

// *** REMPLI LA TABLE CLMNT
function db_clmnt($champ, $debut, $fin, $cache)
{
  $nb_requete=0;
  if($cache=="1"){ mysql_query("DELETE FROM phpl_clmnt_cache WHERE ID_CHAMP='$champ'") or die (mysql_error());}
  else{ mysql_query("DELETE FROM phpl_clmnt") or die (mysql_error());}
          $nb_requete++;

          if (!$fin){$fin=(nb_equipes($champ)*2)-2;}
          if (!$debut){$debut=1;}

// SELECTION DES PARAMETRES
$query="SELECT pts_victoire, pts_nul, pts_defaite FROM phpl_parametres WHERE id_champ='$champ'";
$nb_requete++;
$result=(mysql_query($query)) or die (mysql_error()) ;
      while ($row=mysql_fetch_array($result) )
      {
      $pts_victoire=$row['pts_victoire'];
      $pts_nul=$row['pts_nul'];
      $pts_defaite=$row['pts_defaite'];
      }

// victoires domicile
$query="SELECT dom.id, count(dom.id), phpl_clubs.nom, sum(buts_dom), sum(buts_ext) FROM phpl_equipes as dom, phpl_clubs, phpl_matchs, phpl_journees, phpl_championnats
WHERE dom.id_champ='$champ'
      AND dom.id_club=phpl_clubs.id
      AND dom.id=phpl_matchs.id_equipe_dom
      AND buts_dom > buts_ext
      AND phpl_championnats.id=phpl_journees.id_champ
      AND phpl_journees.id=phpl_matchs.id_journee
      AND phpl_journees.numero>='$debut'
      AND phpl_journees.numero<='$fin'
      GROUP by phpl_clubs.nom ";
      $nb_requete++;
$dom = mysql_query($query) or die (mysql_error());
     while($row= mysql_fetch_array($dom))
     {
     $clmnt[$row[2]]['GDOM']=$row[1];
      if (!isset($clmnt[$row[2]]['BUTSDOMPOUR'])) {$clmnt[$row[2]]['BUTSDOMPOUR']=$row[3];}
      else{$clmnt[$row[2]]['BUTSDOMPOUR']+=$row[3];}
      if (!isset($clmnt[$row[2]]['BUTSDOMCONTRE'])) {$clmnt[$row[2]]['BUTSDOMCONTRE']=$row[4];}
      else{$clmnt[$row[2]]['BUTSDOMCONTRE']+=$row[4];}
     }

// Defaites domicile
$query="SELECT dom.id, count(dom.id), phpl_clubs.nom, sum(buts_dom), sum(buts_ext) FROM phpl_equipes as dom, phpl_clubs, phpl_matchs, phpl_journees, phpl_championnats
WHERE dom.id_champ='$champ'
      AND dom.id_club=phpl_clubs.id
      AND dom.id=phpl_matchs.id_equipe_dom
      AND buts_dom < buts_ext
      AND phpl_championnats.id=phpl_journees.id_champ
      AND phpl_journees.id=phpl_matchs.id_journee
      AND phpl_journees.numero>='$debut'
      AND phpl_journees.numero<='$fin'
      GROUP by phpl_clubs.nom ";

       $dom = mysql_query($query) or die (mysql_error());

     while($row= mysql_fetch_array($dom))
     {
     $clmnt[$row[2]]['PDOM']=$row[1];
      if (!isset($clmnt[$row[2]]['BUTSDOMPOUR'])) {$clmnt[$row[2]]['BUTSDOMPOUR']=$row[3];}
      else{$clmnt[$row[2]]['BUTSDOMPOUR']+=$row[3];}
      if (!isset($clmnt[$row[2]]['BUTSDOMCONTRE'])) {$clmnt[$row[2]]['BUTSDOMCONTRE']=$row[4];}
      else{$clmnt[$row[2]]['BUTSDOMCONTRE']+=$row[4];}
     }
// Nuls domicile
$query="SELECT dom.id, count(dom.id), phpl_clubs.nom, sum(buts_dom), sum(buts_ext) FROM phpl_equipes as dom, phpl_clubs, phpl_matchs, phpl_journees, phpl_championnats
WHERE dom.id_champ='$champ'
      AND dom.id_club=phpl_clubs.id
      AND dom.id=phpl_matchs.id_equipe_dom
      AND buts_dom = buts_ext
      AND buts_dom is not null
      AND buts_ext is not null
      AND phpl_championnats.id=phpl_journees.id_champ
      AND phpl_journees.id=phpl_matchs.id_journee
      AND phpl_journees.numero>='$debut'
      AND phpl_journees.numero<='$fin'
      GROUP by phpl_clubs.nom ";

$dom = mysql_query($query) or die (mysql_error());

     while($row= mysql_fetch_array($dom))
     {
     $clmnt[$row[2]]['NDOM']=$row[1];
      if (!isset($clmnt[$row[2]]['BUTSDOMPOUR'])) {$clmnt[$row[2]]['BUTSDOMPOUR']=$row[3];}
      else{$clmnt[$row[2]]['BUTSDOMPOUR']+=$row[3];}
      if (!isset($clmnt[$row[2]]['BUTSDOMCONTRE'])) {$clmnt[$row[2]]['BUTSDOMCONTRE']=$row[4];}
      else{$clmnt[$row[2]]['BUTSDOMCONTRE']+=$row[4];}
     }
// Resultats à domicile
$query="SELECT phpl_clubs.nom FROM phpl_clubs, phpl_equipes, phpl_championnats
WHERE phpl_equipes.id_champ=phpl_championnats.id
      AND phpl_championnats.id='$champ'
      AND phpl_equipes.id_club=phpl_clubs.id";

$result=mysql_query($query) or die (mysql_error());

        
// RESULTATS EXTERIEURS :
// victoires exterieur
$query="SELECT ext.id, count(ext.id), phpl_clubs.nom, sum(buts_ext), sum(buts_dom) FROM phpl_equipes as ext, phpl_clubs, phpl_matchs, phpl_journees, phpl_championnats
WHERE ext.id_champ='$champ'
      AND ext.id_club=phpl_clubs.id
      AND ext.id=phpl_matchs.id_equipe_ext
      AND buts_ext > buts_dom
      AND phpl_championnats.id=phpl_journees.id_champ
      AND phpl_journees.id=phpl_matchs.id_journee
      AND phpl_journees.numero>='$debut'
      AND phpl_journees.numero<='$fin'
      GROUP by phpl_clubs.nom ";

$dom = mysql_query($query) or die (mysql_error());;


     while($row= mysql_fetch_array($dom))
     {
     $clmnt[$row[2]]['GEXT']=$row[1];
      if (!isset($clmnt[$row[2]]['BUTSEXTPOUR'])) {$clmnt[$row[2]]['BUTSEXTPOUR']=$row[3];}
      else{$clmnt[$row[2]]['BUTSEXTPOUR']+=$row[3];}
      if (!isset($clmnt[$row[2]]['BUTSEXTCONTRE'])) {$clmnt[$row[2]]['BUTSEXTCONTRE']=$row[4];}
      else{$clmnt[$row[2]]['BUTSEXTCONTRE']+=$row[4];}

     }
// Defaites exterieur
$query="SELECT ext.id, count(ext.id), phpl_clubs.nom, sum(buts_ext), sum(buts_dom) FROM phpl_equipes as ext, phpl_clubs, phpl_matchs, phpl_journees, phpl_championnats
WHERE ext.id_champ='$champ'
      AND ext.id_club=phpl_clubs.id
      AND ext.id=phpl_matchs.id_equipe_ext
      AND buts_ext < buts_dom
      AND phpl_championnats.id=phpl_journees.id_champ
      AND phpl_journees.id=phpl_matchs.id_journee
      AND phpl_journees.numero>='$debut'
      AND phpl_journees.numero<='$fin'
      GROUP by phpl_clubs.nom ";

$dom=mysql_query($query) or die (mysql_error());
                    
      While($row= mysql_fetch_array($dom))
      {
      $clmnt[$row[2]]['PEXT']=$row[1];
      if (!isset($clmnt[$row[2]]['BUTSEXTPOUR'])) {$clmnt[$row[2]]['BUTSEXTPOUR']=$row[3];}
      else{$clmnt[$row[2]]['BUTSEXTPOUR']+=$row[3];}
      if (!isset($clmnt[$row[2]]['BUTSEXTCONTRE'])) {$clmnt[$row[2]]['BUTSEXTCONTRE']=$row[4];}
      else{$clmnt[$row[2]]['BUTSEXTCONTRE']+=$row[4];}
      }

// Nuls exterieur
$query="SELECT ext.id, count(ext.id), phpl_clubs.nom, sum(buts_ext), sum(buts_dom) FROM phpl_equipes as ext, phpl_clubs, phpl_matchs, phpl_journees, phpl_championnats
WHERE ext.id_champ='$champ'
      AND ext.id_club=phpl_clubs.id
      AND ext.id=phpl_matchs.id_equipe_ext
      AND buts_ext = buts_dom
      AND buts_dom is not null
      AND buts_ext is not null
      AND phpl_championnats.id=phpl_journees.id_champ
      AND phpl_journees.id=phpl_matchs.id_journee
      AND phpl_journees.numero>='$debut'
      AND phpl_journees.numero<='$fin'
      GROUP by phpl_clubs.nom ";


$dom=mysql_query($query) or die (mysql_error());;

      while($row= mysql_fetch_array($dom))
      {
      $clmnt[$row[2]]['NEXT']=$row[1];
      if (!isset($clmnt[$row[2]]['BUTSEXTPOUR'])) {$clmnt[$row[2]]['BUTSEXTPOUR']=$row[3];}
      else{$clmnt[$row[2]]['BUTSEXTPOUR']+=$row[3];}
      if (!isset($clmnt[$row[2]]['BUTSEXTCONTRE'])) {$clmnt[$row[2]]['BUTSEXTCONTRE']=$row[4];}
      else{$clmnt[$row[2]]['BUTSEXTCONTRE']+=$row[4];}
      }
                 
// TABLEAU DE CLASSEMENT
$query="SELECT phpl_clubs.nom, phpl_equipes.penalite, phpl_equipes.id
        FROM phpl_clubs, phpl_equipes, phpl_championnats
        WHERE phpl_equipes.id_champ=phpl_championnats.id
        AND phpl_championnats.id='$champ'
        AND phpl_equipes.id_club=phpl_clubs.id";

$result=mysql_query($query) or die (mysql_error());

//mysql_query("LOCK TABLE phpl_clmnt WRITE, phpl_equipes WRITE, phpl_clubs WRITE") or die (mysql_error());

    if (mysql_num_rows($result)==0)
    {
    $query="SELECT phpl_clubs.nom, phpl_equipes.id 
            FROM phpl_clubs, phpl_equipes, phpl_championnats
            WHERE phpl_equipes.id_champ=phpl_championnats.id
            AND phpl_championnats.id='$champ'
            AND phpl_equipes.id_club=phpl_clubs.id";

    $result=mysql_query($query) or die (mysql_error());
    }

    while($row = mysql_fetch_array($result))
    {
    $NOM=$row['nom'];
    $DOMJOUES=$clmnt[$NOM]['GDOM']+$clmnt[$NOM]['NDOM'] + $clmnt[$NOM]['PDOM'];
    $EXTJOUES=$clmnt[$NOM]['GEXT']+$clmnt[$NOM]['NEXT'] + $clmnt[$NOM]['PEXT'];
    $JOUES=$EXTJOUES + $DOMJOUES;
    $DOMPOINTS=(($clmnt[$NOM]['GDOM'])*$pts_victoire) + (($clmnt[$NOM]['NDOM'])*$pts_nul) + (($clmnt[$NOM]['PDOM'])*$pts_defaite);
    $EXTPOINTS=(($clmnt[$NOM]['GEXT'])*$pts_victoire) + (($clmnt[$NOM]['NEXT'])*$pts_nul) + (($clmnt[$NOM]['PEXT'])*$pts_defaite);
    $POINTS= $DOMPOINTS+ $EXTPOINTS + $row['penalite'];
    $G=($clmnt[$NOM]['GEXT'])+($clmnt[$NOM]['GDOM']);
    $N=($clmnt[$NOM]['NEXT'])+($clmnt[$NOM]['NDOM']);
    $P=$clmnt[$NOM]['PEXT'] + $clmnt[$NOM]['PDOM'];
    $DOMG=($clmnt[$NOM]['GDOM']);
    $DOMN=($clmnt[$NOM]['NDOM']);
    $DOMP=$clmnt[$NOM]['PDOM'];
    $EXTG=($clmnt[$NOM]['GEXT']);
    $EXTN=($clmnt[$NOM]['NEXT']);
    $EXTP=$clmnt[$NOM]['PEXT'];
    $BUTSPOUR=$clmnt[$NOM]['BUTSEXTPOUR'] + $clmnt[$NOM]['BUTSDOMPOUR'];
    $DOMBUTSPOUR=$clmnt[$NOM]['BUTSDOMPOUR'];
    $EXTBUTSPOUR=$clmnt[$NOM]['BUTSEXTPOUR'];
    $BUTSCONTRE=$clmnt[$NOM]['BUTSEXTCONTRE'] + $clmnt[$NOM]['BUTSDOMCONTRE'];
    $DOMBUTSCONTRE= $clmnt[$NOM]['BUTSDOMCONTRE'];
    $EXTBUTSCONTRE=$clmnt[$NOM]['BUTSEXTCONTRE'] ;
    $DIFF=$BUTSPOUR - $BUTSCONTRE;
    $DOMDIFF=$DOMBUTSPOUR-$DOMBUTSCONTRE;
    $EXTDIFF=$EXTBUTSPOUR - $EXTBUTSCONTRE;
    $PEN = $row['penalite'];
    $NOM=addslashes($row['nom']);

    $question="INSERT INTO ";

    if($cache=="1"){$question.="phpl_clmnt_cache ";}
    else{$question.="phpl_clmnt ";}
            
    $question.="SET NOM='$NOM',
          ID_EQUIPE='$row[id]',
          ID_CHAMP='$champ',
          POINTS='$POINTS',
          DOMPOINTS='$DOMPOINTS',
          EXTPOINTS='$EXTPOINTS',
          JOUES= '$JOUES',
          DOMJOUES= '$DOMJOUES',
          EXTJOUES= '$EXTJOUES',
          G='$G',
          DOMG='$DOMG',
          EXTG='$EXTG',
          N='$N',
          DOMN='$DOMN',
          EXTN='$EXTN',
          P='$P',
          DOMP='$DOMP',
          EXTP='$EXTP',
          BUTSPOUR='$BUTSPOUR',
          DOMBUTSPOUR='$DOMBUTSPOUR',
          EXTBUTSPOUR='$EXTBUTSPOUR',
          BUTSCONTRE='$BUTSCONTRE',
          DOMBUTSCONTRE='$DOMBUTSCONTRE',
          EXTBUTSCONTRE='$EXTBUTSCONTRE',
          DIFF='$DIFF',
          DOMDIFF='$DOMDIFF',
          EXTDIFF='$EXTDIFF',
          PEN='$PEN'";
          $result2=mysql_query($question) or die(mysql_error());

    }
    
if($cache=="1"){$requete="DELETE FROM phpl_clmnt_cache WHERE nom='exempte'" or die (mysql_error());}
else{$requete="DELETE FROM phpl_clmnt WHERE nom='exempte'" or die (mysql_error());}

$resultat=mysql_query($requete) or die (mysql_error());
//mysql_query("UNLOCK TABLES") or die (mysql_error());
}

// Nombres de renseignement dans cette classe à partir de l'id_classe  (utilisé dans phpl_classe.php)
function nb_classe($data)
{
$query="SELECT id FROM phpl_rens WHERE id_classe='$data'";
$result=mysql_query($query);
//if (!$result) die mysql_error();
$nb_classe=mysql_num_rows( $result );
return("$nb_classe");
}

// Nombres de classes enregistrées (ulilisé dans phpl_classe.php)
function nb_classe2()
{
$query="SELECT * FROM phpl_classe";
$result=mysql_query($query);
//if (!$result) die mysql_error();
$nb_classe2=mysql_num_rows( $result );
return("$nb_classe2");
}

// Nombres de renseignements classés (utilisé dans admin/rens.php)
function nb_rens()
{
$query="SELECT id FROM phpl_rens where id_classe>'0'";
$result=mysql_query($query);
$nb_rens=mysql_num_rows( $result );
return("$nb_rens");
}

// Nombres de renseignements enregistrés (utilisé dans rens.php)
function nb_rens2()
{
$query="SELECT * FROM phpl_rens";
$result=mysql_query($query);
$nb_rens2=mysql_num_rows( $result );
return("$nb_rens2");
}

// id du renseignement à partir du nom du rens (utilisé dans rens.php)
function rens2($rens)
{
$query="select id, nom from phpl_rens where nom='$rens'";
$result=mysql_query($query);
//if (!$result) die mysql_error();
$row=mysql_fetch_array($result);
$rens2=$row[0];
return("$rens2");
}
// Affichage des renseignements (utilisé dans gestequipes.php
function aff_rens ($id_classe, $id_clubs)
{
$query="SELECT phpl_donnee.id, phpl_donnee.nom, id_rens, id_clubs, phpl_rens.id, phpl_rens.nom, phpl_rens.id_classe, phpl_clubs.id, etat, phpl_donnee.url, phpl_rens.url
FROM phpl_donnee, phpl_rens, phpl_clubs
WHERE id_clubs='$id_clubs'
      AND id_clubs=phpl_clubs.id
      AND id_rens=phpl_rens.id
      AND id_classe='$id_classe'
      AND etat='1' order by rang";
$result = mysql_query ($query) or die(mysql_error());
$nb_rens=mysql_num_rows($result);

         if ($nb_rens=="0"){echo "<center>".ADMIN_EQUIPE_8."</center>";}

         while($row = mysql_fetch_array($result))
         {
          $donnee_nom=stripslashes($row[1]);
          $rens_nom=stripslashes($row[5]);


           if (empty ($row[9]) and empty ($row[10])){echo "<b>$rens_nom :</b> $donnee_nom <br />";}
           elseif (empty ($row[9])){echo "<b><a href=\"$row[10]\">$rens_nom</a> :</b> $donnee_nom<br />";}
           elseif (empty ($row[10])){echo "<b>$rens_nom :</b> <a href=\"$row[9]\">$donnee_nom</a><br />";}
           else {echo "<b><a href=\"$row[10]\">$rens_nom</a> :</b> <a href=\"$row[9]\">$donnee_nom</a><br />";}
        }
        }

function VerifSession ($user_pseudo,$user_mdp)
{

if ($user_pseudo and $user_mdp)
	{
        $requete= "SELECT mot_de_passe, id_prono FROM phpl_membres WHERE pseudo='$user_pseudo' and admin='1'";
        $result = mysql_query($requete);
        $row = mysql_fetch_array($result);
        
        if ($row["mot_de_passe"] == $user_mdp){;$a=1;}
        else {$a=0;}
                                            
	//session_start();
	}
else {$a=0;}
return ("$a");
}

/**
 * Méthode permettant de calculer automatiquement les pronos
 * des utilisateurs n'ayant pas pronostiqué
 */
function calcul_pronos_auto($matchs_id, $butd, $butv, $gr_champ) {
	
	// calcul des points pour un prono exact
	$requetePtsPronos="SELECT pts_prono_exact, pts_prono_participation FROM phpl_gr_championnats WHERE phpl_gr_championnats.id='$gr_champ'"; 
	$resultatsPtsPronos=mysql_query($requetePtsPronos) or die (mysql_error()); 
	while ($rowPtsPronos=mysql_fetch_array($resultatsPtsPronos)) 
	{ 
		$pts_prono_exact=$rowPtsPronos[0]; 
		$pts_prono_participation=$rowPtsPronos[1]; 
		// avant, le point de participation etait inclus dans les points mis en jeu
		//$points_prono_exact=$pts_prono_exact + $pts_prono_participation ; 
		$points_prono_exact=$pts_prono_exact ; 
	} 
	
	// pour chaque match de la journée...     
	while ( list ($cle, $val_butd)= each ($butd) and list ($cle, $val_butv)= each ($butv) and list ($cle, $val_matchs_id)= each ($matchs_id))
    { 
		if ( !(($val_butd=='') or ($val_butv=='')))
		{	
	
			// récupération des infos du match
			// (permettra de récupérer les id des équipes pour les régles spécifiques)
			/*
			$requete="SELECT *
						FROM phpl_matchs matchs
						LEFT OUTER JOIN phpl_journees journees ON journees.id=matchs.id_journee
						WHERE matchs.id=$id_match";
			$resultats=mysql_query($requete) or die (mysql_error());
			if($row=mysql_fetch_array($resultats)) {
				
			}
			*/
			
			// récupération de la liste des utilisateurs n'ayant pas fait leurs pronos sur ce match
			// et ayant défini une stratégie
			$requete="SELECT *
						FROM phpl_membres membres
						LEFT OUTER JOIN phpl_strategie strategie ON strategie.id_membre=membres.id
						WHERE membres.actif='1'
						AND strategie.id_type IS NOT NULL
						AND membres.id NOT IN (SELECT id_membre FROM phpl_pronostics WHERE id_match=$val_matchs_id)";
			$resultats=mysql_query($requete) or die (mysql_error());
			
			
			while ($row=mysql_fetch_array($resultats)) {
				$id_membre = $row['id_membre'];
				$strategie_globale = $row['id_type'];
				switch($strategie_globale) {
					case 1 :
						$prono = "1";
						break;
					case 2 :
						$prono = "N";
						break;
					case 3 :
						$prono = "2";
						break;
					case 4 :
					case 5 :
						// on calcule les cotes si on ne l'a pas encore fait
						if(!isset($points_prono_domicile))
						{
							// si il y des pronos automatiques à faire sur ce match, on calcule les cotes
							//On compte le nombre de parieurs sur le match 
							$nombre_paris=mysql_query("SELECT COUNT( *) AS parieurs FROM phpl_pronostics WHERE phpl_pronostics.id_match='$val_matchs_id'"); 
							$nb_paris=mysql_fetch_array($nombre_paris); 
							$nb_parieurs=$nb_paris['parieurs']; 
						  
							//On compte le nombre de parieurs sur une victoire de l'equipe à domicile 
							$nombre_1=mysql_query("SELECT COUNT( *) AS domicile FROM phpl_pronostics WHERE phpl_pronostics.id_match='$val_matchs_id' AND pronostic='1'"); 
							$nb_1=mysql_fetch_array($nombre_1); 
							$nb_parieurs1=$nb_1['domicile']; 
						  
							//On compte le nombre de parieurs sur un match nul 
							$nombre_N=mysql_query("SELECT COUNT( *) AS nul FROM phpl_pronostics WHERE phpl_pronostics.id_match='$val_matchs_id' AND pronostic='N'"); 
							$nb_N=mysql_fetch_array($nombre_N); 
							$nb_parieursN=$nb_N['nul']; 
						  
							//On compte le nombre de parieurs sur une victoire de l'equipe à l'exterieur 
							$nombre_2=mysql_query("SELECT COUNT( *) AS visiteur FROM phpl_pronostics WHERE phpl_pronostics.id_match='$val_matchs_id' AND pronostic='2'"); 
							$nb_2=mysql_fetch_array($nombre_2); 
							$nb_parieurs2=$nb_2['visiteur'];
								 
							if($nb_parieurs1 > 0)
							{	   
								$points_prono_domicile=floor(($points_prono_exact*$nb_parieurs)/$nb_parieurs1);
							}
							else
							{
								$points_prono_domicile=0;
							}
							
							if($nb_parieursN > 0)
							{
								$points_prono_nul=floor(($points_prono_exact*$nb_parieurs)/$nb_parieursN);
							}
							else
							{
								$points_prono_nul=0;
							}
							
							if($nb_parieurs2 > 0)
							{							
								$points_prono_visiteur=floor(($points_prono_exact*$nb_parieurs)/$nb_parieurs2);
							}
							else
							{
								$points_prono_visiteur=0;
							}

							$points = array();
							if($points_prono_domicile > 0)
							{
								$points[] = $points_prono_domicile;
							}
							if($points_prono_nul > 0)
							{
								$points[] = $points_prono_nul;
							}
							if($points_prono_visiteur > 0)
							{
								$points[] = $points_prono_visiteur;
							}
							
							$min = min($points);
							$max = max($points);
						}						
						
						if($strategie_globale == 4) {
							if($min == $points_prono_domicile) {
								$prono = "1";
							} else if($min == $points_prono_nul) {
								$prono = "N";
							} else {
								$prono = "2";
							}
						} else {
							if($max == $points_prono_domicile) {
								$prono = "1";
							} else if($max == $points_prono_nul) {
								$prono = "N";
							} else {
								$prono = "2";
							}					
						}
					
						break;				
				}
				$strQuery = "INSERT INTO phpl_pronostics (id_membre, id_champ, id_match, pronostic, auto) VALUES ($id_membre, $gr_champ, $val_matchs_id, '$prono', 1)";
				mysql_query($strQuery) or die ("probleme " .mysql_error());	
			}
		}
	}
}
?>
