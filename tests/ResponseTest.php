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
 * Class ResponseTest
 * @package Sandesh
 */
class ResponseTest extends \PHPUnit_Framework_TestCase
{
    public function testBody()
    {
        $response = new Response(200, $body = new Stream());
        $this->assertSame($body, $response->getBody());
    }

    public function testStatus()
    {
        $response = new Response();
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(
            401,
            $response->withStatus(401)
                ->getStatusCode()
        );
        $this->assertEmpty(
            $response->withStatus(401)
                ->getReasonPhrase()
        );
        $this->assertEquals(
            'Unauthorized',
            $response->withStatus(401, 'Unauthorized')
                ->getReasonPhrase()
        );
    }

    public function testStatusInvalid()
    {
        $response = new Response();
        $this->setExpectedException('InvalidArgumentException');
        $response->withStatus(600);
    }
}
