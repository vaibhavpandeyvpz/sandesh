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
 * Server-side HTTP response sender.
 *
 * Sends HTTP responses to the client by outputting status line, headers, and body.
 * Handles output buffering and automatically adds Content-Length header when possible.
 */
class ServerResponseSender implements ResponseSenderInterface
{
    /**
     * {@inheritdoc}
     *
     * Sends the HTTP response to the client.
     * Outputs the status line, all headers, and the response body.
     * Automatically adds Content-Length header if not present and body size is known.
     *
     * @param  ResponseInterface  $response  The response to send
     * @param  int|null  $obl  Output buffer level to maintain (null = current level)
     *
     * @throws \RuntimeException If headers have already been sent
     */
    public function send(ResponseInterface $response, ?int $obl = null): void
    {
        if (headers_sent()) {
            throw new \RuntimeException('Not sending response as headers already sent.');
        }
        if ($obl === null) {
            $obl = ob_get_level();
        }
        while (ob_get_level() > $obl) {
            ob_end_flush();
        }
        $size = $response->getBody()->getSize();
        if (! $response->hasHeader('Content-Length') && is_int($size)) {
            $response = $response->withHeader('Content-Length', (string) $size);
        }
        $this->sendStatusLine($response);
        $this->sendHeaders($response);
        $this->sendBody($response);
    }

    /**
     * Send the response body.
     *
     * Outputs the response body stream to the client.
     *
     * @param  ResponseInterface  $response  The response containing the body
     */
    protected function sendBody(ResponseInterface $response): void
    {
        echo $response->getBody();
    }

    /**
     * Send all response headers.
     *
     * Outputs all response headers using PHP's header() function.
     * Normalizes header names to proper case (e.g., 'content-type' -> 'Content-Type').
     *
     * @param  ResponseInterface  $response  The response containing the headers
     */
    protected function sendHeaders(ResponseInterface $response): void
    {
        foreach ($response->getHeaders() as $header => $values) {
            $name = str_replace(' ', '-', ucwords(str_replace('-', ' ', $header)));
            foreach ($values as $i => $value) {
                header(sprintf('%s: %s', $name, $value), $i === 0);
            }
        }
    }

    /**
     * Send the HTTP status line.
     *
     * Outputs the HTTP status line (e.g., "HTTP/1.1 200 OK").
     *
     * @param  ResponseInterface  $response  The response containing status information
     */
    protected function sendStatusLine(ResponseInterface $response): void
    {
        header(trim(sprintf(
            'HTTP/%s %d %s',
            $response->getProtocolVersion(),
            $response->getStatusCode(),
            $response->getReasonPhrase()
        )));
    }
}
