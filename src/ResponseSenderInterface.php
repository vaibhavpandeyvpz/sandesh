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

use Psr\Http\Message\ResponseInterface;

/**
 * Interface for sending HTTP responses.
 *
 * Defines the contract for classes that send HTTP responses to clients.
 * Implementations should handle output buffering, headers, and response body.
 */
interface ResponseSenderInterface
{
    /**
     * Send an HTTP response to the client.
     *
     * Outputs the status line, headers, and body of the response.
     *
     * @param  ResponseInterface  $response  The response to send
     * @param  int|null  $obl  Output buffer level to maintain (null = current level)
     *
     * @throws \RuntimeException If headers have already been sent
     */
    public function send(ResponseInterface $response, ?int $obl = null): void;
}
