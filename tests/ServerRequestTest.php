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
use SimpleXMLElement;

/**
 * Class ServerRequestTest
 */
class ServerRequestTest extends TestCase
{
    public function test_attributes(): void
    {
        $request = new ServerRequest;
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
            [
                'something' => false,
                'anything' => 121,
            ],
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

    public function test_cookie_params(): void
    {
        $request = new ServerRequest;
        $this->assertEmpty($request->getCookieParams());
        $cookies = [
            'username' => 'vpz',
            'guid' => 'something-random-1234',
        ];
        $this->assertEquals(
            $cookies,
            $request->withCookieParams($cookies)
                ->getCookieParams()
        );
    }

    public function test_method(): void
    {
        $request = new ServerRequest;
        $this->assertEquals('GET', $request->getMethod());
        $this->assertEquals(
            'POST',
            $request->withMethod('POST')
                ->getMethod()
        );
    }

    public function test_parsed_body(): void
    {
        $request = new ServerRequest;
        $this->assertNull($request->getParsedBody());
        $parsedBody = ['username' => 'vpz'];
        $this->assertEquals(
            $parsedBody,
            $request->withParsedBody($parsedBody)
                ->getParsedBody()
        );
    }

    public function test_parsed_body_json(): void
    {
        $body = new Stream;
        $body->write('{"username": "vpz"}');
        $request = new ServerRequest;
        $request = $request->withBody($body)
            ->withHeader('Content-Type', 'application/json; charset=utf-8');
        $this->assertEquals(
            ['username' => 'vpz'],
            $request->getParsedBody()
        );
        $body->close();
        $this->assertEquals(
            ['username' => 'vpz'],
            $request->getParsedBody()
        );
    }

    public function test_parsed_body_form(): void
    {
        $body = new Stream;
        $body->write('username=vpz&id=121');
        $request = new ServerRequest;
        $request = $request->withBody($body)
            ->withHeader('Content-Type', 'application/x-www-form-urlencoded');
        $this->assertEquals(
            [
                'username' => 'vpz',
                'id' => '121',
            ],
            $request->getParsedBody()
        );
        $body->close();
        $this->assertEquals(
            [
                'username' => 'vpz',
                'id' => '121',
            ],
            $request->getParsedBody()
        );
    }

    public function test_parsed_body_xml(): void
    {
        $body = new Stream;
        $body->write('<user>');
        $body->write('<username>vpz</username>');
        $body->write('<id>121</id>');
        $body->write('</user>');
        $request = new ServerRequest;
        $request = $request->withBody($body)
            ->withHeader('Content-Type', 'text/xml');
        /** @var SimpleXMLElement $parsedBody */
        $parsedBody = $request->getParsedBody();
        $this->assertInstanceOf(SimpleXMLElement::class, $parsedBody);
        $this->assertEquals(2, $parsedBody->count());
        $this->assertEquals('vpz', $parsedBody->username);
        $this->assertEquals('121', $parsedBody->id);
        $body->close();
        $parsedBody = $request->getParsedBody();
        $this->assertInstanceOf(SimpleXMLElement::class, $parsedBody);
        $this->assertEquals(2, $parsedBody->count());
        $this->assertEquals('vpz', $parsedBody->username);
        $this->assertEquals('121', $parsedBody->id);
    }

    public function test_parsed_body_unknown(): void
    {
        $body = new Stream;
        $body->write('Something');
        $request = new ServerRequest;
        $request = $request->withBody($body)
            ->withHeader('Content-Type', 'text/plain');
        $this->assertNull($request->getParsedBody());
    }

    public function test_query_params(): void
    {
        $request = new ServerRequest;
        $this->assertEmpty($request->getQueryParams());
        $query = ['test' => 'true'];
        $this->assertEquals(
            $query,
            $request->withQueryParams($query)
                ->getQueryParams()
        );
    }

    public function test_server_params(): void
    {
        $request = new ServerRequest;
        $server = [
            'CONTENT_TYPE' => 'text/plain',
            'X_POWERED_BY' => 'PHP/7.1',
        ];
        $request = $request->withServerParams($server);
        $this->assertNotEmpty($request->getServerParams());
        $this->assertEquals($server, $request->getServerParams());
    }

    public function test_uploaded_files(): void
    {
        $request = new ServerRequest;
        $this->assertEmpty($request->getUploadedFiles());
        $files = [
            new UploadedFile('php://memory', 128, UPLOAD_ERR_OK, 'something.txt', 'text/plain'),
        ];
        $this->assertNotEmpty(
            $request->withUploadedFiles($files)
                ->getUploadedFiles()
        );
        $this->assertCount(
            1,
            $request->withUploadedFiles($files)
                ->getUploadedFiles()
        );
        $this->expectException(\UnexpectedValueException::class);
        $request->withUploadedFiles(['something.txt']);
    }

    public function test_parsed_body_with_invalid_json(): void
    {
        $body = new Stream;
        $body->write('{invalid json}');
        $request = new ServerRequest;
        $request = $request->withBody($body)
            ->withHeader('Content-Type', 'application/json');
        $parsed = $request->getParsedBody();
        // Should return null if JSON is invalid
        $this->assertNull($parsed);
    }

    public function test_parsed_body_with_invalid_xml(): void
    {
        $body = new Stream;
        $body->write('<invalid>xml');
        $request = new ServerRequest;
        $request = $request->withBody($body)
            ->withHeader('Content-Type', 'text/xml');
        $parsed = $request->getParsedBody();
        // Should return null or false if XML is invalid
        $this->assertTrue($parsed === null || $parsed === false);
    }

    public function test_parsed_body_with_empty_body(): void
    {
        $request = new ServerRequest;
        $request = $request->withBody(new Stream)
            ->withHeader('Content-Type', 'application/json');
        $this->assertNull($request->getParsedBody());
    }

    public function test_parsed_body_caching(): void
    {
        $body = new Stream;
        $body->write('{"test": "value"}');
        $request = new ServerRequest;
        $request = $request->withBody($body)
            ->withHeader('Content-Type', 'application/json');
        $first = $request->getParsedBody();
        $second = $request->getParsedBody();
        $this->assertSame($first, $second);
    }

    public function test_parsed_body_without_json_extension(): void
    {
        // Test JSON without extension (should still work if json extension is loaded)
        $body = new Stream;
        $body->write('{"test": "value"}');
        $request = new ServerRequest;
        $request = $request->withBody($body)
            ->withHeader('Content-Type', 'application/json');
        $parsed = $request->getParsedBody();
        if (extension_loaded('json')) {
            $this->assertIsArray($parsed);
        } else {
            $this->assertNull($parsed);
        }
    }

    public function test_parsed_body_without_libxml_extension(): void
    {
        $body = new Stream;
        $body->write('<root><test>value</test></root>');
        $request = new ServerRequest;
        $request = $request->withBody($body)
            ->withHeader('Content-Type', 'text/xml');
        $parsed = $request->getParsedBody();
        if (extension_loaded('libxml')) {
            $this->assertInstanceOf(\SimpleXMLElement::class, $parsed);
        } else {
            $this->assertNull($parsed);
        }
    }

    public function test_parsed_body_with_content_type_parameters(): void
    {
        $body = new Stream;
        $body->write('{"test": "value"}');
        $request = new ServerRequest;
        $request = $request->withBody($body)
            ->withHeader('Content-Type', 'application/json; charset=utf-8');
        $parsed = $request->getParsedBody();
        if (extension_loaded('json')) {
            $this->assertIsArray($parsed);
            $this->assertEquals('value', $parsed['test']);
        }
    }

    public function test_all_upload_error_codes(): void
    {
        $errorCodes = [
            UPLOAD_ERR_OK,
            UPLOAD_ERR_INI_SIZE,
            UPLOAD_ERR_FORM_SIZE,
            UPLOAD_ERR_PARTIAL,
            UPLOAD_ERR_NO_FILE,
            UPLOAD_ERR_NO_TMP_DIR,
            UPLOAD_ERR_CANT_WRITE,
            UPLOAD_ERR_EXTENSION,
        ];
        foreach ($errorCodes as $errorCode) {
            $file = new UploadedFile('php://memory', 0, $errorCode);
            $this->assertEquals($errorCode, $file->getError());
        }
    }

    public function test_immutability(): void
    {
        $request = new ServerRequest;
        $original = $request;
        $this->assertNotSame($original, $request->withAttribute('test', 'value'));
        $this->assertNotSame($original, $request->withCookieParams(['test' => 'value']));
        $this->assertNotSame($original, $request->withQueryParams(['test' => 'value']));
        $this->assertNotSame($original, $request->withParsedBody(['test' => 'value']));
        $this->assertNotSame($original, $request->withServerParams(['test' => 'value']));
        $this->assertNotSame($original, $request->withUploadedFiles([]));
    }

    public function test_attribute_with_null_value(): void
    {
        $request = new ServerRequest;
        $request = $request->withAttribute('null-value', null);
        $this->assertNull($request->getAttribute('null-value'));
        $this->assertNull($request->getAttribute('null-value', 'default'));
    }

    public function test_without_attribute_on_non_existent(): void
    {
        $request = new ServerRequest;
        $result = $request->withoutAttribute('non-existent');
        $this->assertNotSame($request, $result);
        $this->assertNull($result->getAttribute('non-existent'));
    }
}
