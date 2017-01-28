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

use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\StreamInterface;

/**
 * Class MessageAbstractTest
 * @package Sandesh
 */
class MessageAbstractTest extends \PHPUnit_Framework_TestCase
{
    public function testBody()
    {
        /** @var MessageInterface $message */
        $message = $this->getMockForAbstractClass('Sandesh\\MessageAbstract');
        $this->assertNull($message->getBody());
        $body = $this->getMockBuilder('Psr\\Http\\Message\\StreamInterface')->getMock();
        $this->assertSame($body, $message->withBody($body)->getBody());
    }

    public function testHeaders()
    {
        /** @var MessageInterface $message */
        $message = $this->getMockForAbstractClass('Sandesh\\MessageAbstract');
        $message = $message->withHeader('Content-Length', '128');
        $this->assertInternalType('array', $message->getHeaders());
        $this->assertCount(1, $message->getHeaders());
        $this->assertTrue($message->hasHeader('Content-Length'));
        $this->assertInternalType('array', $message->getHeader('Content-Length'));
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
            array('text/plain'),
            $message->withHeader('Content-Type', 'text/plain')
                ->getHeader('Content-Type')
        );
        $this->assertEquals(
            array('text/plain'),
            $message->withAddedHeader('Content-Type', 'text/plain')
                ->getHeader('Content-Type')
        );
        $this->assertEquals(
            array('text/plain', 'text/html'),
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

    public function testHeadersCaseInsensitive()
    {
        /** @var MessageInterface $message */
        $message = $this->getMockForAbstractClass('Sandesh\\MessageAbstract');
        $message = $message->withHeader('Content-Length', $length = '128')
            ->withHeader('Content-Type', $type = 'text/html; charset=utf-8');
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

    public function testHeaderInvalidName()
    {
        /** @var MessageInterface $message */
        $message = $this->getMockForAbstractClass('Sandesh\\MessageAbstract');
        $this->setExpectedException('InvalidArgumentException');
        $message->withHeader('Some-Invalid<Name', 'Value');
    }

    public function testHeaderInvalidValue()
    {
        /** @var MessageInterface $message */
        $message = $this->getMockForAbstractClass('Sandesh\\MessageAbstract');
        $this->setExpectedException('InvalidArgumentException');
        $message->withHeader('Some-Header', "Value\r\n");
    }

    public function testProtocolVersion()
    {
        /** @var MessageInterface $message */
        $message = $this->getMockForAbstractClass('Sandesh\\MessageAbstract');
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
        $this->setExpectedException('InvalidArgumentException');
        $message->withProtocolVersion('10.0');
    }
}
