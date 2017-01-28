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

/**
 * Class RequestFactoryTest
 * @package Sandesh
 */
class RequestFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateRequest()
    {
        $factory = new RequestFactory();
        $request = $factory->createRequest('GET', 'http://domain.tld:9090/subdir?test=true#phpunit');
        $this->assertInstanceOf('Psr\\Http\\Message\\RequestInterface', $request);
        $this->assertEquals('1.1', $request->getProtocolVersion());
        $this->assertInstanceOf('Psr\\Http\\Message\\UriInterface', $uri = $request->getUri());
        $this->assertEquals('http://domain.tld:9090/subdir?test=true#phpunit', (string)$uri);
    }
}
