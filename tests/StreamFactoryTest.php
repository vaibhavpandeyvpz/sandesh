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
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;

/**
 * Class StreamFactoryTest
 */
class StreamFactoryTest extends TestCase
{
    protected StreamFactoryInterface $factory;

    protected function setUp(): void
    {
        $this->factory = new StreamFactory;
    }

    public function test_stream(): void
    {
        $stream = $this->factory->createStream();
        $this->assertInstanceOf(StreamInterface::class, $stream);
        $this->assertEmpty($stream->getContents());
        $text = 'Hello';
        $stream->write($text);
        $this->assertEquals($text, (string) $stream);
    }

    public function test_stream_from_string(): void
    {
        $text = 'Hello';
        $stream = $this->factory->createStream($text);
        $this->assertInstanceOf(StreamInterface::class, $stream);
        $this->assertEquals($text, (string) $stream);
    }

    public function test_stream_from_file(): void
    {
        $stream = $this->factory->createStreamFromFile(__FILE__, 'r');
        $this->assertInstanceOf(StreamInterface::class, $stream);
        $text = $stream->read(5);
        $this->assertNotEmpty($text);
        $this->assertEquals('<?php', $text);
        $stream->close();
    }

    public function test_stream_from_resource(): void
    {
        $handle = fopen(__FILE__, 'r');
        $stream = $this->factory->createStreamFromResource($handle);
        $this->assertInstanceOf(StreamInterface::class, $stream);
        $text = $stream->read(5);
        $this->assertNotEmpty($text);
        $this->assertEquals('<?php', $text);
        $stream->close();
    }

    public function test_stream_with_empty_string(): void
    {
        $stream = $this->factory->createStream('');
        $this->assertInstanceOf(StreamInterface::class, $stream);
        $this->assertEmpty((string) $stream);
    }

    public function test_stream_from_file_with_write_mode(): void
    {
        $tempFile = tempnam(sys_get_temp_dir(), 'sandesh');
        $stream = $this->factory->createStreamFromFile($tempFile, 'w');
        $stream->write('Test content');
        $stream->close();
        $this->assertEquals('Test content', file_get_contents($tempFile));
        unlink($tempFile);
    }

    public function test_stream_from_file_with_append_mode(): void
    {
        $tempFile = tempnam(sys_get_temp_dir(), 'sandesh');
        file_put_contents($tempFile, 'Original');
        $stream = $this->factory->createStreamFromFile($tempFile, 'a');
        $stream->write(' Appended');
        $stream->close();
        $this->assertStringContainsString('Original', file_get_contents($tempFile));
        $this->assertStringContainsString('Appended', file_get_contents($tempFile));
        unlink($tempFile);
    }

    public function test_stream_from_non_existent_file(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->factory->createStreamFromFile('/non/existent/file.txt', 'r');
    }
}
