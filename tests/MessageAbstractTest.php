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
use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\StreamInterface;

/**
 * Class MessageAbstractTest
 */
class MessageAbstractTest extends TestCase
{
    public function test_body(): void
    {
        /** @var MessageInterface $message */
        $message = $this->getMockForAbstractClass(MessageAbstract::class);
        $this->assertInstanceOf(StreamInterface::class, $message->getBody());
        $body = $this->createMock(StreamInterface::class);
        $this->assertSame($body, $message->withBody($body)->getBody());
    }

    public function test_headers(): void
    {
        /** @var MessageInterface $message */
        $message = $this->getMockForAbstractClass(MessageAbstract::class);
        $message = $message->withHeader('Content-Length', '128');
        $this->assertIsArray($message->getHeaders());
        $this->assertCount(1, $message->getHeaders());
        $this->assertTrue($message->hasHeader('Content-Length'));
        $this->assertIsArray($message->getHeader('Content-Length'));
        $this->assertEquals('128', $message->getHeaderLine('Content-Length'));
        $this->assertFalse($message->hasHeader('Content-Type'));
        $this->assertEmpty($message->getHeaderLine('Content-Type'));
        $this->assertTrue(
            $message->withHeader('Content-Type', 'text/plain')
                ->hasHeader('Content-Type')
        );
        $this->assertFalse(
            $message->withHeader('Content-Type', 'text/plain')
                ->withoutHeader('Content-Type')
                ->hasHeader('Content-Type')
        );
        $this->assertEquals(
            ['text/plain'],
            $message->withHeader('Content-Type', 'text/plain')
                ->getHeader('Content-Type')
        );
        $this->assertEquals(
            ['text/plain'],
            $message->withAddedHeader('Content-Type', 'text/plain')
                ->getHeader('Content-Type')
        );
        $this->assertEquals(
            ['text/plain', 'text/html'],
            $message->withHeader('Content-Type', 'text/plain')
                ->withAddedHeader('Content-Type', 'text/html')
                ->getHeader('Content-Type')
        );
        $this->assertEquals(
            'text/plain,text/html',
            $message->withHeader('Content-Type', 'text/plain')
                ->withAddedHeader('Content-Type', 'text/html')
                ->getHeaderLine('Content-Type')
        );
    }

    public function test_headers_case_insensitive(): void
    {
        /** @var MessageInterface $message */
        $message = $this->getMockForAbstractClass(MessageAbstract::class);
        $length = '128';
        $type = 'text/html; charset=utf-8';
        $message = $message->withHeader('Content-Length', $length)
            ->withHeader('Content-Type', $type);
        $this->assertTrue($message->hasHeader('Content-Length'));
        $this->assertTrue($message->hasHeader('content-length'));
        $this->assertEquals($length, $message->getHeaderLine('Content-Length'));
        $this->assertEquals($length, $message->getHeaderLine('content-length'));
        $this->assertTrue($message->hasHeader('Content-Type'));
        $this->assertTrue($message->hasHeader('content-type'));
        $this->assertEquals($type, $message->getHeaderLine('Content-Type'));
        $this->assertEquals($type, $message->getHeaderLine('content-type'));
        $this->assertTrue(
            $message->withHeader('X-Powered-By', 'PHP/7.1')
                ->hasHeader('x-powered-by')
        );
        $this->assertTrue(
            $message->withHeader('x-powered-by', 'PHP/7.1')
                ->hasHeader('X-Powered-By')
        );
        $this->assertFalse(
            $message->withoutHeader('Content-Length')
                ->hasHeader('content-length')
        );
        $this->assertFalse(
            $message->withoutHeader('content-length')
                ->hasHeader('Content-Length')
        );
    }

    public function test_header_invalid_name(): void
    {
        /** @var MessageInterface $message */
        $message = $this->getMockForAbstractClass(MessageAbstract::class);
        $this->expectException(\InvalidArgumentException::class);
        $message->withHeader('Some-Invalid<Name', 'Value');
    }

