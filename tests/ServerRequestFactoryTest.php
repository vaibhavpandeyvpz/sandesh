<?php

/*
 * This file is part of vaibhavpandeyvpz/sandesh package.
 *
 * (c) Vaibhav Pandey <contact@vaibhavpandey.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.md.
 */

namespace Sandesh;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;

/**
 * Class ServerRequestFactoryTest
 * @package Sandesh
 */
class ServerRequestFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateServerRequest()
    {
        $factory = new ServerRequestFactory();
        $request = $factory->createServerRequest('POST', 'http://domain.tld:9090/subdir?test=true#phpunit');
        $this->assertInstanceOf(ServerRequestInterface::class, $request);
        $this->assertEquals('1.1', $request->getProtocolVersion());
        $this->assertInstanceOf(UriInterface::class, $request->getUri());
        $this->assertEquals('POST', $request->getMethod());
        $this->assertEquals('domain.tld', $request->getUri()->getHost());
        $this->assertEquals(9090, $request->getUri()->getPort());
        $this->assertEquals('http://domain.tld:9090/subdir?test=true#phpunit', (string)$request->getUri());
    }

    public function testCreateServerRequestWithServerParams()
    {
        $factory = new ServerRequestFactory();
        $request = $factory->createServerRequest('POST', 'http://domain.tld:9090/subdir?test=true#phpunit', [
            'CONTENT_LENGTH' => '128',
            'CONTENT_TYPE' => 'application/x-www-form-urlencoded',
            'HTTP_HOST' => 'domain.tld:9090',
            'HTTP_INVALID' => null,
            'HTTP_X_REWRITE_URL' => '/some-fancy-url',
            'HTTP_X_ORIGINAL_URL' => '/subdir?test=true#phpunit',
            'QUERY_STRING' => 'test=true',
            'REQUEST_METHOD' => 'POST',
            'REQUEST_URI' => 'http://domain.tld:9090/subdir#phpunit',
            'SERVER_PORT' => '9090',
            'SERVER_PROTOCOL' => 'HTTP/1.0',
        ]);
        $this->assertInstanceOf(ServerRequestInterface::class, $request);
        $this->assertEquals('1.0', $request->getProtocolVersion());
        $this->assertInstanceOf(UriInterface::class, $request->getUri());
        $this->assertEquals('POST', $request->getMethod());
        $this->assertEquals('domain.tld', $request->getUri()->getHost());
        $this->assertEquals(9090, $request->getUri()->getPort());
        $this->assertEquals('http://domain.tld:9090/subdir?test=true#phpunit', (string)$request->getUri());
        $this->assertEquals('128', $request->getHeaderLine('Content-Length'));
        $this->assertEquals('application/x-www-form-urlencoded', $request->getHeaderLine('Content-Type'));
    }
}
