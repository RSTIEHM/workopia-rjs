<?php
require __DIR__ . "/../vendor/autoload.php";
require '../helpers.php';

use Framework\Router;




$router = new Router();
$routes = require basePath('routes.php');



// GET CURRENT URI & METHOD ======ENTRY POINT=============
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);




$router->route($uri);
