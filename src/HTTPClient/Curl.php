<?php

namespace Wilkques\HttpClient\HTTPClient;

/**
 * cURL session manager
 */
class Curl
{
    /** @var \CurlHandle */
    private $curlHandle;
    /** @var string */
    private $url;
    /** @var CurlHTTPClient */
    private $curlHTTPClient;

    /**
     * @param string $url
     */
    public function __construct(string $url = '')
    {
        $this->setUrl($url);
    }

    /**
     * @param string $url
     * 
     * @return static
     */
    public function setUrl(string $url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param \CurlHandle|boolean $curlHandle
     * 
     * @return static
     */
    public function setCurlHandle($curlHandle)
    {
        $this->curlHandle = $curlHandle;

        return $this;
    }

    /**
     * @return \CurlHandle|boolean
     */
    public function getCurlHandle()
    {
        return $this->curlHandle;
    }

    /**
     * @return static
     */
    public function init()
    {
        return $this->setCurlHandle(curl_init($this->getUrl()));
    }

    /**
     * @param CurlHTTPClient $curlHTTPClient
     * 
     * @return static
     */
    public function setCurlHttpClient(CurlHTTPClient $curlHTTPClient)
    {
        $this->curlHTTPClient = $curlHTTPClient;

        return $this;
    }

    /**
     * @return CurlHTTPClient
     */
    public function getCurlHttpClient()
    {
        return $this->curlHTTPClient;
    }

    /**
     * Set multiple options for a cURL transfer
     *
     * @param array $options Returns TRUE if all options were successfully set. If an option could not be
     * successfully set, FALSE is immediately returned, ignoring any future options in the options array.
     * @return bool
     */
    public function setoptArray(array $options)
    {
        return curl_setopt_array($this->getCurlHandle(), $options);
    }

    /**
     * Perform a cURL session
     *
     * @return string|bool Returns TRUE on success or FALSE on failure. However, if the CURLOPT_RETURNTRANSFER
     * option is set, it will return the result on success, FALSE on failure.
     */
    public function exec()
    {
        return curl_exec($this->getCurlHandle());
    }

    /**
     * Gets information about the last transfer.
     *
     * @return array
     */
    public function getinfo()
    {
        return curl_getinfo($this->getCurlHandle());
    }

    /**
     * @return int Returns the error number or 0 (zero) if no error occurred.
     */
    public function errno()
    {
        return curl_errno($this->getCurlHandle());
    }

    /**
     * @return string Returns the error message or '' (the empty string) if no error occurred.
     */
    public function error()
    {
        return curl_error($this->getCurlHandle());
    }

    /**
     * Closes a cURL session and frees all resources. The cURL handle, ch, is also deleted.
     */
    public function close()
    {
        curl_close($this->getCurlHandle());
    }

    public function __call($method, $arguments)
    {
        return $this->getCurlHttpClient()->$method(...$arguments);
    }
}
