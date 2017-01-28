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

use Interop\Http\Factory\ResponseFactoryInterface;

/**
 * Class ResponseFactory
 * @package Sandesh
 */
class ResponseFactory implements ResponseFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function createResponse($code = 200)
    {
        return new Response($code);
    }
}
