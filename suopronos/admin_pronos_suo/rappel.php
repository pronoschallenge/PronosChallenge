<?php
  require ("../config.php") ;
  require ("fonctions.php");
  ouverture ();
?>

<html>
<head>

</head>

<body>

<table class=phpl width="80%">
            <tr>
              <td class=phpl2 align="center" colspan="3">Rappel aux utilisateurs</td>
            </tr>
            <tr>
            <td align="left">
<?php
// récupération de l'id du championnat
$requete="SELECT phpl_gr_championnats.id FROM phpl_gr_championnats WHERE phpl_gr_championnats.activ_prono='1' ORDER by id desc";
$resultat=mysql_query ($requete) or die ("probleme " .mysql_error());
$row= mysql_fetch_array($resultat); 
$gr_champ=$row[0];  

// calcul de la date de demain
$nbJrs=1;
if (isset($_REQUEST['nbjours']))
{
  $nbJrs=$_REQUEST['nbjours'];
} 

$intDemain = time () + (86400 * $nbJrs);

$date_demain=date('Y-m-d', $intDemain);
$date_demain_debut=date('Y-m-d 00:00:00', $intDemain);
$date_demain_fin=date('Y-m-d 23:59:59', $intDemain);

echo "<b>Matchs du ".$date_demain." :</b><br>";

// on vérifie s'il y a des pronos à faire pour le lendemain
      
$queryMatchs="SELECT phpl_matchs.id, phpl_clubs.nom, CLEXT.nom, phpl_matchs.date_reelle, phpl_journees.numero
        FROM phpl_clubs, phpl_clubs as CLEXT, phpl_matchs, phpl_journees, phpl_equipes, phpl_equipes as EXT, phpl_gr_championnats
        WHERE phpl_clubs.id=phpl_equipes.id_club
        AND CLEXT.id=EXT.id_club
        AND phpl_equipes.id=phpl_matchs.id_equipe_dom
        AND EXT.id=phpl_matchs.id_equipe_ext
        AND phpl_matchs.id_journee=phpl_journees.id
        AND phpl_journees.id_champ=phpl_gr_championnats.id_champ
        AND phpl_gr_championnats.id='$gr_champ'
        AND phpl_matchs.buts_dom is null
        AND phpl_matchs.buts_ext is null
        AND phpl_clubs.nom!='exempte'
        AND CLEXT.nom!='exempte'
        AND phpl_matchs.date_reelle>='$date_demain_debut' 
        AND phpl_matchs.date_reelle<='$date_demain_fin'
        ORDER by phpl_matchs.date_reelle, phpl_clubs.nom";
                  
