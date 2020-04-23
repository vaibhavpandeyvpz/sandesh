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

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UploadedFileInterface;
use Psr\Http\Message\UriInterface;

/**
 * Class ServerRequest
 * @package Sandesh
 */
class ServerRequest extends Request implements ServerRequestInterface
{
    /**
     * @var array
     */
    protected $attributes = [];

    /**
     * @var array
     */
    protected $cookieParams = [];

    /**
     * @var mixed
     */
    protected $parsedBody;

    /**
     * @var array
     */
    protected $queryParams = [];

    /**
     * @var array
     */
    protected $serverParams = [];

    /**
     * @var UploadedFileInterface[]
     */
    protected $uploadedFiles = [];

    /**
     * Request constructor.
     * @param string $method
     * @param UriInterface $uri
     * @param array $serverParams
     */
    public function __construct($method = 'GET', UriInterface $uri = null, array $serverParams = [])
    {
        parent::__construct($method, $uri);
        $this->serverParams = $serverParams;
    }

    /**
     * {@inheritdoc}
     */
    public function getAttribute($name, $default = null)
    {
        return array_key_exists($name, $this->attributes) ? $this->attributes[$name] : $default;
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * {@inheritdoc}
     */
    public function getCookieParams()
    {
        return $this->cookieParams;
    }

    /**
     * {@inheritdoc}
     */
    public function getParsedBody()
    {
        if ($this->parsedBody || !$this->body) {
            return $this->parsedBody;
        }
        $type = $this->getHeaderLine('Content-Type');
        if ($type) {
            list($type) = explode(';', $type, 2);
        }
        $body = (string)$this->getBody();
        switch ($type) {
            case 'application/json':
                if (extension_loaded('json')) {
                    /** @noinspection PhpComposerExtensionStubsInspection */
                    $this->parsedBody = json_decode($body, true);
                }
                break;
            case 'application/x-www-form-urlencoded':
                parse_str($body, $data);
                $this->parsedBody = $data;
                break;
            case 'text/xml':
                if (extension_loaded('libxml')) {
                    /** @noinspection PhpComposerExtensionStubsInspection */
                    $disabled = libxml_disable_entity_loader(true);
                    /** @noinspection PhpComposerExtensionStubsInspection */
                    $xml = simplexml_load_string($body);
                    /** @noinspection PhpComposerExtensionStubsInspection */
                    libxml_disable_entity_loader($disabled);
                    $this->parsedBody = $xml;
                }
                break;
            default:
                break;
        }
        return $this->parsedBody;
    }

    /**
     * {@inheritdoc}
     */
    public function getQueryParams()
    {
        return $this->queryParams;
    }

    /**
     * {@inheritdoc}
     */
    public function getServerParams()
    {
        return $this->serverParams;
    }

    /**
     * {@inheritdoc}
     */
    public function getUploadedFiles()
    {
        return $this->uploadedFiles;
    }

    /**
     * {@inheritdoc}
     */
    public function withAttribute($name, $value)
    {
        $clone = clone $this;
        $clone->attributes[$name] = $value;
        return $clone;
    }

    /**
     * {@inheritdoc}
     */
    public function withCookieParams(array $cookies)
    {
        $clone = clone $this;
        $clone->cookieParams = $cookies;
        return $clone;
    }

    /**
     * {@inheritdoc}
     */
    public function withQueryParams(array $query)
    {
        $clone = clone $this;
        $clone->queryParams = $query;
        return $clone;
    }

    /**
     * {@inheritdoc}
     */
    public function withServerParams(array $server)
    {
        $clone = clone $this;
        $clone->serverParams = $server;
        return $clone;
    }

    /**
     * {@inheritdoc}
     */
    public function withUploadedFiles(array $uploadedFiles)
    {
        MessageValidations::assertUploadedFiles($uploadedFiles);
        $clone = clone $this;
        $clone->uploadedFiles = $uploadedFiles;
        return $clone;
    }

    /**
     * {@inheritdoc}
     */
    public function withParsedBody($data)
    {
        $clone = clone $this;
        $clone->parsedBody = $data;
        return $clone;
    }

    /**
     * {@inheritdoc}
     */
    public function withoutAttribute($name)
    {
        $clone = clone $this;
        unset($clone->attributes[$name]);
        return $clone;
    }
}
