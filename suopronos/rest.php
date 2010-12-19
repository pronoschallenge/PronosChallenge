<?php

require_once './lib/dbutil.php';

require_once './lib/tonic/tonic.php';
require_once './rest/classement.php';

$uri = $_SERVER['REQUEST_URI'];
$config = array();
$config['uri'] = substr($uri, strlen("/rest"));

$request = new Request($config);
$resource = $request->loadResource();
$response = $resource->exec($request);
$response->output();

?>
