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

use Interop\Http\Factory\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;

/**
 * Class StreamFactoryTest
 * @package Sandesh
 */
class StreamFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var StreamFactoryInterface
     */
    protected $factory;

    public function setUp()
    {
        $this->factory = new StreamFactory();
    }

    public function testStream()
    {
        $this->assertInstanceOf(
            StreamInterface::class,
            $stream = $this->factory->createStream()
        );
        $this->assertEmpty($stream->getContents());
        $stream->write($text = 'Hello');
        $this->assertEquals($text, (string)$stream);
    }

    public function testStreamFromString()
    {
        $this->assertInstanceOf(
            StreamInterface::class,
            $stream = $this->factory->createStream($text = 'Hello')
        );
        $this->assertEquals($text, (string)$stream);
    }

    public function testStreamFromFile()
    {
        $this->assertInstanceOf(
            StreamInterface::class,
            $stream = $this->factory->createStreamFromFile(__FILE__, 'r')
        );
        $this->assertNotEmpty($text = $stream->read(5));
        $this->assertEquals('<?php', $text);
        $stream->close();
    }

    public function testStreamFromResource()
    {
        $handle = fopen(__FILE__, 'r');
        $this->assertInstanceOf(
            StreamInterface::class,
            $stream = $this->factory->createStreamFromResource($handle)
        );
        $this->assertNotEmpty($text = $stream->read(5));
        $this->assertEquals('<?php', $text);
        $stream->close();
    }
}
