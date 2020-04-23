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
 * Class UriTest
 * @package Sandesh
 */
class UriTest extends \PHPUnit_Framework_TestCase
{
    public function testAuthority()
    {
        $uri = new Uri();
        $this->assertEmpty($uri->getAuthority());
        $this->assertEquals(
            'domain.tld',
            $uri->withHost('domain.tld')
                ->getAuthority()
        );
        $this->assertEquals(
            'domain.tld:9090',
            $uri->withHost('domain.tld')
                ->withPort(9090)
                ->getAuthority()
        );
        $this->assertEquals(
            'someone@domain.tld:9090',
            $uri->withHost('domain.tld')
                ->withPort(9090)
                ->withUserInfo('someone')
                ->getAuthority()
        );
        $this->assertEquals(
            'someone:secret@domain.tld:9090',
            $uri->withHost('domain.tld')
                ->withPort(9090)
                ->withUserInfo('someone', 'secret')
                ->getAuthority()
        );
    }

    public function testFragment()
    {
        $uri = new Uri();
        $this->assertEmpty($uri->getFragment());
        $this->assertEquals(
            'phpunit',
            $uri->withFragment('phpunit')
                ->getFragment()
        );
        $this->assertEquals(
            '%23phpunit',
            $uri->withFragment('#phpunit')
                ->getFragment()
        );
        $this->assertEquals(
            'phpunit%20%5E4.0%20%7C%7C%20%5E5.0',
            $uri->withFragment('phpunit ^4.0 || ^5.0')
                ->getFragment()
        );
    }

    public function testHost()
    {
        $uri = new Uri();
        $this->assertEmpty($uri->getHost());
        $this->assertEquals(
            'domain.tld',
            $uri->withHost('domain.tld')
                ->getHost()
        );
        $this->assertEquals(
            'domain.tld',
            $uri->withHost('DOMAIN.tld')
                ->getHost()
        );
        $this->assertEquals(
            'domain.tld',
            $uri->withHost('domain.TLD')
                ->getHost()
        );
        $this->assertEquals(
            'domain.tld',
            $uri->withHost('DoMaIn.TlD')
                ->getHost()
        );
    }

    public function testPath()
    {
        $uri = new Uri();
        $this->assertEmpty($uri->getPath());
        $this->assertEquals(
            '/subdir',
            $uri->withPath('/subdir')
                ->getPath()
        );
        $this->assertEquals(
            '/subdir',
            $uri->withPath('//subdir')
                ->getPath()
        );
        $this->assertEquals(
            'subdir',
            $uri->withPath('subdir')
                ->getPath()
        );
    }

    public function testPathWithQuery()
    {
        $uri = new Uri();
        $this->setExpectedException(\InvalidArgumentException::class);
        $uri->withPath('/subdir?test=true')
            ->getPath();
    }

    public function testPathWithFragment()
    {
        $uri = new Uri();
        $this->setExpectedException(\InvalidArgumentException::class);
        $uri->withPath('/subdir#phpunit')
            ->getPath();
    }

    public function testPort()
    {
        $uri = new Uri();
        $this->assertNull($uri->getPort());
        $this->assertEquals(
            9090,
            $uri->withPort(9090)
                ->getPort()
        );
    }

    public function testPortInvalid()
    {
        $uri = new Uri();
        $this->setExpectedException(\InvalidArgumentException::class);
        $uri->withPort(-999);
    }

    public function testQuery()
    {
        $uri = new Uri();
        $this->assertEmpty($uri->getQuery());
        $this->assertEquals(
            'test=true',
            $uri->withQuery('test=true')
                ->getQuery()
        );
        $this->assertEquals(
            'test=true',
            $uri->withQuery('?test=true')
                ->getQuery()
        );
        $this->assertEquals(
            'test=true&debug',
            $uri->withQuery('?test=true&debug')
                ->getQuery()
        );
    }

    public function testQueryInvalid()
    {
        $uri = new Uri();
        $this->setExpectedException(\InvalidArgumentException::class);
        $uri->withQuery('test=true#phpunit');
    }

    public function testScheme()
    {
        $uri = new Uri();
        $this->assertEmpty($uri->getScheme());
        $this->assertEquals(
            'http',
            $uri->withScheme('http')
                ->getScheme()
        );
        $this->assertEquals(
            'https',
            $uri->withScheme('https')
                ->getScheme()
        );
        $this->assertEquals(
            'http',
            $uri->withScheme('http://')
                ->getScheme()
        );
    }

    public function testUserInfo()
    {
        $uri = new Uri();
        $this->assertEmpty($uri->getUserInfo());
        $this->assertEquals(
            'someone',
            $uri->withUserInfo('someone')
                ->getUserInfo()
        );
        $this->assertEquals(
            'someone:secret',
            $uri->withUserInfo('someone', 'secret')
                ->getUserInfo()
        );
        $this->assertEmpty(
            $uri->withUserInfo(null, 'secret')
                ->getScheme()
        );
    }

    public function testImmutability()
    {
        $uri = new Uri();
        $this->assertNotSame($uri, $uri->withFragment('phpunit'));
        $this->assertNotSame($uri, $uri->withHost('domain.tld'));
        $this->assertNotSame($uri, $uri->withPath('/subdir'));
        $this->assertNotSame($uri, $uri->withPort(9090));
        $this->assertNotSame($uri, $uri->withQuery('test=true'));
        $this->assertNotSame($uri, $uri->withScheme('http'));
        $this->assertNotSame($uri, $uri->withUserInfo('someone', 'secret'));
    }

    public function testToString()
    {
        $uri = new Uri();
        $uri = $uri->withFragment('phpunit')
            ->withHost('domain.tld')
            ->withPath('/subdir')
            ->withPort(9090)
            ->withQuery('test=true')
            ->withScheme('http')
            ->withUserInfo('someone', 'secret');
        $this->assertEquals('http://someone:secret@domain.tld:9090/subdir?test=true#phpunit', (string)$uri);
    }
}
