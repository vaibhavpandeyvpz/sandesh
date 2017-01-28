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

use Psr\Http\Message\UriInterface;

/**
 * Class ServerRequestFactoryTest
 * @package Sandesh
 */
class ServerRequestFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param array $server
     * @param string $method
     * @param UriInterface|string $uri
     * @dataProvider provideServerRequestFactoryArgs
     */
    public function testCreateServerRequest(array $server, $method = null, $uri = null)
    {
        $factory = new ServerRequestFactory();
        $request = $factory->createServerRequest($server, $method, $uri);
        $this->assertInstanceOf('Psr\\Http\\Message\\ServerRequestInterface', $request);
        $this->assertEquals('1.0', $request->getProtocolVersion());
        $this->assertInstanceOf('Psr\\Http\\Message\\UriInterface', $request->getUri());
        $this->assertEquals('GET', $request->getMethod());
        $this->assertEquals('domain.tld', $request->getUri()->getHost());
        $this->assertEquals(9090, $request->getUri()->getPort());
        $this->assertEquals('http://domain.tld:9090/subdir?test=true#phpunit', (string)$request->getUri());
    }

    public function provideServerRequestFactoryArgs()
    {
        return array(
            array(array(
                'HTTP_HOST' => 'domain.tld:9090',
                'HTTP_INVALID' => null,
                'HTTP_X_REWRITE_URL' => '/some-fancy-url',
                'HTTP_X_ORIGINAL_URL' => '/subdir?test=true#phpunit',
                'QUERY_STRING' => 'test=true',
                'REQUEST_METHOD' => 'GET',
                'REQUEST_URI' => 'http://domain.tld:9090/subdir#phpunit',
                'SERVER_PORT' => '9090',
                'SERVER_PROTOCOL' => 'HTTP/1.0',
            )),
            array(array('SERVER_PROTOCOL' => 'HTTP/1.0'), 'GET', 'http://domain.tld:9090/subdir?test=true#phpunit')
        );
    }
}
