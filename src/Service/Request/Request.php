<?php

namespace App\Service\Request;

class Request
{
    private string $url;
    private string $method;
    private array $body;
    private array $queryParams;
    private array $headers;
    private string $host;
    private string $protocol;
    private string $ip;

    public function __construct()
    {
        $this->url = $_SERVER['REQUEST_URI'];
        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->body = $this->getBody();
        $this->queryParams = $this->getQueryParams();
        $this->headers = getallheaders();
        $this->host = $_SERVER['HTTP_HOST'];
        $this->protocol = $_SERVER['SERVER_PROTOCOL'];
        $this->ip = $_SERVER['REMOTE_ADDR'];
    }

    public function getRequest(): array
    {
        return [
            'url' => $this->url,
            'method' => $this->method,
        ];
    }

 
    public function getBody(): array
    {
        $input = file_get_contents('php://input');

        if ($this->method === 'POST' || $this->method === 'PUT' || $this->method === 'PATCH') {
            $data = json_decode($input, true);
            return $data ?? [];
        }
        return [];
    }

   
    public function getQueryParams(): array
    {
        $query = parse_url($this->url, PHP_URL_QUERY);
        if ($query) {
            parse_str($query, $params);
            return $params;
        }
        return [];
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

   
    public function getMethod(): string
    {
        return $this->method;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getPath(): string
    {
        return parse_url($this->url, PHP_URL_PATH);
    }

    public function getQuery(): string
    {
        return parse_url($this->url, PHP_URL_QUERY);
    }

    public function getHost(): string
    {
        return $this->host;
    }

    public function getProtocol(): string
    {
        return $this->protocol;
    }

    public function getIp(): string
    {
        return $this->ip;
    }



}

