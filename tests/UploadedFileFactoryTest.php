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
use Psr\Http\Message\UploadedFileInterface;

/**
 * Class UploadedFileFactoryTest
 */
class UploadedFileFactoryTest extends TestCase
{
    public function test_uploaded_file(): void
    {
        $factory = new UploadedFileFactory;
        $stream = (new StreamFactory)
            ->createStreamFromFile(__FILE__);
        $size = filesize(__FILE__);
        $name = basename(__FILE__);
        $file = $factory->createUploadedFile(
            $stream,
            $size,
            UPLOAD_ERR_OK,
            $name,
            'text/plain');
        $this->assertInstanceOf(UploadedFileInterface::class, $file);
        $this->assertEquals($name, $file->getClientFilename());
        $this->assertEquals('text/plain', $file->getClientMediaType());
        $this->assertEquals(UPLOAD_ERR_OK, $file->getError());
        $this->assertEquals($size, $file->getSize());
        $this->assertInstanceOf(StreamInterface::class, $file->getStream());
    }

    public function test_create_uploaded_file_with_null_values(): void
    {
        $factory = new UploadedFileFactory;
        $stream = (new StreamFactory)->createStream('content');
        $file = $factory->createUploadedFile($stream, 7, UPLOAD_ERR_OK, null, null);
        $this->assertInstanceOf(UploadedFileInterface::class, $file);
        $this->assertNull($file->getClientFilename());
        $this->assertNull($file->getClientMediaType());
    }

    public function test_create_uploaded_file_with_error(): void
    {
        $factory = new UploadedFileFactory;
        $stream = (new StreamFactory)->createStream('content');
        $file = $factory->createUploadedFile($stream, 0, UPLOAD_ERR_NO_FILE, 'error.txt', 'text/plain');
        $this->assertEquals(UPLOAD_ERR_NO_FILE, $file->getError());
    }

    public function test_create_uploaded_files_with_nested_array(): void
    {
        $tempFile1 = tempnam(sys_get_temp_dir(), 'sandesh1');
        $tempFile2 = tempnam(sys_get_temp_dir(), 'sandesh2');
        file_put_contents($tempFile1, 'content1');
        file_put_contents($tempFile2, 'content2');

        $factory = new UploadedFileFactory;
        $files = [
            'file1' => [
                'tmp_name' => $tempFile1,
                'size' => filesize($tempFile1),
                'error' => UPLOAD_ERR_OK,
                'name' => 'file1.txt',
                'type' => 'text/plain',
            ],
            'files' => [
                'file2' => [
                    'tmp_name' => $tempFile2,
                    'size' => filesize($tempFile2),
                    'error' => UPLOAD_ERR_OK,
                    'name' => 'file2.txt',
                    'type' => 'text/plain',
                ],
            ],
        ];
        $uploadedFiles = $factory->createUploadedFiles($files);
        $this->assertIsArray($uploadedFiles);
        $this->assertArrayHasKey('file1', $uploadedFiles);
        $this->assertInstanceOf(UploadedFileInterface::class, $uploadedFiles['file1']);
        $this->assertArrayHasKey('files', $uploadedFiles);
        $this->assertIsArray($uploadedFiles['files']);
        $this->assertArrayHasKey('file2', $uploadedFiles['files']);
        $this->assertInstanceOf(UploadedFileInterface::class, $uploadedFiles['files']['file2']);

        unlink($tempFile1);
        unlink($tempFile2);
    }

    public function test_create_uploaded_files_with_multiple_files(): void
    {
        $tempFile1 = tempnam(sys_get_temp_dir(), 'sandesh1');
        $tempFile2 = tempnam(sys_get_temp_dir(), 'sandesh2');
        file_put_contents($tempFile1, 'content1');
        file_put_contents($tempFile2, 'content2');

        $factory = new UploadedFileFactory;
        // Structure it as a single file entry with array values (like $_FILES['files'])
        $files = [
            'files' => [
                'tmp_name' => [$tempFile1, $tempFile2],
                'size' => [filesize($tempFile1), filesize($tempFile2)],
                'error' => [UPLOAD_ERR_OK, UPLOAD_ERR_OK],
                'name' => ['file1.txt', 'file2.txt'],
                'type' => ['text/plain', 'text/plain'],
            ],
        ];
        $uploadedFiles = $factory->createUploadedFiles($files);
        $this->assertIsArray($uploadedFiles);
        $this->assertArrayHasKey('files', $uploadedFiles);
        $fileArray = $uploadedFiles['files'];
        $this->assertIsArray($fileArray);
        $this->assertCount(2, $fileArray);
        $this->assertInstanceOf(UploadedFileInterface::class, $fileArray[0]);
        $this->assertInstanceOf(UploadedFileInterface::class, $fileArray[1]);

        unlink($tempFile1);
        unlink($tempFile2);
    }
}
