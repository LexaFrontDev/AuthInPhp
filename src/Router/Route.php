<?php

namespace App\Router;

#[\Attribute]
class Route
{
    public string $url;
    public string $action;
    public string $method;

    public function __construct(string $url, string $action, string $method = 'GET')
    {
        $this->url = $url;
        $this->action = $action;
        $this->method = strtoupper($method);
    }
}
