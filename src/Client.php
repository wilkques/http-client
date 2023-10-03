<?php

namespace Wilkques\Http;

use Wilkques\Http\Contracts\ClientInterface;
use Wilkques\Http\Exceptions\CurlExecutionException;

class Client implements ClientInterface
{
    /** @var array */
    private $headers = [];

    /** @var CurlHandle */
    private $handle;

    /** @var array */
    private $options = [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HEADER => true,
        CURLOPT_PROTOCOLS => CURLPROTO_HTTPS | CURLPROTO_HTTP,
        CURLOPT_REDIR_PROTOCOLS => CURLPROTO_HTTPS,
    ];

    /** @var array */
    private $files = [];

    /** @var bool */
    protected $async = false;

    /**
     * CurlHTTPClient constructor.
     */
    public function __construct(CurlHandle $handle = null)
    {
        $handle && $this->setHandle($handle);

        $this->asJson()->acceptJson();
    }

    /**
     * @param string $url
     * 
     * @return static
     */
    public function setUrl(string $url)
    {
        return $this->setOptions([
            CURLOPT_URL => $url,
        ]);
    }

    /**
     * @return static
     */
    public function async()
    {
        $this->async = true;

        return $this;
    }

    /**
     * @param CurlHandle $handle
     * 
     * @return static
     */
    public function setHandle(CurlHandle $handle)
    {
        $this->handle = $handle;

        return $this;
    }

    /**
     * @return CurlHandle
     */
    public function getHandle()
    {
        return $this->handle;
    }

    /**
     * @param string $channelToken
     * 
     * @return static
     */
    public function withToken(string $token, string $type = 'Bearer')
    {
        return $this->withHeaders([sprintf("Authorization: %s", trim("{$type} {$token}"))]);
    }

    /**
     * @param string $url
     * @param array $data
     * 
     * @return string
     */
    protected function urlBuilder(string $url, $data)
    {
        return $data ? $url .= '?' . http_build_query($data, '', '&', PHP_QUERY_RFC3986) : $url;
    }

    /**
     * Sends GET request to API.
     *
     * @param string $url Request URL.
     * @param array[] $query Request body
     * 
     * @return Response Response of API request.
     * 
     * @throws CurlExecutionException
     */
    public function get(string $url, array $query = [])
    {
        return $this->methodGet()->sendRequest('GET', $this->urlBuilder($url, $query));
    }

    /**
     * Sends PUT request to API.
     *
     * @param string $url Request URL.
     * @param array[] $data Request body or resource path.
     * @param array|null $query
     * 
     * @return Response Response of API request.
     * 
     * @throws CurlExecutionException
     */
    public function put(string $url, array $data = [], array $query = null)
    {
        return $this->sendRequest('PUT', $this->urlBuilder($url, $query), $data);
    }

    /**
     * Sends PATCH request to API.
     *
     * @param string $url Request URL.
     * @param array[] $data Request body or resource path.
     * @param array|null $query
     * 
     * @return Response Response of API request.
     * 
     * @throws CurlExecutionException
     */
    public function patch(string $url, array $data = [], array $query = null)
    {
        return $this->sendRequest('PATCH', $this->urlBuilder($url, $query), $data);
    }


    /**
     * Sends POST request to API.
     *
     * @param string $url Request URL.
     * @param array[] $data Request body or resource path.
     * @param array|null $query
     * 
     * @return Response Response of API request.
     * 
     * @throws CurlExecutionException
     */
    public function post(string $url, array $data = [], array $query = null)
    {
        return $this->methodPost()->sendRequest('POST', $this->urlBuilder($url, $query), $data);
    }

    /**
     * Sends DELETE request to API.
     *
     * @param string $url Request URL.
     * @param array[] $query
     * 
     * @return Response Response of API request.
     * 
     * @throws CurlExecutionException
     */
    public function delete(string $url, array $query = [])
    {
        return $this->sendRequest('DELETE', $this->urlBuilder($url, $query));
    }

    /**
     * Specify the request's content type.
     *
     * @param  string  $contentType
     * 
     * @return static
     */
    public function contentType(string $contentType)
    {
        return $this->withHeaders(['Content-Type' => $contentType]);
    }

    /**
     * @param array $headers
     * 
     * @return static
     */
    public function withHeaders(array $headers)
    {
        $this->headers = array_replace_recursive($this->getHeaders(), $headers);

        return $this;
    }

    /**
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * @param string $key
     * 
     * @return string
     */
    public function getHeader(string $key)
    {
        return $this->getHeaders()[$key];
    }

    /**
     * @return static
     */
    public function asForm()
    {
        return $this->contentType('application/x-www-form-urlencoded; charset=utf-8');
    }

    /**
     * @return static
     */
    public function asJson()
    {
        return $this->contentType('application/json; charset=utf-8');
    }

    /**
     * @return static
     */
    public function asMultipart()
    {
        return $this->contentType('multipart/form-data; charset=utf-8; boundary=' . uniqid());
    }

