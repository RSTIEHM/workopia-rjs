<?php
require __DIR__ . "/../vendor/autoload.php";

// MAKES ROUTER CLASS AVAILABLE
use Framework\Router;
use Framework\Session;

Session::start();

require '../helpers.php';

$router = new Router();
// REGISTER ROUTES ==================
$routes = require basePath('routes.php');

// ============= GET CURRENT URI & METHOD  ==============
// ==============     ENTRY POINT  ======================
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
// $uri example passed to $router->route(#uri) =  /listings/21 || /listings etc....

$router->route($uri);
