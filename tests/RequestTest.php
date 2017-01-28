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
 * Class RequestTest
 * @package Sandesh
 */
class RequestTest extends \PHPUnit_Framework_TestCase
{
    public function testMethod()
    {
        $request = new Request();
        $this->assertNotEmpty($request->getMethod());
        $this->assertEquals('GET', $request->getMethod());
        $this->assertEquals('PUT', $request->withMethod('PUT')->getMethod());
    }

    public function testMethodInvalid()
    {
        $this->setExpectedException('InvalidArgumentException');
        new Request('something');
    }

    public function testRequestTarget()
    {
        $request = new Request();
        $this->assertNotEmpty($request->getRequestTarget());
        $this->assertEquals('/', $request->getRequestTarget());
        $this->assertEquals(
            '/user/profile',
            $request->withRequestTarget('/user/profile')
                ->getRequestTarget()
        );
        $uri = new UriFactory();
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

    public function testUri()
    {
        $request = new Request();
        $this->assertSame($uri = new Uri(), $request->withUri($uri)->getUri());
    }

    public function testUriPreserveHost()
    {
        $request = new Request();
        $factory = new UriFactory();
        $request = $request->withUri($factory->createUri('http://domain.tld:9090'), true);
        $this->assertEquals('domain.tld:9090', $request->getHeaderLine('Host'));
        $request = $request->withUri($factory->createUri('http://otherdomain.tld'), true);
        $this->assertEquals('domain.tld:9090', $request->getHeaderLine('Host'));
    }
}
