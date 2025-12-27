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
 * Class HttpMethodTest
 */
class HttpMethodTest extends TestCase
{
    public function test_all_http_methods(): void
    {
        $methods = [
            HttpMethod::GET,
            HttpMethod::POST,
            HttpMethod::PUT,
            HttpMethod::DELETE,
            HttpMethod::PATCH,
            HttpMethod::HEAD,
            HttpMethod::OPTIONS,
            HttpMethod::TRACE,
            HttpMethod::CONNECT,
        ];
        foreach ($methods as $method) {
            $this->assertInstanceOf(HttpMethod::class, $method);
            $this->assertIsString($method->toString());
        }
    }

    public function test_from_string_with_valid_method(): void
    {
        $this->assertEquals(HttpMethod::GET, HttpMethod::fromString('GET'));
        $this->assertEquals(HttpMethod::POST, HttpMethod::fromString('POST'));
        $this->assertEquals(HttpMethod::GET, HttpMethod::fromString('get'));
        $this->assertEquals(HttpMethod::POST, HttpMethod::fromString('post'));
    }

    public function test_from_string_with_invalid_method(): void
    {
        $this->expectException(\ValueError::class);
        HttpMethod::fromString('INVALID');
    }

    public function test_to_string(): void
    {
        $this->assertEquals('GET', HttpMethod::GET->toString());
        $this->assertEquals('POST', HttpMethod::POST->toString());
        $this->assertEquals('PUT', HttpMethod::PUT->toString());
    }

    public function test_value_property(): void
    {
        $this->assertEquals('GET', HttpMethod::GET->value);
        $this->assertEquals('POST', HttpMethod::POST->value);
    }
}
