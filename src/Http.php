<?php

namespace Wilkques\HttpClient;

use Wilkques\HttpClient\HTTPClient\CurlHTTPClient;

/**
 * @method static asForm() add header Content-Type application/x-www-form-urlencoded; charset=utf-8
 * @method static asJson() add header Content-Type application/json; charset=utf-8
 * @method static withHeaders(array $headers) add headers
 * @method static withToken(string $token, string $type = 'Bearer') add Authorization token
 * @method static attach(string $filePath = null) file upload
 * @method static contentType(string $contentType) custom Content-Type
 * @method response get(string $url, array $data = []) http method get
 * @method response post(string $url, array $data, array $query = null) http method post
 * @method response put(string $url, array $data = [], array $query = null)
 * @method response patch(string $url, array $data = [], array $query = null)
 * @method response delete(string $url, array $query = null)
 */
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