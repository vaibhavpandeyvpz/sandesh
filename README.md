# vaibhavpandeyvpz/sandesh
[PSR-7](https://github.com/php-fig/http-message) and [PSR-17](https://github.com/php-fig/fig-standards/blob/master/proposed/http-factory/http-factory.md) (Draft) implementation, works with [PHP](http://php.net) >= 5.3.

> Sandesh: `संदेश` (message)

[![Build status][build-status-image]][build-status-url]
[![Code Coverage][code-coverage-image]][code-coverage-url]
[![Latest Version][latest-version-image]][latest-version-url]
[![Downloads][downloads-image]][downloads-url]
[![PHP Version][php-version-image]][php-version-url]
[![License][license-image]][license-url]

[![SensioLabsInsight][insights-image]][insights-url]

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
    ->createServerRequest($_SERVER);

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

License
---
See [LICENSE.md][license-url] file.

[build-status-image]: https://img.shields.io/travis/vaibhavpandeyvpz/sandesh.svg?style=flat-square
[build-status-url]: https://travis-ci.org/vaibhavpandeyvpz/sandesh
[code-coverage-image]: https://img.shields.io/codecov/c/github/vaibhavpandeyvpz/sandesh.svg?style=flat-square
[code-coverage-url]: https://codecov.io/gh/vaibhavpandeyvpz/sandesh
[latest-version-image]: https://img.shields.io/github/release/vaibhavpandeyvpz/sandesh.svg?style=flat-square
[latest-version-url]: https://github.com/vaibhavpandeyvpz/sandesh/releases
[downloads-image]: https://img.shields.io/packagist/dt/vaibhavpandeyvpz/sandesh.svg?style=flat-square
[downloads-url]: https://packagist.org/packages/vaibhavpandeyvpz/sandesh
[php-version-image]: http://img.shields.io/badge/php-5.3+-8892be.svg?style=flat-square
[php-version-url]: https://packagist.org/packages/vaibhavpandeyvpz/sandesh
[license-image]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[license-url]: LICENSE.md
[insights-image]: https://insight.sensiolabs.com/projects/56d0d01c-b1c2-4d5d-80b3-cc9c576f049e/small.png
[insights-url]: https://insight.sensiolabs.com/projects/56d0d01c-b1c2-4d5d-80b3-cc9c576f049e
