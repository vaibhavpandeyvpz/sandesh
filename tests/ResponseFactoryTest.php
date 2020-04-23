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
 * Class ResponseFactoryTest
 * @package Sandesh
 */
class ResponseFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testStatusCode()
    {
        $factory = new ResponseFactory();
        $this->assertEquals(200, $factory->createResponse()->getStatusCode());
        $this->assertEquals(404, $factory->createResponse(404)->getStatusCode());
        $response = $factory->createResponse(401, 'Unauthorised');
        $this->assertEquals(401, $response->getStatusCode());
        $this->assertEquals('Unauthorised', $response->getReasonPhrase());
    }
}
