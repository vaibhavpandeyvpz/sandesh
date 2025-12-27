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
 * Class StreamTest
 */
class StreamTest extends TestCase
{
    public function test_stream_creation(): void
    {
        $this->assertInstanceOf(
            StreamInterface::class,
            new Stream
        );
        $this->assertInstanceOf(
            StreamInterface::class,
            new Stream(fopen('php://memory', 'w+'))
        );
        $this->expectException(\InvalidArgumentException::class);
        new Stream(9999);
    }

    public function test_close(): void
    {
        $stream = new Stream;
        $this->assertTrue($stream->isReadable());
        $this->assertTrue($stream->isSeekable());
        $this->assertTrue($stream->isWritable());
        $stream->close();
        $this->assertFalse($stream->isReadable());
        $this->assertFalse($stream->isSeekable());
        $this->assertFalse($stream->isWritable());
    }

    public function test_detach(): void
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

    public function test_eof(): void
    {
        $resource = fopen('php://memory', 'w+');
        fwrite($resource, 'Something');
        fseek($resource, 0);
        $stream = new Stream($resource);
        $this->assertFalse($stream->eof());
        while (feof($resource) === false) {
            fread($resource, 4);
        }
        $this->assertTrue($stream->eof());
        fseek($resource, 0);
        $this->assertFalse($stream->eof());
        $stream->detach();
        $this->assertTrue($stream->eof());
    }

    public function test_contents(): void
    {
        $stream = new Stream;
        $this->assertEmpty($stream->getContents());
        $stream->write('Some');
        $stream->rewind();
        $this->assertEquals('Some', $stream->getContents());
        $stream->write('thing');
        $stream->rewind();
        $this->assertEquals('Something', $stream->getContents());
        $stream->detach();
        $this->expectException(\RuntimeException::class);
        $stream->getContents();
    }

    public function test_readable(): void
    {
        $stream = new Stream('php://memory', 'r');
        $this->assertTrue($stream->isReadable());
        $this->assertFalse($stream->isWritable());
        $tempnam = tempnam(sys_get_temp_dir(), 'rndm');
        $stream = new Stream($tempnam, 'w');
        $this->assertFalse($stream->isReadable());
        $stream->close();
        unlink($tempnam);
    }

    public function test_writable(): void
    {
        $stream = new Stream('php://memory', 'r');
        $this->assertFalse($stream->isWritable());
        $stream = new Stream('php://memory', 'w');
        $this->assertTrue($stream->isWritable());
    }

    public function test_read(): void
    {
        $stream = new Stream;
        $stream->write('Something');
        $stream->seek(0);
        $this->assertEquals('Some', $stream->read(4));
        $this->assertEquals('thing', $stream->read(5));
    }

    public function test_read_failure(): void
    {
        $stream = new Stream('php://memory');
        $stream->close();
        $this->expectException(\RuntimeException::class);
        $stream->read(1);
    }

    public function test_rewind(): void
    {
        $stream = new Stream;
        $stream->write('Something');
        while (! $stream->eof()) {
            $stream->read(4);
        }
        $this->assertTrue($stream->eof());
        $this->assertEquals(9, $stream->tell());
        $stream->rewind();
        $this->assertEquals(0, $stream->tell());
    }

    public function test_seek(): void
    {
        $temp = tempnam(sys_get_temp_dir(), 'sandesh');
        $file = fopen($temp, 'w');
        fwrite($file, 'Something');
        fclose($file);
        $stream = new Stream($temp);
        $stream->seek(4);
        $this->assertEquals(4, $stream->tell());
        $stream->close();
        unlink($temp);
        $this->expectException(\RuntimeException::class);
        $stream->seek(-4);
    }

    public function test_size(): void
    {
        $stream = new Stream;
        $text = 'Something';
        $stream->write($text);
        $this->assertEquals(strlen($text), $stream->getSize());
    }

    public function test_tell(): void
    {
        $resource = fopen('php://memory', 'w+');
        $stream = new Stream($resource);
        $stream->write('Something');
        fseek($resource, 4);
        $this->assertEquals(4, $stream->tell());
        $stream->seek(6);
        $this->assertEquals(6, $stream->tell());
    }

    public function test_tell_failure(): void
    {
        $stream = new Stream;
        $stream->close();
        $this->expectException(\RuntimeException::class);
        $stream->tell();
    }

    public function test_write(): void
    {
        $stream = new Stream;
        $this->assertEquals(9, $stream->write('Something'));
        $this->assertEquals(9, $stream->getSize());
        $this->assertEquals('Something', (string) $stream);
    }

    public function test_write_failure(): void
    {
        $stream = new Stream('php://memory', 'r');
        $this->expectException(\RuntimeException::class);
        $stream->write('Something');
    }

    public function test_to_string(): void
    {
        $stream = new Stream('php://memory', 'w+');
        $stream->write('Something');
        $stream->write('.');
        $this->assertEquals('Something.', (string) $stream);
        $stream->detach();
        $this->assertEmpty((string) $stream);
    }

    public function test_seek_with_seek_cur(): void
    {
        $stream = new Stream;
        $stream->write('Something');
        $stream->seek(0);
        $stream->seek(4, SEEK_CUR);
        $this->assertEquals(4, $stream->tell());
    }

    public function test_seek_with_seek_end(): void
    {
        $stream = new Stream;
        $stream->write('Something');
        $stream->seek(-4, SEEK_END);
        $this->assertEquals(5, $stream->tell());
        $this->assertEquals('hing', $stream->read(4));
    }

    public function test_get_metadata(): void
    {
        $stream = new Stream;
        $metadata = $stream->getMetadata();
        $this->assertIsArray($metadata);
        $this->assertArrayHasKey('mode', $metadata);
        $mode = $stream->getMetadata('mode');
        $this->assertIsString($mode);
        $this->assertNull($stream->getMetadata('non-existent-key'));
    }

    public function test_get_size_when_unknown(): void
    {
        $stream = new Stream('php://input', 'r');
        // php://input may not have a known size
        $size = $stream->getSize();
        $this->assertTrue($size === null || is_int($size));
    }

    public function test_read_beyond_eof(): void
    {
        $stream = new Stream;
        $stream->write('Test');
        $stream->rewind();
        $this->assertEquals('Test', $stream->read(10));
        $this->assertTrue($stream->eof());
    }

    public function test_write_failure_on_closed_stream(): void
    {
        $stream = new Stream;
        $stream->close();
        $this->expectException(\RuntimeException::class);
        $stream->write('test');
    }

    public function test_get_contents_failure(): void
    {
        // php://memory in 'w' mode might still be readable in some PHP versions
        // Instead, test with a truly non-readable stream or closed stream
        $stream = new Stream;
        $stream->close();
        $this->expectException(\RuntimeException::class);
        $stream->getContents();
    }

    public function test_seek_failure_on_non_seekable(): void
    {
        $stream = new Stream('php://input', 'r');
        if (! $stream->isSeekable()) {
            $this->expectException(\RuntimeException::class);
            $stream->seek(0);
        } else {
            $this->markTestSkipped('php://input is seekable in this environment');
        }
    }

    public function test_tell_after_detach(): void
    {
        $stream = new Stream;
        $stream->detach();
        $this->expectException(\RuntimeException::class);
        $stream->tell();
    }
}
