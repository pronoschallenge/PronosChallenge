<?php
  
/**
 * An example resource
 * @uri /gazouilli/
 */
class GazouilliResource extends Resource {

    function isSecured() {
		// maybe we have caught authentication data in $_SERVER['REMOTE_USER']
		if((!$_SERVER['PHP_AUTH_USER'] || !$_SERVER['PHP_AUTH_PW']) && preg_match('/Basic+(.*)$/i', $_SERVER['REMOTE_USER'], $matches)) {
			list($name, $password) = explode(':', base64_decode($matches[1]));
			$_SERVER['PHP_AUTH_USER'] = strip_tags($name);
			$_SERVER['PHP_AUTH_PW'] = strip_tags($password);
		}
		
		return (isset($_SERVER['PHP_AUTH_USER']) 
					&& isset($_SERVER['PHP_AUTH_PW'])
					&& VerifSession($_SERVER['PHP_AUTH_USER'],$_SERVER['PHP_AUTH_PW'])=="1");
    }
    
    function post($request) {
        
        ouverture ();
        
        $response = new Response($request);
        
		if(!($this->isSecured())) {
			$response->body = "401";
			return $response;
		}
		
		$contenu = file_get_contents('php://input');
		if(empty($contenu)) {
			$response->body = "500:NO_DATA";
			return $response;
		} else if(strlen($contenu) > 140) {
			$response->body = "le message fait plus de 140 caractères";
			return $response;
		}       
        
        $user_pseudo = $_SERVER['PHP_AUTH_USER'];
        
		$requete= "SELECT pseudo, id_prono FROM phpl_membres WHERE pseudo='$user_pseudo'";
		$result = mysql_query($requete);
		$row = mysql_fetch_array($result);
		$user_id=$row[1];			
        
        $contenu = addslashes($contenu);
        
        mysql_query("INSERT INTO phpl_gazouillis (id_membre, contenu, reponse_a) VALUES ('$user_id','$contenu',null)") or die ("probleme " .mysql_error());
        
        $response->code = Response::OK;
        $response->addHeader('content-type', 'text/plain');	

        return $response;
        
    }
     
}

?>

