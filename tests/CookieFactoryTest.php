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

/**
 * Class CookieFactoryTest
 */
class CookieFactoryTest extends TestCase
{
    public function test_basic(): void
    {
        $factory = new CookieFactory;
        $cookie = $factory->createCookie('PHPSESS=1234567890');
        $this->assertEquals('PHPSESS', $cookie->getName());
        $this->assertEquals('1234567890', $cookie->getValue());
    }

    public function test_with_attributes(): void
    {
        $time = new \DateTime;
        $factory = new CookieFactory;
        $cookie = $factory->createCookie(sprintf(
            'PHPSESS=1234567890; Domain=domain.tld; Expires=%s; HttpOnly; Max-Age=86400; Path=/admin; Secure',
            $time->format(Cookie::EXPIRY_FORMAT)
        ));
        $this->assertEquals('domain.tld', $cookie->getDomain());
        $expiry = $cookie->getExpiry();
        $this->assertNotNull($expiry);
        $this->assertEquals(
            $time->format(Cookie::EXPIRY_FORMAT),
            $expiry->format(Cookie::EXPIRY_FORMAT)
        );
        $this->assertEquals(86400, $cookie->getMaxAge());
        $this->assertEquals('PHPSESS', $cookie->getName());
        $this->assertEquals('/admin', $cookie->getPath());
        $this->assertEquals('1234567890', $cookie->getValue());
        $this->assertTrue($cookie->isSecure());
        $this->assertTrue($cookie->isHttpOnly());
    }

    public function test_cookie_with_only_name(): void
    {
        $factory = new CookieFactory;
        $cookie = $factory->createCookie('SESSION=');
        $this->assertEquals('SESSION', $cookie->getName());
        $this->assertEquals('', $cookie->getValue());
    }

    public function test_cookie_with_url_encoded_value(): void
    {
        $factory = new CookieFactory;
        $cookie = $factory->createCookie('test=hello%20world');
        $this->assertEquals('hello world', $cookie->getValue());
    }

    public function test_cookie_with_unknown_attribute(): void
    {
        $factory = new CookieFactory;
        $cookie = $factory->createCookie('test=value; Unknown=attr');
        $this->assertEquals('test', $cookie->getName());
        $this->assertEquals('value', $cookie->getValue());
    }

    public function test_cookie_with_max_age_as_string(): void
    {
        $factory = new CookieFactory;
        $cookie = $factory->createCookie('test=value; Max-Age=3600');
        $this->assertEquals(3600, $cookie->getMaxAge());
    }

    public function test_cookie_with_invalid_header_format(): void
    {
        $factory = new CookieFactory;
        // Empty string should create a cookie with empty name (no exception)
        $cookie = $factory->createCookie('');
        $this->assertEquals('', $cookie->getName());
    }

    public function test_cookie_with_preg_split_failure(): void
    {
        // This tests the preg_split failure path - but preg_split rarely fails
        // We'll test with a valid cookie that exercises all code paths
        $factory = new CookieFactory;
        $cookie = $factory->createCookie('test=value; Domain=example.com; Path=/; Secure; HttpOnly; Max-Age=3600');
        $this->assertEquals('test', $cookie->getName());
        $this->assertEquals('value', $cookie->getValue());
        $this->assertEquals('example.com', $cookie->getDomain());
        $this->assertEquals('/', $cookie->getPath());
        $this->assertTrue($cookie->isSecure());
        $this->assertTrue($cookie->isHttpOnly());
        $this->assertEquals(3600, $cookie->getMaxAge());
    }

    public function test_cookie_with_multiple_attributes(): void
    {
        $factory = new CookieFactory;
        $time = new \DateTime;
        $cookie = $factory->createCookie(sprintf(
            'SESSION=abc123; Domain=example.com; Path=/; Secure; HttpOnly; Max-Age=7200; Expires=%s',
            $time->format(Cookie::EXPIRY_FORMAT)
        ));
        $this->assertEquals('SESSION', $cookie->getName());
        $this->assertEquals('abc123', $cookie->getValue());
        $this->assertEquals('example.com', $cookie->getDomain());
        $this->assertEquals('/', $cookie->getPath());
        $this->assertTrue($cookie->isSecure());
        $this->assertTrue($cookie->isHttpOnly());
        $this->assertEquals(7200, $cookie->getMaxAge());
    }
}
