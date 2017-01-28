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
 * Class CookieFactoryTest
 * @package Sandesh
 */
class CookieFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testBasic()
    {
        $factory = new CookieFactory();
        $cookie = $factory->createCookie('PHPSESS=1234567890');
        $this->assertEquals('PHPSESS', $cookie->getName());
        $this->assertEquals('1234567890', $cookie->getValue());
    }

    public function testWithAttributes()
    {
        $time = new \DateTime();
        $factory = new CookieFactory();
        $cookie = $factory->createCookie(sprintf(
            'PHPSESS=1234567890; Domain=domain.tld; Expires=%s; HttpOnly; Max-Age=86400; Path=/admin; Secure',
            $time->format(Cookie::EXPIRY_FORMAT)));
        $this->assertEquals('domain.tld', $cookie->getDomain());
        $this->assertEquals(
            $time->format(Cookie::EXPIRY_FORMAT),
            $cookie->getExpiry()->format(Cookie::EXPIRY_FORMAT));
        $this->assertEquals(86400, $cookie->getMaxAge());
        $this->assertEquals('PHPSESS', $cookie->getName());
        $this->assertEquals('/admin', $cookie->getPath());
        $this->assertEquals('1234567890', $cookie->getValue());
        $this->assertTrue($cookie->isSecure());
        $this->assertTrue($cookie->isHttpOnly());
    }
}
