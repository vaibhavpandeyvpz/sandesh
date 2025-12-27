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

/**
 * Stream implementation.
 *
 * Represents a data stream as defined in PSR-7.
 * This class provides methods to read from, write to, and manipulate
 * a stream resource (file handle, memory stream, etc.).
 */
class Stream implements StreamInterface
{
    /**
     * The underlying stream resource.
     *
     * @var resource|null
     *
     * @phpstan-ignore-next-line - resource type not fully supported
     */
    protected $resource;

    /**
     * Stream constructor.
     *
     * @param  string|resource  $resource  Stream resource or file path
     * @param  string  $mode  File mode (default: 'w+')
     *
     * @throws \RuntimeException If the stream cannot be opened
     * @throws \InvalidArgumentException If the resource is invalid
     */
    public function __construct(mixed $resource = 'php://memory', string $mode = 'w+')
    {
        if (is_string($resource)) {
            $error = null;
            set_error_handler(function () use ($resource, $mode, &$error) {
                $error = new \RuntimeException("Failed to open '{$resource}' in '{$mode}' mode");
            });
            $handle = fopen($resource, $mode);
            restore_error_handler();
            if ($error instanceof \RuntimeException) {
                throw $error;
            }
            if ($handle === false) {
                throw new \RuntimeException("Failed to open '{$resource}' in '{$mode}' mode");
            }
            $this->resource = $handle;
        } elseif (is_resource($resource)) {
            $this->resource = $resource;
        } else {
            throw new \InvalidArgumentException('$resource must be either string or a valid resource');
        }
    }

    /**
     * {@inheritdoc}
     *
     * Reads all data from the stream into a string.
     *
     * @return string The stream contents as a string
     */
    public function __toString(): string
    {
        try {
            $this->rewind();

            return $this->getContents();
        } catch (\Exception) {
        }

        return '';
    }

    /**
     * {@inheritdoc}
     *
     * Closes the stream and any underlying resources.
     */
    public function close(): void
    {
        $resource = $this->detach();
        if (is_resource($resource)) {
            fclose($resource);
        }
    }

    /**
     * {@inheritdoc}
     *
     * Separates any underlying resources from the stream.
     *
     * @return resource|null The underlying resource, if any
     */
    public function detach()
    {
        $resource = $this->resource;
        unset($this->resource);

        return $resource;
    }

    /**
     * {@inheritdoc}
     *
     * @return bool Returns true if the stream is at end-of-file
     */
    public function eof(): bool
    {
        if (isset($this->resource)) {
            return feof($this->resource);
        }

        return true;
    }

    /**
     * {@inheritdoc}
     *
     * @return string The remaining contents in the stream
     *
     * @throws \RuntimeException If unable to read or the stream is not readable
     */
    public function getContents(): string
    {
        if ($this->isReadable()) {
            $contents = stream_get_contents($this->resource);
            if ($contents !== false) {
                return $contents;
            }
            throw new \RuntimeException('Unable to get contents from underlying resource');
        }
        throw new \RuntimeException('Underlying resource is not readable');
    }

    /**
     * {@inheritdoc}
     *
     * @param  string|null  $key  Specific metadata to retrieve
     * @return mixed|array<string, mixed> If no key is provided, returns an associative array
     *                                    of all metadata. If a key is provided, returns the value
     *                                    for that key, or null if the key does not exist
     */
    public function getMetadata(?string $key = null): mixed
    {
        $metadata = stream_get_meta_data($this->resource);
        if ($key !== null) {
            return $metadata[$key] ?? null;
        }

        return $metadata;
    }

    /**
     * {@inheritdoc}
     *
     * @return int|null Returns the size in bytes if known, or null if unknown
     */
    public function getSize(): ?int
    {
        $stats = fstat($this->resource);

        return isset($stats['size']) ? (int) $stats['size'] : null;
    }

    /**
     * {@inheritdoc}
     *
     * @return bool Whether the stream is readable
     */
    public function isReadable(): bool
    {
        if (! isset($this->resource)) {
            return false;
        }
        $mode = $this->getMetadata('mode');

        return is_string($mode) && (str_contains($mode, 'r') || str_contains($mode, '+'));
    }

    /**
     * {@inheritdoc}
     *
     * @return bool Whether the stream is seekable
     */
    public function isSeekable(): bool
    {
        if (! isset($this->resource)) {
            return false;
        }

        return (bool) $this->getMetadata('seekable');
    }

    /**
     * {@inheritdoc}
     *
     * @return bool Whether the stream is writable
     */
    public function isWritable(): bool
    {
        if (! isset($this->resource)) {
            return false;
        }
        $mode = $this->getMetadata('mode');
        if (! is_string($mode)) {
            return false;
        }

        return str_contains($mode, 'x')
            || str_contains($mode, 'w')
            || str_contains($mode, 'c')
            || str_contains($mode, 'a')
            || str_contains($mode, '+');
    }

    /**
     * {@inheritdoc}
     *
     * @param  int  $length  Read up to $length bytes from the stream
     * @return string The data read from the stream
     *
     * @throws \RuntimeException If unable to read or the stream is not readable
     */
    public function read(int $length): string
    {
        if (! $this->isReadable()) {
            throw new \RuntimeException('Stream is not readable');
        }
        $contents = fread($this->resource, $length);
        if ($contents !== false) {
            return $contents;
        }
        throw new \RuntimeException('Unable to read from underlying resource');
    }

    /**
     * {@inheritdoc}
     *
     * Seek to the beginning of the stream.
     *
     * @throws \RuntimeException If the stream is not seekable
     */
    public function rewind(): void
    {
        $this->seek(0);
    }

    /**
     * {@inheritdoc}
     *
     * @param  int  $offset  Stream offset
     * @param  int  $whence  Specifies how the cursor position will be calculated
     *                       (SEEK_SET, SEEK_CUR, or SEEK_END)
     *
     * @throws \RuntimeException On failure
     */
    public function seek(int $offset, int $whence = SEEK_SET): void
    {
        if (! $this->isSeekable()) {
            throw new \RuntimeException('Stream is not seekable');
        }
        if (fseek($this->resource, $offset, $whence) !== 0) {
            throw new \RuntimeException("Unable to seek stream upto offset {$offset}");
        }
    }

    /**
     * {@inheritdoc}
     *
     * @return int Position of the file read/write pointer
     *
     * @throws \RuntimeException On error
     */
    public function tell(): int
    {
        if (! isset($this->resource)) {
            throw new \RuntimeException('Cannot determine position from detached resource');
        }
        $position = ftell($this->resource);
        if ($position !== false) {
            return $position;
        }
        throw new \RuntimeException('Unable to determine stream position');
    }

    /**
     * {@inheritdoc}
     *
     * @param  string  $string  The string to write
     * @return int Returns the number of bytes written
     *
     * @throws \RuntimeException On failure
     */
    public function write(string $string): int
    {
        if (! $this->isWritable()) {
            throw new \RuntimeException('Stream is not writable');
        }
        $result = fwrite($this->resource, $string);
        if ($result !== false) {
            return $result;
        }
        throw new \RuntimeException('Unable to write to underlying resource');
    }
}
