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
 * Class Cookie
 * @package Sandesh
 */
class Cookie implements CookieInterface
{
    const EXPIRY_FORMAT = 'l, d-M-Y H:i:s T';

    /**
     * @var string|null
     */
    protected $domain;

    /**
     * @var \DateTimeInterface|null
     */
    protected $expiry;

    /**
     * @var bool
     */
    protected $httpOnly = false;

    /**
     * @var int
     */
    protected $maxAge = 0;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string|null
     */
    protected $path;

    /**
     * @var bool
     */
    protected $secure = false;

    /**
     * @var string|null
     */
    protected $value;

    /**
     * Cookie constructor.
     * @param string $name
     */
    public function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * {@inheritdoc}
     */
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * {@inheritdoc}
     */
    public function getExpiry()
    {
        return $this->expiry;
    }

    /**
     * {@inheritdoc}
     */
    public function getMaxAge()
    {
        return $this->maxAge;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * {@inheritdoc}
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * {@inheritdoc}
     */
    public function isHttpOnly()
    {
        return $this->httpOnly;
    }

    /**
     * {@inheritdoc}
     */
    public function isSecure()
    {
        return $this->secure;
    }

    /**
     * {@inheritdoc}
     */
    public function withDomain($domain)
    {
        $clone = clone $this;
        $clone->domain = $domain;
        return $clone;
    }

    /**
     * {@inheritdoc}
     */
    public function withExpiry($expiry)
    {
        if (null !== $expiry) {
            MessageValidations::assertCookieExpiry($expiry);
        }
        $clone = clone $this;
        $clone->expiry = MessageValidations::normalizeCookieExpiry($expiry);
        return $clone;
    }

    /**
     * {@inheritdoc}
     */
    public function withHttpOnly($flag)
    {
        $clone = clone $this;
        $clone->httpOnly = (bool)$flag;
        return $clone;
    }

    /**
     * {@inheritdoc}
     */
    public function withMaxAge($age)
    {
        $clone = clone $this;
        $clone->maxAge = (int)$age;
        return $clone;
    }

    /**
     * {@inheritdoc}
     */
    public function withName($name)
    {
        $clone = clone $this;
        $clone->name = $name;
        return $clone;
    }

    /**
     * {@inheritdoc}
     */
    public function withPath($path)
    {
        $clone = clone $this;
        $clone->path = $path;
        return $clone;
    }

    /**
     * {@inheritdoc}
     */
    public function withSecure($flag)
    {
        $clone = clone $this;
        $clone->secure = (bool)$flag;
        return $clone;
    }

    /**
     * {@inheritdoc}
     */
    public function withValue($value)
    {
        $clone = clone $this;
        $clone->value = $value;
        return $clone;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        $params = array($this->name . '=' . urlencode($this->value));
        if ($this->domain) {
            $params[] = "Domain={$this->domain}";
        }
        if ($this->expiry) {
            $params[] = "Expires=" . $this->expiry->format(self::EXPIRY_FORMAT);
        }
        if ($this->httpOnly) {
            $params[] = 'HttpOnly';
        }
        if ($this->maxAge > 0) {
            $params[] = "Max-Age={$this->maxAge}";
        }
        if ($this->path) {
            $params[] = "Path={$this->path}";
        }
        if ($this->secure) {
            $params[] = 'Secure';
        }
        return implode('; ', $params);
    }
}
