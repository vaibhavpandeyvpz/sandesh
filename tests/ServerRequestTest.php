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
 * Class ServerRequestTest
 * @package Sandesh
 */
class ServerRequestTest extends \PHPUnit_Framework_TestCase
{
    public function testAttributes()
    {
        $request = new ServerRequest();
        $this->assertEmpty($request->getAttributes());
        $this->assertNull($request->getAttribute('something'));
        $this->assertFalse($request->getAttribute('something', false));
        $this->assertEquals(121, $request->getAttribute('something', 121));
        $this->assertFalse(
            $request->withAttribute('something', false)
                ->getAttribute('something')
        );
        $this->assertEquals(
            121,
            $request->withAttribute('something', 121)
                ->getAttribute('something')
        );
        $this->assertEquals(
            array(
                'something' => false,
                'anything' => 121,
            ),
            $request->withAttribute('something', false)
                ->withAttribute('anything', 121)
                ->getAttributes()
        );
        $this->assertFalse(
            $request->withAttribute('something', true)
                ->withoutAttribute('something')
                ->getAttribute('something', false)
        );
    }

    public function testCookieParams()
    {
        $request = new ServerRequest();
        $this->assertEmpty($request->getCookieParams());
        $this->assertEquals(
            $cookies = array(
                'username' => 'vpz',
                'guid' => 'something-random-1234',
            ),
            $request->withCookieParams($cookies)
                ->getCookieParams()
        );
    }

    public function testMethod()
    {
        $request = new ServerRequest();
        $this->assertEquals('GET', $request->getMethod());
        $this->assertEquals(
            'POST',
            $request->withMethod('POST')
                ->getMethod()
        );
    }

    public function testParsedBody()
    {
        $request = new ServerRequest();
        $this->assertNull($request->getParsedBody());
        $this->assertEquals(
            $parsedBody = array('username' => 'vpz'),
            $request->withParsedBody($parsedBody)
                ->getParsedBody()
        );
    }

    public function testParsedBodyJson()
    {
        $body = new Stream();
        $body->write('{"username": "vpz"}');
        $request = new ServerRequest();
        $request = $request->withBody($body)
            ->withHeader('Content-Type', 'application/json; charset=utf-8');
        $this->assertEquals(
            array('username' => 'vpz'),
            $request->getParsedBody()
        );
        $body->close();
        $this->assertEquals(
            array('username' => 'vpz'),
            $request->getParsedBody()
        );
    }

    public function testParsedBodyForm()
    {
        $body = new Stream();
        $body->write('username=vpz&id=121');
        $request = new ServerRequest();
        $request = $request->withBody($body)
            ->withHeader('Content-Type', 'application/x-www-form-urlencoded');
        $this->assertEquals(
            array(
                'username' => 'vpz',
                'id' => '121'
            ),
            $request->getParsedBody()
        );
        $body->close();
        $this->assertEquals(
            array(
                'username' => 'vpz',
                'id' => '121'
            ),
            $request->getParsedBody()
        );
    }

    public function testParsedBodyXml()
    {
        $body = new Stream();
        $body->write('<user>');
        $body->write('<username>vpz</username>');
        $body->write('<id>121</id>');
        $body->write('</user>');
        $request = new ServerRequest();
        $request = $request->withBody($body)
            ->withHeader('Content-Type', 'text/xml');
        /** @var \SimpleXMLElement $parsedBody */
        $parsedBody = $request->getParsedBody();
        $this->assertInstanceOf(\SimpleXMLElement::class, $parsedBody);
        $this->assertEquals(2, $parsedBody->count());
        $this->assertEquals('vpz', $parsedBody->username);
        $this->assertEquals('121', $parsedBody->id);
        $body->close();
        $parsedBody = $request->getParsedBody();
        $this->assertInstanceOf(\SimpleXMLElement::class, $parsedBody);
        $this->assertEquals(2, $parsedBody->count());
        $this->assertEquals('vpz', $parsedBody->username);
        $this->assertEquals('121', $parsedBody->id);
    }

    public function testParsedBodyUnknown()
    {
        $body = new Stream();
        $body->write('Something');
        $request = new ServerRequest();
        $request = $request->withBody($body)
            ->withHeader('Content-Type', 'text/plain');
        $this->assertNull($request->getParsedBody());
    }

    public function testQueryParams()
    {
        $request = new ServerRequest();
        $this->assertEmpty($request->getCookieParams());
        $this->assertEquals(
            $query = array('test' => 'true'),
            $request->withQueryParams($query)
                ->getQueryParams()
        );
    }

    public function testServerParams()
    {
        $request = new ServerRequest();
        $request = $request->withServerParams($server = array(
            'CONTENT_TYPE' => 'text/plain',
            'X_POWERED_BY' => 'PHP/7.1',
        ));
        $this->assertNotEmpty($request->getServerParams());
        $this->assertEquals($server, $request->getServerParams());
    }

    public function testUploadedFiles()
    {
        $request = new ServerRequest();
        $this->assertEmpty($request->getUploadedFiles());
        $files = array(
            new UploadedFile('php://memory', 128, UPLOAD_ERR_OK, 'something.txt', 'text/plain')
        );
        $this->assertNotEmpty(
            $request->withUploadedFiles($files)
                ->getUploadedFiles()
        );
        $this->assertCount(
            1,
            $request->withUploadedFiles($files)
                ->getUploadedFiles()
        );
        $this->setExpectedException(\UnexpectedValueException::class);
        $request->withUploadedFiles(array('something.txt'));
    }
}
