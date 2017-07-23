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
 * Class CookieTest
 * @package Sandesh
 */
class CookieTest extends \PHPUnit_Framework_TestCase
{
    public function testDomain()
    {
        $cookie = new Cookie($name = 'somename');
        $this->assertNull($cookie->getDomain());
        $cookie = $cookie->withDomain($domain = 'domain.tld');
        $this->assertEquals($domain, $cookie->getDomain());
    }

    public function testHttpOnly()
    {
        $cookie = new Cookie('somename');
        $this->assertFalse($cookie->isHttpOnly());
        $this->assertTrue($cookie->withHttpOnly(true)->isHttpOnly());
    }

    public function testMaxAge()
    {
        $cookie = new Cookie('somename');
        $this->assertEquals(0, $cookie->getMaxAge());
        $cookie = $cookie->withMaxAge($age = 86400);
        $this->assertEquals($age, $cookie->getMaxAge());
    }

    public function testName()
    {
        $cookie = new Cookie($name = 'somename');
        $this->assertEquals($name, $cookie->getName());
        $cookie = $cookie->withName($name = 'othername');
        $this->assertEquals($name, $cookie->getName());
    }

    public function testPath()
    {
        $cookie = new Cookie($name = 'somename');
        $this->assertNull($cookie->getPath());
        $cookie = $cookie->withPath($path = '/');
        $this->assertEquals($path, $cookie->getPath());
    }

    public function testSecure()
    {
        $cookie = new Cookie('somename');
        $this->assertFalse($cookie->isSecure());
        $this->assertTrue($cookie->withSecure(true)->isSecure());
    }

    public function testValue()
    {
        $cookie = new Cookie('somename');
        $this->assertNull($cookie->getValue());
        $cookie = $cookie->withValue($value = 'somevalue');
        $this->assertEquals($value, $cookie->getValue());
        $cookie = $cookie->withValue(null);
        $this->assertNull($cookie->getValue());
    }

    public function testToString()
    {
        $time = new \DateTime();
        $cookie = new Cookie('PHPSESS');
        $expected = sprintf(
            'PHPSESS=1234567890; Domain=domain.tld; Expires=%s; HttpOnly; Max-Age=86400; Path=/admin; Secure',
            $time->format(Cookie::EXPIRY_FORMAT)
        );
        $this->assertEquals($expected, (string)$cookie->withValue('1234567890')
            ->withDomain('domain.tld')
            ->withExpiry($time)
            ->withHttpOnly(true)
            ->withMaxAge(86400)
            ->withPath('/admin')
            ->withSecure(true));
    }
}
