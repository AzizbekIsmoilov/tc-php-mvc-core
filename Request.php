<?php

namespace app\core;

class Request
{
    const POST = 'POST';
    const GET = 'GET';
    public function getPath()
    {
        $path = $_SERVER['REQUEST_URI'] ?? '/';
        $position = strpos($path, '?');
        if ($position ===false){
            return $path;
        }
        return substr($path,0, $position);
    }
    
    public function method()
    {
        return strtoupper($_SERVER['REQUEST_METHOD']);
    }
    public function isGet(): bool
    {
        return $this->method() === self::GET;
    }
    public function isPost(): bool
    {
        return $this->method() === self::POST;
    }
    public function getBody(): array
    {
        $body = [];
        if ($this->method() == self::GET){
            foreach ($_GET as $key => $value){
                $body[$key] = filter_input(INPUT_GET, $key, FILTER_SANITIZE_SPECIAL_CHARS);
            }
        }
        if ($this->method() == self::POST){
            foreach ($_POST as $key => $value){
                $body[$key] = filter_input(INPUT_POST, $key, FILTER_SANITIZE_SPECIAL_CHARS);
            }
        }
        return $body;
    }

}