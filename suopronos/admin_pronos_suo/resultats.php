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

// si le numero de journee n'est pas précisé, on prend la dernière journée
if (empty ($numero))
{   
	$requete="SELECT max(phpl_journees.numero) from phpl_journees, phpl_matchs where phpl_journees.id=phpl_matchs.id_journee and buts_dom is not NULL and phpl_journees.id_champ='$champ'";
	$resultats=mysql_query($requete);
	while ($row=mysql_fetch_array($resultats))
    {
    	$numero=$row[0];
    }
    if ($numero=="") 
    {
	    $numero="1";
	}
}
// si aucune journée n'a encore été jouée, on prend la première journée
if (empty ($numero))
{
	$numero=1;
}

// si on a  validé des résultats...
if ($action2=="1")
{
	// si on a saisi des résultats, on réaffiche la journée
    $numero=$numero-1;
	
	reset ($date_reelle);
    reset ($butd);
    reset ($butv);
    reset ($matchs_id);

	/************** calcul des pronostics de l'utilisateur si il ne les a pas fait ! ******************/
	// Récupération de l'id du groupe championnat
	$requete="SELECT id FROM phpl_gr_championnats WHERE id_champ='$champ'";
	$resultats=mysql_query($requete) or die (mysql_error());
	if ($row=mysql_fetch_array($resultats))
	{		
		$gr_champ = $row['id'];	
	}
	calcul_pronos_auto($matchs_id, $butd, $butv, $gr_champ);
	/*************************************************/
	
	// pour chaque match de la journée...     
	while ( list ($cle, $val_date_reelle)= each ($date_reelle) and list ($cle, $val_butd)= each ($butd) and list ($cle, $val_butv)= each ($butv) and list ($cle, $val_matchs_id)= each ($matchs_id))
    {       
	        
    	$date_us=format_date_us($val_date_reelle);
       	if ( !(($val_butd=='') or ($val_butv=='')))
        {
	        /************** Pronos classique ******************/
	   		// Requete pour la mise à jour des scores des matchs
           	$requete="UPDATE phpl_matchs SET phpl_matchs.buts_dom='$val_butd', phpl_matchs.buts_ext='$val_butv', phpl_matchs.date_reelle='$date_us'
                     	WHERE phpl_matchs.id='$val_matchs_id'";

            // Récupération des paramètres des points
           	$requete4="SELECT pts_prono_exact, pts_prono_participation FROM phpl_gr_championnats WHERE id_champ='$champ'";
           	$resultats4=mysql_query($requete4) or die (mysql_error());
           	while ($row4=mysql_fetch_array($resultats4))
           	{
            	$pts_prono_exact=$row4[0];
              	$pts_prono_participation=$row4[1];
              	$points_prono_exact=$pts_prono_exact + $pts_prono_participation ;
           	}

           	// Récupération des pronostics des joueurs
           	$requete2="SELECT pronostic, id_membre FROM phpl_pronostics WHERE id_match='$val_matchs_id'";
           	$resultats2=mysql_query($requete2) or die (mysql_error());
           	while ($row2=mysql_fetch_array($resultats2))
           	{
            	if ($val_butd>$val_butv and $row2[0]=="1"){$query3="UPDATE phpl_pronostics SET points='$points_prono_exact', participation='1' WHERE id_membre='$row2[1]' AND id_match='$val_matchs_id'";}
              	elseif ($val_butd==$val_butv and $row2[0]=="N"){$query3="UPDATE phpl_pronostics SET points='$points_prono_exact', participation='1' WHERE id_membre='$row2[1]' AND id_match='$val_matchs_id'";}
              	elseif ($val_butd<$val_butv and $row2[0]=="2"){$query3="UPDATE phpl_pronostics SET points='$points_prono_exact', participation='1' WHERE id_membre='$row2[1]' AND id_match='$val_matchs_id'";}
              	else {$query3="UPDATE phpl_pronostics SET points='$pts_prono_participation', participation='1' WHERE id_membre='$row2[1]' AND id_match='$val_matchs_id'";}
              	mysql_query($query3) or die (mysql_error());
            }
            /*************************************************/
            
            /************** Pronos HOURRA ! ******************/
		   	//On compte le nombre de parieur sur le match
		   	$nombre_pronos=mysql_query("SELECT COUNT(*) AS parieurs FROM phpl_pronostics WHERE id_match='$val_matchs_id'");
		   	$nb_pronos=mysql_fetch_array($nombre_pronos);
		   	$nb_parieurs=$nb_pronos['parieurs'];
		   
		  	//On compte le nombre de parieur sur une victoire de l'equipe à domicile
		   	$nombre_1=mysql_query("SELECT COUNT(*) AS domicile FROM phpl_pronostics WHERE id_match='$val_matchs_id' AND pronostic='1'");
		   	$nb_1=mysql_fetch_array($nombre_1);
		   	$nb_parieurs1=$nb_1['domicile'];
		   
		   	//On compte le nombre de parieur sur un match nul
		   	$nombre_N=mysql_query("SELECT COUNT(*) AS nul FROM phpl_pronostics WHERE id_match='$val_matchs_id' AND pronostic='N'");
		   	$nb_N=mysql_fetch_array($nombre_N);
		   	$nb_parieursN=$nb_N['nul'];
		   
		   	//On compte le nombre de parieur sur une victoire de l'equipe à l'exterieur
		   	$nombre_2=mysql_query("SELECT COUNT(*) AS visiteur FROM phpl_pronostics WHERE id_match='$val_matchs_id' AND pronostic='2'");
		   	$nb_2=mysql_fetch_array($nombre_2);
		   	$nb_parieurs2=$nb_2['visiteur'];
		   
		   	//On attribue les points 
         	if ($nb_parieurs1=="0")
		 	{
			 	$points_prono_domicile=$pts_prono_participation;
			}
		 	else
		 	{
			 	$points_prono_domicile=floor(($pts_prono_exact*$nb_parieurs)/$nb_parieurs1)+$pts_prono_participation;
			}
		 
		 	if ($nb_parieursN=="0")
		 	{
			 	$points_prono_nul=$pts_prono_participation;
			}
		 	else
		 	{
			 	$points_prono_nul=floor(($pts_prono_exact*$nb_parieurs)/$nb_parieursN)+$pts_prono_participation;
			}
		 
		 	if ($nb_parieurs2=="0")
		 	{
			 	$points_prono_visiteur=$pts_prono_participation;
			}
		 	else
         	{
	         	$points_prono_visiteur=floor(($pts_prono_exact*$nb_parieurs)/$nb_parieurs2)+$pts_prono_participation;
	        }

	        $requete5="SELECT pronostic, id_membre FROM phpl_pronostics WHERE id_match='$val_matchs_id'";
           	$resultats5=mysql_query($requete5) or die (mysql_error());
           	while ($row5=mysql_fetch_array($resultats5))
           	{
            	if ($val_butd>$val_butv and $row5[0]=="1"){$query4="UPDATE phpl_pronostics SET points_hourra='$points_prono_domicile', participation='1' WHERE id_membre='$row5[1]' AND id_match='$val_matchs_id'";}
              	elseif ($val_butd==$val_butv and $row5[0]=="N"){$query4="UPDATE phpl_pronostics SET points_hourra='$points_prono_nul', participation='1' WHERE id_membre='$row5[1]' AND id_match='$val_matchs_id'";}
              	elseif ($val_butd<$val_butv and $row5[0]=="2"){$query4="UPDATE phpl_pronostics SET points_hourra='$points_prono_visiteur', participation='1' WHERE id_membre='$row5[1]' AND id_match='$val_matchs_id'";}
              	else {$query4="UPDATE phpl_pronostics SET points_hourra='$pts_prono_participation', participation='1' WHERE id_membre='$row5[1]' AND id_match='$val_matchs_id'";}
              	mysql_query($query4) or die (mysql_error());
            }
            /*************************************************/
           
         //}            
		}
        elseif (($val_butv=='') or ($val_butd==''))
       	{
        	$requete="UPDATE phpl_matchs SET phpl_matchs.buts_dom=NULL, phpl_matchs.buts_ext=NULL, phpl_matchs.date_reelle='$date_us'
                   		WHERE phpl_matchs.id='$val_matchs_id' " ;
         	$requete2="SELECT pronostic, id_membre FROM phpl_pronostics WHERE id_match='$val_matchs_id'";
         	$resultats2=mysql_query($requete2) or die (mysql_error());
         	while ($row2=mysql_fetch_array($resultats2))
         	{ 
            	mysql_query("UPDATE phpl_pronostics SET points = '0', points_hourra = '0', participation='1' WHERE id_membre='$row2[1]' AND id_match='$val_matchs_id'") or die (mysql_error());
         	} 
       	}
	
    	// Mise à jour des scores des matchs
    	mysql_query($requete);
    }     
}

?>

<table class=phpl width="80%">
            <tr>
              <td class=phpl2 align="center" colspan="3"><? echo ADMIN_RESULTS_TITRE." "; affich_champ ($champ); ?></td>
            </tr>
            <tr>
              <td align="center"><? journees ($champ, $numero, $action);?><br /><br />
              </td>
              </tr>
              <tr>
              <td>
              <table align=center cellspacing="0" width="100%">
              <form method="post" action="">
              
       <?
       resultats ($champ, $numero);
       ?>  
    </form>

              


</td>
            </tr>
</table>
<br /><br />
