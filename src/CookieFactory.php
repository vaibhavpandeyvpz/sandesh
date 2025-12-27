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
 * Cookie factory implementation.
 *
 * Creates Cookie instances from Set-Cookie header strings.
 * Parses cookie attributes including Domain, Path, Expires, Max-Age,
 * Secure, and HttpOnly flags according to RFC 6265.
 */
class CookieFactory implements CookieFactoryInterface
{
    /**
     * {@inheritdoc}
     *
     * @param  string  $header  Set-Cookie header string
     * @return CookieInterface A new Cookie instance
     *
     * @throws \InvalidArgumentException If the header format is invalid
     */
    public function createCookie(string $header): CookieInterface
    {
        $parts = preg_split('~\\s*[;]\\s*~', $header);
        if ($parts === false || $parts === []) {
            throw new \InvalidArgumentException('Invalid cookie header format');
        }
        $firstPart = array_shift($parts);
        if ($firstPart === null) {
            throw new \InvalidArgumentException('Invalid cookie header format');
        }
        [$name, $value] = explode('=', $firstPart, 2) + ['', ''];
        $cookie = new Cookie($name);
        if ($value !== '') {
            $cookie = $cookie->withValue(urldecode($value));
        }
        foreach ($parts as $nvp) {
            $nvpParts = explode('=', $nvp, 2);
            $paramName = strtolower($nvpParts[0]);
            $paramValue = $nvpParts[1] ?? null;
            $cookie = match ($paramName) {
                'domain' => $cookie->withDomain($paramValue),
                'expires' => $cookie->withExpiry($paramValue),
                'httponly' => $cookie->withHttpOnly(true),
                'max-age' => $cookie->withMaxAge((int) ($paramValue ?? 0)),
                'path' => $cookie->withPath($paramValue),
                'secure' => $cookie->withSecure(true),
                default => $cookie,
            };
        }

        return $cookie;
    }
}
