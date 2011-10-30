<?php
  
/**
 * An example resource
 * @uri /gazouillis/(.*)?/(.*)?
 */
class GazouillisResource extends Resource {
    
    function get($request, $debut, $limit) {
        
        $response = new Response($request);
        $response->code = Response::OK;
        $response->addHeader('content-type', 'text/plain');

        $data = array();

		ouverture ();

		if($debut == null)
		{
			$debut = 0;
		}

		if($limit == null)
		{
			$limit = 10;
		}

		// requete pour récupérer tous les gazouillis
		$requete="SELECT id_membre, pseudo, contenu, date_creation 
			FROM phpl_gazouillis, phpl_membres 
			WHERE phpl_gazouillis.id_membre=phpl_membres.id
			ORDER BY date_creation DESC
			LIMIT ".$debut.",".$limit."";

		$resultat=mysql_query($requete);
					
		while ($row=mysql_fetch_array($resultat))
		{
			$url_avatar = "http://".$_SERVER['SERVER_NAME']."/suopronos/prono/images/avatars/".$row["id_membre"].".gif";
			if(!remote_file_exists($url_avatar))
			{
				$url_avatar = "http://".$_SERVER['SERVER_NAME']."/suopronos/prono/images/avatars/no_avatar.png";
			}

       		array_push($data, array("id_membre" => $row["id_membre"], "pseudo" => $row["pseudo"], "url_avatar" => $url_avatar, "contenu" => utf8_encode($row["contenu"]), "date" => $row["date_creation"]));

		}

		$response->body = json_encode(array("gazouillis" => $data));

        return $response;
        
    }
     
}

?>

