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

use Psr\Http\Message\ServerRequestFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;

/**
 * Class ServerRequestFactory
 * @package Sandesh
 */
class ServerRequestFactory implements ServerRequestFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function createServerRequest(string $method, $uri, array $serverParams = []): ServerRequestInterface
    {
        if (is_string($uri)) {
            $factory = new UriFactory();
            $uri = $factory->createUri($uri);
        }
        $request = new ServerRequest($method, $uri, $serverParams);
        if (count($serverParams)) {
            $protocolVersion = self::getProtocolVersion($serverParams);
            $request = $request->withProtocolVersion($protocolVersion);
            $headers = self::getHeaders($serverParams);
            foreach ($headers as $name => $value) {
                $request = $request->withHeader($name, $value);
            }
        }
        $request = $request->withBody(self::getPhpInputStream());
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $request;
    }

    /**
     * @param array $server
     * @return array
     */
    protected static function getHeaders(array $server)
    {
        $headers = [];
        static $pick = ['CONTENT_', 'HTTP_'];
        foreach ($server as $key => $value) {
            if (!$value) {
                continue;
            }
            if (strpos($key, 'REDIRECT_') === 0) {
                $key = substr($key, 9);
                if (array_key_exists($key, $server)) {
                    continue;
                }
            }
            foreach ($pick as $prefix) {
                if (strpos($key, $prefix) === 0) {
                    if ($prefix !== $pick[0]) {
                        $key = substr($key, strlen($prefix));
                    }
                    $key = strtolower(strtr($key, '_', '-'));
                    $headers[$key] = $value;
                    continue;
                }
            }
        }
        return $headers;
    }

    /**
     * @return StreamInterface
     */
    protected static function getPhpInputStream()
    {
        $temp = fopen('php://temp', 'w+');
        stream_copy_to_stream($input = fopen('php://input', 'r'), $temp);
        fclose($input);
        $stream = new Stream($temp);
        $stream->rewind();
        return $stream;
    }

    /**
     * @param array $server
     * @return string
     */
    protected static function getProtocolVersion(array $server)
    {
        if (isset($server['SERVER_PROTOCOL'])) {
            return str_replace('HTTP/', '', $server['SERVER_PROTOCOL']);
        }
        return '1.1';
    }
}
