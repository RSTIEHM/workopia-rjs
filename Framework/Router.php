<?php

namespace Framework;

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

        list($controller, $controllerMethod) = explode('@', $action);

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
     * Load Error Page
     * @param int $httpcode
     * @return void 
     */

    public function error($httpcode = 404)
    {
        http_response_code($httpcode);
        loadView("error/{$httpcode}");
        exit;
    }


    /**
     * ROUTE THE REQUEST 
     * @param string $uri 
     * @param string $method
     * @return void
     */

    public function route($uri, $method)
    {
        foreach ($this->routes as $route) {
            if ($route['uri'] === $uri && $route['method'] === $method) {
                //EXTRACT CONTTROLLER AND METHOD ================
                $controller = 'App\\Controllers\\' . $route['controller'];
                $controllerMethod = $route['controllerMethod'];
                // INSTANTIATE THE CONTROLLER AND CALL THE METHOD
                $controllerInstance = new $controller();
                $controllerInstance->$controllerMethod();
                return;
            }
        }
        $this->error(404);
    }
}
