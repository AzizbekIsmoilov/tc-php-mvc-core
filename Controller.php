<?php

namespace AzizbekIsmoilov\phpmvc;

use AzizbekIsmoilov\phpmvc\middlewares\BaseMiddleware;

abstract class Controller
{
    public string $layout = 'main';
    public string $action = '';
    /**
     * @var BaseMiddleware[]
     */
    public array $middlewares =[];
    public function setLayout($layout)
    {
        $this->layout = $layout;
    }
    public function render($view, $params=[])
    {
        return Application::$app->view->renderView($view, $params);
    }
    
    public function registerMiddleware(BaseMiddleware $middleware)
    {
        $this->middlewares[] = $middleware;
    }

    /**
     * @return array
     */
    public function getMiddlewares(): array
    {
        return $this->middlewares;
    }
}