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

use Psr\Http\Message\ServerRequestFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

/**
 * Server-side HTTP request factory implementation.
 *
 * Creates ServerRequest instances as defined in PSR-17.
 * This factory can create server requests from HTTP method strings, URI strings or objects,
 * and server parameters (e.g., $_SERVER). It automatically extracts headers and protocol
 * version from server parameters and sets up the request body from php://input.
 */
class ServerRequestFactory implements ServerRequestFactoryInterface
{
    /**
     * {@inheritdoc}
     *
     * @param  string  $method  HTTP method (e.g., 'GET', 'POST')
     * @param  string|UriInterface  $uri  URI string or UriInterface instance
     * @param  array<string, mixed>  $serverParams  Server parameters (e.g., $_SERVER)
     * @return ServerRequestInterface A new ServerRequest instance
     *
     * @throws \InvalidArgumentException If the method is invalid
     * @throws \RuntimeException If php://temp cannot be opened
     */
    public function createServerRequest(string $method, $uri, array $serverParams = []): ServerRequestInterface
    {
        if (is_string($uri)) {
            $factory = new UriFactory;
            $uri = $factory->createUri($uri);
        }
        $request = new ServerRequest($method, $uri, $serverParams);
        if ($serverParams !== []) {
            $request = $request->withProtocolVersion(self::getProtocolVersion($serverParams));
            foreach (self::getHeaders($serverParams) as $name => $value) {
                $request = $request->withHeader($name, $value);
            }
        }

        return $request->withBody(self::getPhpInputStream());
    }

    /**
     * Extract HTTP headers from server parameters.
     *
     * Parses $_SERVER-style arrays to extract HTTP headers.
     * Handles CONTENT_* and HTTP_* prefixed keys, and REDIRECT_* normalization.
     *
     * @param  array<string, mixed>  $server  Server parameters
     * @return array<string, string> Normalized headers (lowercase keys)
     */
    protected static function getHeaders(array $server): array
    {
        $headers = [];
        $pick = ['CONTENT_', 'HTTP_'];
        foreach ($server as $key => $value) {
            if (! $value) {
                continue;
            }
            if (str_starts_with($key, 'REDIRECT_')) {
                $normalizedKey = substr($key, 9);
                if (array_key_exists($normalizedKey, $server)) {
                    continue;
                }
                $key = $normalizedKey;
            }
            foreach ($pick as $prefix) {
                if (str_starts_with($key, $prefix)) {
                    if ($prefix !== $pick[0]) {
                        $key = substr($key, strlen($prefix));
                    }
                    $headers[strtolower(strtr($key, '_', '-'))] = (string) $value;

                    continue 2;
                }
            }
        }

        return $headers;
    }

    /**
     * Get the request body stream from php://input.
     *
     * Creates a stream containing the raw request body from php://input.
     * The stream is rewound to the beginning for reading.
     *
     * @return StreamInterface A stream containing the request body
     *
     * @throws \RuntimeException If php://temp cannot be opened
     */
    protected static function getPhpInputStream(): StreamInterface
    {
        $temp = fopen('php://temp', 'w+');
        if ($temp === false) {
            throw new \RuntimeException('Failed to open php://temp');
        }
        $input = fopen('php://input', 'r');
        if ($input !== false) {
            stream_copy_to_stream($input, $temp);
            fclose($input);
        }
        $stream = new Stream($temp);
        $stream->rewind();

        return $stream;
    }

    /**
     * Extract HTTP protocol version from server parameters.
     *
     * @param  array<string, mixed>  $server  Server parameters
     * @return string Protocol version (default: '1.1')
     */
    protected static function getProtocolVersion(array $server): string
    {
        if (isset($server['SERVER_PROTOCOL']) && is_string($server['SERVER_PROTOCOL'])) {
            return str_replace('HTTP/', '', $server['SERVER_PROTOCOL']);
        }

        return '1.1';
    }
}
