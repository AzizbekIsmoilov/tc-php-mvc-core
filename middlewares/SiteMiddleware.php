<?php

namespace AzizbekIsmoilov\phpmvc\middlewares;

use AzizbekIsmoilov\phpmvc\Application;
use AzizbekIsmoilov\phpmvc\exception\ForbiddenException;

class SiteMiddleware extends BaseMiddleware
{
    public array $actions = [];
    public function __construct(array $actions =[])
    {
        $this->actions = $actions;
    }

    /**
     * @throws ForbiddenException
     */
    public function execute()
    {
        if (Application::isGuest()){
            if (empty($this->actions) || in_array(Application::$app->controller->action, $this->actions)){
                throw new ForbiddenException();
            }
        }
    }
}