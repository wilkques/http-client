<?php

namespace Wilkques\HttpClient\HTTPClient;

use Wilkques\HttpClient\Exceptions\CurlExecutionException;
use Wilkques\HttpClient\Response;

class CurlHTTPClient implements HTTPClient
{
    /** @var array */
    private $headers = [];
    /** @var Curl */
    private $curl;
    /** @var array */
    private $options = [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HEADER => true,
    ];
    /** @var array */
    private $files = [];

    /**
     * CurlHTTPClient constructor.
     */
    public function __construct()
    {
        $this->asJson()->acceptJson();
    }

    /**
     * @param Curl $curl
     * 
     * @return static
     */
    public function setCurl(Curl $curl)
    {
        $this->curl = $curl;

        return $this;
    }

    /**
     * @return Curl
     */
    public function getCurl()
    {
        return $this->curl;
    }

    /**
     * @param string $channelToken
     * 
     * @return static
     */
    public function withToken(string $token, string $type = 'Bearer')
    {
        $this->withHeaders([sprintf("Authorization: %s", trim("$type $token"))]);

        return $this;
    }

    /**
     * @param string $url
     * @param array $data
     * 
     * @return string
     */
    protected function urlBuilder(string $url, $data)
    {
        return $data ? $url .= '?' . http_build_query($data) : $url;
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
        $this->headers = array_replace_recursive($this->headers, $headers);

        return $this;
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
    private function customRequest(string $method)
    {
        $this->setOptions([
            CURLOPT_CUSTOMREQUEST => $method,
        ]);

        return $this;
    }

    /**
     * @return static
     */
    private function methodGet()
    {
        $this->setOptions([
            CURLOPT_HTTPGET => true
        ]);

        return $this;
    }

    /**
     * @return static
     */
    private function methodPost()
    {
        $this->setOptions([
            CURLOPT_POST => true
        ]);

        return $this;
    }

    /**
     * @return static
     */
    private function methodPut()
    {
        $this->setOptions([
            CURLOPT_PUT => true
        ]);

        return $this;
    }

    /**
     * @return static
     */
    private function setHeaders()
    {
        $this->setOptions([
            CURLOPT_HTTPHEADER => $this->headers
        ]);

        return $this;
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
        $this->setOptions([CURLOPT_POSTFIELDS => $fields]);

        return $this;
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
        $this->methodPut()->setOptions([
            CURLOPT_INFILE => fopen($filePath, 'r'),
            CURLOPT_INFILESIZE => filesize($filePath)
        ]);

        return $this;
    }

    /**
     * @return static
     */
    private function fileClose()
    {
        if (isset($this->getOptions()[CURLOPT_INFILE])) {
            fclose($this->getOptions()[CURLOPT_INFILE]);
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
     * @param string $method
     * @param string|array|null $reqBody
     * 
     * @return static cUrl options
     */
    private function options(string $method, $reqBody = null)
    {
        $this->customRequest($method)->setHeaders();

        if (is_null($reqBody)) {
            return $this->noContentLength();
        }

        return $this->postFields(array_replace_recursive($reqBody, $this->getFiles()));
    }

    /**
     * @param string $method
     * @param string $url
     * @param string|array|null $reqBody
     * 
     * @throws CurlExecutionException
     * 
     * @return Response
     */
    private function sendRequest(string $method, string $url, $reqBody = null)
    {
        return new Response(
            $this->execCurl($url, $this->options(
                $method,
                $reqBody
            )->getOptions()),
            $this
        );
    }

    /**
     * @param string $url
     * @param array $options
     * 
     * @return string
     */
    protected function execCurl(string $url, array $options)
    {
        $this->setUrl($url)->init()->setoptArray($options);

        $result = $this->exec();

        if ($this->errno()) throw new CurlExecutionException($this->error(), $this->errno());

        return $result;
    }

    /**
     * @param array $additionalHeader
     * 
     * @return array
     */
    protected function additionalHeader(array $additionalHeader)
    {
        return array_merge($this->getToken(), $additionalHeader);
    }

    /**
     * @return Curl
     */
    public function newCurl()
    {
        return $this->getCurl() ?: $this->setCurl(new Curl)->setCurlHttpClient($this);
    }

    /**
     * @param string $method
     * @param array $arguments
     * 
     * @return Curl
     */
    public function __call(string $method, array $arguments)
    {
        return $this->newCurl()->$method(...$arguments);
    }

    /**
     * @param string $method
     * @param array $arguments
     * 
     * @return Curl
     */
    public static function __callStatic($method, $arguments)
    {
        return (new static)->$method(...$arguments);
    }

    public function __destruct()
    {
        $this->fileClose();
        $this->close();
    }
}
