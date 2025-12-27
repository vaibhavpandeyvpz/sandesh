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
use Psr\Http\Message\StreamInterface;

/**
 * HTTP response message implementation.
 *
 * Represents an HTTP response message as defined in PSR-7.
 * This class provides methods to access and modify the response status code,
 * reason phrase, and body.
 */
class Response extends MessageAbstract implements ResponseInterface
{
    /**
     * HTTP response reason phrase.
     */
    protected string $reasonPhrase = '';

    /**
     * HTTP response status code (e.g., 200, 404, 500).
     */
    protected int $statusCode;

    /**
     * Response constructor.
     *
     * @param  int  $code  HTTP status code (default: 200)
     * @param  string  $reasonPhrase  Reason phrase for the status code
     * @param  StreamInterface|string|resource|null  $body  Response body
     *
     * @throws \InvalidArgumentException If the status code is invalid
     */
    public function __construct(int $code = 200, string $reasonPhrase = '', mixed $body = null)
    {
        MessageValidations::assertStatusCode($code);
        $this->statusCode = $code;
        $this->reasonPhrase = $reasonPhrase;
        if ($body instanceof StreamInterface) {
            $this->body = $body;
        } elseif (is_resource($body)) {
            $this->body = new Stream($body, 'wb+');
        } elseif (is_string($body)) {
            $stream = new Stream('php://memory', 'wb+');
            $stream->write($body);
            $stream->rewind();
            $this->body = $stream;
        } else {
            $this->body = new Stream('php://memory', 'wb+');
        }
    }

    /**
     * {@inheritdoc}
     *
     * @return string The reason phrase
     */
    public function getReasonPhrase(): string
    {
        return $this->reasonPhrase;
    }

    /**
     * {@inheritdoc}
     *
     * @return int The status code
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * {@inheritdoc}
     *
     * @param  int  $code  The 3-digit integer result code to set
     * @param  string  $reasonPhrase  The reason phrase to use with the provided status code
     * @return static A new instance with the specified status code and reason phrase
     *
     * @throws \InvalidArgumentException For invalid status codes
     */
    public function withStatus(int $code, string $reasonPhrase = ''): static
    {
        MessageValidations::assertStatusCode($code);
        $clone = clone $this;
        $clone->statusCode = $code;
        $clone->reasonPhrase = $reasonPhrase;

        return $clone;
    }
}
