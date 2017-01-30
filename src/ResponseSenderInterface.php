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

use Psr\Http\Message\ResponseInterface;

/**
 * Interface ResponseSenderInterface
 * @package Sandesh
 */
interface ResponseSenderInterface
{
    /**
     * @param ResponseInterface $response
     * @param int $obl
     */
    public function send(ResponseInterface $response, $obl= null);
}
