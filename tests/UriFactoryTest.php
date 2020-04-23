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
 * Class UriFactoryTest
 * @package Sandesh
 */
class UriFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testUri()
    {
        $factory = new UriFactory();
        $this->assertInstanceOf(UriInterface::class, $uri = $factory->createUri());
        $this->assertEmpty((string)$uri);
        $uri = $factory->createUri($url = 'http://someone:secret@domain.tld:9090/subdir?test=true#phpunit');
        $this->assertInstanceOf(UriInterface::class, $uri);
        $this->assertEquals('http', $uri->getScheme());
        $this->assertEquals('someone:secret', $uri->getUserInfo());
        $this->assertEquals('domain.tld', $uri->getHost());
        $this->assertEquals(9090, $uri->getPort());
        $this->assertEquals('someone:secret@domain.tld:9090', $uri->getAuthority());
        $this->assertEquals('/subdir', $uri->getPath());
        $this->assertEquals('test=true', $uri->getQuery());
        $this->assertEquals('phpunit', $uri->getFragment());
        $this->assertEquals($url, (string)$uri);
        $this->assertEquals($url, (string)$uri->withPath('subdir'));
    }

    public function testUriInvalidString()
    {
        $this->setExpectedException(\InvalidArgumentException::class);
        $factory = new UriFactory();
        $factory->createUri('http:///domain.tld/');
    }
}
