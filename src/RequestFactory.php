<?php

declare(strict_types=1);

/*
 * This file is part of vaibhavpandeyvpz/sandesh package.
 *
 * (c) Vaibhav Pandey <contact@vaibhavpandey.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.md.
 */

namespace Sandesh;

use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\UriInterface;

/**
 * HTTP request factory implementation.
 *
 * Creates Request instances as defined in PSR-17.
 * This factory can create requests from HTTP method strings and URI strings or objects.
 */
class RequestFactory implements RequestFactoryInterface
{
    /**
     * {@inheritdoc}
     *
     * @param  string  $method  HTTP method (e.g., 'GET', 'POST')
     * @param  string|UriInterface  $uri  URI string or UriInterface instance
     * @return RequestInterface A new Request instance
     *
     * @throws \InvalidArgumentException If the method is invalid
     */
    public function createRequest(string $method, $uri): RequestInterface
    {
        if (is_string($uri)) {
            $factory = new UriFactory;
            $uri = $factory->createUri($uri);
        }

        return new Request($method, $uri);
    }
}
