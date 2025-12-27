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
 * Class CookieTest
 */
class CookieTest extends TestCase
{
    public function test_domain(): void
    {
        $name = 'somename';
        $cookie = new Cookie($name);
        $this->assertNull($cookie->getDomain());
        $domain = 'domain.tld';
        $cookie = $cookie->withDomain($domain);
        $this->assertEquals($domain, $cookie->getDomain());
    }

    public function test_http_only(): void
    {
        $cookie = new Cookie('somename');
        $this->assertFalse($cookie->isHttpOnly());
        $this->assertTrue($cookie->withHttpOnly(true)->isHttpOnly());
    }

    public function test_max_age(): void
    {
        $cookie = new Cookie('somename');
        $this->assertEquals(0, $cookie->getMaxAge());
        $age = 86400;
        $cookie = $cookie->withMaxAge($age);
        $this->assertEquals($age, $cookie->getMaxAge());
    }

    public function test_name(): void
    {
        $name = 'somename';
        $cookie = new Cookie($name);
        $this->assertEquals($name, $cookie->getName());
        $name = 'othername';
        $cookie = $cookie->withName($name);
        $this->assertEquals($name, $cookie->getName());
    }

    public function test_path(): void
    {
        $name = 'somename';
        $cookie = new Cookie($name);
        $this->assertNull($cookie->getPath());
        $path = '/';
        $cookie = $cookie->withPath($path);
        $this->assertEquals($path, $cookie->getPath());
    }

    public function test_secure(): void
    {
        $cookie = new Cookie('somename');
        $this->assertFalse($cookie->isSecure());
        $this->assertTrue($cookie->withSecure(true)->isSecure());
    }

    public function test_value(): void
    {
        $cookie = new Cookie('somename');
        $this->assertNull($cookie->getValue());
        $value = 'somevalue';
        $cookie = $cookie->withValue($value);
        $this->assertEquals($value, $cookie->getValue());
        $cookie = $cookie->withValue(null);
        $this->assertNull($cookie->getValue());
    }

    public function test_to_string(): void
    {
        $time = new \DateTime;
        $cookie = new Cookie('PHPSESS');
        $expected = sprintf(
            'PHPSESS=1234567890; Domain=domain.tld; Expires=%s; HttpOnly; Max-Age=86400; Path=/admin; Secure',
            $time->format(Cookie::EXPIRY_FORMAT)
        );
        $this->assertEquals($expected, (string) $cookie->withValue('1234567890')
            ->withDomain('domain.tld')
            ->withExpiry($time)
            ->withHttpOnly(true)
            ->withMaxAge(86400)
            ->withPath('/admin')
            ->withSecure(true));
    }

    public function test_expiry_with_string(): void
    {
        $cookie = new Cookie('test');
        $time = new \DateTime;
        $timeString = $time->format(Cookie::EXPIRY_FORMAT);
        $cookie = $cookie->withExpiry($timeString);
        $expiry = $cookie->getExpiry();
        if ($expiry !== null) {
            $this->assertEquals($time->format(Cookie::EXPIRY_FORMAT), $expiry->format(Cookie::EXPIRY_FORMAT));
        }
    }

    public function test_expiry_with_int(): void
    {
        $cookie = new Cookie('test');
        $timestamp = time() + 3600;
        $cookie = $cookie->withExpiry($timestamp);
        $expiry = $cookie->getExpiry();
        $this->assertTrue($expiry === null || $expiry instanceof \DateTimeInterface);
    }

    public function test_expiry_with_null(): void
    {
        $cookie = new Cookie('test');
        $cookie = $cookie->withExpiry(new \DateTime);
        $this->assertNotNull($cookie->getExpiry());
        $cookie = $cookie->withExpiry(null);
        $this->assertNull($cookie->getExpiry());
    }

    public function test_to_string_with_minimal_cookie(): void
    {
        $cookie = new Cookie('test');
        $cookie = $cookie->withValue('value');
        $this->assertEquals('test=value', (string) $cookie);
    }

    public function test_to_string_with_null_value(): void
    {
        $cookie = new Cookie('test');
        $this->assertEquals('test=', (string) $cookie);
    }

    public function test_immutability(): void
    {
        $cookie = new Cookie('test');
        $original = $cookie;
        $this->assertNotSame($original, $cookie->withDomain('domain.tld'));
        $this->assertNotSame($original, $cookie->withExpiry(new \DateTime));
        $this->assertNotSame($original, $cookie->withHttpOnly(true));
        $this->assertNotSame($original, $cookie->withMaxAge(3600));
        $this->assertNotSame($original, $cookie->withName('new'));
        $this->assertNotSame($original, $cookie->withPath('/'));
        $this->assertNotSame($original, $cookie->withSecure(true));
        $this->assertNotSame($original, $cookie->withValue('value'));
    }
}
