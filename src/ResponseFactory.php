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

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * HTTP response factory implementation.
 *
 * Creates Response instances as defined in PSR-17.
 * This factory can create responses with various status codes and reason phrases.
 */
class ResponseFactory implements ResponseFactoryInterface
{
    /**
     * {@inheritdoc}
     *
     * @param  int  $code  HTTP status code (default: 200)
     * @param  string  $reasonPhrase  Reason phrase for the status code
     * @return ResponseInterface A new Response instance
     *
     * @throws \InvalidArgumentException If the status code is invalid
     */
    public function createResponse(int $code = 200, string $reasonPhrase = ''): ResponseInterface
    {
        return new Response($code, $reasonPhrase);
    }
}
