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

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UploadedFileInterface;
use Psr\Http\Message\UriInterface;

/**
 * Server-side HTTP request message implementation.
 *
 * Represents a server-side HTTP request message as defined in PSR-7.
 * Extends the base Request class with server-specific functionality including
 * server parameters, cookie parameters, query parameters, uploaded files,
 * parsed body, and request attributes.
 */
class ServerRequest extends Request implements ServerRequestInterface
{
    /**
     * Request attributes (application-specific data).
     *
     * @var array<string, mixed>
     */
    protected array $attributes = [];

    /**
     * Cookie parameters (parsed from Cookie header).
     *
     * @var array<string, string>
     */
    protected array $cookieParams = [];

    /**
     * Parsed body data (e.g., JSON, form data).
     */
    protected mixed $parsedBody = null;

    /**
     * Query string parameters.
     *
     * @var array<string, string>
     */
    protected array $queryParams = [];

    /**
     * Server and execution environment parameters (e.g., $_SERVER).
     *
     * @var array<string, mixed>
     */
    protected array $serverParams = [];

    /**
     * Uploaded files.
     *
     * @var array<UploadedFileInterface>
     */
    protected array $uploadedFiles = [];

    /**
     * ServerRequest constructor.
     *
     * @param  string  $method  HTTP method (default: 'GET')
     * @param  UriInterface|null  $uri  Request URI
     * @param  array<string, mixed>  $serverParams  Server parameters (e.g., $_SERVER)
     *
     * @throws \InvalidArgumentException If the method is invalid
     */
    public function __construct(string $method = 'GET', ?UriInterface $uri = null, array $serverParams = [])
    {
        parent::__construct($method, $uri);
        $this->serverParams = $serverParams;
    }

    /**
     * {@inheritdoc}
     *
     * @param  string  $name  The attribute name
     * @param  mixed  $default  Default value to return if the attribute does not exist
     * @return mixed The attribute value or default
     */
    public function getAttribute(string $name, mixed $default = null): mixed
    {
        return array_key_exists($name, $this->attributes) ? $this->attributes[$name] : $default;
    }

    /**
     * {@inheritdoc}
     *
     * @return array<string, mixed> All attributes
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * {@inheritdoc}
     *
     * @return array<string, string> Cookie parameters
     */
    public function getCookieParams(): array
    {
        return $this->cookieParams;
    }

    /**
     * {@inheritdoc}
     *
     * Parses the request body based on Content-Type header.
     * Supports JSON, form-urlencoded, and XML content types.
     *
     * @return mixed The parsed body data, or null if parsing fails or is not supported
     */
    public function getParsedBody(): mixed
    {
        if ($this->parsedBody !== null || $this->body === null) {
            return $this->parsedBody;
        }
        $type = $this->getHeaderLine('Content-Type');
        if ($type !== '') {
            [$type] = explode(';', $type, 2);
        }
        $body = (string) $this->getBody();
        $trimmedType = trim($type);

        $this->parsedBody = match ($trimmedType) {
            'application/json' => extension_loaded('json') ? json_decode($body, true) : null,
            'application/x-www-form-urlencoded' => (static function (string $b): array {
                parse_str($b, $data);

                return $data;
            })($body),
            'text/xml' => extension_loaded('libxml') ? (static function (string $b) {
                // In PHP 8.0+, entity loading is disabled by default, so libxml_disable_entity_loader is not needed
                // For PHP < 8.0, we disable entity loading for security
                if (PHP_VERSION_ID < 80000 && function_exists('libxml_disable_entity_loader')) {
                    $disabled = libxml_disable_entity_loader(true);
                    $xml = simplexml_load_string($b);
                    libxml_disable_entity_loader($disabled);

                    return $xml;
                }

                return simplexml_load_string($b);
            })($body) : null,
            default => null,
        };

        return $this->parsedBody;
    }

    /**
     * {@inheritdoc}
     *
     * @return array<string, string> Query string parameters
     */
    public function getQueryParams(): array
    {
        return $this->queryParams;
    }

    /**
     * {@inheritdoc}
     *
     * @return array<string, mixed> Server and execution environment parameters
     */
    public function getServerParams(): array
    {
        return $this->serverParams;
    }

    /**
     * {@inheritdoc}
     *
     * @return array<UploadedFileInterface> Uploaded files
     */
    public function getUploadedFiles(): array
    {
        return $this->uploadedFiles;
    }

    /**
     * {@inheritdoc}
     *
     * @param  string  $name  The attribute name
     * @param  mixed  $value  The attribute value
     * @return static A new instance with the specified attribute
     */
    public function withAttribute(string $name, mixed $value): static
    {
        $clone = clone $this;
        $clone->attributes[$name] = $value;

        return $clone;
    }

    /**
     * {@inheritdoc}
     *
     * @param  array<string, string>  $cookies  Cookie parameters
     * @return static A new instance with the specified cookie parameters
     */
    public function withCookieParams(array $cookies): static
    {
        $clone = clone $this;
        $clone->cookieParams = $cookies;

        return $clone;
    }

    /**
     * {@inheritdoc}
     *
     * @param  array<string, string>  $query  Query string parameters
     * @return static A new instance with the specified query parameters
     */
    public function withQueryParams(array $query): static
    {
        $clone = clone $this;
        $clone->queryParams = $query;

        return $clone;
    }

    /**
     * {@inheritdoc}
     *
     * @param  array<string, mixed>  $server  Server parameters
     * @return static A new instance with the specified server parameters
     */
    public function withServerParams(array $server): static
    {
        $clone = clone $this;
        $clone->serverParams = $server;

        return $clone;
    }

    /**
     * {@inheritdoc}
     *
     * @param  array<UploadedFileInterface>  $uploadedFiles  Uploaded files
     * @return static A new instance with the specified uploaded files
     *
     * @throws \UnexpectedValueException If any file is not an UploadedFileInterface instance
     */
    public function withUploadedFiles(array $uploadedFiles): static
    {
        MessageValidations::assertUploadedFiles($uploadedFiles);
        $clone = clone $this;
        $clone->uploadedFiles = $uploadedFiles;

        return $clone;
    }

    /**
     * {@inheritdoc}
     *
     * @param  mixed  $data  The parsed body data
     * @return static A new instance with the specified parsed body
     */
    public function withParsedBody(mixed $data): static
    {
        $clone = clone $this;
        $clone->parsedBody = $data;

        return $clone;
    }

    /**
     * {@inheritdoc}
     *
     * @param  string  $name  The attribute name to remove
     * @return static A new instance without the specified attribute
     */
    public function withoutAttribute(string $name): static
    {
        $clone = clone $this;
        unset($clone->attributes[$name]);

        return $clone;
    }
}
