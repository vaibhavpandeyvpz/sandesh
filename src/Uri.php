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

use Psr\Http\Message\UriInterface;

/**
 * Class Uri
 * @package Sandesh
 */
class Uri implements UriInterface
{
    /**
     * @var string
     */
    protected $fragment = '';

    /**
     * @var string
     */
    protected $host = '';

    /**
     * @var string
     */
    protected $password = '';

    /**
     * @var string
     */
    protected $path = '';

    /**
     * @var int
     */
    protected $port;

    /**
     * @var string
     */
    protected $query = '';

    /**
     * @var string
     */
    protected $scheme = '';

    /**
     * @var string
     */
    protected $user = '';

    /**
     * {@inheritdoc}
     */
    public function getAuthority()
    {
        $authority = $this->host;
        $info = $this->getUserInfo();
        if ($info) {
            $authority = "{$info}@{$authority}";
        }
        if ($this->port) {
            $authority = "{$authority}:{$this->port}";
        }
        return $authority;
    }

    /**
     * {@inheritdoc}
     */
    public function getFragment()
    {
        return $this->fragment;
    }

    /**
     * {@inheritdoc}
     */
    public function getHost()
    {
        return $this->host;
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
    public function getPort()
    {
        return $this->port;
    }

    /**
     * {@inheritdoc}
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * {@inheritdoc}
     */
    public function getScheme()
    {
        return $this->scheme;
    }

    /**
     * {@inheritdoc}
     */
    public function getUserInfo()
    {
        $info = $this->user;
        if ($info && $this->password) {
            $info .= (':' . $this->password);
        }
        return $info;
    }

    /**
     * {@inheritdoc}
     */
    public function withFragment($fragment)
    {
        $fragment = MessageValidations::normalizeFragment($fragment);
        $clone = clone $this;
        $clone->fragment = $fragment;
        return $clone;
    }

    /**
     * {@inheritdoc}
     */
    public function withHost($host)
    {
        $host = strtolower($host);
        $clone = clone $this;
        $clone->host = $host;
        return $clone;
    }

    /**
     * {@inheritdoc}
     */
    public function withPath($path)
    {
        MessageValidations::assertPath($path);
        $path = MessageValidations::normalizePath($path);
        $clone = clone $this;
        $clone->path = $path;
        return $clone;
    }

    /**
     * {@inheritdoc}
     */
    public function withPort($port)
    {
        if ($port !== null) {
            MessageValidations::assertTcpUdpPort($port = (int)$port);
        }
        $clone = clone $this;
        $clone->port = $port;
        return $clone;
    }

    /**
     * {@inheritdoc}
     */
    public function withQuery($query)
    {
        MessageValidations::assertQuery($query);
        $query = MessageValidations::normalizeQuery($query);
        $clone = clone $this;
        $clone->query = $query;
        return $clone;
    }

    /**
     * {@inheritdoc}
     */
    public function withScheme($scheme)
    {
        $scheme = MessageValidations::normalizeScheme($scheme);
        $clone = clone $this;
        $clone->scheme = $scheme;
        return $clone;
    }

    /**
     * {@inheritdoc}
     */
    public function withUserInfo($user, $password = null)
    {
        $clone = clone $this;
        $clone->user = $user;
        $clone->password = $password;
        return $clone;
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        $uri = '';
        if ($this->scheme) {
            $uri .= "{$this->scheme}://";
        }
        $authority = $this->getAuthority();
        if ($authority) {
            $uri .= $authority;
        }
        if ($path = $this->path) {
            if ('/' !== $path[0]) {
                $path = "/{$path}";
            }
            $uri .= $path;
        }
        if ($this->query) {
            $uri .= "?{$this->query}";
        }
        if ($this->fragment) {
            $uri .= "#{$this->fragment}";
        }
        return $uri;
    }
}
