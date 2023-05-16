<?php

namespace Wilkques\Http;

class Pool
{
    /** @var \Wilkques\Http\CurlMultiHandle */
    protected $handle;

    /** @var Client */
    protected $client;

    /** @var array */
    protected $pool;

    /**
     * @return \Wilkques\Http\CurlMultiHandle
     */
    public function getHandle()
    {
        return $this->handle;
    }

    /**
     * @return CurlMultiHandle
     */
    public function newMultiHandle()
    {
        return $this->handle = $this->getHandle() ?? new CurlMultiHandle;
    }

    /**
     * @param callable $callback
     * 
     * @return array
     */
    public function pool($callback)
    {
        // init
        $this->newMultiHandle()->init();
        
        $callback($this);

        $response = $this->addHandle()->mulitExec()->response();

        return $response;
    }

    /**
     * @return static
     */
    protected function addHandle()
    {
        foreach ($this->getRequests() as $client) {
            $this->getHandle()->addHandle($client);
        }

        return $this;
    }

    /**
     * @return static
     */
    protected function mulitExec()
    {
        $handle = $this->getHandle();

        do {
            $status = $handle->exec($active);

            if ($active) {
                $handle->select();
            }
        } while ($active && $status == CURLM_OK);

        return $this;
    }

    /**
     * @return array
     */
    protected function response()
    {
        $handle = $this->getHandle();

        foreach ($this->getRequests() as $index => $client) {
            $response[$index] = new Response($handle->content($client), $client->getInfo());

            $handle->removeHandle($client);
        }

        return $response;
    }

    /**
     * @return Client
     */
    public function newClient()
    {
        return $this->client = new Client;
    }

    /**
     * Add a request to the pool with a key.
     *
     * @param  string  $key
     * @return \Illuminate\Http\Client\PendingRequest
     */
    public function as(string $key)
    {
        return $this->pool[$key] = $this->asyncRequest();
    }

    /**
     * Retrieve a new async pending request.
     *
     * @return \Illuminate\Http\Client\PendingRequest
     */
    protected function asyncRequest()
    {
        return $this->newClient()->async();
    }

    /**
     * Retrieve the requests in the pool.
     *
     * @return array
     */
    public function getRequests()
    {
        return $this->pool;
    }

    /**
     * @param string $method
     * @param array $arguments
     * 
     * @return mixed
     */
    public function __call(string $method, array $arguments)
    {
        return $this->pool[] = $this->asyncRequest()->$method(...$arguments);
    }

    public function __destruct()
    {
        $this->getHandle()->close();
    }
}