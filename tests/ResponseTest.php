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
use Psr\Http\Message\StreamInterface;

/**
 * Class ResponseTest
 */
class ResponseTest extends TestCase
{
    public function test_body(): void
    {
        $body = new Stream;
        $response = new Response(200, '', $body);
        $this->assertSame($body, $response->getBody());
    }

    public function test_status(): void
    {
        $response = new Response;
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(
            401,
            $response->withStatus(401)
                ->getStatusCode()
        );
        $this->assertEmpty(
            $response->withStatus(401)
                ->getReasonPhrase()
        );
        $this->assertEquals(
            'Unauthorized',
            $response->withStatus(401, 'Unauthorized')
                ->getReasonPhrase()
        );
    }

    public function test_status_invalid(): void
    {
        $response = new Response;
        $this->expectException(\InvalidArgumentException::class);
        $response->withStatus(600);
    }

    public function test_status_boundary_values(): void
    {
        $response = new Response;
        // Test minimum valid status code
        $this->assertEquals(100, $response->withStatus(100)->getStatusCode());
        // Test maximum valid status code
        $this->assertEquals(599, $response->withStatus(599)->getStatusCode());
        // Test invalid below minimum
        $this->expectException(\InvalidArgumentException::class);
        $response->withStatus(99);
        // Test invalid above maximum
        $this->expectException(\InvalidArgumentException::class);
        $response->withStatus(600);
    }

    public function test_status_code_ranges(): void
    {
        $response = new Response;
        // 1xx Informational
        $this->assertEquals(101, $response->withStatus(101)->getStatusCode());
        // 2xx Success
        $this->assertEquals(200, $response->withStatus(200)->getStatusCode());
        $this->assertEquals(201, $response->withStatus(201)->getStatusCode());
        $this->assertEquals(204, $response->withStatus(204)->getStatusCode());
        // 3xx Redirection
        $this->assertEquals(301, $response->withStatus(301)->getStatusCode());
        $this->assertEquals(302, $response->withStatus(302)->getStatusCode());
        // 4xx Client Error
        $this->assertEquals(400, $response->withStatus(400)->getStatusCode());
        $this->assertEquals(404, $response->withStatus(404)->getStatusCode());
        $this->assertEquals(422, $response->withStatus(422)->getStatusCode());
        // 5xx Server Error
        $this->assertEquals(500, $response->withStatus(500)->getStatusCode());
        $this->assertEquals(503, $response->withStatus(503)->getStatusCode());
    }

    public function test_body_with_string(): void
    {
        $response = new Response(200, '', 'test content');
        $this->assertInstanceOf(StreamInterface::class, $response->getBody());
        $this->assertEquals('test content', (string) $response->getBody());
    }

    public function test_body_with_resource(): void
    {
        $resource = fopen('php://memory', 'w+');
        fwrite($resource, 'resource content');
        rewind($resource);
        $response = new Response(200, '', $resource);
        $this->assertInstanceOf(StreamInterface::class, $response->getBody());
        $this->assertEquals('resource content', (string) $response->getBody());
    }

    public function test_body_with_null(): void
    {
        $response = new Response(200, '', null);
        $this->assertInstanceOf(StreamInterface::class, $response->getBody());
    }

    public function test_immutability(): void
    {
        $response = new Response;
        $this->assertNotSame($response, $response->withStatus(404));
        $this->assertNotSame($response, $response->withBody(new Stream));
    }

    public function test_status_with_string_code(): void
    {
        $response = new Response;
        $this->expectException(\TypeError::class);
        $response->withStatus('200');
    }
}
