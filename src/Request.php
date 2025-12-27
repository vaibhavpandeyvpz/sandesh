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

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\UriInterface;

/**
 * HTTP request message implementation.
 *
 * Represents an HTTP request message as defined in PSR-7.
 * This class provides methods to access and modify the request method,
 * request target, and URI.
 */
class Request extends MessageAbstract implements RequestInterface
{
    /**
     * HTTP request method (e.g., 'GET', 'POST').
     */
    protected string $method;

    /**
     * The request target (e.g., '/path?query' or '*')
     */
    protected ?string $requestTarget = null;

    /**
     * The request URI.
     */
    protected ?UriInterface $uri = null;

    /**
     * Request constructor.
     *
     * @param  string  $method  HTTP method (default: 'GET')
     * @param  UriInterface|null  $uri  Request URI
     *
     * @throws \InvalidArgumentException If the method is invalid
     */
    public function __construct(string $method = 'GET', ?UriInterface $uri = null)
    {
        MessageValidations::assertMethod($method);
        $this->method = $method;
        $this->uri = $uri;
    }

    /**
     * {@inheritdoc}
     *
     * @return string The request method
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * {@inheritdoc}
     *
     * @return string The request target (e.g., '/path?query' or '*')
     */
    public function getRequestTarget(): string
    {
        if ($this->requestTarget !== null) {
            return $this->requestTarget;
        }
        if ($this->uri === null) {
            return '/';
        }
        $target = $this->uri->getPath();
        $query = $this->uri->getQuery();
        if ($query !== '') {
            $target .= "?{$query}";
        }

        return $target !== '' ? $target : '/';
    }

    /**
     * {@inheritdoc}
     *
     * @return UriInterface The request URI
     */
    public function getUri(): UriInterface
    {
        return $this->uri ?? new Uri;
    }

    /**
     * {@inheritdoc}
     *
     * @param  string  $method  Case-sensitive HTTP method
     * @return static A new instance with the specified method
     *
     * @throws \InvalidArgumentException For invalid HTTP methods
     */
    public function withMethod(string $method): static
    {
        MessageValidations::assertMethod($method);
        $clone = clone $this;
        $clone->method = $method;

        return $clone;
    }

    /**
     * {@inheritdoc}
     *
     * @param  string  $requestTarget  The request target (e.g., '/path?query' or '*')
     * @return static A new instance with the specified request target
     */
    public function withRequestTarget(string $requestTarget): static
    {
        $clone = clone $this;
        $clone->requestTarget = $requestTarget;

        return $clone;
    }

    /**
     * {@inheritdoc}
     *
     * @param  UriInterface  $uri  The new URI
     * @param  bool  $preserveHost  Preserve the original Host header if present
     * @return static A new instance with the specified URI
     */
    public function withUri(UriInterface $uri, bool $preserveHost = false): static
    {
        $clone = clone $this;
        $clone->uri = $uri;
        if ($preserveHost) {
            if ($this->hasHeader('Host')) {
                return $clone;
            }
            $host = $uri->getHost();
            if ($host !== '') {
                $port = $uri->getPort();
                if ($port !== null) {
                    $host .= ":{$port}";
                }

                return $clone->withHeader('Host', $host);
            }
        }

        return $clone;
    }
}
