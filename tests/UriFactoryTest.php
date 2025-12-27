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
 * Class UriFactoryTest
 */
class UriFactoryTest extends TestCase
{
    public function test_uri(): void
    {
        $factory = new UriFactory;
        $uri = $factory->createUri();
        $this->assertInstanceOf(UriInterface::class, $uri);
        $this->assertEmpty((string) $uri);
        $url = 'http://someone:secret@domain.tld:9090/subdir?test=true#phpunit';
        $uri = $factory->createUri($url);
        $this->assertInstanceOf(UriInterface::class, $uri);
        $this->assertEquals('http', $uri->getScheme());
        $this->assertEquals('someone:secret', $uri->getUserInfo());
        $this->assertEquals('domain.tld', $uri->getHost());
        $this->assertEquals(9090, $uri->getPort());
        $this->assertEquals('someone:secret@domain.tld:9090', $uri->getAuthority());
        $this->assertEquals('/subdir', $uri->getPath());
        $this->assertEquals('test=true', $uri->getQuery());
        $this->assertEquals('phpunit', $uri->getFragment());
        $this->assertEquals($url, (string) $uri);
        $this->assertEquals($url, (string) $uri->withPath('subdir'));
    }

    public function test_uri_invalid_string(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $factory = new UriFactory;
        $factory->createUri('http:///domain.tld/');
    }

    public function test_uri_with_empty_string(): void
    {
        $factory = new UriFactory;
        $uri = $factory->createUri('');
        $this->assertInstanceOf(UriInterface::class, $uri);
        $this->assertEmpty((string) $uri);
    }

    public function test_uri_with_partial_components(): void
    {
        $factory = new UriFactory;
        // Test with only scheme and host
        $uri = $factory->createUri('https://example.com');
        $this->assertEquals('https', $uri->getScheme());
        $this->assertEquals('example.com', $uri->getHost());
        // Test with only path
        $uri = $factory->createUri('/path/to/resource');
        $this->assertEquals('/path/to/resource', $uri->getPath());
        // Test with only query
        $uri = $factory->createUri('?key=value');
        $this->assertEquals('key=value', $uri->getQuery());
        // Test with only fragment
        $uri = $factory->createUri('#section');
        $this->assertEquals('section', $uri->getFragment());
    }

    public function test_uri_with_ipv4_address(): void
    {
        $factory = new UriFactory;
        $uri = $factory->createUri('http://192.168.1.1:8080/path');
        $this->assertEquals('192.168.1.1', $uri->getHost());
        $this->assertEquals(8080, $uri->getPort());
    }

    public function test_uri_with_ipv6_address(): void
    {
        $factory = new UriFactory;
        $uri = $factory->createUri('http://[2001:db8::1]:8080/path');
        $this->assertEquals('[2001:db8::1]', $uri->getHost());
        $this->assertEquals(8080, $uri->getPort());
    }

    public function test_uri_with_special_characters(): void
    {
        $factory = new UriFactory;
        $uri = $factory->createUri('http://example.com/path%20with%20spaces?key=value%20here');
        $this->assertStringContainsString('path', $uri->getPath());
        $this->assertStringContainsString('key', $uri->getQuery());
    }
}
