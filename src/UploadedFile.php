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

use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UploadedFileInterface;

/**
 * Uploaded file implementation.
 *
 * Represents an uploaded file as defined in PSR-7.
 * This class provides methods to access uploaded file metadata
 * and move the file to a target location.
 */
class UploadedFile implements UploadedFileInterface
{
    /**
     * Write buffer size for file operations (8KB).
     *
     * @var int
     */
    private const WRITE_BUFFER = 8192;

    /**
     * The client-provided filename.
     */
    protected ?string $clientFilename = null;

    /**
     * The client-provided media type.
     */
    protected ?string $clientMediaType = null;

    /**
     * Upload error code (UPLOAD_ERR_* constant).
     */
    protected int $error;

    /**
     * Path to the uploaded file (if stored as file path).
     */
    protected ?string $file = null;

    /**
     * Whether the file has been moved.
     */
    protected bool $moved = false;

    /**
     * File size in bytes.
     */
    protected int $size;

    /**
     * Stream interface for the uploaded file (if stored as stream).
     */
    protected ?StreamInterface $stream = null;

    /**
     * UploadedFile constructor.
     *
     * @param  StreamInterface|string|resource  $file  The file (stream, path, or resource)
     * @param  int  $size  File size in bytes
     * @param  int  $error  Upload error code (UPLOAD_ERR_* constant)
     * @param  string|null  $clientFilename  The client-provided filename
     * @param  string|null  $clientMediaType  The client-provided media type
     *
     * @throws \InvalidArgumentException If error code is invalid or file type is invalid
     */
    public function __construct(
        mixed $file,
        int $size,
        int $error,
        ?string $clientFilename = null,
        ?string $clientMediaType = null
    ) {
        if ($error < 0 || $error > 8) {
            throw new \InvalidArgumentException('Error status must be one of UPLOAD_ERR_* constants');
        }
        $this->error = $error;
        if ($error === UPLOAD_ERR_OK) {
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
        $this->size = $size;
        $this->clientFilename = $clientFilename;
        $this->clientMediaType = $clientMediaType;
    }

    /**
     * {@inheritdoc}
     *
     * @return string|null The client-provided filename, or null if not provided
     */
    public function getClientFilename(): ?string
    {
        return $this->clientFilename;
    }

    /**
     * {@inheritdoc}
     *
     * @return string|null The client-provided media type, or null if not provided
     */
    public function getClientMediaType(): ?string
    {
        return $this->clientMediaType;
    }

    /**
     * {@inheritdoc}
     *
     * @return int The upload error code (UPLOAD_ERR_* constant)
     */
    public function getError(): int
    {
        return $this->error;
    }

    /**
     * {@inheritdoc}
     *
     * @return int|null The file size in bytes, or null if unknown
     */
    public function getSize(): ?int
    {
        return $this->size;
    }

    /**
     * {@inheritdoc}
     *
     * @return StreamInterface A stream representing the uploaded file
     *
     * @throws \RuntimeException If no file or stream is available
     */
    public function getStream(): StreamInterface
    {
        if ($this->stream === null) {
            if ($this->file === null) {
                throw new \RuntimeException('No file or stream available');
            }
            $this->stream = new Stream($this->file, 'r+');
        }

        return $this->stream;
    }

    /**
     * {@inheritdoc}
     *
     * @param  string  $targetPath  Path to which to move the uploaded file
     *
     * @throws \RuntimeException If the file has already been moved or if the move fails
     */
    public function moveTo(string $targetPath): void
    {
        if ($this->moved) {
            throw new \RuntimeException('This file has already been moved');
        }
        $src = $this->getStream();
        $src->rewind();
        $dest = new Stream($targetPath, 'w');
        while (! $src->eof()) {
            $data = $src->read(self::WRITE_BUFFER);
            $dest->write($data);
        }
        $src->close();
        $dest->close();
        $this->moved = true;
    }
}
