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

use PHPUnit\Framework\TestCase;

/**
 * Class ResponseFactoryTest
 */
class ResponseFactoryTest extends TestCase
{
    public function test_status_code(): void
    {
        $factory = new ResponseFactory;
        $this->assertEquals(200, $factory->createResponse()->getStatusCode());
        $this->assertEquals(404, $factory->createResponse(404)->getStatusCode());
        $response = $factory->createResponse(401, 'Unauthorised');
        $this->assertEquals(401, $response->getStatusCode());
        $this->assertEquals('Unauthorised', $response->getReasonPhrase());
    }

    public function test_create_response_with_all_status_codes(): void
    {
        $factory = new ResponseFactory;
        $statusCodes = [200, 201, 301, 400, 404, 500, 503];
        foreach ($statusCodes as $code) {
            $response = $factory->createResponse($code);
            $this->assertEquals($code, $response->getStatusCode());
        }
    }

    public function test_create_response_with_empty_reason_phrase(): void
    {
        $factory = new ResponseFactory;
        $response = $factory->createResponse(204);
        $this->assertEquals('', $response->getReasonPhrase());
    }
}
