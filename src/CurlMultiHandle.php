<?php

namespace Wilkques\Http;

class CurlMultiHandle
{
    /** @var \CurlMultiHandle */
    private $curlMultiHandle;

    /**
     * @return static
     */
    public function init()
    {
        return $this->setCurlMultiHandle(curl_multi_init());
    }

    /**
     * @param \CurlMultiHandle|boolean $curlMultiHandle
     * 
     * @return static
     */
    public function setCurlMultiHandle($curlMultiHandle)
    {
        $this->curlMultiHandle = $curlMultiHandle;

        return $this;
    }

    /**
     * @return \CurlMultiHandle|boolean
     */
    public function getCurlMultiHandle()
    {
        return $this->curlMultiHandle;
    }

    /**
     * @param int $option
     * @param mixed $value
     * 
     * @return bool
     */
    public function setOpt($option, $value)
    {
        return curl_multi_setopt($this->getCurlMultiHandle(), $option, $value);
    }

    /**
     * @param \Wilkques\Http\CurlHandle|\Wilkques\Http\Client $client
     * 
     * @return static
     */
    public function addHandle($client)
    {
        return curl_multi_add_handle($this->getCurlMultiHandle(), $client->getCurlHandle());
    }

    /**
     * @param int $active
     * 
     * @return int
     */
    public function exec(&$active = null)
    {
        return curl_multi_exec($this->getCurlMultiHandle(), $active);
    }

    /**
     * @param float $timeout
     * 
     * @return int
     */
    public function select(float $timeout = 1.0)
    {
        return curl_multi_select($this->getCurlMultiHandle(), $timeout);
    }

    /**
     * @param \Wilkques\Http\CurlHandle|\Wilkques\Http\Client $client
     * 
     * @return static
     */
    public function removeHandle($client)
    {
        return curl_multi_remove_handle($this->getCurlMultiHandle(), $client->getCurlHandle());
    }

    /**
     * @param int|null &$queue
     * 
     * @return array|false
     */
    public function getInfo(?int &$queue = null)
    {
        if (!$queue) {
            return curl_multi_info_read($this->getCurlMultiHandle());
        }

        return curl_multi_info_read($this->getCurlMultiHandle(), $queue);
    }

    /**
     * @param \Wilkques\Http\CurlHandle|\Wilkques\Http\Client $client
     * 
     * @return string
     */
    public function content($client)
    {
        return curl_multi_getcontent($client->getCurlHandle());
    }

    /**
     * @return int
     */
    public function errorno()
    {
        return curl_multi_errno($this->getCurlMultiHandle());
    }

    /**
     * @param int $status
     * 
     * @return string
     */
    public function error($status)
    {
        return curl_multi_strerror($status);
    }

    public function close()
    {
        $this->getCurlMultiHandle() && curl_multi_close($this->getCurlMultiHandle());
    }
}
