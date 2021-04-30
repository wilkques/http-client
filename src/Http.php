<?php

namespace Wilkques\HttpClient;

use Wilkques\HttpClient\HTTPClient\CurlHTTPClient;

class Http
{
    public function newCurlHttpClient()
    {
        return new CurlHTTPClient;
    }

    public function __call($method, $arguments)
    {
        return $this->newCurlHttpClient()->$method(...$arguments);
    }

    public static function __callStatic($method, $arguments)
    {
        return (new static)->$method(...$arguments);
    }
}