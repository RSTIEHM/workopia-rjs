<?php

namespace Framework;

use App\Controllers\ErrorController;
use Framework\Middleware\Authorize;

class Router
{
    protected $routes = [];

    /**
     * Add A new Route
     * @param string $method
     * @param string $uri
     * @param string $action 
     * @param array $middleware
     * @return void 
     * 
     */

    // =====================================
    public function registerRoute($method, $uri, $action, $middleware = [])
    {

        // Assign variables as if they were an array
        list($controller, $controllerMethod) = explode('@', $action);

        $this->routes[] = [
            'method' => $method,
            'uri' => $uri,
            'controller' => $controller,
            'controllerMethod' => $controllerMethod,
            'middleware' => $middleware
        ];
    }

    /**
     * Add a GET ROUTE 
     * @param string $uri
     * @param string $controller
     * @param array $middleware
     * @return void 
     */

    // =====================================

    public function get($uri, $controller, $middleware = [])
    {
        $this->registerRoute('GET', $uri, $controller, $middleware);
    }

    /**
     * Add a POST ROUTE 
     * @param string $uri
     * @param string $controller
     * @return void 
     */

    // =====================================

    public function post($uri, $controller, $middleware = [])
    {
        $this->registerRoute('POST', $uri, $controller, $middleware);
    }

    // =====================================

    /**
     * Add a PUT ROUTE 
     * @param string $uri
     * @param string $controller
     * @return void 
     */

    // =====================================

    public function put($uri, $controller,  $middleware = [])
    {
        $this->registerRoute('PUT', $uri, $controller,  $middleware);
    }

    /**
     * Add a DELETE ROUTE 
     * @param string $uri
     * @param string $controller
     * @return void 
     */

    // =====================================

    public function delete($uri, $controller,  $middleware = [])
    {
        $this->registerRoute('DELETE', $uri, $controller,  $middleware);
    }

    // =====================================



    /**
     * ROUTE THE REQUEST 
     * @param string $uri 
     * @param string $method
     * @return void
     */

    // =====================================
    public function route($uri)
    {
        $requestMethod = $_SERVER['REQUEST_METHOD'];
        // CHECK FOR METHOD INPUT ====================
        if ($requestMethod === 'POST' && isset($_POST['_method'])) {
            // OVERIDE REQEUST
            $requestMethod = strtoupper($_POST["_method"]);
        }
        foreach ($this->routes as $route) {
            // Split the URI into segments
            $uriSegments = explode('/', trim($uri, '/'));

            // Split the route URI into segments
            $routeSegments = explode('/', trim($route['uri'], '/'));
            // Check if the number of segments matches && METHOD
            if (count($uriSegments) === count($routeSegments) && strtoupper($route['method']) === $requestMethod) {
                $params = [];

                // Compare each segment
                $match = true;

                for ($i = 0; $i < count($uriSegments); $i++) {
                    if ($routeSegments[$i] !== $uriSegments[$i] && !preg_match('/\{(.+?)\}/', $routeSegments[$i])) {
                        $match = false;
                        break;
                    }
                    if (preg_match('/\{(.+?)\}/', $routeSegments[$i], $matches)) {
                        // This segment is a parameter, so store it
                        $params[$matches[1]] = $uriSegments[$i];
                    }
                }

                if ($match) {
                    foreach ($route['middleware'] as $role) {
                        (new Authorize())->handle($role);
                    }
                    // MATCH IS TRUE Extract controller and method from route
                    $controller = 'App\\Controllers\\' . $route['controller'];
                    $controllerMethod = $route['controllerMethod'];

                    // Instantiate the controller and call the method, passing parameters
                    $controllerInstance = new $controller();
                    $controllerInstance->$controllerMethod($params);
                    return;
                }
            }
        }
        ErrorController::notFound();
    }
    // =====================================
}
