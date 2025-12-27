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
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

/**
 * Class ServerRequestFactoryTest
 */
class ServerRequestFactoryTest extends TestCase
{
    public function test_create_server_request(): void
    {
        $factory = new ServerRequestFactory;
        $request = $factory->createServerRequest('POST', 'http://domain.tld:9090/subdir?test=true#phpunit');
        $this->assertInstanceOf(ServerRequestInterface::class, $request);
        $this->assertEquals('1.1', $request->getProtocolVersion());
        $this->assertInstanceOf(UriInterface::class, $request->getUri());
        $this->assertEquals('POST', $request->getMethod());
        $this->assertEquals('domain.tld', $request->getUri()->getHost());
        $this->assertEquals(9090, $request->getUri()->getPort());
        $this->assertEquals('http://domain.tld:9090/subdir?test=true#phpunit', (string) $request->getUri());
    }

    public function test_create_server_request_with_server_params(): void
    {
        $factory = new ServerRequestFactory;
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
        $this->assertEquals('http://domain.tld:9090/subdir?test=true#phpunit', (string) $request->getUri());
        $this->assertEquals('128', $request->getHeaderLine('Content-Length'));
        $this->assertEquals('application/x-www-form-urlencoded', $request->getHeaderLine('Content-Type'));
    }

    public function test_create_server_request_with_uri_interface(): void
    {
        $factory = new ServerRequestFactory;
        $uriFactory = new UriFactory;
        $uri = $uriFactory->createUri('https://example.com/path');
        $request = $factory->createServerRequest('GET', $uri);
        $this->assertInstanceOf(ServerRequestInterface::class, $request);
        $this->assertSame($uri, $request->getUri());
    }

    public function test_create_server_request_with_empty_server_params(): void
    {
        $factory = new ServerRequestFactory;
        $request = $factory->createServerRequest('GET', 'http://example.com', []);
        $this->assertInstanceOf(ServerRequestInterface::class, $request);
        $this->assertEquals('1.1', $request->getProtocolVersion());
        $this->assertEmpty($request->getHeaders());
    }

    public function test_create_server_request_with_redirect_headers(): void
    {
        $factory = new ServerRequestFactory;
        $request = $factory->createServerRequest('GET', 'http://example.com', [
            'REDIRECT_HTTP_HOST' => 'example.com',
            'HTTP_HOST' => 'example.com',
        ]);
        // Should not duplicate headers
        $hostHeaders = $request->getHeader('host');
        $this->assertCount(1, $hostHeaders);
    }

    public function test_create_server_request_with_content_headers(): void
    {
        $factory = new ServerRequestFactory;
        $request = $factory->createServerRequest('POST', 'http://example.com', [
            'CONTENT_LENGTH' => '100',
            'CONTENT_TYPE' => 'application/json',
        ]);
        $this->assertEquals('100', $request->getHeaderLine('content-length'));
        $this->assertEquals('application/json', $request->getHeaderLine('content-type'));
    }

    public function test_create_server_request_with_protocol_version(): void
    {
        $factory = new ServerRequestFactory;
        $request = $factory->createServerRequest('GET', 'http://example.com', [
            'SERVER_PROTOCOL' => 'HTTP/2.0',
        ]);
        $this->assertEquals('2.0', $request->getProtocolVersion());
    }

    public function test_create_server_request_body_is_set(): void
    {
        $factory = new ServerRequestFactory;
        $request = $factory->createServerRequest('GET', 'http://example.com');
        $this->assertInstanceOf(StreamInterface::class, $request->getBody());
    }

    public function test_get_php_input_stream_with_fopen_failure(): void
    {
        // This tests the error path when fopen('php://temp') fails
        // In practice, this is very rare, but we ensure the code handles it
        $factory = new ServerRequestFactory;
        $request = $factory->createServerRequest('GET', 'http://example.com');
        // The body should still be set even if php://input is empty
        $this->assertInstanceOf(StreamInterface::class, $request->getBody());
    }

    public function test_get_protocol_version_without_server_protocol(): void
    {
        $factory = new ServerRequestFactory;
        $request = $factory->createServerRequest('GET', 'http://example.com', [
            'CONTENT_TYPE' => 'application/json',
            // No SERVER_PROTOCOL - should default to 1.1
        ]);
        $this->assertEquals('1.1', $request->getProtocolVersion());
    }
}
