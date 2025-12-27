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
 * Interface for creating Cookie instances from Set-Cookie headers.
 *
 * Defines the contract for factories that parse Set-Cookie header strings
 * and create Cookie instances.
 */
interface CookieFactoryInterface
{
    /**
     * Create a Cookie instance from a Set-Cookie header string.
     *
     * @param  string  $header  Set-Cookie header string
     * @return CookieInterface A new Cookie instance
     *
     * @throws \InvalidArgumentException If the header format is invalid
     */
    public function createCookie(string $header): CookieInterface;
}
