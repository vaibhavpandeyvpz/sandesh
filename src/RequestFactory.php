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

use Interop\Http\Factory\RequestFactoryInterface;

/**
 * Class RequestFactory
 * @package Sandesh
 */
class RequestFactory implements RequestFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function createRequest($method, $uri)
    {
        if (is_string($uri)) {
            $factory = new UriFactory();
            $uri = $factory->createUri($uri);
        }
        return new Request($method, $uri);
    }
}
