<?php

namespace Wilkques\Http\Contracts;

interface ClientInterface
{
    /**
     * Sends GET request to API.
     *
     * @param string $url Request URL.
     * @param array[] $query Request body
     * 
     * @return \Wilkques\HttpClient\Response Response of API request.
     * 
     * @throws \Wilkques\HttpClient\Exceptions\CurlExecutionException
     */
    public function get(string $url, array $query = []);

    /**
     * Sends POST request to API.
     *
     * @param string $url Request URL.
     * @param array[] $data Request body or resource path.
     * @param array|null $query
     * 
     * @return \Wilkques\HttpClient\Response Response of API request.
     * 
     * @throws \Wilkques\HttpClient\Exceptions\CurlExecutionException
     */
    public function post(string $url, array $data = [], ?array $query = null);

    /**
     * Sends DELETE request to API.
     *
     * @param string $url Request URL.
     * @param array[] $query
     * 
     * @return \Wilkques\HttpClient\Response Response of API request.
     * 
     * @throws \Wilkques\HttpClient\Exceptions\CurlExecutionException
     */
    public function delete(string $url, array $query = []);

    /**
     * Sends PUT request to API.
     *
     * @param string $url Request URL.
     * @param array[] $data Request body or resource path.
     * @param array|null $query
     * 
     * @return \Wilkques\HttpClient\Response Response of API request.
     * 
     * @throws \Wilkques\HttpClient\Exceptions\CurlExecutionException
     */
    public function put(string $url, array $data = [], ?array $query = null);

    /**
     * Sends PATCH request to API.
     *
     * @param string $url Request URL.
     * @param array[] $data Request body or resource path.
     * @param array|null $query
     * 
     * @return \Wilkques\HttpClient\Response Response of API request.
     * 
     * @throws \Wilkques\HttpClient\Exceptions\CurlExecutionException
     */
    public function patch(string $url, array $data = [], array $query = []);

    /**
     * header
     * 
     * 'Content-Type' => 'application/x-www-form-urlencoded; charset=utf-8'
     * 
     * @return static
     */
    public function asForm();

    /**
     * header
     * 
     * 'Content-Type' => 'application/json; charset=utf-8'
     * 
     * @return static
     */
    public function asJson();

    /**
     * header
     * 
     * 'Content-Type' => 'multipart/form-data; charset=utf-8'
     * 
     * @return static
     */
    public function asMultipart();

    /**
     * header
     * 
     * 'Authorization' => $token
     * 
     * @param string $token
     * @param string $type
     * 
     * @return static
     */
    public function withToken(string $token, string $type = 'Bearer');

    /**
     * headers
     * 
     * @param array $headers
     * 
     * @return static
     */
    public function withHeaders(array $headers);

    /**
     * attach file
     * 
     * @param array|string $name
     * @param string|null $filePath
     * @param string|null $mimeType
     * @param string|null $reName
     * 
     * @return static
     */
    public function attach($name, string $filePath = '', ?string $mimeType = null, ?string $reName = null);
}
