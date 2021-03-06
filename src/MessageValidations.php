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

use Psr\Http\Message\UploadedFileInterface;

/**
 * Class MessageValidations
 * @package Sandesh
 */
class MessageValidations
{
    const CHAR_SUB_DELIMS = '!\$&\'\(\)\*\+,;=';

    const CHAR_UNRESERVED = 'a-zA-Z0-9_\-\.~\pL';

    /**
     * MessageValidations constructor.
     * @codeCoverageIgnore
     */
    private function __construct()
    {
    }

    /**
     * @param mixed $value
     */
    public static function assertCookieExpiry($value)
    {
        if (!($value instanceof \DateTime) && !is_string($value) && !is_int($value)) {
            throw new \InvalidArgumentException(
                "Cookie expiry must be string, int or an instance of \\DateTime; '%s' given",
                is_object($value) ? get_class($value) : gettype($value)
            );
        }
    }

    /**
     * @param string $name
     */
    public static function assertHeaderName($name)
    {
        if (!preg_match('@^[a-zA-Z0-9\'`#$%&*+.^_|~!-]+$@', $name)) {
            throw new \InvalidArgumentException("'{$name}' is not valid header name");
        }
    }

    /**
     * @param string $value
     */
    public static function assertHeaderValue($value)
    {
        if (
            preg_match("~(?:(?:(?<!\r)\n)|(?:\r(?!\n))|(?:\r\n(?![ \t])))~", $value) ||
            preg_match('~[^\x09\x0a\x0d\x20-\x7E\x80-\xFE]~', $value)
        ) {
            throw new \InvalidArgumentException("'{$value}' is not valid header value");
        }
    }

    /**
     * @param string $value
     */
    public static function assertMethod($value)
    {
        if (!in_array($value, ['CONNECT', 'DELETE', 'GET', 'HEAD', 'OPTIONS', 'PATCH', 'POST', 'PUT', 'TRACE'])) {
            throw new \InvalidArgumentException("'{$value}' is not a valid HTTP method");
        }
    }

    /**
     * @param string $path
     */
    public static function assertPath($path)
    {
        if (false !== stripos($path, '?')) {
            throw new \InvalidArgumentException('$path must not contain query parameters');
        } elseif (false !== stripos($path, '#')) {
            throw new \InvalidArgumentException('$path must not contain hash fragment');
        }
    }

    /**
     * @param string $version
     */
    public static function assertProtocolVersion($version)
    {
        if (!preg_match('~^[1-9](?:.[0-9])?$~', $version)) {
            throw new \InvalidArgumentException("{$version} is not a valid HTTP protocol version name");
        }
    }

    /**
     * @param string $query
     */
    public static function assertQuery($query)
    {
        if (false !== stripos($query, '#')) {
            throw new \InvalidArgumentException('$query must not contain hash fragment');
        }
    }

    /**
     * @param int $code
     */
    public static function assertStatusCode($code)
    {
        if (!is_numeric($code) || (100 > $code) || (600 <= $code)) {
            throw new \InvalidArgumentException(sprintf(
                'Status code must be an integer between 100 and 599; %s given',
                is_numeric($code) ? $code : gettype($code)
            ));
        }
    }

    /**
     * @param int $port
     */
    public static function assertTcpUdpPort($port)
    {
        if ((0 > $port) || (65535 <= $port)) {
            throw new \InvalidArgumentException('$port must be a valid integer within TCP/UDP port range');
        }
    }

    /**
     * @param UploadedFileInterface[] $files
     */
    public static function assertUploadedFiles(array $files)
    {
        foreach ($files as $file) {
            if (!$file instanceof UploadedFileInterface) {
                throw new \UnexpectedValueException(sprintf(
                    'Uploaded file must be an instance of Psr\\Http\\Message\\UploadedFileInterface; %s given',
                    is_scalar($file) ? gettype($file) : get_class($file)
                ));
            }
        }
    }

    /**
     * @param \DateTimeInterface|string|int|null $value
     * @return string
     */
    public static function normalizeCookieExpiry($value)
    {
        if (is_string($value)) {
            $value = \DateTime::createFromFormat(Cookie::EXPIRY_FORMAT, $value);
        } elseif (is_int($value)) {
            $value = \DateTime::createFromFormat('U', $value);
        }
        if ($value instanceof \DateTime) {
            return $value;
        }
    }

    /**
     * @param string $fragment
     * @return string
     */
    public static function normalizeFragment($fragment)
    {
        if ($fragment && (0 === stripos($fragment, '#'))) {
            $fragment = '%23' . substr($fragment, 1);
        }
        return self::normalizeQueryOrFragment($fragment);
    }

    /**
     * @param string $query
     * @return string
     */
    public static function normalizeQuery($query)
    {
        if ($query && (0 === stripos($query, '?'))) {
            $query = substr($query, 1);
        }
        $nvps = explode('&', $query);
        foreach ($nvps as $i => $nvp) {
            $pair = explode('=', $nvp, 2);
            if (count($pair) === 1) {
                $pair[] = null;
            }
            list($name, $value) = $pair;
            if (is_null($value)) {
                $nvps[$i] = self::normalizeQueryOrFragment($name);
                continue;
            }
            $nvps[$i] = sprintf('%s=%s', self::normalizeQueryOrFragment($name), self::normalizeQueryOrFragment($value));
        }
        return implode('&', $nvps);
    }

    /**
     * @param string $string
     * @return string
     */
    public static function normalizeQueryOrFragment($string)
    {
        return preg_replace_callback(
            '#(?:[^' . self::CHAR_UNRESERVED . self::CHAR_SUB_DELIMS . '%:@\/\?]+|%(?![A-Fa-f0-9]{2}))#u',
            [__CLASS__, 'rawUrlEncodeSubject'],
            $string
        );
    }

    /**
     * @param string $path
     * @return string
     */
    public static function normalizePath($path)
    {
        $path = preg_replace_callback(
            '#(?:[^' . self::CHAR_UNRESERVED . ':@&=\+\$,\/;%]+|%(?![A-Fa-f0-9]{2}))#u',
            [__CLASS__, 'rawUrlEncodeSubject'],
            $path
        );
        if ($path && ('/' === $path[0])) {
            $path = ('/' . ltrim($path, '/'));
        }
        return $path;
    }

    /**
     * @param string $scheme
     * @return string
     */
    public static function normalizeScheme($scheme)
    {
        return preg_replace('~:(//)?$~', '', strtolower($scheme));
    }

    /**
     * @param array $matches
     * @return string
     */
    private static function rawUrlEncodeSubject(array $matches)
    {
        return rawurlencode($matches[0]);
    }
}
