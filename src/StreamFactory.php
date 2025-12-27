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

use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;

/**
 * Stream factory implementation.
 *
 * Creates Stream instances as defined in PSR-17.
 * This factory can create streams from strings, file paths, or resources.
 */
class StreamFactory implements StreamFactoryInterface
{
    /**
     * {@inheritdoc}
     *
     * @param  string  $content  Initial content for the stream
     * @return StreamInterface A new Stream instance
     */
    public function createStream(string $content = ''): StreamInterface
    {
        $stream = new Stream;
        if ($content !== '') {
            $stream->write($content);
        }

        return $stream;
    }

    /**
     * {@inheritdoc}
     *
     * @param  string  $filename  Path to the file
     * @param  string  $mode  File mode (default: 'r')
     * @return StreamInterface A new Stream instance
     *
     * @throws \RuntimeException If the file cannot be opened
     */
    public function createStreamFromFile(string $filename, string $mode = 'r'): StreamInterface
    {
        return new Stream($filename, $mode);
    }

    /**
     * {@inheritdoc}
     *
     * @param  resource  $resource  The resource to wrap
     * @return StreamInterface A new Stream instance
     */
    public function createStreamFromResource($resource): StreamInterface
    {
        return new Stream($resource);
    }
}
