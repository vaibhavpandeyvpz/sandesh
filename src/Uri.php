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

use Psr\Http\Message\UriInterface;

/**
 * URI implementation.
 *
 * Represents a URI (Uniform Resource Identifier) as defined in RFC 3986.
 * This class provides methods to access and modify all components of a URI:
 * scheme, authority (user info, host, port), path, query, and fragment.
 */
class Uri implements UriInterface
{
    /**
     * URI fragment (the part after '#').
     */
    protected string $fragment = '';

    /**
     * URI host (domain name or IP address).
     */
    protected string $host = '';

    /**
     * URI password component (for user info).
     */
    protected ?string $password = null;

    /**
     * URI path component.
     */
    protected string $path = '';

    /**
     * URI port number.
     */
    protected ?int $port = null;

    /**
     * URI query string (the part after '?').
     */
    protected string $query = '';

    /**
     * URI scheme (e.g., 'http', 'https').
     */
    protected string $scheme = '';

    /**
     * URI user component (for user info).
     */
    protected string $user = '';

    /**
     * {@inheritdoc}
     *
     * @return string The authority component (userinfo@host:port)
     */
    public function getAuthority(): string
    {
        if ($this->host === '') {
            return '';
        }
        $authority = $this->host;
        $info = $this->getUserInfo();
        if ($info !== '') {
            $authority = "{$info}@{$authority}";
        }
        if ($this->port !== null) {
            $authority .= ":{$this->port}";
        }

        return $authority;
    }

    /**
     * {@inheritdoc}
     *
     * @return string The fragment component
     */
    public function getFragment(): string
    {
        return $this->fragment;
    }

    /**
     * {@inheritdoc}
     *
     * @return string The host component
     */
    public function getHost(): string
    {
        return $this->host;
    }

    /**
     * {@inheritdoc}
     *
     * @return string The path component
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * {@inheritdoc}
     *
     * @return int|null The port number, or null if no port is specified
     */
    public function getPort(): ?int
    {
        return $this->port;
    }

    /**
     * {@inheritdoc}
     *
     * @return string The query string component
     */
    public function getQuery(): string
    {
        return $this->query;
    }

    /**
     * {@inheritdoc}
     *
     * @return string The scheme component
     */
    public function getScheme(): string
    {
        return $this->scheme;
    }

    /**
     * {@inheritdoc}
     *
     * @return string The user info component (user:password)
     */
    public function getUserInfo(): string
    {
        $info = $this->user;
        if ($info !== '' && $this->password !== null && $this->password !== '') {
            $info .= ':'.$this->password;
        } elseif ($info !== '' && $this->password === '') {
            // Empty string password should show as user:
            $info .= ':';
        }

        return $info;
    }

    /**
     * {@inheritdoc}
     *
     * @param  string  $fragment  The fragment to use with the new instance
     * @return static A new instance with the specified fragment
     */
    public function withFragment(string $fragment): static
    {
        $fragment = MessageValidations::normalizeFragment($fragment);
        $clone = clone $this;
        $clone->fragment = $fragment;

        return $clone;
    }

    /**
     * {@inheritdoc}
     *
     * @param  string  $host  The hostname to use with the new instance
     * @return static A new instance with the specified host
     */
    public function withHost(string $host): static
    {
        $host = strtolower($host);
        $clone = clone $this;
        $clone->host = $host;

        return $clone;
    }

    /**
     * {@inheritdoc}
     *
     * @param  string  $path  The path to use with the new instance
     * @return static A new instance with the specified path
     *
     * @throws \InvalidArgumentException If the path contains query or fragment
     */
    public function withPath(string $path): static
    {
        MessageValidations::assertPath($path);
        $path = MessageValidations::normalizePath($path);
        $clone = clone $this;
        $clone->path = $path;

        return $clone;
    }

    /**
     * {@inheritdoc}
     *
     * @param  int|null  $port  The port to use with the new instance
     * @return static A new instance with the specified port
     *
     * @throws \InvalidArgumentException If the port is out of valid range
     */
    public function withPort(?int $port): static
    {
        if ($port !== null) {
            MessageValidations::assertTcpUdpPort($port);
        }
        $clone = clone $this;
        $clone->port = $port;

        return $clone;
    }

    /**
     * {@inheritdoc}
     *
     * @param  string  $query  The query string to use with the new instance
     * @return static A new instance with the specified query string
     *
     * @throws \InvalidArgumentException If the query contains a fragment
     */
    public function withQuery(string $query): static
    {
        MessageValidations::assertQuery($query);
        $query = MessageValidations::normalizeQuery($query);
        $clone = clone $this;
        $clone->query = $query;

        return $clone;
    }

    /**
     * {@inheritdoc}
     *
     * @param  string  $scheme  The scheme to use with the new instance
     * @return static A new instance with the specified scheme
     */
    public function withScheme(string $scheme): static
    {
        $scheme = MessageValidations::normalizeScheme($scheme);
        $clone = clone $this;
        $clone->scheme = $scheme;

        return $clone;
    }

    /**
     * {@inheritdoc}
     *
     * @param  string  $user  The user name to use for authority
     * @param  string|null  $password  The password associated with $user
     * @return static A new instance with the specified user information
     */
    public function withUserInfo(string $user, ?string $password = null): static
    {
        $clone = clone $this;
        $clone->user = $user;
        $clone->password = $password;

        return $clone;
    }

    /**
     * {@inheritdoc}
     *
     * Returns the string representation of the URI.
     *
     * @return string The complete URI as a string
     */
    public function __toString(): string
    {
        $uri = '';
        if ($this->scheme !== '') {
            $uri .= "{$this->scheme}://";
        }
        $authority = $this->getAuthority();
        if ($authority !== '') {
            $uri .= $authority;
        }
        $path = $this->path;
        if ($path !== '') {
            if (! str_starts_with($path, '/')) {
                $path = "/{$path}";
            }
            $uri .= $path;
        }
        if ($this->query !== '') {
            $uri .= "?{$this->query}";
        }
        if ($this->fragment !== '') {
            $uri .= "#{$this->fragment}";
        }

        return $uri;
    }
}
