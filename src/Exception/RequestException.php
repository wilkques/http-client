<?php

namespace Wilkques\HttpClient\Exception;

use Wilkques\HttpClient\Exception\CurlExecutionException;
use Wilkques\HttpClient\Response;

class RequestException extends CurlExecutionException
{
    public function __construct(Response $response)
    {
        parent::__construct($response->body(), $response->status());
    }
}
