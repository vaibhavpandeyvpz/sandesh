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
 * HTTP cookie implementation.
 *
 * Represents an HTTP cookie as defined in RFC 6265.
 * This class provides methods to access and modify cookie properties
 * including name, value, domain, path, expiry, security flags, etc.
 */
class Cookie implements CookieInterface
{
    /**
     * Date format for cookie expiry (RFC 1123 format).
     *
     * @var string
     */
    public const EXPIRY_FORMAT = 'l, d-M-Y H:i:s T';

    /**
     * Cookie domain attribute.
     */
    protected ?string $domain = null;

    /**
     * Cookie expiry date/time.
     */
    protected ?DateTimeInterface $expiry = null;

    /**
     * HttpOnly flag (prevents JavaScript access).
     */
    protected bool $httpOnly = false;

    /**
     * Max-Age attribute (cookie lifetime in seconds).
     */
    protected int $maxAge = 0;

    /**
     * Cookie name.
     */
    protected string $name;

    /**
     * Cookie path attribute.
     */
    protected ?string $path = null;

    /**
     * Secure flag (requires HTTPS).
     */
    protected bool $secure = false;

    /**
     * Cookie value.
     */
    protected ?string $value = null;

    /**
     * Cookie constructor.
     *
     * @param  string  $name  The cookie name
     */
    public function __construct(string $name)
    {
        $this->name = $name;
    }

    /**
     * {@inheritdoc}
     *
     * @return string|null The domain attribute, or null if not set
     */
    public function getDomain(): ?string
    {
        return $this->domain;
    }

    /**
     * {@inheritdoc}
     *
     * @return DateTimeInterface|null The expiry date/time, or null if not set
     */
    public function getExpiry(): ?DateTimeInterface
    {
        return $this->expiry;
    }

    /**
     * Get the Max-Age attribute.
     *
     * @return int The Max-Age value in seconds (0 if not set)
     */
    public function getMaxAge(): int
    {
        return $this->maxAge;
    }

    /**
     * {@inheritdoc}
     *
     * @return string The cookie name
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     *
     * @return string|null The path attribute, or null if not set
     */
    public function getPath(): ?string
    {
        return $this->path;
    }

    /**
     * {@inheritdoc}
     *
     * @return string|null The cookie value, or null if not set
     */
    public function getValue(): ?string
    {
        return $this->value;
    }

    /**
     * {@inheritdoc}
     *
     * @return bool Whether the HttpOnly flag is set
     */
    public function isHttpOnly(): bool
    {
        return $this->httpOnly;
    }

    /**
     * {@inheritdoc}
     *
     * @return bool Whether the Secure flag is set
     */
    public function isSecure(): bool
    {
        return $this->secure;
    }

    /**
     * {@inheritdoc}
     *
     * @param  string|null  $domain  The domain attribute
     * @return static A new instance with the specified domain
     */
    public function withDomain(?string $domain): static
    {
        $clone = clone $this;
        $clone->domain = $domain;

        return $clone;
    }

    /**
     * {@inheritdoc}
     *
     * @param  DateTimeInterface|string|int|null  $expiry  The expiry date/time
     * @return static A new instance with the specified expiry
     *
     * @throws \InvalidArgumentException If the expiry format is invalid
     */
    public function withExpiry(DateTimeInterface|string|int|null $expiry): static
    {
        if ($expiry !== null) {
            MessageValidations::assertCookieExpiry($expiry);
        }
        $clone = clone $this;
        $clone->expiry = MessageValidations::normalizeCookieExpiry($expiry);

        return $clone;
    }

    /**
     * {@inheritdoc}
     *
     * @param  bool  $flag  Whether to set the HttpOnly flag
     * @return static A new instance with the specified HttpOnly flag
     */
    public function withHttpOnly(bool $flag): static
    {
        $clone = clone $this;
        $clone->httpOnly = $flag;

        return $clone;
    }

    /**
     * Set the Max-Age attribute.
     *
     * @param  int  $age  The Max-Age value in seconds
     * @return static A new instance with the specified Max-Age
     */
    public function withMaxAge(int $age): static
    {
        $clone = clone $this;
        $clone->maxAge = $age;

        return $clone;
    }

    /**
     * {@inheritdoc}
     *
     * @param  string  $name  The cookie name
     * @return static A new instance with the specified name
     */
    public function withName(string $name): static
    {
        $clone = clone $this;
        $clone->name = $name;

        return $clone;
    }

    /**
     * {@inheritdoc}
     *
     * @param  string|null  $path  The path attribute
     * @return static A new instance with the specified path
     */
    public function withPath(?string $path): static
    {
        $clone = clone $this;
        $clone->path = $path;

        return $clone;
    }

    /**
     * {@inheritdoc}
     *
     * @param  bool  $flag  Whether to set the Secure flag
     * @return static A new instance with the specified Secure flag
     */
    public function withSecure(bool $flag): static
    {
        $clone = clone $this;
        $clone->secure = $flag;

        return $clone;
    }

    /**
     * {@inheritdoc}
     *
     * @param  string|null  $value  The cookie value
     * @return static A new instance with the specified value
     */
    public function withValue(?string $value): static
    {
        $clone = clone $this;
        $clone->value = $value;

        return $clone;
    }

    /**
     * Get the cookie as a Set-Cookie header string.
     *
     * Formats the cookie according to RFC 6265 for use in Set-Cookie headers.
     *
     * @return string The cookie formatted as a Set-Cookie header value
     */
    public function __toString(): string
    {
        $params = [$this->name.'='.urlencode($this->value ?? '')];
        if ($this->domain !== null) {
            $params[] = "Domain={$this->domain}";
        }
        if ($this->expiry !== null) {
            $params[] = 'Expires='.$this->expiry->format(self::EXPIRY_FORMAT);
        }
        if ($this->httpOnly) {
            $params[] = 'HttpOnly';
        }
        if ($this->maxAge > 0) {
            $params[] = "Max-Age={$this->maxAge}";
        }
        if ($this->path !== null) {
            $params[] = "Path={$this->path}";
        }
        if ($this->secure) {
            $params[] = 'Secure';
        }

        return implode('; ', $params);
    }
}
