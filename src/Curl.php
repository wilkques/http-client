<?php

namespace Wilkques\Http;

/**
 * cURL session manager
 */
class Curl
{
    /** @var \CurlHandle */
    private $curlHandle;
    /** @var string */
    private $url;
    /** @var Client */
    private $client;

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
     * @param Client $curlHTTPClient
     * 
     * @return static
     */
    public function setClient(Client $client)
    {
        $this->client = $client;

        return $this;
    }

    /**
     * @return CurlHTTPClient
     */
    public function getClient()
    {
        return $this->client;
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
     * create curl file
     * 
     * @return \CURLFile
     */
    public function createFile($filePath, $mimeType, $fileName)
    {
        return curl_file_create($filePath, $mimeType, $fileName);
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
        $this->getCurlHandle() && curl_close($this->getCurlHandle());
    }

    public function __call($method, $arguments)
    {
        return $this->getClient()->$method(...$arguments);
    }
}
