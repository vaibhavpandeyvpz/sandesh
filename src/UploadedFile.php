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
use Psr\Http\Message\UploadedFileInterface;

/**
 * Class UploadedFile
 * @package Sandesh
 */
class UploadedFile implements UploadedFileInterface
{
    const WRITE_BUFFER = 8192;

    /**
     * @var string
     */
    protected $clientFilename;

    /**
     * @var string
     */
    protected $clientMediaType;

    /**
     * @var int
     */
    protected $error;

    /**
     * @var string
     */
    protected $file;

    /**
     * @var bool
     */
    protected $moved = false;

    /**
     * @var int
     */
    protected $size;

    /**
     * @var StreamInterface
     */
    protected $stream;

    /**
     * UploadedFile constructor.
     * @param string|resource $file
     * @param int $size
     * @param int $error
     * @param string $clientFilename
     * @param string $clientMediaType
     * @throws \InvalidArgumentException
     */
    public function __construct($file, $size, $error, $clientFilename = null, $clientMediaType = null)
    {
        if (is_int($error) && (0 <= $error) && (8 >= $error)) {
            $this->error = $error;
        } else {
            throw new \InvalidArgumentException('Error status must be one of UPLOAD_ERR_* constants');
        }
        if (UPLOAD_ERR_OK === $error) {
            if (is_string($file)) {
                $this->file = $file;
            } elseif (is_resource($file)) {
                $this->stream = new Stream($file);
            } elseif ($file instanceof StreamInterface) {
                $this->stream = $file;
            } else {
                throw new \InvalidArgumentException(
                    '$file must be a valid file path, a resource or an instance of Psr\\Http\\Message\\StreamInterface'
                );
            }
        }
        if (is_int($size)) {
            $this->size = $size;
        } else {
            throw new \InvalidArgumentException('Size of UploadedFile must be an integer');
        }
        $this->clientFilename = $clientFilename;
        $this->clientMediaType = $clientMediaType;
    }

    /**
     * {@inheritdoc}
     */
    public function getClientFilename()
    {
        return $this->clientFilename;
    }

    /**
     * {@inheritdoc}
     */
    public function getClientMediaType()
    {
        return $this->clientMediaType;
    }

    /**
     * {@inheritdoc}
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * {@inheritdoc}
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * {@inheritdoc}
     */
    public function getStream()
    {
        if (!$this->stream) {
            $this->stream = new Stream($this->file, 'r+');
        }
        return $this->stream;
    }

    /**
     * {@inheritdoc}
     */
    public function moveTo($targetPath)
    {
        if ($this->moved) {
            throw new \RuntimeException('This file has already been moved');
        }
        $src = $this->getStream();
        $src->rewind();
        $dest = new Stream($targetPath, 'w');
        while (!$src->eof()) {
            $data = $src->read(self::WRITE_BUFFER);
            if (!$dest->write($data)) {
                break;
            }
        }
        $src->close();
        $dest->close();
        $this->moved = true;
    }
}
