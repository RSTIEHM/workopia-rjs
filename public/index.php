<?php
session_start();
require __DIR__ . "/../vendor/autoload.php";
require '../helpers.php';

// MAKES ROUTER CLASS AVAILABLE
use Framework\Router;

$router = new Router();
// REGISTER ROUTES ==================
$routes = require basePath('routes.php');

// GET CURRENT URI & METHOD ======ENTRY POINT=============
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
// inspectAndDie($uri);

$router->route($uri);
