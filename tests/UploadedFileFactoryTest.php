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
use Psr\Http\Message\UploadedFileFactoryInterface;
use Psr\Http\Message\UploadedFileInterface;

/**
 * Class UploadedFileFactoryTest
 * @package Sandesh
 */
class UploadedFileFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var UploadedFileFactoryInterface
     */
    protected $factory;

    public function testUploadedFile()
    {
        $factory = new UploadedFileFactory();
        $stream = (new StreamFactory())
            ->createStreamFromFile(__FILE__);
        $file = $factory->createUploadedFile(
            $stream,
            $size = filesize(__FILE__),
            UPLOAD_ERR_OK,
            $name = basename(__FILE__),
            'text/plain');
        $this->assertInstanceOf(UploadedFileInterface::class, $file);
        $this->assertEquals($name, $file->getClientFilename());
        $this->assertEquals('text/plain', $file->getClientMediaType());
        $this->assertEquals(UPLOAD_ERR_OK, $file->getError());
        $this->assertEquals($size, $file->getSize());
        $this->assertInstanceOf(StreamInterface::class, $file->getStream());
    }
}
