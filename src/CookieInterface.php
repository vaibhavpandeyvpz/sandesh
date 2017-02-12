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
 * Interface CookieInterface
 * @package Sandesh
 */
interface CookieInterface
{
    /**
     * @return string
     */
    public function getDomain();

    /**
     * @return \DateTimeInterface
     */
    public function getExpiry();

    /**
     * @return string
     */
    public function getName();

    /**
     * @return string
     */
    public function getPath();

    /**
     * @return string
     */
    public function getValue();

    /**
     * @return bool
     */
    public function isHttpOnly();

    /**
     * @return bool
     */
    public function isSecure();

    /**
     * @param string|null $domain
     * @return static
     */
    public function withDomain($domain);

    /**
     * @param \DateTimeInterface|string|int|null $expiry
     * @return static
     */
    public function withExpiry($expiry);

    /**
     * @param bool $flag
     * @return static
     */
    public function withHttpOnly($flag);

    /**
     * @param string $name
     * @return static
     */
    public function withName($name);

    /**
     * @param string|null $path
     * @return static
     */
    public function withPath($path);

    /**
     * @param bool $flag
     * @return static
     */
    public function withSecure($flag);

    /**
     * @param string|null $value
     * @return static
     */
    public function withValue($value);
}
