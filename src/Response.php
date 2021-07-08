<?php

namespace Wilkques\HttpClient;

use Wilkques\HttpClient\Exception\RequestException;

/**
 * A class represents API response.
 */
class Response
{
    /** @var int */
    private $httpStatus;
    /** @var string */
    private $body;
    /** @var string[] */
    private $headers;
    /** @var array */
    private $curlInfo;

    /**
     * Response constructor.
     *
     * @param string $result
     * @param array $info
     */
    public function __construct(string $result = null, array $info = null)
    {
        $this->setInfo($info)->response($result);
    }

    /**
     * @param array|null $info
     * 
     * @return static
     */
    public function setInfo(array $info = null)
    {
        $this->curlInfo = $info;

        return $this;
    }

    /**
     * @return array
     */
    public function info()
    {
        return $this->curlInfo;
    }

    /**
     * @param string $result
     * 
     * @return static
     */
    protected function response(string $result = null)
    {
        [
            'http_code'     => $httpStatus,
            'header_size'   => $responseHeaderSize
        ] = $this->info();

        $this->setHttpStatus($httpStatus)
            ->setHeaders($this->responseHeaders($result, $responseHeaderSize))
            ->setBody($this->bodyHandle($result, $responseHeaderSize));

        return $this;
    }

    /**
     * @return array
     */
    protected function responseHeaders($result, $responseHeaderSize)
    {
        $responseHeaderStr = substr($result, 0, $responseHeaderSize);
        $responseHeaders = [];
        foreach (explode("\r\n", $responseHeaderStr) as $responseHeader) {
            $kv = explode(':', $responseHeader, 2);
            if (count($kv) === 2) {
                $responseHeaders[$kv[0]] = trim($kv[1]);
            }
        }

        return $responseHeaders;
    }

    /**
     * @return string|false
     */
    protected function bodyHandle($result, $responseHeaderSize)
    {
        return substr($result, $responseHeaderSize);
    }

    /**
     * @return RequestException
     */
    protected function getThrows()
    {
        return new RequestException($this);
    }

    /**
     * @param callable $callable
     * 
     * @return $this
     */
    public function throw(callable $callable = null)
    {
        if ($this->failed()) {
            if ($callable && is_callable($callable)) {
                $callable($this, $this->getThrows());
            }

            throw new RequestException($this);
        }

        return $this;
    }

    /**
     * @param int $code
     * 
     * @return static
     */
    public function setHttpStatus(int $code = 200)
    {
        $this->httpStatus = $code;

        return $this;
    }

    /**
     * Returns HTTP status code of response.
     *
     * @return int HTTP status code of response.
     */
    public function getHttpStatus()
    {
        return $this->httpStatus;
    }

    /**
     * Get the status code of the response.
     *
     * @return int
     */
    public function status()
    {
        return (int) $this->getHttpStatus();
    }

    /**
     * @param string $body
     * 
     * @return static
     */
    public function setBody(string $body = '')
    {
        $this->body = $body;

        return $this;
    }

    /**
     * Returns raw response body.
     *
     * @return string Raw request body.
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @return string
     */
    public function body()
    {
        return (string) $this->getBody();
    }

    /**
     * Returns response body as array (it means, returns JSON decoded body).
     *
     * @return array Request body that is JSON decoded.
     */
    public function json()
    {
        return json_decode($this->body(), true);
    }

    /**
     * Returns the value of the specified response header.
     *
     * @param string $name A String specifying the header name.
     * 
     * @return string|null A response header string, or null if the response does not have a header of that name.
     */
    public function header($key)
    {
        return $this->headers[$key] ?? null;
    }

    /**
     * @param array $headers
     * 
     * @return static
     */
    public function setHeaders(array $headers = [])
    {
        $this->headers = $headers;

        return $this;
    }

    /**
     * Returns all of response headers.
     *
     * @return string[] All of the response headers.
     */
    public function headers()
    {
        return $this->headers;
    }

    /**
     * Determine if the request was successful.
     *
     * @return bool
     */
    public function successful()
    {
        return $this->status() >= 200 && $this->status() < 300;
    }

    /**
     * Determine if the response code was "OK".
     *
     * @return bool
     */
    public function ok()
    {
        return $this->status() === 200;
    }

    /**
     * Determine if the response was a redirect.
     *
     * @return bool
     */
    public function redirect()
    {
        return $this->status() >= 300 && $this->status() < 400;
    }

    /**
     * Determine if the response indicates a client or server error occurred.
     *
     * @return bool
     */
    public function failed()
    {
        return $this->serverError() || $this->clientError();
    }

    /**
     * Determine if the response indicates a client error occurred.
     *
     * @return bool
     */
    public function clientError()
    {
        return $this->status() >= 400 && $this->status() < 500;
    }

    /**
     * Determine if the response indicates a server error occurred.
     *
     * @return bool
     */
    public function serverError()
    {
        return $this->status() >= 500;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->body();
    }
}
