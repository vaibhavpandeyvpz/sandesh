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
 * Class UploadedFileTest
 * @package Sandesh
 */
class UploadedFileTest extends \PHPUnit_Framework_TestCase
{
    public function testCreation()
    {
        $file = new UploadedFile('php://memory', 128, UPLOAD_ERR_OK, 'somefile.txt', 'text/plain');
        $this->assertEquals('somefile.txt', $file->getClientFilename());
        $this->assertEquals('text/plain', $file->getClientMediaType());
        $this->assertEquals(UPLOAD_ERR_OK, $file->getError());
        $this->assertEquals(128, $file->getSize());
        $this->assertInstanceOf(StreamInterface::class, $file->getStream());
    }

    public function testCreationInvalidFile()
    {
        $this->setExpectedException(\InvalidArgumentException::class);
        new UploadedFile(1, '128', UPLOAD_ERR_OK);
    }

    public function testCreationInvalidSize()
    {
        $this->setExpectedException(\InvalidArgumentException::class);
        new UploadedFile('php://memory', '128', UPLOAD_ERR_OK);
    }

    public function testCreationInvalidError()
    {
        $this->setExpectedException(\InvalidArgumentException::class);
        new UploadedFile('php://memory', '128', UPLOAD_ERR_EXTENSION + 1);
    }

    public function testMove()
    {
        $source = tempnam(sys_get_temp_dir(), 'sandesh');
        $file = fopen($source, 'w');
        fputs($file, 'Something');
        fclose($file);
        $file = new UploadedFile($source, filesize($source), UPLOAD_ERR_OK, 'something.txt', 'text/plain');
        $file->moveTo(tempnam(sys_get_temp_dir(), 'sandesh'));
        $this->setExpectedException(\RuntimeException::class);
        $file->moveTo(tempnam(sys_get_temp_dir(), 'sandesh'));
    }
}