    /**
     * @param array $options
     * 
     * @return static
     */
    public function setOptions(array $options)
    {
        $this->options = array_replace_recursive($this->getOptions(), $options);

        return $this;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param string|int $key
     * 
     * @return mixed|null
     */
    public function getOption($key)
    {
        return $this->options[$key] ?? null;
    }

    /**
     * @return static
     */
    public function acceptJson()
    {
        return $this->withHeaders([
            'Accept' => 'application/json; charset=utf-8'
        ]);
    }

    /**
     * @param string $method
     * 
     * @return static
     */
    private function httpMethod(string $method)
    {
        return $this->setOptions([
            CURLOPT_CUSTOMREQUEST => $method,
        ]);
    }

    /**
     * @return static
     */
    private function methodGet()
    {
        return $this->setOptions([
            CURLOPT_HTTPGET => true
        ]);
    }

    /**
     * @return static
     */
    private function methodPost()
    {
        return $this->setOptions([
            CURLOPT_POST => true
        ]);
    }

    /**
     * @return static
     */
    private function methodPut()
    {
        return $this->setOptions([
            CURLOPT_PUT => true
        ]);
    }

    /**
     * @return static
     */
    private function buildHeaders()
    {
        $headers = $this->getHeaders();

        return $this->setOptions([
            CURLOPT_HTTPHEADER => array_map(function ($item, $index) {
                if (is_string($index)) {
                    return "{$index}: {$item}";
                }

                return $item;
            }, $headers, array_keys($headers))
        ]);
    }

    /**
     * @return static
     */
    private function noContentLength()
    {
        return $this->withHeaders(['Content-Length' => '0']);
    }

    /**
     * @param string|array|null $fields
     * 
     * @return static
     */
    private function postFields($fields)
    {
        return $this->setOptions([CURLOPT_POSTFIELDS => $this->fieldsEncode($fields)]);
    }

    /**
     * @param string|array|null $fields
     * 
     * @return static
     */
    protected function fieldsEncode($fields)
    {
        switch ($this->getHeader('Content-Type')) {
            case 'application/x-www-form-urlencoded; charset=utf-8':
                return http_build_query($fields);
                break;
                // case 'application/json; charset=utf-8':
                //     $fields = json_encode($fields);
                //     break;
            default:
                return $fields;
                break;
        }
    }

    /**
     * @param array|string $name
     * @param string|null $filePath
     * @param string|null $mimeType
     * @param string|null $reName
     * 
     * @return static
     */
    public function attach($name, string $filePath = '', string $mimeType = null, string $reName = null)
    {
        if (is_array($name)) {
            foreach ($name as $file) {
                $this->attach(...$file);
            }

            return $this;
        }

        $mimeType = $mimeType ?? mime_content_type($filePath);

        $fileName = $reName ?? pathinfo($filePath)['basename'];

        return $this->setFiles($name, $this->createFile($filePath, $mimeType, $fileName));
    }

    /**
     * Only send one File
     * 
     * @param string $filePath
     * 
     * @return static
     */
    public function attachUploadFile(string $filePath)
    {
        return $this->methodPut()->setOptions([
            CURLOPT_UPLOAD => true,
            CURLOPT_INFILE => fopen($filePath, 'rb'),
            CURLOPT_INFILESIZE => filesize($filePath)
        ]);
    }

    /**
     * @return static
     */
    private function fileClose()
    {
        if ($cURLFile = $this->getOption(CURLOPT_INFILE)) {
            fclose($cURLFile);
        }

        if ($files = $this->getFiles()) {
            foreach ($files as $file) {
                fclose($file->getStream());
            }
        }

        return $this;
    }

    /**
     * @param string $key
     * @param \CURLFile $cURLFile
     * 
     * @return static
     */
    public function setFiles(string $key, \CURLFile $cURLFile)
    {
        $this->files[$key] = $cURLFile;

        return $this;
    }

    /**
     * @return array
     */
    public function getFiles()
    {
        return $this->files;
    }

    /**
     * @param string|array|null $requestBody
     * 
     * @return static cUrl options
     */
    private function buildRequestBody($requestBody = null)
    {
        if (is_null($requestBody)) {
            return $this->noContentLength();
        }

        return $this->postFields(array_replace_recursive($requestBody, $this->getFiles()));
    }

    /**
     * @param string $method
     * @param string $url
     * @param string|array|null $requestBody
     * 
     * @throws CurlExecutionException
     * 
     * @return Response
     */
    private function sendRequest(string $method, string $url, $requestBody = null)
    {
        $this->setUrl($url)
            ->httpMethod($method)
            ->buildHeaders()
            ->buildRequestBody($requestBody)
            ->init()
            ->setoptArray(
                $this->getOptions()
            );

        if ($this->async) {
            return $this;
        }

        return new Response($this->execCurl(), $this->getInfo());
    }

    /**
     * @return string
     */
    protected function execCurl()
    {
        $result = $this->exec();

        if ($this->errno()) throw new CurlExecutionException($this->error(), $this->errno());

        return $result;
    }

    /**
     * @return CurlHandle
     */
    public function newCurl()
    {
        return $this->getHandle() ?: $this->setHandle(new CurlHandle)->setClient($this);
    }

    /**
     * @param string $method
     * @param array $arguments
     * 
     * @return mixed
     */
    public function __call(string $method, array $arguments)
    {
        return $this->newCurl()->$method(...$arguments);
    }

    /**
     * @param string $method
     * @param array $arguments
     * 
     * @return mixed
     */
    public static function __callStatic($method, $arguments)
    {
        return (new static)->$method(...$arguments);
    }

    /**
     * Client destruct.
     */
    public function __destruct()
    {
        $this->fileClose()->close();
    }
}
