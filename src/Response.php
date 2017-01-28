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
use Psr\Http\Message\StreamInterface;

/**
 * Class Response
 * @package Sandesh
 */
class Response extends MessageAbstract implements ResponseInterface
{
    /**
     * @var string
     */
    protected $reasonPhrase = '';

    /**
     * @var int
     */
    protected $statusCode;

    /**
     * Response constructor.
     * @param int $code
     * @param string $body
     */
    public function __construct($code = 200, $body = 'php://memory')
    {
        MessageValidations::assertStatusCode($code);
        $this->statusCode = $code;
        if ($body instanceof StreamInterface) {
            $this->body = $body;
        } elseif (is_resource($body) || is_string($body)) {
            $this->body = new Stream($body, 'wb+');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getReasonPhrase()
    {
        return $this->reasonPhrase;
    }

    /**
     * {@inheritdoc}
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * {@inheritdoc}
     */
    public function withStatus($code, $reasonPhrase = '')
    {
        MessageValidations::assertStatusCode($code);
        $clone = clone $this;
        $clone->statusCode = $code;
        $clone->reasonPhrase = $reasonPhrase;
        return $clone;
    }
}
