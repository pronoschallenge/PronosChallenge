<?php
  
/**
 * An example resource
 * @uri /compte/(.*)?/(.*)?
 */
class CompteResource extends Resource {
    
    function get($request, $username, $password) {
        
        $response = new Response($request);
        $response->code = Response::OK;
        $response->addHeader('content-type', 'text/plain');

		ouverture ();

		if (VerifSession ($username,$password)=="1")
		{
			$response->body = json_encode(array("compte" => "ok"));
		}
		else
		{
			$response->body = json_encode(array("compte" => "ko"));
		}
		
		return $response;        
    }
    
}

?>

