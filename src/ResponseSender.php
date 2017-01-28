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

use Psr\Http\Message\ResponseInterface;

/**
 * Class ResponseSender
 * @package Sandesh
 */
class ResponseSender implements ResponseSenderInterface
{
    /**
     * {@inheritdoc}
     */
    public function send(ResponseInterface $response)
    {
        if (headers_sent()) {
            throw new \RuntimeException('Not sending response as headers already sent');
        }
        $this->sendStatusLine($response);
        $this->sendHeaders($response);
        $this->sendBody($response);
    }

    /**
     * @param ResponseInterface $response
     */
    protected function sendBody(ResponseInterface $response)
    {
        echo $response->getBody();
    }

    /**
     * @param ResponseInterface $response
     */
    protected function sendHeaders(ResponseInterface $response)
    {
        foreach ($response->getHeaders() as $header => $values) {
            $name = str_replace(' ', '-', ucwords(str_replace('-', ' ', $header)));
            foreach ($values as $i => $value) {
                header(sprintf('%s: %s', $name, $value), $i === 0);
            }
        }
    }

    /**
     * @param ResponseInterface $response
     */
    protected function sendStatusLine(ResponseInterface $response)
    {
        header(trim(sprintf(
            'HTTP/%s %d %s',
            $response->getProtocolVersion(),
            $response->getStatusCode(),
            $response->getReasonPhrase()
        )));
    }
}
