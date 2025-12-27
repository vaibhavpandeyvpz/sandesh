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

use DateTime;
use DateTimeInterface;
use Psr\Http\Message\UploadedFileInterface;

/**
 * Message validation utilities.
 *
 * Provides static methods for validating and normalizing HTTP message components
 * including methods, status codes, headers, URIs, cookies, and uploaded files.
 * This class cannot be instantiated.
 *
 * @internal
 */
final class MessageValidations
{
    /**
     * Character class for URI sub-delimiters.
     *
     * @var string
     */
    private const CHAR_SUB_DELIMS = '!\$&\'\(\)\*\+,;=';

    /**
     * Character class for URI unreserved characters.
     *
     * @var string
     */
    private const CHAR_UNRESERVED = 'a-zA-Z0-9_\-\.~\pL';

    /**
     * Private constructor to prevent instantiation.
     *
     * @codeCoverageIgnore
     */
    private function __construct() {}

    /**
     * Assert that a cookie expiry value is valid.
     *
     * @param  mixed  $value  The expiry value to validate
     *
     * @throws \InvalidArgumentException If the value is not a valid expiry type
     */
    public static function assertCookieExpiry(mixed $value): void
    {
        if (! ($value instanceof DateTime) && ! is_string($value) && ! is_int($value)) {
            throw new \InvalidArgumentException(
                sprintf(
                    "Cookie expiry must be string, int or an instance of \\DateTime; '%s' given",
                    is_object($value) ? get_class($value) : gettype($value)
                )
            );
        }
    }

    /**
     * Assert that a header name is valid.
     *
     * Validates header names according to RFC 7230.
     *
     * @param  string  $name  The header name to validate
     *
     * @throws \InvalidArgumentException If the header name is invalid
     */
    public static function assertHeaderName(string $name): void
    {
        if (! preg_match('@^[a-zA-Z0-9\'`#$%&*+.^_|~!-]+$@', $name)) {
            throw new \InvalidArgumentException("'{$name}' is not valid header name");
        }
    }

    /**
     * Assert that a header value is valid.
     *
     * Validates header values according to RFC 7230.
     *
     * @param  string  $value  The header value to validate
     *
     * @throws \InvalidArgumentException If the header value is invalid
     */
    public static function assertHeaderValue(string $value): void
    {
        if (
            preg_match("~(?:(?:(?<!\r)\n)|(?:\r(?!\n))|(?:\r\n(?![ \t])))~", $value) ||
            preg_match('~[^\x09\x0a\x0d\x20-\x7E\x80-\xFE]~', $value)
        ) {
            throw new \InvalidArgumentException("'{$value}' is not valid header value");
        }
    }

    /**
     * Assert that an HTTP method is valid.
     *
     * @param  string  $value  The HTTP method to validate
     *
     * @throws \InvalidArgumentException If the method is not a valid HTTP method
     */
    public static function assertMethod(string $value): void
    {
        try {
            HttpMethod::fromString($value);
        } catch (\ValueError) {
            throw new \InvalidArgumentException("'{$value}' is not a valid HTTP method");
        }
    }

    /**
     * Assert that a URI path is valid.
     *
     * Paths must not contain query strings or fragments.
     *
     * @param  string  $path  The path to validate
     *
     * @throws \InvalidArgumentException If the path contains query or fragment
     */
    public static function assertPath(string $path): void
    {
        if (str_contains($path, '?') || str_contains($path, '#')) {
            throw new \InvalidArgumentException(
                str_contains($path, '?')
                    ? '$path must not contain query parameters'
                    : '$path must not contain hash fragment'
            );
        }
    }

    /**
     * Assert that an HTTP protocol version is valid.
     *
     * @param  string  $version  The protocol version to validate (e.g., '1.1', '2.0')
     *
     * @throws \InvalidArgumentException If the version format is invalid
     */
    public static function assertProtocolVersion(string $version): void
    {
        if (! preg_match('~^[1-9](?:.[0-9])?$~', $version)) {
            throw new \InvalidArgumentException("{$version} is not a valid HTTP protocol version name");
        }
    }

    /**
     * Assert that a URI query string is valid.
     *
     * Query strings must not contain fragments.
     *
     * @param  string  $query  The query string to validate
     *
     * @throws \InvalidArgumentException If the query contains a fragment
     */
    public static function assertQuery(string $query): void
    {
        if (str_contains(strtolower($query), '#')) {
            throw new \InvalidArgumentException('$query must not contain hash fragment');
        }
    }

    /**
     * Assert that an HTTP status code is valid.
     *
     * Status codes must be integers between 100 and 599.
     *
     * @param  int|string  $code  The status code to validate
     *
     * @throws \InvalidArgumentException If the status code is out of range
     */
    public static function assertStatusCode(int|string $code): void
    {
        $codeInt = is_int($code) ? $code : (int) $code;
        if (! is_numeric($code) || $codeInt < 100 || $codeInt >= 600) {
            throw new \InvalidArgumentException(sprintf(
                'Status code must be an integer between 100 and 599; %s given',
                is_numeric($code) ? (string) $code : gettype($code)
            ));
        }
    }

