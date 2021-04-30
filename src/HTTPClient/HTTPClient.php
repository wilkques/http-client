<?php

namespace Wilkques\HttpClient\HTTPClient;

/**
 * The interface that represents HTTP client API.
 *
 * If you want to switch using HTTP client, please implement this.
 */
interface HTTPClient
{
    /**
     * Sends GET request to API.
     *
     * @param string $url Request URL.
     * @param array $data Request body or resource path.
     * @param array $query
     * 
     * @return Response Response of API request.
     * 
     * @throws CurlExecutionException
     */
    public function get(string $url, array $data = []);

    /**
     * Sends POST request to API.
     *
     * @param string $url Request URL.
     * @param array $data Request body or resource path.
     * @param array $query
     * 
     * @return Response Response of API request.
     * 
     * @throws CurlExecutionException
     */
    public function post(string $url, array $data);

    /**
     * Sends DELETE request to API.
     *
     * @param string $url Request URL.
     * @param array $data Request body or resource path.
     * @param array $query
     * 
     * @return Response Response of API request.
     * 
     * @throws CurlExecutionException
     */
    public function delete(string $url);

    /**
     * Sends PUT request to API.
     *
     * @param string $url Request URL.
     * @param array $data Request body or resource path.
     * @param array $query
     * 
     * @return Response Response of API request.
     * 
     * @throws CurlExecutionException
     */
    public function put(string $url);

    /**
     * Sends PATCH request to API.
     *
     * @param string $url Request URL.
     * @param array $data Request body or resource path.
     * @param array $query
     * 
     * @return Response Response of API request.
     * 
     * @throws CurlExecutionException
     */
    public function patch(string $url);

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
}
