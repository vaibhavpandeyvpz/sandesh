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
 * Class Stream
 * @package Sandesh
 */
class Stream implements StreamInterface
{
    /**
     * @var resource
     */
    protected $resource;

    /**
     * Stream constructor.
     * @param string|resource $resource
     * @param string $mode
     */
    public function __construct($resource = 'php://memory', $mode = 'w+')
    {
        if (is_string($resource)) {
            $error = null;
            set_error_handler(function () use ($resource, $mode, &$error) {
                $error = new \RuntimeException("Failed to open '{$resource}' in '{$mode}' mode");
            });
            $resource = fopen($resource, $mode);
            restore_error_handler();
            if ($error instanceof \RuntimeException) {
                throw $error;
            }
            $this->resource = $resource;
        } elseif (is_resource($resource)) {
            $this->resource = $resource;
        } else {
            throw new \InvalidArgumentException('$resource must be either string or a valid resource');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        try {
            $this->rewind();
            return $this->getContents();
        } catch (\Exception $e) {
        }
        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function close()
    {
        $resource = $this->detach();
        fclose($resource);
    }

    /**
     * {@inheritdoc}
     */
    public function detach()
    {
        $resource = $this->resource;
        unset($this->resource);
        return $resource;
    }

    /**
     * {@inheritdoc}
     */
    public function eof()
    {
        if (isset($this->resource)) {
            return feof($this->resource);
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getContents()
    {
        if ($this->isReadable()) {
            $contents = stream_get_contents($this->resource);
            if (false !== $contents) {
                return $contents;
            }
            throw new \RuntimeException('Unable to get contents from underlying resource');
        }
        throw new \RuntimeException('Underlying resource is not readable');
    }

    /**
     * {@inheritdoc}
     */
    public function getMetadata($key = null)
    {
        $metadata = stream_get_meta_data($this->resource);
        if ($key) {
            $metadata = isset($metadata[$key]) ? $metadata[$key] : null;
        }
        return $metadata;
    }

    /**
     * {@inheritdoc}
     */
    public function getSize()
    {
        $stats = fstat($this->resource);
        return isset($stats['size']) ? (int)$stats['size'] : null;
    }

    /**
     * {@inheritdoc}
     */
    public function isReadable()
    {
        if (!isset($this->resource)) {
            return false;
        }
        $mode = $this->getMetadata('mode');
        return strstr($mode, 'r') || strstr($mode, '+');
    }

    /**
     * {@inheritdoc}
     */
    public function isSeekable()
    {
        if (!isset($this->resource)) {
            return false;
        }
        return $this->getMetadata('seekable');
    }

    /**
     * {@inheritdoc}
     */
    public function isWritable()
    {
        if (!isset($this->resource)) {
            return false;
        }
        $mode = $this->getMetadata('mode');
        return strstr($mode, 'x')
            || strstr($mode, 'w')
            || strstr($mode, 'c')
            || strstr($mode, 'a')
            || strstr($mode, '+');
    }

    /**
     * {@inheritdoc}
     */
    public function read($length)
    {
        if (!$this->isReadable()) {
            throw new \RuntimeException('Stream is not readable');
        }
        $contents = fread($this->resource, $length);
        if (false !== $contents) {
            return $contents;
        }
        throw new \RuntimeException('Unable to read from underlying resource');
    }

    /**
     * {@inheritdoc}
     */
    public function rewind()
    {
        $this->seek(0);
    }

    /**
     * {@inheritdoc}
     */
    public function seek($offset, $whence = SEEK_SET)
    {
        if (!$this->isSeekable()) {
            throw new \RuntimeException('Stream is not seekable');
        }
        if (0 !== fseek($this->resource, $offset, $whence)) {
            throw new \RuntimeException("Unable to seek stream upto offset {$offset}");
        }
    }

    /**
     * {@inheritdoc}
     */
    public function tell()
    {
        if (!isset($this->resource)) {
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
     */
    public function write($string)
    {
        if (!$this->isWritable()) {
            throw new \RuntimeException('Stream is not writable');
        }
        $result = fwrite($this->resource, $string);
        if (false !== $result) {
            return $result;
        }
        throw new \RuntimeException('Unable to write to underlying resource');
    }
}
