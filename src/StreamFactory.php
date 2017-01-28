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

use Interop\Http\Factory\StreamFactoryInterface;

/**
 * Class StreamFactory
 * @package Sandesh
 */
class StreamFactory implements StreamFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function createStream($contents = '')
    {
        $stream = new Stream();
        if ($contents) {
            $stream->write($contents);
        }
        return $stream;
    }

    /**
     * {@inheritdoc}
     */
    public function createStreamFromFile($file, $mode = 'r')
    {
        return new Stream($file, $mode);
    }

    /**
     * {@inheritdoc}
     */
    public function createStreamFromResource($resource)
    {
        return new Stream($resource);
    }
}
