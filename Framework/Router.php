<?php

namespace Framework;

use App\Controllers\ErrorController;


class Router
{
    protected $routes = [];

    /**
     * Add A new Route
     * @param string $method
     * @param string $uri
     * @param string $action 
     * @return void 
     * 
     */

    public function registerRoute($method, $uri, $action)
    {
        // inspect($method);
        // inspect($uri);
        // inspect($action);
        // Assign variables as if they were an array
        list($controller, $controllerMethod) = explode('@', $action);
        // inspect($controller);
        // inspect($controllerMethod);
        $this->routes[] = [
            'method' => $method,
            'uri' => $uri,
            'controller' => $controller,
            'controllerMethod' => $controllerMethod
        ];
    }

    /**
     * Add a GET ROUTE 
     * @param string $uri
     * @param string $controller
     * @return void 
     */

    public function get($uri, $controller)
    {
        $this->registerRoute('GET', $uri, $controller);
    }

    /**
     * Add a POST ROUTE 
     * @param string $uri
     * @param string $controller
     * @return void 
     */

    public function post($uri, $controller)
    {
        $this->registerRoute('POST', $uri, $controller);
    }

    /**
     * Add a PUT ROUTE 
     * @param string $uri
     * @param string $controller
     * @return void 
     */

    public function put($uri, $controller)
    {
        $this->registerRoute('PUT', $uri, $controller);
    }

    /**
     * Add a DELETE ROUTE 
     * @param string $uri
     * @param string $controller
     * @return void 
     */

    public function delete($uri, $controller)
    {
        $this->registerRoute('DELETE', $uri, $controller);
    }




    /**
     * ROUTE THE REQUEST 
     * @param string $uri 
     * @param string $method
     * @return void
     */

    public function route($uri)
    {
        $requestMethod = $_SERVER['REQUEST_METHOD'];
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
}