    public function test_header_invalid_value(): void
    {
        /** @var MessageInterface $message */
        $message = $this->getMockForAbstractClass(MessageAbstract::class);
        $this->expectException(\InvalidArgumentException::class);
        $message->withHeader('Some-Header', "Value\r\n");
    }

    public function test_protocol_version(): void
    {
        /** @var MessageInterface $message */
        $message = $this->getMockForAbstractClass(MessageAbstract::class);
        $this->assertNotEmpty($message->getProtocolVersion());
        $this->assertEquals('1.1', $message->getProtocolVersion());
        $this->assertEquals(
            '1.0',
            $message->withProtocolVersion('1.0')
                ->getProtocolVersion()
        );
        $this->assertEquals(
            '1.1',
            $message->withProtocolVersion('1.1')
                ->getProtocolVersion()
        );
        $this->expectException(\InvalidArgumentException::class);
        $message->withProtocolVersion('10.0');
    }

    public function test_header_with_array_value(): void
    {
        /** @var MessageInterface $message */
        $message = $this->getMockForAbstractClass(MessageAbstract::class);
        $message = $message->withHeader('Accept', ['text/html', 'application/json']);
        $this->assertEquals(['text/html', 'application/json'], $message->getHeader('Accept'));
        $this->assertEquals('text/html,application/json', $message->getHeaderLine('Accept'));
    }

    public function test_with_added_header_when_header_does_not_exist(): void
    {
        /** @var MessageInterface $message */
        $message = $this->getMockForAbstractClass(MessageAbstract::class);
        $message = $message->withAddedHeader('X-Custom', 'value1');
        $this->assertEquals(['value1'], $message->getHeader('X-Custom'));
        $message = $message->withAddedHeader('X-Custom', 'value2');
        $this->assertEquals(['value1', 'value2'], $message->getHeader('X-Custom'));
    }

    public function test_multiple_header_values(): void
    {
        /** @var MessageInterface $message */
        $message = $this->getMockForAbstractClass(MessageAbstract::class);
        $message = $message->withHeader('Accept', 'text/html')
            ->withAddedHeader('Accept', 'application/json')
            ->withAddedHeader('Accept', 'text/plain');
        $this->assertEquals(['text/html', 'application/json', 'text/plain'], $message->getHeader('Accept'));
        $this->assertEquals('text/html,application/json,text/plain', $message->getHeaderLine('Accept'));
    }

    public function test_empty_header_value(): void
    {
        /** @var MessageInterface $message */
        $message = $this->getMockForAbstractClass(MessageAbstract::class);
        $message = $message->withHeader('X-Empty', '');
        $this->assertEquals([''], $message->getHeader('X-Empty'));
        $this->assertEquals('', $message->getHeaderLine('X-Empty'));
    }

    public function test_protocol_version_edge_cases(): void
    {
        /** @var MessageInterface $message */
        $message = $this->getMockForAbstractClass(MessageAbstract::class);
        $this->assertEquals('2.0', $message->withProtocolVersion('2.0')->getProtocolVersion());
        $this->expectException(\InvalidArgumentException::class);
        $message->withProtocolVersion('0.9');
        $this->expectException(\InvalidArgumentException::class);
        $message->withProtocolVersion('1.10');
    }

    public function test_immutability(): void
    {
        /** @var MessageInterface $message */
        $message = $this->getMockForAbstractClass(MessageAbstract::class);
        $original = $message;
        $this->assertNotSame($original, $message->withHeader('X-Test', 'value'));
        $this->assertNotSame($original, $message->withBody($this->createMock(StreamInterface::class)));
        $this->assertNotSame($original, $message->withProtocolVersion('1.0'));
        $message = $message->withHeader('X-Test', 'value');
        $this->assertNotSame($message, $message->withoutHeader('X-Test'));
    }

    public function test_get_header_with_non_existent_header(): void
    {
        /** @var MessageInterface $message */
        $message = $this->getMockForAbstractClass(MessageAbstract::class);
        $this->assertEquals([], $message->getHeader('Non-Existent'));
        $this->assertEquals('', $message->getHeaderLine('Non-Existent'));
    }
}
