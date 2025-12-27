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

/**
 * HTTP method enumeration.
 *
 * Represents valid HTTP methods as defined in RFC 7231 and other relevant RFCs.
 * This enum provides type-safe HTTP method handling.
 */
enum HttpMethod: string
{
    case CONNECT = 'CONNECT';
    case DELETE = 'DELETE';
    case GET = 'GET';
    case HEAD = 'HEAD';
    case OPTIONS = 'OPTIONS';
    case PATCH = 'PATCH';
    case POST = 'POST';
    case PUT = 'PUT';
    case TRACE = 'TRACE';

    /**
     * Create an HttpMethod instance from a string.
     *
     * Converts a string HTTP method to the corresponding enum case.
     * The method name is case-insensitive.
     *
     * @param  string  $method  The HTTP method string (e.g., 'GET', 'post', 'Put')
     * @return self The corresponding HttpMethod enum case
     *
     * @throws \ValueError If the method is not a valid HTTP method
     */
    public static function fromString(string $method): self
    {
        return self::from(strtoupper($method));
    }

    /**
     * Get the string representation of the HTTP method.
     *
     * @return string The HTTP method as a string (e.g., 'GET', 'POST')
     */
    public function toString(): string
    {
        return $this->value;
    }
}
