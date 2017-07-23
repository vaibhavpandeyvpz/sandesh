# vaibhavpandeyvpz/sandesh
[PSR-7](https://github.com/php-fig/http-message) and [PSR-17](https://github.com/php-fig/fig-standards/blob/master/proposed/http-factory/http-factory.md) (Draft) implementation, works with [PHP](http://php.net) >= 5.3.

> Sandesh: `संदेश` (message)

[![Build status](https://img.shields.io/travis/vaibhavpandeyvpz/sandesh.svg?style=flat-square)](https://travis-ci.org/vaibhavpandeyvpz/sandesh)
[![Code Coverage](https://img.shields.io/codecov/c/github/vaibhavpandeyvpz/sandesh.svg?style=flat-square)](https://codecov.io/gh/vaibhavpandeyvpz/sandesh)
[![Latest Version](https://img.shields.io/github/release/vaibhavpandeyvpz/sandesh.svg?style=flat-square)](https://github.com/vaibhavpandeyvpz/sandesh/releases)
[![Downloads](https://img.shields.io/packagist/dt/vaibhavpandeyvpz/sandesh.svg?style=flat-square)](https://packagist.org/packages/vaibhavpandeyvpz/sandesh)
[![PHP Version](http://img.shields.io/badge/php-5.3+-8892be.svg?style=flat-square)](https://packagist.org/packages/vaibhavpandeyvpz/sandesh)
[![License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE)

[![SensioLabsInsight](https://insight.sensiolabs.com/projects/56d0d01c-b1c2-4d5d-80b3-cc9c576f049e/small.png)](https://insight.sensiolabs.com/projects/56d0d01c-b1c2-4d5d-80b3-cc9c576f049e)

Install
---
```bash
composer require vaibhavpandeyvpz/sandesh
```

Usage
---
```php
<?php

/**
 * @desc Creates an instance of Psr\Http\Message\RequestInterface.
 */
$request = (new Sandesh\RequestFactory())
    ->createRequest('POST', 'https://api.example.com/user/save');

/**
 * @desc Creates an instance of Psr\Http\Message\ServerRequestInterface.
 */
$request = (new Sandesh\ServerRequestFactory())
    ->createServerRequestFromArray($_SERVER);

/**
 * @desc Creates an instance of Psr\Http\Message\ResponseInterface.
 */
$response = (new Sandesh\ResponseFactory())
    ->createResponse(404);

/**
 * @desc Creates an instance of Psr\Http\Message\StreamInterface.
 */
$stream = (new Sandesh\StreamFactory())
    ->createStream();

// or
$stream = (new Sandesh\StreamFactory())
    ->createStreamFromFile('/path/to/file');

// or
$stream = (new Sandesh\StreamFactory())
    ->createStreamFromResource(fopen('php://input', 'r'));

/**
 * @desc Creates an instance of Psr\Http\Message\UriInterface.
 */
$uri = (new Sandesh\UriFactory())
    ->createUri('http://domain.tld:9090/subdir?test=true#phpunit');
```

Bonus
---
```php
<?php

/**
 * @desc Parse Set-Cookie header(s) and create an instance of Sandesh\CookieInterface.
 */
$cookie = (new Sandesh\CookieFactory())
    ->createCookie('PHPSESS=1234567890; Domain=domain.tld; Expires=Wed, 21 Oct 2015 07:28:00 GMT; HttpOnly; Max-Age=86400; Path=/admin; Secure');

/**
 * @desc After making changes you can just cast it to a RFC-6265 valid string as show below.
 */
$header = (string)$cookie;
```

Documentation
-------
To view detailed instructions, please visit the [Wiki](https://github.com/vaibhavpandeyvpz/sandesh/wiki).

License
---
See [LICENSE](LICENSE) file.