$resultMatchs=mysql_query ($queryMatchs);
if(mysql_num_rows($resultMatchs)==0)
{
  echo "aucun match demain...";
}
else
{ 
  // si il y a des pronos demain...
  $tabRowsMatchs = array();
  while ($rowMatchs=mysql_fetch_array($resultMatchs))
  {  
    array_push($tabRowsMatchs, $rowMatchs);
  }
  
  // on récupère tous les pronostiqueurs
  $queryUsers="SELECT id, pseudo, mail, actif FROM phpl_membres";
  $resultUsers=mysql_query ($queryUsers);
  while ($rowUsers=mysql_fetch_array($resultUsers))
  { 
    // si il est actif...
    if($rowUsers[3]=='1') 
    {
      // pour chaque utilisateur...
      $sendMail = false;
      $mailBody = "<span style=\"font-family: Verdana; font-size: 10pt\">";
      $mailBody .= "Attention ".$rowUsers[1]." ! Vous n'avez toujours pas pronostiqu� les matchs suivants :<br>";
      for($i=0; $i<count($tabRowsMatchs); $i++)
      {
        $query3="SELECT * FROM phpl_pronostics WHERE id_match=".$tabRowsMatchs[$i][0]." AND id_membre=".$rowUsers[0]." AND pronostic IS NOT NULL";
        $result3=mysql_query ($query3);
        if(mysql_num_rows($result3)==0)
        {
          $sendMail = true;
          $mailBody .= "<br>".$tabRowsMatchs[$i][1]." - ".$tabRowsMatchs[$i][2]."";
        }    
      }
      
      if($nbJrs==1)
      {
        $mailBody .= "<br><br>Vous avez jusqu'� demain pour le faire !";
      }
      else
      {
        $mailBody .= "<br><br>Vous avez jusqu'au ".$date_demain." pour le faire !";
      }
      
      $mailBody .= "<br><br>Les admins de PronosChallenge";
      
      $mailBody .= "<br><a href=\"http://www.pronoschallenge.fr\">http://www.pronoschallenge.fr</a>";
      $mailBody .= "<br>";
      $mailBody .= "<br><a href=\"mailto:pronoschallenge@pronoschallenge.fr\">pronoschallenge@pronoschallenge.fr</a>";
      
      $mailBody .= "</span>";
      
      if($sendMail && $rowUsers[2]) 
      {
        $email = $rowUsers[2];
        //$email = "thomas.delhomenie@wanadoo.fr";
        $sujet = "Vos pronostics ".$rowUsers[1]." !";
        //$sujet = "Petite erreur ! (bis) :P";
        $message = $mailBody;
        //$message = "Bon comme vous l'aurez surement compris, c'�tait encore juste un test. Normalement c'est le dernier, donc le prochain que vous recevrez devra etre pris en compte ;)<br>�a ne vous empeche bien sur pas de faire vos pronos maintenant :)";
        $headers = "From: pronoschallenge.info@online.fr\n";
        $headers .= "Bcc: pronoschallenge.info@online.fr"."\r\n";
        $headers .= "MIME-version: 1.0\n";
        $headers .= "Content-type: text/html; charset= iso-8859-1\n";
        if(mail($email,$sujet,$message,$headers))
        {
          echo "<br>Email envoy� � ".$rowUsers[1];
        }
        else
        {
          echo "<br>Erreur lors de l'envoi du mail � ".$rowUsers[1];
        }
        sleep(2);
      }      
    }    
  }
}






/*
$debut=0;
$fin=1;

$result=mysql_query("SELECT accession, barrage, relegation FROM phpl_parametres WHERE id_champ='$champ'");
 while ($row=mysql_fetch_array($result))
  {
    $accession = $row['accession'];
    $barrage = $row['barrage'] + $accession;
    $relegation = nb_equipes($champ)- $row['relegation'];
  }
$legende=CONSULT_CLMNT_MSG4.$debut.CONSULT_CLMNT_MSG5.$fin;
$query="SELECT max(phpl_journees.numero) FROM phpl_journees, phpl_matchs WHERE phpl_journees.id=phpl_matchs.id_journee AND buts_dom is not NULL and phpl_journees.id_champ='$champ'";
$result=mysql_query ($query);
$row=mysql_fetch_array($result);
$max=$row[0];
                                
while ($fin<=$max)
{                 
@db_clmnt($champ, $debut, $fin, 0);

             
$query="SELECT * FROM phpl_clmnt ORDER BY POINTS DESC, DIFF DESC, BUTSPOUR DESC, BUTSCONTRE ASC, NOM";
$result=mysql_query($query) or die (mysql_error());
$pl=1;

      while ($row=mysql_fetch_array($result))
      {   
        $x=0;
        $id_equipe=$row["ID_EQUIPE"];

        $query="INSERT INTO phpl_clmnt_graph (id_equipe, fin, classement) VALUES ('$id_equipe','$fin', '$pl')" ;
        mysql_query($query);
        $pl++;                    
      }
$fin++;
      }     

$query="SELECT phpl_clmnt_graph.id_equipe FROM phpl_clmnt_graph, phpl_equipes WHERE phpl_equipes.id=phpl_clmnt_graph.id_equipe
                                                 and phpl_equipes.id_champ=$champ";
$result=mysql_query($query);
$nb_saving=mysql_num_rows($result);

$query="SELECT * FROM phpl_equipes WHERE id_champ=$champ";
$result=mysql_query($query);
$nb_equipes=mysql_num_rows($result);
             
@db_clmnt($champ, $debut, $fin, 1);


if ($nb_saving=$max*$nb_equipes){

echo ADMIN_GRAPH; include ("tps2.php3");}
else {echo ADMIN_GRAPH_4;}
*/
?>
</body>
</td></tr></table>
