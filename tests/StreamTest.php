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

use Psr\Http\Message\StreamInterface;

/**
 * Class StreamTest
 * @package Sandesh
 */
class StreamTest extends \PHPUnit_Framework_TestCase
{
    public function testStreamCreation()
    {
        $this->assertInstanceOf(
            StreamInterface::class,
            new Stream()
        );
        $this->assertInstanceOf(
            StreamInterface::class,
            new Stream(fopen('php://memory', 'w+'))
        );
        $this->setExpectedException(\InvalidArgumentException::class);
        new Stream(9999);
    }

    public function testClose()
    {
        $stream = new Stream();
        $this->assertTrue($stream->isReadable());
        $this->assertTrue($stream->isSeekable());
        $this->assertTrue($stream->isWritable());
        $stream->close();
        $this->assertFalse($stream->isReadable());
        $this->assertFalse($stream->isSeekable());
        $this->assertFalse($stream->isWritable());
    }

    public function testDetach()
    {
        $resource = fopen('php://memory', 'w+');
        $stream = new Stream($resource);
        $this->assertTrue($stream->isReadable());
        $this->assertTrue($stream->isSeekable());
        $this->assertTrue($stream->isWritable());
        $detached = $stream->detach();
        $this->assertSame($resource, $detached);
        $this->assertFalse($stream->isReadable());
        $this->assertFalse($stream->isSeekable());
        $this->assertFalse($stream->isWritable());
    }

    public function testEof()
    {
        $resource = fopen('php://memory', 'w+');
        fputs($resource, 'Something');
        fseek($resource, 0);
        $stream = new Stream($resource);
        $this->assertFalse($stream->eof());
        while (false === feof($resource)) {
            fread($resource, 4);
        }
        $this->assertTrue($stream->eof());
        fseek($resource, 0);
        $this->assertFalse($stream->eof());
        $stream->detach();
        $this->assertTrue($stream->eof());
    }

    public function testContents()
    {
        $stream = new Stream();
        $this->assertEmpty($stream->getContents());
        $stream->write('Some');
        $stream->rewind();
        $this->assertEquals('Some', $stream->getContents());
        $stream->write('thing');
        $stream->rewind();
        $this->assertEquals('Something', $stream->getContents());
        $stream->detach();
        $this->setExpectedException(\RuntimeException::class);
        $stream->getContents();
    }

    public function testReadable()
    {
        $stream = new Stream('php://memory', 'r');
        $this->assertTrue($stream->isReadable());
        $this->assertFalse($stream->isWritable());
        $tempnam = tempnam(sys_get_temp_dir(), 'rndm');
        $stream = new Stream($tempnam, 'w');
        $this->assertFalse($stream->isReadable());
    }

    public function testWritable()
    {
        $stream = new Stream('php://memory', 'r');
        $this->assertFalse($stream->isWritable());
        $stream = new Stream('php://memory', 'w');
        $this->assertTrue($stream->isWritable());
    }

    public function testRead()
    {
        $stream = new Stream();
        $stream->write('Something');
        $stream->seek(0);
        $this->assertEquals('Some', $stream->read(4));
        $this->assertEquals('thing', $stream->read(5));
    }

    public function testReadFailure()
    {
        $stream = new Stream('php://memory');
        $stream->close();
        $this->setExpectedException(\RuntimeException::class);
        $stream->read(1);
    }

    public function testRewind()
    {
        $stream = new Stream();
        $stream->write('Something');
        while (!$stream->eof()) {
            $stream->read(4);
        }
        $this->assertTrue($stream->eof());
        $this->assertEquals(9, $stream->tell());
        $stream->rewind();
        $this->assertEquals(0, $stream->tell());
    }

    public function testSeek()
    {
        $temp = tempnam(sys_get_temp_dir(), 'sandesh');
        $file = fopen($temp, 'w');
        fputs($file, 'Something');
        fclose($file);
        $stream = new Stream($temp);
        $stream->seek(4);
        $this->assertEquals(4, $stream->tell());
        $this->setExpectedException(\RuntimeException::class);
        $stream->seek(-4);
    }

    public function testSize()
    {
        $stream = new Stream();
        $stream->write($text = 'Something');
        $this->assertEquals(strlen($text), $stream->getSize());
    }

    public function testTell()
    {
        $resource = fopen('php://memory', 'w+');
        $stream = new Stream($resource);
        $stream->write('Something');
        fseek($resource, 4);
        $this->assertEquals(4, $stream->tell());
        $stream->seek(6);
        $this->assertEquals(6, $stream->tell());
    }

    public function testTellFailure()
    {
        $stream = new Stream();
        $stream->close();
        $this->setExpectedException(\RuntimeException::class);
        $stream->tell();
    }

    public function testWrite()
    {
        $stream = new Stream();
        $this->assertEquals(9, $stream->write('Something'));
        $this->assertEquals(9, $stream->getSize());
        $this->assertEquals('Something', (string)$stream);
    }

    public function testWriteFailure()
    {
        $stream = new Stream('php://memory', 'r');
        $this->setExpectedException(\RuntimeException::class);
        $stream->write('Something');
    }

    public function testToString()
    {
        $stream = new Stream('php://memory', 'w+');
        $stream->write('Something');
        $stream->write('.');
        $this->assertEquals('Something.', (string)$stream);
        $stream->detach();
        $this->assertEmpty((string)$stream);
    }
}
