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

use Interop\Http\Factory\UriFactoryInterface;

/**
 * Class UriFactory
 * @package Sandesh
 */
class UriFactory implements UriFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function createUri($uri = '')
    {
        $obj = new Uri();
        if (empty($uri)) {
            return $obj;
        }
        $url = parse_url($uri);
        if (!$url) {
            throw new \InvalidArgumentException('URL passed is not a well-formed URI');
        }
        if (isset($url['fragment'])) {
            $obj = $obj->withFragment($url['fragment']);
        }
        if (isset($url['host'])) {
            $obj = $obj->withHost($url['host']);
        }
        if (isset($url['path'])) {
            $obj = $obj->withPath($url['path']);
        }
        if (isset($url['port'])) {
            $obj = $obj->withPort($url['port']);
        }
        if (isset($url['query'])) {
            $obj = $obj->withQuery($url['query']);
        }
        if (isset($url['scheme'])) {
            $obj = $obj->withScheme($url['scheme']);
        }
        if (isset($url['user'])) {
            $password = isset($url['pass']) ? $url['pass'] : null;
            $obj = $obj->withUserInfo($url['user'], $password);
        }
        return $obj;
    }
}
