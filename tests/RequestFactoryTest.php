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

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\UriInterface;

/**
 * Class RequestFactoryTest
 */
class RequestFactoryTest extends TestCase
{
    public function test_create_request(): void
    {
        $factory = new RequestFactory;
        $request = $factory->createRequest('GET', 'http://domain.tld:9090/subdir?test=true#phpunit');
        $this->assertInstanceOf(RequestInterface::class, $request);
        $this->assertEquals('1.1', $request->getProtocolVersion());
        $uri = $request->getUri();
        $this->assertInstanceOf(UriInterface::class, $uri);
        $this->assertEquals('http://domain.tld:9090/subdir?test=true#phpunit', (string) $uri);
    }

    public function test_create_request_with_uri_interface(): void
    {
        $factory = new RequestFactory;
        $uriFactory = new UriFactory;
        $uri = $uriFactory->createUri('https://example.com/path');
        $request = $factory->createRequest('POST', $uri);
        $this->assertInstanceOf(RequestInterface::class, $request);
        $this->assertEquals('POST', $request->getMethod());
        $this->assertSame($uri, $request->getUri());
    }

    public function test_create_request_with_all_methods(): void
    {
        $factory = new RequestFactory;
        $methods = ['GET', 'POST', 'PUT', 'DELETE', 'PATCH', 'HEAD', 'OPTIONS', 'TRACE', 'CONNECT'];
        foreach ($methods as $method) {
            $request = $factory->createRequest($method, 'http://example.com');
            $this->assertEquals($method, $request->getMethod());
        }
    }
}
