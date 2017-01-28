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
        $file = $factory->createUploadedFile(__FILE__, $size = filesize(__FILE__), UPLOAD_ERR_OK, $name = basename(__FILE__), 'text/plain');
        $this->assertInstanceOf('Psr\\Http\\Message\\UploadedFileInterface', $file);
        $this->assertEquals($name, $file->getClientFilename());
        $this->assertEquals('text/plain', $file->getClientMediaType());
        $this->assertEquals(UPLOAD_ERR_OK, $file->getError());
        $this->assertEquals($size, $file->getSize());
        $this->assertInstanceOf('Psr\\Http\\Message\\StreamInterface', $file->getStream());
    }
}