    /**
     * Assert that a TCP/UDP port number is valid.
     *
     * Port numbers must be integers between 0 and 65534.
     *
     * @param  int  $port  The port number to validate
     *
     * @throws \InvalidArgumentException If the port is out of range
     */
    public static function assertTcpUdpPort(int $port): void
    {
        if ($port < 0 || $port >= 65535) {
            throw new \InvalidArgumentException('$port must be a valid integer within TCP/UDP port range');
        }
    }

    /**
     * Assert that uploaded files array contains only UploadedFileInterface instances.
     *
     * @param  array<UploadedFileInterface>  $files  The uploaded files array to validate
     *
     * @throws \UnexpectedValueException If any file is not an UploadedFileInterface instance
     */
    public static function assertUploadedFiles(array $files): void
    {
        foreach ($files as $file) {
            if (! $file instanceof UploadedFileInterface) {
                throw new \UnexpectedValueException(sprintf(
                    'Uploaded file must be an instance of Psr\\Http\\Message\\UploadedFileInterface; %s given',
                    is_scalar($file) ? gettype($file) : get_class($file)
                ));
            }
        }
    }

    /**
     * Normalize a cookie expiry value to a DateTime instance.
     *
     * @param  DateTimeInterface|string|int|null  $value  The expiry value to normalize
     * @return DateTime|null The normalized DateTime instance, or null if invalid
     */
    public static function normalizeCookieExpiry(DateTimeInterface|string|int|null $value): ?DateTime
    {
        if (is_string($value)) {
            $dateTime = DateTime::createFromFormat(Cookie::EXPIRY_FORMAT, $value);
            if ($dateTime !== false) {
                return $dateTime;
            }
        } elseif (is_int($value)) {
            $dateTime = DateTime::createFromFormat('U', (string) $value);
            if ($dateTime !== false) {
                return $dateTime;
            }
        } elseif ($value instanceof DateTime) {
            return $value;
        }

        return null;
    }

    /**
     * Normalize a URI fragment string.
     *
     * Removes leading '#' and URL-encodes invalid characters.
     *
     * @param  string  $fragment  The fragment to normalize
     * @return string The normalized fragment
     */
    public static function normalizeFragment(string $fragment): string
    {
        if ($fragment !== '' && str_starts_with($fragment, '#')) {
            $fragment = '%23'.substr($fragment, 1);
        }

        return self::normalizeQueryOrFragment($fragment);
    }

    /**
     * Normalize a URI query string.
     *
     * Removes leading '?' and URL-encodes invalid characters in name-value pairs.
     *
     * @param  string  $query  The query string to normalize
     * @return string The normalized query string
     */
    public static function normalizeQuery(string $query): string
    {
        if ($query !== '' && str_starts_with($query, '?')) {
            $query = substr($query, 1);
        }
        if ($query === '') {
            return '';
        }
        $nvps = explode('&', $query);
        foreach ($nvps as $i => $nvp) {
            $pair = explode('=', $nvp, 2);
            [$name, $value] = $pair + ['', null];
            if ($value === null) {
                $nvps[$i] = self::normalizeQueryOrFragment($name);
            } else {
                $nvps[$i] = sprintf(
                    '%s=%s',
                    self::normalizeQueryOrFragment($name),
                    self::normalizeQueryOrFragment($value)
                );
            }
        }

        return implode('&', $nvps);
    }

    /**
     * Normalize a query string or fragment component.
     *
     * URL-encodes characters that are not unreserved or sub-delimiters.
     *
     * @param  string  $string  The string to normalize
     * @return string The normalized string
     */
    public static function normalizeQueryOrFragment(string $string): string
    {
        return preg_replace_callback(
            '#(?:[^'.self::CHAR_UNRESERVED.self::CHAR_SUB_DELIMS.'%:@\/\?]+|%(?![A-Fa-f0-9]{2}))#u',
            self::rawUrlEncodeSubject(...),
            $string
        );
    }

    /**
     * Normalize a URI path string.
     *
     * URL-encodes invalid characters and ensures proper path formatting.
     *
     * @param  string  $path  The path to normalize
     * @return string The normalized path
     */
    public static function normalizePath(string $path): string
    {
        $path = preg_replace_callback(
            '#(?:[^'.self::CHAR_UNRESERVED.':@&=\+\$,\/;%]+|%(?![A-Fa-f0-9]{2}))#u',
            self::rawUrlEncodeSubject(...),
            $path
        );
        if ($path !== '' && str_starts_with($path, '/')) {
            $path = '/'.ltrim($path, '/');
        }

        return $path;
    }

    /**
     * Normalize a URI scheme string.
     *
     * Converts to lowercase and removes trailing colons and slashes.
     *
     * @param  string  $scheme  The scheme to normalize
     * @return string The normalized scheme
     */
    public static function normalizeScheme(string $scheme): string
    {
        return preg_replace('~:(//)?$~', '', strtolower($scheme));
    }

    /**
     * URL-encode a matched string.
     *
     * Callback function for preg_replace_callback.
     *
     * @param  array<int, string>  $matches  The regex matches
     * @return string The URL-encoded string
     */
    private static function rawUrlEncodeSubject(array $matches): string
    {
        return rawurlencode($matches[0]);
    }
}
