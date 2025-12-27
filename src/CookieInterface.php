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

use DateTimeInterface;

/**
 * Interface for HTTP cookies.
 *
 * Represents an HTTP cookie as defined in RFC 6265.
 * Provides methods to access and modify cookie properties.
 */
interface CookieInterface
{
    /**
     * Get the domain attribute.
     *
     * @return string|null The domain attribute, or null if not set
     */
    public function getDomain(): ?string;

    /**
     * Get the expiry date/time.
     *
     * @return DateTimeInterface|null The expiry date/time, or null if not set
     */
    public function getExpiry(): ?DateTimeInterface;

    /**
     * Get the Max-Age attribute.
     *
     * @return int The Max-Age value in seconds (0 if not set)
     */
    public function getMaxAge(): int;

    /**
     * Get the cookie name.
     *
     * @return string The cookie name
     */
    public function getName(): string;

    /**
     * Get the path attribute.
     *
     * @return string|null The path attribute, or null if not set
     */
    public function getPath(): ?string;

    /**
     * Get the cookie value.
     *
     * @return string|null The cookie value, or null if not set
     */
    public function getValue(): ?string;

    /**
     * Check if the HttpOnly flag is set.
     *
     * @return bool Whether the HttpOnly flag is set
     */
    public function isHttpOnly(): bool;

    /**
     * Check if the Secure flag is set.
     *
     * @return bool Whether the Secure flag is set
     */
    public function isSecure(): bool;

    /**
     * Create a new instance with the specified domain.
     *
     * @param  string|null  $domain  The domain attribute
     * @return static A new instance with the specified domain
     */
    public function withDomain(?string $domain): static;

    /**
     * Create a new instance with the specified expiry.
     *
     * @param  DateTimeInterface|string|int|null  $expiry  The expiry date/time
     * @return static A new instance with the specified expiry
     *
     * @throws \InvalidArgumentException If the expiry format is invalid
     */
    public function withExpiry(DateTimeInterface|string|int|null $expiry): static;

    /**
     * Create a new instance with the specified HttpOnly flag.
     *
     * @param  bool  $flag  Whether to set the HttpOnly flag
     * @return static A new instance with the specified HttpOnly flag
     */
    public function withHttpOnly(bool $flag): static;

    /**
     * Create a new instance with the specified Max-Age.
     *
     * @param  int  $age  The Max-Age value in seconds
     * @return static A new instance with the specified Max-Age
     */
    public function withMaxAge(int $age): static;

    /**
     * Create a new instance with the specified name.
     *
     * @param  string  $name  The cookie name
     * @return static A new instance with the specified name
     */
    public function withName(string $name): static;

    /**
     * Create a new instance with the specified path.
     *
     * @param  string|null  $path  The path attribute
     * @return static A new instance with the specified path
     */
    public function withPath(?string $path): static;

    /**
     * Create a new instance with the specified Secure flag.
     *
     * @param  bool  $flag  Whether to set the Secure flag
     * @return static A new instance with the specified Secure flag
     */
    public function withSecure(bool $flag): static;

    /**
     * Create a new instance with the specified value.
     *
     * @param  string|null  $value  The cookie value
     * @return static A new instance with the specified value
     */
    public function withValue(?string $value): static;
}
