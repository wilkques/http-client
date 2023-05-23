<?php

namespace Wilkques\Http;

use Wilkques\Http\Exceptions\CurlExecutionException;
use Wilkques\Http\Exceptions\CurlMultiExecutionException;

class Pool
{
    /** @var \Wilkques\Http\CurlMultiHandle */
    protected $handle;

    /** @var Client */
    protected $client;

    /** @var array */
    protected $pool;

    /** @var array */
    protected $options;

    /** @var float */
    protected $timeout = 100.0;

    /** @var bool */
    protected $sort = true;

    /**
     * construct
     */
    public function __construct()
    {
        $this->boot();
    }

    /**
     * init
     */
    public function boot()
    {
        $this->options = [
            'response'  => [
                'sort'  => true
            ],
            'timeout'   => 100,
            'fulfilled' => function (Response $response, $key) {
                return $response;
            },
            'rejected'  => function (CurlExecutionException $e, $key) {
                return $e;
            },
            'runtimeRejected' => function (CurlMultiExecutionException $e) {
                return $e;
            }
        ];

        return $this;
    }

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
     * @param array $options
     * 
     * @return static
     */
    protected function options(array $options = [])
    {
        $options = array_merge_recursive_distinct($this->options, $options);

        $this->timeout = array_take_off_recursive($options, 'timeout');

        $this->sort = array_take_off_recursive($options, 'response.sort', true);

        $this->options = $options;

        return $this;
    }

    /**
     * @param array $options
     * 
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param callable $callback
     * @param array $options
     * 
     * @return array
     */
    public function pool($callback, array $options = [])
    {
        // init
        $this->options($options)->newMultiHandle()->init();

        $options = $this->getOptions();

        $response = $this->setOptions(
            array_take_off_recursive($options, 'options')
        )->addHandle($callback($this))->mulitExec()->response();

        if ($this->sort) {
            $response = array_replace($this->pool, $response);
        }

        return $response;
    }

    /**
     * @param array $options
     * 
     * @return static
     */
    protected function setOptions(array $options)
    {
        if ($options) {
            foreach ($options as $key => $option) {
                $this->handle->setOpt($key, $option);
            }
        }

        return $this;
    }

    /**
     * @param array $pool
     * 
     * @return static
     */
    protected function addHandle($pool)
    {
        foreach ($pool as $client) {
            $this->getHandle()->addHandle($client);

            $client->close();
        }

        return $this;
    }

    /**
     * @return static
     */
    protected function mulitExec()
    {
        $timeout = $this->timeout;

        $handle = $this->getHandle();

        $runtimeRejected = array_take_off_recursive($this->options, 'runtimeRejected');

        $active = null;

        do {
            if ($active && $handle->select($timeout) === -1) {
                $exceptionStr = ($mrc = $handle->errorno()) ? $handle->error($mrc) : 'system select failed';

                $rejected = $runtimeRejected(new \Wilkques\Http\Exceptions\CurlMultiExecutionException($exceptionStr));

                if ($rejected instanceof \Exception) {
                    throw $rejected;
                }
                // Perform a usleep if a select returns -1.
                // See: https://bugs.php.net/bug.php?id=61141
                usleep($timeout);
            }

            while ($handle->exec($active) === CURLM_CALL_MULTI_PERFORM);
        } while ($active);

        return $this;
    }

    /**
     * @return array
     */
    protected function clientCurlPool()
    {
        $pool = [];

        foreach ($this->pool as $key => $client) {
            $pool[$key] = $client->getCurlHandle();
        }

        return $pool;
    }

    /**
     * @param \Closure|callback $fulfilled
     * @param \Closure|callback $rejected
     * 
     * @return array
     */
    protected function clientHandle($fulfilled, $rejected)
    {
        $handle = $this->getHandle();

        $client = $this->client;
        // curl pool
        $pool = $this->clientCurlPool();

        while ($done = $handle->getInfo()) {
            // pool key
            $key = array_search($done['handle'], $pool);

            $client = $client->setCurlHandle($done['handle']);

            $handle->removeHandle($client);

            if ($errno = $client->errno()) {
                $response[$key] = $rejected(new CurlExecutionException($client->error(), $errno), $key);
            } else {
                $response[$key] = $fulfilled(new Response($handle->content($client), $client->getInfo()), $key);
            }
        }

        return $response;
    }

    /**
     * @return array
     */
    protected function response()
    {
        $options = $this->getOptions();

        $fulfilled = array_take_off_recursive($options, 'fulfilled');

        $rejected = array_take_off_recursive($options, 'rejected');

        return $this->clientHandle($fulfilled, $rejected);
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
     * @return static
     */
    public function as(string $key)
    {
        return $this->pool[$key] = $this->asyncRequest();
    }

    /**
     * Retrieve a new async pending request.
     *
     * @return Client
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

    /**
     * destruct
     */
    public function __destruct()
    {
        $this->getHandle()->close();
    }
}
