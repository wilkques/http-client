<?php

namespace Wilkques\Http\Exceptions;

use Wilkques\Http\Exceptions\CurlExecutionException;
use Wilkques\Http\Response;

class RequestException extends CurlExecutionException
{
    /**
     * @param Response $response
     */
    public function __construct(Response $response)
    {
        parent::__construct($this->prepareMessage($response), $response->status());
    }

    /**
     * Prepare the exception message.
     *
     * @param  Response $response
     * @return string
     */
    protected function prepareMessage(Response $response)
    {
        $message = "HTTP request returned status code {$response->status()}";

        $summary = $response->body();

        return is_null($summary) ? $message : $message .= ":\n{$summary}\n";
    }
}
