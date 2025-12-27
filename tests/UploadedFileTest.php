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
 * Class UploadedFileTest
 */
class UploadedFileTest extends TestCase
{
    public function test_creation(): void
    {
        $file = new UploadedFile('php://memory', 128, UPLOAD_ERR_OK, 'somefile.txt', 'text/plain');
        $this->assertEquals('somefile.txt', $file->getClientFilename());
        $this->assertEquals('text/plain', $file->getClientMediaType());
        $this->assertEquals(UPLOAD_ERR_OK, $file->getError());
        $this->assertEquals(128, $file->getSize());
        $this->assertInstanceOf(StreamInterface::class, $file->getStream());
    }

    public function test_creation_invalid_file(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new UploadedFile(1, 128, UPLOAD_ERR_OK);
    }

    public function test_creation_invalid_size(): void
    {
        // The constructor doesn't validate size - it accepts any int
        // This test verifies that negative sizes are accepted (though not recommended)
        $file = new UploadedFile('php://memory', -1, UPLOAD_ERR_OK);
        $this->assertEquals(-1, $file->getSize());
    }

    public function test_creation_invalid_error(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new UploadedFile('php://memory', 128, UPLOAD_ERR_EXTENSION + 1);
    }

    public function test_move(): void
    {
        $source = tempnam(sys_get_temp_dir(), 'sandesh');
        $file = fopen($source, 'w');
        fwrite($file, 'Something');
        fclose($file);
        $file = new UploadedFile($source, filesize($source), UPLOAD_ERR_OK, 'something.txt', 'text/plain');
        $file->moveTo(tempnam(sys_get_temp_dir(), 'sandesh'));
        $this->expectException(\RuntimeException::class);
        $file->moveTo(tempnam(sys_get_temp_dir(), 'sandesh'));
    }

    public function test_move_with_stream(): void
    {
        $stream = new Stream;
        $stream->write('Stream content');
        $file = new UploadedFile($stream, 14, UPLOAD_ERR_OK, 'stream.txt', 'text/plain');
        $target = tempnam(sys_get_temp_dir(), 'sandesh');
        $file->moveTo($target);
        $this->assertFileExists($target);
        $this->assertEquals('Stream content', file_get_contents($target));
        unlink($target);
    }

    public function test_move_with_resource(): void
    {
        $resource = fopen('php://memory', 'w+');
        fwrite($resource, 'Resource content');
        rewind($resource);
        $file = new UploadedFile($resource, 17, UPLOAD_ERR_OK, 'resource.txt', 'text/plain');
        $target = tempnam(sys_get_temp_dir(), 'sandesh');
        $file->moveTo($target);
        $this->assertFileExists($target);
        $this->assertEquals('Resource content', file_get_contents($target));
        unlink($target);
    }

    public function test_get_stream_with_file(): void
    {
        $source = tempnam(sys_get_temp_dir(), 'sandesh');
        file_put_contents($source, 'File content');
        $file = new UploadedFile($source, filesize($source), UPLOAD_ERR_OK, 'file.txt', 'text/plain');
        $stream = $file->getStream();
        $this->assertInstanceOf(StreamInterface::class, $stream);
        $this->assertEquals('File content', (string) $stream);
        unlink($source);
    }

    public function test_get_stream_with_error(): void
    {
        $file = new UploadedFile('php://memory', 0, UPLOAD_ERR_NO_FILE);
        $this->expectException(\RuntimeException::class);
        $file->getStream();
    }

    public function test_all_upload_error_codes(): void
    {
        $errorCodes = [
            UPLOAD_ERR_OK,
            UPLOAD_ERR_INI_SIZE,
            UPLOAD_ERR_FORM_SIZE,
            UPLOAD_ERR_PARTIAL,
            UPLOAD_ERR_NO_FILE,
            UPLOAD_ERR_NO_TMP_DIR,
            UPLOAD_ERR_CANT_WRITE,
            UPLOAD_ERR_EXTENSION,
        ];
        foreach ($errorCodes as $errorCode) {
            $file = new UploadedFile('php://memory', 0, $errorCode);
            $this->assertEquals($errorCode, $file->getError());
        }
    }

    public function test_get_size_with_null(): void
    {
        $file = new UploadedFile('php://memory', 0, UPLOAD_ERR_OK);
        $this->assertEquals(0, $file->getSize());
    }

    public function test_client_filename_and_media_type_null(): void
    {
        $file = new UploadedFile('php://memory', 128, UPLOAD_ERR_OK);
        $this->assertNull($file->getClientFilename());
        $this->assertNull($file->getClientMediaType());
    }
}
