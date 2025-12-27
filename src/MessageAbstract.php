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

use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\StreamInterface;

/**
 * Abstract base class for HTTP messages.
 *
 * Provides common functionality for HTTP request and response messages
 * as defined in PSR-7. This class implements the MessageInterface and
 * handles headers, protocol version, and message body.
 */
abstract class MessageAbstract implements MessageInterface
{
    /**
     * The message body stream.
     */
    protected ?StreamInterface $body = null;

    /**
     * Normalized header names mapped to their original case.
     *
     * Keys are lowercase header names, values are the original case.
     *
     * @var array<string, string>
     */
    protected array $headerNames = [];

    /**
     * Message headers.
     *
     * Keys are header names in their original case, values are arrays of header values.
     *
     * @var array<string, array<string>>
     */
    protected array $headers = [];

    /**
     * HTTP protocol version (e.g., '1.1', '2.0').
     */
    protected string $protocolVersion = '1.1';

    /**
     * {@inheritdoc}
     *
     * @return StreamInterface The message body stream
     */
    public function getBody(): StreamInterface
    {
        return $this->body ?? new Stream;
    }

    /**
     * {@inheritdoc}
     *
     * @param  string  $name  Case-insensitive header field name
     * @return array<string> An array of string values for the header
     */
    public function getHeader(string $name): array
    {
        if ($this->hasHeader($name)) {
            $name = $this->headerNames[strtolower($name)];

            return $this->headers[$name];
        }

        return [];
    }

    /**
     * {@inheritdoc}
     *
     * @param  string  $name  Case-insensitive header field name
     * @return string A string of all header values concatenated with commas
     */
    public function getHeaderLine(string $name): string
    {
        $values = $this->getHeader($name);

        return $values !== [] ? implode(',', $values) : '';
    }

    /**
     * {@inheritdoc}
     *
     * @return array<string, array<string>> Returns an associative array of all message headers
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * {@inheritdoc}
     *
     * @return string Protocol version string (e.g., '1.1', '2.0')
     */
    public function getProtocolVersion(): string
    {
        return $this->protocolVersion;
    }

    /**
     * {@inheritdoc}
     *
     * @param  string  $name  Case-insensitive header field name
     * @return bool Returns true if the header exists, false otherwise
     */
    public function hasHeader(string $name): bool
    {
        return array_key_exists(strtolower($name), $this->headerNames);
    }

    /**
     * {@inheritdoc}
     *
     * @param  string  $name  Case-insensitive header field name
     * @param  string|array<string>  $value  Header value(s)
     * @return static A new instance with the appended header
     *
     * @throws \InvalidArgumentException For invalid header names or values
     */
    public function withAddedHeader(string $name, $value): static
    {
        MessageValidations::assertHeaderName($name);
        if (! $this->hasHeader($name)) {
            return $this->withHeader($name, $value);
        }
        $normalizedName = $this->headerNames[strtolower($name)];
        $value = is_array($value) ? $value : [$value];
        array_walk($value, MessageValidations::assertHeaderValue(...));
        $clone = clone $this;
        $clone->headers[$normalizedName] = [...$clone->headers[$normalizedName], ...$value];

        return $clone;
    }

    /**
     * {@inheritdoc}
     *
     * @param  StreamInterface  $body  The new body stream
     * @return static A new instance with the specified body
     */
    public function withBody(StreamInterface $body): static
    {
        $clone = clone $this;
        $clone->body = $body;

        return $clone;
    }

    /**
     * {@inheritdoc}
     *
     * @param  string  $name  Case-insensitive header field name
     * @param  string|array<string>  $value  Header value(s)
     * @return static A new instance with the specified header
     *
     * @throws \InvalidArgumentException For invalid header names or values
     */
    public function withHeader(string $name, $value): static
    {
        MessageValidations::assertHeaderName($name);
        $value = is_array($value) ? $value : [$value];
        array_walk($value, MessageValidations::assertHeaderValue(...));
        $clone = clone $this;
        $clone->headerNames[strtolower($name)] = $name;
        $clone->headers[$name] = $value;

        return $clone;
    }

    /**
     * {@inheritdoc}
     *
     * @param  string  $name  Case-insensitive header field name to remove
     * @return static A new instance without the specified header
     */
    public function withoutHeader(string $name): static
    {
        $clone = clone $this;
        if ($this->hasHeader($name)) {
            unset($clone->headers[$this->headerNames[strtolower($name)]], $clone->headerNames[strtolower($name)]);
        }

        return $clone;
    }

    /**
     * {@inheritdoc}
     *
     * @param  string  $version  Protocol version (e.g., '1.1', '2.0')
     * @return static A new instance with the specified protocol version
     *
     * @throws \InvalidArgumentException For invalid protocol version
     */
    public function withProtocolVersion(string $version): static
    {
        MessageValidations::assertProtocolVersion($version);
        $clone = clone $this;
        $clone->protocolVersion = $version;

        return $clone;
    }
}
