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
 * Interface CookieFactoryInterface
 * @package Sandesh
 */
interface CookieFactoryInterface
{
    /**
     * @param string $header
     * @return CookieInterface
     */
    public function createCookie($header);
}
