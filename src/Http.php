<?php

namespace Wilkques\Http;

use Wilkques\Http\Client;

/**
 * @method static \Wilkques\Http\Client asForm() add header Content-Type application/x-www-form-urlencoded; charset=utf-8
 * @method static \Wilkques\Http\Client asJson() add header Content-Type application/json; charset=utf-8
 * @method static \Wilkques\Http\Client asMultipart() add header Content-Type multipart/form-data; charset=utf-8
 * @method static \Wilkques\Http\Client withHeaders(array $headers) add headers
 * @method static \Wilkques\Http\Client withToken(string $token, string $type = 'Bearer') add Authorization token
 * @method static \Wilkques\Http\Client attach(string|array $name, ?string $filePath = null, ?string $mimeType = null, ?string $reName = null) file upload
 * @method static \Wilkques\Http\Client attachUploadFile(string $filePath) Only send one File.
 * @method static \Wilkques\Http\Client contentType(string $contentType) custom Content-Type
 * @method static \Wilkques\Http\Response get(string $url, array $data = []) http method get
 * @method static \Wilkques\Http\Response post(string $url, array $data, array $query = null) http method post
 * @method static \Wilkques\Http\Response put(string $url, array $data = [], array $query = null)
 * @method static \Wilkques\Http\Response patch(string $url, array $data = [], array $query = null)
 * @method static \Wilkques\Http\Response delete(string $url, array $query = null)
 */
class Http
{
    /** @var Client */
    protected $client;

    /**
     * @return mixed
     */
    public function newClient()
    {
        return $this->client = $this->client ?? new Client;
    }

    /**
     * @param string $method
     * @param array $arguments
     * 
     * @return mixed
     */
    public function __call(string $method, array $arguments)
    {
        return $this->newClient()->$method(...$arguments);
    }

    /**
     * @param string $method
     * @param array $arguments
     * 
     * @return mixed
     */
    public static function __callStatic($method, $arguments)
    {
        return (new static)->$method(...$arguments);
    }
}