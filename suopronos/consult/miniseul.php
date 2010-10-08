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
<?
if (isset($_GET['champmini'])){$champmini=$_GET['champmini'];}
if (isset($_GET['typemini'])) 
{
	$typemini=$_GET['typemini'];
}
else
{
	$typemini='General';
}
if (isset($_GET['nb_dessusmini'])) {$nb_dessusmini=$_GET['nb_dessusmini'];}
if (isset($_GET['nb_dessousmini'])) {$nb_dessousmini=$_GET['nb_dessousmini'];}
if (isset($_GET['presentationmini'])) {$presentationmini=$_GET['presentationmini'];}
if (isset($_GET['lienmini'])) {$lienmini=$_GET['lienmini'];}
if (isset($_GET['classmini'])) {$classmini=$_GET['classmini'];}
if (isset($_GET['ouverture'])) {$ouverture=$_GET['ouverture'];}


require ("../config.php");
require ("fonctions.php");
   
ouverture();

if (isset($typemini))
{
	// RAPPEL DES PARAMETRES du CHAMPIONNAT
	$result=mysql_query("SELECT * FROM phpl_parametres WHERE id_champ='$champmini'");
	while ($row=mysql_fetch_array($result))
	{
    	$accessionmini = $row['accession'];
		$barragemini = $row['barrage'] + $accessionmini;
		$id_equipe_fetiche=$row['id_equipe_fetiche'];
		$relegation = $row['relegation'];
	}
    $requete = "SELECT * FROM phpl_equipes, phpl_clubs WHERE phpl_clubs.id=phpl_equipes.id_club AND id_champ='$champmini' AND phpl_clubs.nom='exempte'";
    $resultats=mysql_query($requete);
    $exempte=mysql_num_rows($resultats);
    if ($exempte=='1') {$relegationmini = nb_equipes($champmini)- $relegation-1;}
    else {$relegationmini = nb_equipes($champmini)- $relegation;}

    //$relegationmini = nb_equipes($champmini)- $row[relegation];
  
	$legendemini='';

	echo "<div style=\"margin-top:1px;padding-top:3px;\" align=\"center\">";
	echo "Type : <select onChange=\"$('#pronos_classement').load('http://".$_SERVER['SERVER_NAME'].substr($_SERVER['PHP_SELF'], 0, strrpos($_SERVER['PHP_SELF'],'/'))."/../consult/miniseul.php?champmini=".$champmini."&typemini='+this.options[this.selectedIndex].value+'&presentationmini=1&lienmini=non&classmini=1');\">";
	echo "<option value=\"General\"";
	if($typemini=="General") { echo "selected"; }
	echo ">General</option>";
	echo "<option value=\"Domicile\"";
	if($typemini=="Domicile") { echo "selected"; }
	echo ">Domicile</option>";
	echo "<option value=\"Ext&eacute;rieur\"";
	if($typemini=="Extérieur") { echo "selected"; }
	echo ">Ext&eacute;rieur</option>";
	echo "</select>";

  	switch($typemini)
	{                        
		case GENERAL;    // CLASSEMENT GENERAL
        {

			$requetemini="SELECT * FROM phpl_clmnt_cache WHERE ID_CHAMP='$champmini' ORDER BY POINTS DESC, DIFF DESC, BUTSPOUR DESC , BUTSCONTRE ASC, NOM";

           	if ($presentationmini=="1")
        	{
          		if ($classmini=='1')
        		{
         			clmntred($legendemini, $typemini, $accessionmini, $barragemini, $relegationmini,  $champmini, $requetemini, $lienmini, $PHPLEAGUE_RACINE, $id_equipe_fetiche);
          		}
        		else
        		{
        			clmntmini($legendemini, $typemini, $accessionmini, $barragemini, $relegationmini,  $champmini, $requetemini, $nb_dessusmini, $nb_dessousmini, $lienmini, $PHPLEAGUE_RACINE, $id_equipe_fetiche);
        		}
        	}
        	if ($presentationmini=="2")
        	{
        		if ($classmini=="1")
        		{
        			clmnt_barrered($legendemini, $typemini, $accessionmini, $barragemini, $relegationmini,  $champmini, $requetemini, $lienmini, $PHPLEAGUE_RACINE, $id_equipe_fetiche);
          		}
        		else
        		{
        			clmntmini_barre($legendemini, $typemini, $accessionmini, $barragemini, $relegationmini,  $champmini, $requetemini, $nb_dessusmini, $nb_dessousmini, $lienmini, $PHPLEAGUE_RACINE, $id_equipe_fetiche);
        		}
        	}
		}
		break;

		case DOMICILE;
        {
        	$requetemini="SELECT NOM, DOMPOINTS, DOMJOUES, DOMG,  DOMN, DOMP, DOMBUTSPOUR, DOMBUTSCONTRE, DOMDIFF, ID_EQUIPE FROM phpl_clmnt_cache WHERE ID_CHAMP='$champmini' ORDER BY DOMPOINTS DESC, DOMDIFF DESC";
        	if ($presentationmini=="1")
        	{
          		if ($classmini=='1')
        		{
         			clmntred($legendemini, $typemini, $accessionmini, $barragemini, $relegationmini,  $champmini, $requetemini, $lienmini, $PHPLEAGUE_RACINE, $id_equipe_fetiche);
          		}
        		else
        		{
        			clmntmini($legendemini, $typemini, $accessionmini, $barragemini, $relegationmini,  $champmini, $requetemini, $nb_dessusmini, $nb_dessousmini, $lienmini, $PHPLEAGUE_RACINE, $id_equipe_fetiche);
        		}
        	}
        	if ($presentationmini=="2")
        	{
        		if ($classmini=='1')
        		{
         			clmnt_barrered($legendemini, $typemini, $accessionmini, $barragemini, $relegationmini, $champminimini, $requetemini, $lienmini, $PHPLEAGUE_RACINE, $id_equipe_fetiche);
          		}
        		else
        		{
        			clmntmini_barre ($legendemini, $typemini, $accessionmini, $barragemini, $relegationmini,  $champmini, $requetemini, $nb_dessusmini, $nb_dessousmini, $lienmini, $PHPLEAGUE_RACINE, $id_equipe_fetiche);
        		}
        	}
		}
		break;

		case ATTAQUE;
        {
        	$requetemini="SELECT * FROM phpl_clmnt_cache WHERE ID_CHAMP='$champmini' ORDER BY BUTSPOUR DESC, DIFF DESC";
        	if ($presentationmini=="1")
        	{
          		if ($classmini=='1')
        		{
         			clmntred($legendemini, $typemini, $accessionmini, $barragemini, $relegationmini,  $champmini, $requetemini, $lienmini, $PHPLEAGUE_RACINE, $id_equipe_fetiche);
          		}
        		else
        		{
        			clmntmini($legendemini, $typemini, $accessionmini, $barragemini, $relegationmini,  $champmini, $requetemini, $nb_dessusmini, $nb_dessousmini, $lienmini, $PHPLEAGUE_RACINE, $id_equipe_fetiche);
        		}
        	}
        	if ($presentationmini=="2")
        	{
        		if ($classmini=='1')
        		{
         			clmnt_barrered($legendemini, $typemini, $accessionmini, $barragemini, $relegationmini, $champminimini, $requetemini, $lienmini, $PHPLEAGUE_RACINE, $id_equipe_fetiche);
          		}
        		else
        		{
        			clmntmini_barre ($legendemini, $typemini, $accessionmini, $barragemini, $relegationmini,  $champmini, $requetemini, $nb_dessusmini, $nb_dessousmini, $lienmini, $PHPLEAGUE_RACINE, $id_equipe_fetiche);
        		}
        	}
		}
		break;


		case DEFENSE;
        {  
	        $requetemini="SELECT * FROM phpl_clmnt_cache WHERE ID_CHAMP='$champmini' ORDER BY BUTSCONTRE ASC, DIFF DESC";
    	    if ($presentationmini=="1")
        	{
          		if ($classmini=='1')
       			{
         			clmntred($legendemini, $typemini, $accessionmini, $barragemini, $relegationmini,  $champmini, $requetemini, $lienmini, $PHPLEAGUE_RACINE, $id_equipe_fetiche);
          		}
        		else
        		{
        			clmntmini($legendemini, $typemini, $accessionmini, $barragemini, $relegationmini,  $champmini, $requetemini, $nb_dessusmini, $nb_dessousmini, $lienmini, $PHPLEAGUE_RACINE, $id_equipe_fetiche);
        		}
        	}
        	if ($presentationmini=="2")
        	{
        		if ($classmini=='1')
        		{
         			clmnt_barrered($legendemini, $typemini, $accessionmini, $barragemini, $relegationmini, $champminimini, $requetemini, $lienmini, $PHPLEAGUE_RACINE, $id_equipe_fetiche);
          		}
        		else
        		{
        			clmntmini_barre ($legendemini, $typemini, $accessionmini, $barragemini, $relegationmini,  $champmini, $requetemini, $nb_dessusmini, $nb_dessousmini, $lienmini, $PHPLEAGUE_RACINE, $id_equipe_fetiche);
        		}
        	}
		}
		break;

		case GOALDIFF;
        {
        	$requetemini="SELECT * FROM phpl_clmnt_cache WHERE ID_CHAMP='$champmini' ORDER BY DIFF DESC, BUTSPOUR DESC, BUTSCONTRE ASC ";
        	if ($presentationmini=="1")
        	{
          		if ($classmini=='1')
        		{
         			clmntred($legendemini, $typemini, $accessionmini, $barragemini, $relegationmini,  $champmini, $requetemini, $lienmini, $PHPLEAGUE_RACINE, $id_equipe_fetiche);
          		}
        		else
        		{
        			clmntmini($legendemini, $typemini, $accessionmini, $barragemini, $relegationmini,  $champmini, $requetemini, $nb_dessusmini, $nb_dessousmini, $lienmini, $PHPLEAGUE_RACINE, $id_equipe_fetiche);
        		}
        	}
        	if ($presentationmini=="2")
        	{
        		if ($classmini=='1')
        		{
         			clmnt_barrered($legendemini, $typemini, $accessionmini, $barragemini, $relegationmini, $champminimini, $requetemini, $lienmini, $PHPLEAGUE_RACINE, $id_equipe_fetiche);
          		}
        		else
        		{
        			clmntmini_barre ($legendemini, $typemini, $accessionmini, $barragemini, $relegationmini,  $champmini, $requetemini, $nb_dessusmini, $nb_dessousmini, $lienmini, $PHPLEAGUE_RACINE, $id_equipe_fetiche);
        		}
        	}
		}
		break;

		case EXTERIEUR;
        {
        	$requetemini="SELECT NOM, EXTPOINTS, EXTJOUES, EXTG,  EXTN, EXTP, EXTBUTSPOUR, EXTBUTSCONTRE, EXTDIFF, ID_EQUIPE FROM phpl_clmnt_cache WHERE ID_CHAMP='$champmini' ORDER BY EXTPOINTS DESC, EXTDIFF DESC ";
        	if ($presentationmini=="1")
        	{
          		if ($classmini=='1')
        		{
         			clmntred($legendemini, $typemini, $accessionmini, $barragemini, $relegationmini,  $champmini, $requetemini, $lienmini, $PHPLEAGUE_RACINE, $id_equipe_fetiche);
          		}
        		else
        		{
        			clmntmini($legendemini, $typemini, $accessionmini, $barragemini, $relegationmini,  $champmini, $requetemini, $nb_dessusmini, $nb_dessousmini, $lienmini, $PHPLEAGUE_RACINE, $id_equipe_fetiche);
        		}
        	}
        	if ($presentationmini=="2")
        	{
        		if ($classmini=='1')
        		{
         			clmnt_barrered($legendemini, $typemini, $accessionmini, $barragemini, $relegationmini, $champminimini, $requetemini, $lienmini, $PHPLEAGUE_RACINE, $id_equipe_fetiche);
          		}
        		else
        		{
        			clmntmini_barre ($legendemini, $typemini, $accessionmini, $barragemini, $relegationmini,  $champmini, $requetemini, $nb_dessusmini, $nb_dessousmini, $lienmini, $PHPLEAGUE_RACINE, $id_equipe_fetiche);
        		}
        	}
		}
		break;
	}

	echo "</div>";
}
?>
