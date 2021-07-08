# Http Client
## How to start
````
composer require wilkques/http-client
````
## How to use
```php

// request
$response = Http::get('<url>', [ ... ]);

$response = Http::withHeaders([ ... ]) // add header

$response = Http::asForm() // add header application/x-www-form-urlencoded

$response = Http::asJson() // add header application/json

$response = Http::attach('<file path>') // add file

$response = Http::post('<url>', [ ... ]) // method post

$response = Http::put('<url>', [ ... ]) // method put

$response = Http::patch('<url>', [ ... ]) // method patch

$response = Http::delete('<url>', [ ... ]) // method delete


// response
$response->status(); // get http status code

$response->body(); // get body

$response->json(); // get json_decode body

$response->headers(); //get headers

$response->header('<key>'); // get header

$response->ok(); // bool

$response->redirect(); // bool

$response->successful(); // bool

$response->failed(); // bool

$response->clientError(); // bool

$response->serverError(); // bool

$response->throw(); // throw exception

$response->throw(function ($response, $exception) {
    // code
});
```