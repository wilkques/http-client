<?php

namespace Wilkques\HttpClient;

use Wilkques\HttpClient\HTTPClient\CurlHTTPClient;

/**
 * @method static static asForm() add header Content-Type application/x-www-form-urlencoded; charset=utf-8
 * @method static static asJson() add header Content-Type application/json; charset=utf-8
 * @method static static withHeaders(array $headers) add headers
 * @method static static withToken(string $token, string $type = 'Bearer') add Authorization token
 * @method static static attach(string $filePath = null) file upload
 * @method static static contentType(string $contentType) custom Content-Type
 * @method static \Wilkques\HttpClient\Response get(string $url, array $data = []) http method get
 * @method static \Wilkques\HttpClient\Response post(string $url, array $data, array $query = null) http method post
 * @method static \Wilkques\HttpClient\Response put(string $url, array $data = [], array $query = null)
 * @method static \Wilkques\HttpClient\Response patch(string $url, array $data = [], array $query = null)
 * @method static \Wilkques\HttpClient\Response delete(string $url, array $query = null)
 */
class Http
{
    /** @var CurlHTTPClient */
    protected $curlHttpClient;

    /**
     * @return CurlHTTPClient
     */
    public function newCurlHttpClient()
    {
        return $this->curlHttpClient = $this->curlHttpClient ?? new CurlHTTPClient;
    }

    /**
     * @param string $method
     * @param array $arguments
     * 
     * @return CurlHTTPClient
     */
    public function __call(string $method, array $arguments)
    {
        return $this->newCurlHttpClient()->$method(...$arguments);
    }

    /**
     * @param string $method
     * @param array $arguments
     * 
     * @return CurlHTTPClient
     */
    public static function __callStatic($method, $arguments)
    {
        return (new static)->$method(...$arguments);
    }
}