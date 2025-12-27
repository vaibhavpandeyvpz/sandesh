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
 * Class MessageValidationsTest
 */
class MessageValidationsTest extends TestCase
{
    public function test_assert_method_with_valid_methods(): void
    {
        $methods = ['GET', 'POST', 'PUT', 'DELETE', 'PATCH', 'HEAD', 'OPTIONS', 'TRACE', 'CONNECT'];
        foreach ($methods as $method) {
            MessageValidations::assertMethod($method);
            MessageValidations::assertMethod(strtolower($method));
        }
        $this->assertTrue(true); // If we get here, no exceptions were thrown
    }

    public function test_assert_method_with_invalid_method(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        MessageValidations::assertMethod('INVALID');
    }

    public function test_assert_status_code_boundary_values(): void
    {
        // Valid boundaries
        MessageValidations::assertStatusCode(100);
        MessageValidations::assertStatusCode(199);
        MessageValidations::assertStatusCode(200);
        MessageValidations::assertStatusCode(299);
        MessageValidations::assertStatusCode(300);
        MessageValidations::assertStatusCode(399);
        MessageValidations::assertStatusCode(400);
        MessageValidations::assertStatusCode(499);
        MessageValidations::assertStatusCode(500);
        MessageValidations::assertStatusCode(599);
        $this->assertTrue(true);
    }

    public function test_assert_status_code_invalid_below_minimum(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        MessageValidations::assertStatusCode(99);
    }

    public function test_assert_status_code_invalid_above_maximum(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        MessageValidations::assertStatusCode(600);
    }

    public function test_assert_status_code_with_string_numeric(): void
    {
        // String numeric values should be accepted and converted
        MessageValidations::assertStatusCode('200');
        MessageValidations::assertStatusCode('404');
        $this->assertTrue(true);
    }

    public function test_assert_status_code_with_non_numeric_string(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        MessageValidations::assertStatusCode('not-a-number');
    }

    public function test_assert_tcp_udp_port_boundary_values(): void
    {
        MessageValidations::assertTcpUdpPort(0);
        MessageValidations::assertTcpUdpPort(65534);
        $this->assertTrue(true);
    }

    public function test_assert_tcp_udp_port_invalid(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        MessageValidations::assertTcpUdpPort(-1);
        $this->expectException(\InvalidArgumentException::class);
        MessageValidations::assertTcpUdpPort(65535);
    }

    public function test_assert_path_with_query(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        MessageValidations::assertPath('/path?query=value');
    }

    public function test_assert_path_with_fragment(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        MessageValidations::assertPath('/path#fragment');
    }

    public function test_assert_query_with_fragment(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        MessageValidations::assertQuery('key=value#fragment');
    }

    public function test_assert_protocol_version_valid(): void
    {
        MessageValidations::assertProtocolVersion('1.0');
        MessageValidations::assertProtocolVersion('1.1');
        MessageValidations::assertProtocolVersion('2.0');
        $this->assertTrue(true);
    }

    public function test_assert_protocol_version_invalid(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        MessageValidations::assertProtocolVersion('0.9');
        $this->expectException(\InvalidArgumentException::class);
        MessageValidations::assertProtocolVersion('10.0');
        $this->expectException(\InvalidArgumentException::class);
        MessageValidations::assertProtocolVersion('1.10');
    }

    public function test_assert_header_name_valid(): void
    {
        $validNames = ['Content-Type', 'X-Custom-Header', 'Accept-Language', 'User-Agent'];
        foreach ($validNames as $name) {
            MessageValidations::assertHeaderName($name);
        }
        $this->assertTrue(true);
    }

    public function test_assert_header_name_invalid(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        MessageValidations::assertHeaderName('Invalid<Name');
        $this->expectException(\InvalidArgumentException::class);
        MessageValidations::assertHeaderName('Invalid Name');
    }

    public function test_assert_header_value_valid(): void
    {
        $validValues = ['text/plain', 'application/json', 'value with spaces'];
        foreach ($validValues as $value) {
            MessageValidations::assertHeaderValue($value);
        }
        $this->assertTrue(true);
    }

    public function test_assert_header_value_invalid(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        MessageValidations::assertHeaderValue("value\r\nwith newline");
        $this->expectException(\InvalidArgumentException::class);
        MessageValidations::assertHeaderValue("value with\nnewline");
    }

    public function test_assert_cookie_expiry_with_date_time(): void
    {
        MessageValidations::assertCookieExpiry(new \DateTime);
        $this->assertTrue(true);
    }

    public function test_assert_cookie_expiry_with_string(): void
    {
        MessageValidations::assertCookieExpiry('Wed, 21 Oct 2015 07:28:00 GMT');
        $this->assertTrue(true);
    }

    public function test_assert_cookie_expiry_with_int(): void
    {
        MessageValidations::assertCookieExpiry(time());
        $this->assertTrue(true);
    }

    public function test_assert_cookie_expiry_invalid(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        MessageValidations::assertCookieExpiry([]);
    }

    public function test_assert_uploaded_files_valid(): void
    {
        $files = [
            new UploadedFile('php://memory', 0, UPLOAD_ERR_OK),
            new UploadedFile('php://memory', 0, UPLOAD_ERR_OK),
        ];
        MessageValidations::assertUploadedFiles($files);
        $this->assertTrue(true);
    }

    public function test_assert_uploaded_files_invalid(): void
    {
        $this->expectException(\UnexpectedValueException::class);
        MessageValidations::assertUploadedFiles(['not an uploaded file']);
    }

    public function test_normalize_query_with_empty_string(): void
    {
        $this->assertEquals('', MessageValidations::normalizeQuery(''));
        $this->assertEquals('', MessageValidations::normalizeQuery('?'));
    }

    public function test_normalize_query_with_special_characters(): void
    {
        $query = 'key=value with spaces';
        $normalized = MessageValidations::normalizeQuery($query);
        $this->assertStringContainsString('key', $normalized);
        $this->assertStringContainsString('value', $normalized);
    }

    public function test_normalize_path_with_special_characters(): void
    {
        $path = '/path with spaces';
        $normalized = MessageValidations::normalizePath($path);
        $this->assertStringStartsWith('/', $normalized);
    }

    public function test_normalize_scheme(): void
    {
        $this->assertEquals('http', MessageValidations::normalizeScheme('http'));
        $this->assertEquals('http', MessageValidations::normalizeScheme('HTTP'));
        $this->assertEquals('http', MessageValidations::normalizeScheme('http://'));
        $this->assertEquals('https', MessageValidations::normalizeScheme('https://'));
    }

    public function test_normalize_fragment(): void
    {
        $this->assertEquals('%23test', MessageValidations::normalizeFragment('#test'));
        $this->assertEquals('test', MessageValidations::normalizeFragment('test'));
    }
}
