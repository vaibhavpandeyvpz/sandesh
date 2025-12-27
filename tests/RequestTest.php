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
use Psr\Http\Message\UriInterface;

/**
 * Class RequestTest
 */
class RequestTest extends TestCase
{
    public function test_method(): void
    {
        $request = new Request;
        $this->assertNotEmpty($request->getMethod());
        $this->assertEquals('GET', $request->getMethod());
        $this->assertEquals('PUT', $request->withMethod('PUT')->getMethod());
    }

    public function test_method_invalid(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new Request('something');
    }

    public function test_request_target(): void
    {
        $request = new Request;
        $this->assertNotEmpty($request->getRequestTarget());
        $this->assertEquals('/', $request->getRequestTarget());
        $this->assertEquals(
            '/user/profile',
            $request->withRequestTarget('/user/profile')
                ->getRequestTarget()
        );
        $uri = new UriFactory;
        $this->assertEquals(
            '/subdir',
            $request->withUri($uri->createUri('http://domain.tld/subdir'))
                ->getRequestTarget()
        );
        $this->assertEquals(
            '/subdir?test=true',
            $request->withUri($uri->createUri('http://domain.tld/subdir?test=true'))
                ->getRequestTarget()
        );
    }

    public function test_uri(): void
    {
        $request = new Request;
        $uri = new Uri;
        $this->assertSame($uri, $request->withUri($uri)->getUri());
    }

    public function test_uri_preserve_host(): void
    {
        $request = new Request;
        $factory = new UriFactory;
        $request = $request->withUri($factory->createUri('http://domain.tld:9090'), true);
        $this->assertEquals('domain.tld:9090', $request->getHeaderLine('Host'));
        $request = $request->withUri($factory->createUri('http://otherdomain.tld'), true);
        $this->assertEquals('domain.tld:9090', $request->getHeaderLine('Host'));
    }

    public function test_uri_without_preserve_host(): void
    {
        $request = new Request;
        $factory = new UriFactory;
        $request = $request->withUri($factory->createUri('http://domain.tld:9090'), false);
        $this->assertFalse($request->hasHeader('Host'));
    }

    public function test_uri_with_empty_host(): void
    {
        $request = new Request;
        $uri = new Uri;
        $request = $request->withUri($uri->withHost(''), true);
        $this->assertFalse($request->hasHeader('Host'));
    }

    public function test_get_uri_when_null(): void
    {
        $request = new Request;
        $uri = $request->getUri();
        $this->assertInstanceOf(UriInterface::class, $uri);
        $this->assertEmpty((string) $uri);
    }

    public function test_request_target_with_empty_path(): void
    {
        $request = new Request;
        $uri = new Uri;
        $uri = $uri->withPath('');
        $request = $request->withUri($uri);
        $this->assertEquals('/', $request->getRequestTarget());
    }

    public function test_request_target_with_query_only(): void
    {
        $request = new Request;
        $uri = new Uri;
        $uri = $uri->withQuery('test=true');
        $request = $request->withUri($uri);
        $this->assertEquals('?test=true', $request->getRequestTarget());
    }

    public function test_all_http_methods(): void
    {
        $methods = ['GET', 'POST', 'PUT', 'DELETE', 'PATCH', 'HEAD', 'OPTIONS', 'TRACE', 'CONNECT'];
        foreach ($methods as $method) {
            $request = new Request($method);
            $this->assertEquals($method, $request->getMethod());
        }
    }

    public function test_immutability(): void
    {
        $request = new Request;
        $this->assertNotSame($request, $request->withMethod('POST'));
        $this->assertNotSame($request, $request->withRequestTarget('/test'));
        $this->assertNotSame($request, $request->withUri(new Uri));
    }
}
