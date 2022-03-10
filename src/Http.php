<?php

namespace Wilkques\Http;

use Wilkques\Http\Client;

/**
 * @method static static asForm() add header Content-Type application/x-www-form-urlencoded; charset=utf-8
 * @method static static asJson() add header Content-Type application/json; charset=utf-8
 * @method static static asMultipart() add header Content-Type multipart/form-data; charset=utf-8
 * @method static static withHeaders(array $headers) add headers
 * @method static static withToken(string $token, string $type = 'Bearer') add Authorization token
 * @method static static attach(string|array $name, ?string $filePath = null, ?string $mimeType = null, ?string $reName = null) file upload
 * @method static static attachUploadFile(string $filePath) Only send one File.
 * @method static static contentType(string $contentType) custom Content-Type
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