# Sandesh

[![Latest Version](https://img.shields.io/packagist/v/vaibhavpandeyvpz/sandesh.svg?style=flat-square)](https://packagist.org/packages/vaibhavpandeyvpz/sandesh)
[![Downloads](https://img.shields.io/packagist/dt/vaibhavpandeyvpz/sandesh.svg?style=flat-square)](https://packagist.org/packages/vaibhavpandeyvpz/sandesh)
[![PHP Version](https://img.shields.io/packagist/php-v/vaibhavpandeyvpz/sandesh.svg?style=flat-square)](https://packagist.org/packages/vaibhavpandeyvpz/sandesh)
[![License](https://img.shields.io/packagist/l/vaibhavpandeyvpz/sandesh.svg?style=flat-square)](LICENSE)
[![Build Status](https://img.shields.io/github/actions/workflow/status/vaibhavpandeyvpz/sandesh/tests.yml?branch=master&style=flat-square)](https://github.com/vaibhavpandeyvpz/sandesh/actions)

A modern, PHP 8.2+ implementation of [PSR-7](https://www.php-fig.org/psr/psr-7/) (HTTP Message Interfaces) and [PSR-17](https://www.php-fig.org/psr/psr-17/) (HTTP Factories). Sandesh (संदेश) means "message" in Hindi.

## Features

- ✅ Full PSR-7 and PSR-17 compliance
- ✅ PHP 8.2+ with strict types and modern features
- ✅ Immutable message objects
- ✅ Type-safe HTTP method enum
- ✅ Cookie parsing and management (RFC 6265)
- ✅ Server response sender
- ✅ Automatic body parsing (JSON, form data, XML)
- ✅ Comprehensive test coverage

## Installation

```bash
composer require vaibhavpandeyvpz/sandesh
```

## Requirements

- PHP >= 8.2
- PSR-7 HTTP Message Interfaces (^2.0)
- PSR-17 HTTP Factory Interfaces (^1.1)

## Quick Start

### Creating HTTP Messages

#### Request

```php
<?php

use Sandesh\RequestFactory;
use Sandesh\Request;

// Using factory
$request = (new RequestFactory())
    ->createRequest('POST', 'https://api.example.com/users')
    ->withHeader('Content-Type', 'application/json')
    ->withBody($stream);

// Direct instantiation
$request = new Request('GET', 'https://example.com');
```

#### Server Request

```php
<?php

use Sandesh\ServerRequestFactory;

// Create from server superglobal
$request = (new ServerRequestFactory())
    ->createServerRequest('POST', 'https://example.com/api', $_SERVER)
    ->withParsedBody(['name' => 'John'])
    ->withQueryParams(['page' => 1])
    ->withCookieParams($_COOKIE)
    ->withUploadedFiles($uploadedFiles);
```

#### Response

```php
<?php

use Sandesh\ResponseFactory;
use Sandesh\Response;

// Using factory
$response = (new ResponseFactory())
    ->createResponse(200)
    ->withHeader('Content-Type', 'application/json')
    ->withBody($stream);

// Direct instantiation with body
$response = new Response(404, 'Not Found', 'Page not found');
```

### Working with URIs

```php
<?php

use Sandesh\UriFactory;

$uri = (new UriFactory())
    ->createUri('https://user:pass@example.com:8080/path?query=value#fragment');

echo $uri->getScheme();    // 'https'
echo $uri->getHost();      // 'example.com'
echo $uri->getPort();      // 8080
echo $uri->getPath();      // '/path'
echo $uri->getQuery();     // 'query=value'
echo $uri->getFragment();  // 'fragment'
```

### Working with Streams

```php
<?php

use Sandesh\StreamFactory;

// Create empty stream
$stream = (new StreamFactory())->createStream();
$stream->write('Hello, World!');

// Create from file
$stream = (new StreamFactory())
    ->createStreamFromFile('/path/to/file.txt', 'r');

// Create from resource
$resource = fopen('php://input', 'r');
$stream = (new StreamFactory())
    ->createStreamFromResource($resource);

// Read stream
$content = $stream->getContents();
$stream->rewind(); // Reset pointer
```

### Working with Uploaded Files

```php
<?php

use Sandesh\UploadedFileFactory;
use Sandesh\StreamFactory;

$streamFactory = new StreamFactory();
$fileFactory = new UploadedFileFactory();

$stream = $streamFactory->createStreamFromFile($_FILES['avatar']['tmp_name']);

$uploadedFile = $fileFactory->createUploadedFile(
    $stream,
    $_FILES['avatar']['size'],
    $_FILES['avatar']['error'],
    $_FILES['avatar']['name'],
    $_FILES['avatar']['type']
);

// Move uploaded file
$uploadedFile->moveTo('/path/to/destination.jpg');
```

### Working with Cookies

```php
<?php

use Sandesh\CookieFactory;

// Parse Set-Cookie header
$cookie = (new CookieFactory())
    ->createCookie('session=abc123; Domain=.example.com; Path=/; HttpOnly; Secure; Max-Age=3600');

// Access cookie properties
echo $cookie->getName();     // 'session'
echo $cookie->getValue();    // 'abc123'
echo $cookie->getDomain();   // '.example.com'
echo $cookie->getPath();     // '/'
echo $cookie->isHttpOnly();  // true
echo $cookie->isSecure();    // true

// Convert back to Set-Cookie header string
$header = (string) $cookie;
```

### Sending Responses

```php
<?php

use Sandesh\ServerResponseSender;
use Sandesh\ResponseFactory;

$response = (new ResponseFactory())
    ->createResponse(200)
    ->withHeader('Content-Type', 'application/json')
    ->withBody($stream);

$sender = new ServerResponseSender();
$sender->send($response);
```

### Working with HTTP Methods

```php
<?php

use Sandesh\HttpMethod;

// Type-safe HTTP method enum
$method = HttpMethod::POST;
echo $method->toString(); // 'POST'

// Create from string
$method = HttpMethod::fromString('get'); // HttpMethod::GET

// Use in request
$request = new Request($method->toString(), $uri);
```

## Advanced Usage

### Immutable Message Pattern

All message objects are immutable. Methods like `withHeader()`, `withBody()`, etc., return new instances:

```php
<?php

$request = new Request('GET', 'https://example.com');
$request2 = $request->withHeader('X-Custom', 'value');

// $request is unchanged
assert($request->hasHeader('X-Custom') === false);
assert($request2->hasHeader('X-Custom') === true);
```

### Automatic Body Parsing

Server requests automatically parse common content types:

```php
<?php

// JSON body
$request = $serverRequest->withHeader('Content-Type', 'application/json')
    ->withBody($jsonStream);
$data = $serverRequest->getParsedBody(); // Automatically decoded JSON

// Form data
$request = $serverRequest->withHeader('Content-Type', 'application/x-www-form-urlencoded')
    ->withBody($formStream);
$data = $serverRequest->getParsedBody(); // Parsed as array

// XML body
$request = $serverRequest->withHeader('Content-Type', 'text/xml')
    ->withBody($xmlStream);
$data = $serverRequest->getParsedBody(); // SimpleXMLElement instance
```

### Request Attributes

Server requests support custom attributes for middleware and routing:

```php
<?php

$request = $request->withAttribute('route', 'users.show')
    ->withAttribute('user', $user);

$route = $request->getAttribute('route');
$user = $request->getAttribute('user');
```

## API Reference

### Core Classes

- **`Request`** - HTTP request message
- **`Response`** - HTTP response message
- **`ServerRequest`** - Server-side HTTP request with parsed data
- **`Uri`** - URI implementation
- **`Stream`** - Stream implementation
- **`UploadedFile`** - Uploaded file implementation
- **`Cookie`** - Cookie implementation (RFC 6265)
- **`HttpMethod`** - Type-safe HTTP method enum

### Factory Classes

- **`RequestFactory`** - Creates request instances
- **`ResponseFactory`** - Creates response instances
- **`ServerRequestFactory`** - Creates server request instances
- **`UriFactory`** - Creates URI instances
- **`StreamFactory`** - Creates stream instances
- **`UploadedFileFactory`** - Creates uploaded file instances
- **`CookieFactory`** - Creates cookie instances from headers

### Utilities

- **`ServerResponseSender`** - Sends HTTP responses to the client

## Testing

```bash
# Run tests
vendor/bin/phpunit

# Run tests with coverage
vendor/bin/phpunit --coverage-text
```

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## Author

**Vaibhav Pandey**

- Email: contact@vaibhavpandey.com
- GitHub: [@vaibhavpandeyvpz](https://github.com/vaibhavpandeyvpz)

## Links

- [GitHub Repository](https://github.com/vaibhavpandeyvpz/sandesh)
- [Packagist](https://packagist.org/packages/vaibhavpandeyvpz/sandesh)
- [PSR-7 Specification](https://www.php-fig.org/psr/psr-7/)
- [PSR-17 Specification](https://www.php-fig.org/psr/psr-17/)
