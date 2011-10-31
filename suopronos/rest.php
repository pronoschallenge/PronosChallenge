<?php

require_once './lib/dbutil.php';
require_once './prono/fonctions.php';
require_once './lib/tonic/tonic.php';

require_once './rest/classement.php';
require_once './rest/compte.php';
require_once './rest/pronos.php';
require_once './rest/gazouillis.php';
require_once './rest/gazouilli.php';
require_once './rest/profil.php';


$uri = $_SERVER['REQUEST_URI'];
$config = array();
$config['uri'] = substr($uri, strlen("/rest"));

$request = new Request($config);
$resource = $request->loadResource();
$response = $resource->exec($request);
$response->output();

?>
