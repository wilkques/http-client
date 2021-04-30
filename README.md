# Http Client
## How to start
````
composer require wilkques/http-client
````
## How to use
```php

// request
$response = Http::get('<url>', [ ... ]);

Http::withHeaders([ ... ]) // add header

Http::asForm() // add header application/x-www-form-urlencoded

Http::asJson() // add header application/json

Http::attach('<file path>') // add file

Http::post('<url>', [ ... ]) // method post

Http::put('<url>', [ ... ]) // method put

Http::patch('<url>', [ ... ]) // method patch

Http::delete('<url>', [ ... ]) // method delete


// response
$response->getHTTPStatus(); // get http status code

$response->getRawBody(); // get body

$response->getJSONDecodedBody(); // get json_decode body

$response->getHeaders(); //get headers

$response->getHeader('<key>'); // get header
```