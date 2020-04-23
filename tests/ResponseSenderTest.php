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
 * @return bool
 */
function headers_sent()
{
    global $_GLOBALS;
    if (isset($_GLOBALS['dno'])) {
        // do not override
        return \headers_sent();
    }
    return false;
}

/**
 * @param string $value
 * @return bool|array
 */
function header($value = null)
{
    static $values;
    if (is_null($value)) {
        return $values;
    }
    if (is_null($values)) {
        $values = array();
    }
    $values[] = $value;
    return false;
}

/**
 * Class ResponseSenderTest
 * @package Sandesh
 */
class ResponseSenderTest extends \PHPUnit_Framework_TestCase
{
    protected $response;

    protected function setUp()
    {
        $response = new Response();
        $response = $response->withStatus(404, 'Not Found')
            ->withHeader('content-type', 'text/plain')
            ->withHeader('X-Powered-By', 'PHP/7.0');
        $response->getBody()->write('This URL does not exist.');
        $this->response = $response;
    }

    public function testHeadersSent()
    {
        global $_GLOBALS;
        $_GLOBALS['dno'] = true;
        $sender = new ResponseSender();
        $this->setExpectedException(\RuntimeException::class);
        $sender->send($this->response);
        unset($_GLOBALS['dno']);
    }

    public function testHeaders()
    {
        $sender = new ResponseSender();
        $sender->send($this->response);
        $headers = header();
        $this->assertInternalType('array', $headers);
        $this->assertEquals('HTTP/1.1 404 Not Found', $headers[0]);
        $this->assertEquals('Content-Type: text/plain', $headers[1]);
        $this->assertEquals('X-Powered-By: PHP/7.0', $headers[2]);
    }

    public function testBody()
    {
        $sender = new ResponseSender();
        $this->expectOutputString('This URL does not exist.');
        $sender->send($this->response);
    }
}
