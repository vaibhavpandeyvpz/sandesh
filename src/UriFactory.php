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

use Psr\Http\Message\UriFactoryInterface;
use Psr\Http\Message\UriInterface;

/**
 * URI factory implementation.
 *
 * Creates Uri instances as defined in PSR-17.
 * This factory can create URIs from URI strings, parsing all components
 * (scheme, host, port, path, query, fragment, user info).
 */
class UriFactory implements UriFactoryInterface
{
    /**
     * {@inheritdoc}
     *
     * @param  string  $uri  URI string to parse
     * @return UriInterface A new Uri instance
     *
     * @throws \InvalidArgumentException If the URI string is malformed
     */
    public function createUri(string $uri = ''): UriInterface
    {
        $obj = new Uri;
        if ($uri === '') {
            return $obj;
        }
        $url = parse_url($uri);
        if ($url === false) {
            throw new \InvalidArgumentException('URL passed is not a well-formed URI');
        }
        if (isset($url['fragment'])) {
            $obj = $obj->withFragment($url['fragment']);
        }
        if (isset($url['host'])) {
            $obj = $obj->withHost($url['host']);
        }
        if (isset($url['path'])) {
            $obj = $obj->withPath($url['path']);
        }
        if (isset($url['port'])) {
            $obj = $obj->withPort($url['port']);
        }
        if (isset($url['query'])) {
            $obj = $obj->withQuery($url['query']);
        }
        if (isset($url['scheme'])) {
            $obj = $obj->withScheme($url['scheme']);
        }
        if (isset($url['user'])) {
            $password = $url['pass'] ?? null;
            $obj = $obj->withUserInfo($url['user'], $password);
        }

        return $obj;
    }
}
