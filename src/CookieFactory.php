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

/**
 * Class CookieFactory
 * @package Sandesh
 */
class CookieFactory implements CookieFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function createCookie($header)
    {
        $parts = preg_split('~\\s*[;]\\s*~', $header);
        list($name, $value) = explode('=', array_shift($parts), 2);
        $cookie = new Cookie($name);
        if (is_string($value)) {
            $cookie = $cookie->withValue(urldecode($value));
        }
        while ($nvp = array_shift($parts)) {
            $nvp = explode('=', $nvp, 2);
            $value = count($nvp) === 2 ? $nvp[1] : null;
            switch (strtolower($nvp[0])) {
                case 'domain':
                    $cookie = $cookie->withDomain($value);
                    break;
                case 'expires':
                    $cookie = $cookie->withExpiry($value);
                    break;
                case 'httponly':
                    $cookie = $cookie->withHttpOnly(true);
                    break;
                case 'max-age':
                    $cookie = $cookie->withMaxAge($value);
                    break;
                case 'path':
                    $cookie = $cookie->withPath($value);
                    break;
                case 'secure':
                    $cookie = $cookie->withSecure(true);
                    break;
            }
        }
        return $cookie;
    }
}
