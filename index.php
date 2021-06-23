<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

define('ROOT', dirname(__FILE__));
define('SITE','mars');
require_once(ROOT.'/classes/Router.php');
require_once(ROOT.'/classes/Db.php');

session_start();

$router = new Router();
$router->run();

