<?php
namespace AzizbekIsmoilov\phpmvc;


use AzizbekIsmoilov\phpmvc\exception\NotFoundException;

class Router
{
    protected array $routes =[];
    public Request $request;
    public Response $response;
    public function __construct(Request $request, Response $response)
    {
        $this->request = $request;
        $this->response = $response;
    }

    public function get($path, $collback)
    {
        $this->routes['get'][$path] = $collback;
    }
    public function post($path, $collback)
    {
        $this->routes['post'][$path] = $collback;
    }

    public function resolve()
    {
        $path = $this->request->getPath();
        $method = strtolower($this->request->method());
        $callback = $this->routes[$method][$path] ?? false;
        if ($callback === false){
            $this->response->setStatusCode(404);
           throw new NotFoundException();
        }
        if (is_string($callback)){
            return Application::$app->view->renderView($callback);
        }
        if (is_array($callback)){
            /** @var  $controller Controller*/
            $controller = new $callback[0]();
            Application::$app->controller = $controller;
            $controller->action = $callback[1];
            $callback[0] = $controller;
            foreach ($controller->getMiddlewares() as $middleware) {
                $middleware->execute();
            }
        }
        echo call_user_func($callback, $this->request,$this->response);
    }
}