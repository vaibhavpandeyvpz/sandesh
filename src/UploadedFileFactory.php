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

use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UploadedFileFactoryInterface;
use Psr\Http\Message\UploadedFileInterface;

/**
 * Uploaded file factory implementation.
 *
 * Creates UploadedFile instances as defined in PSR-17.
 * This factory can create uploaded files from streams and can also
 * process $_FILES-style arrays to create multiple uploaded files.
 */
class UploadedFileFactory implements UploadedFileFactoryInterface
{
    /**
     * {@inheritdoc}
     *
     * @param  StreamInterface  $stream  The stream representing the uploaded file
     * @param  int|null  $size  File size in bytes
     * @param  int  $error  Upload error code (UPLOAD_ERR_* constant)
     * @param  string|null  $clientFilename  The client-provided filename
     * @param  string|null  $clientMediaType  The client-provided media type
     * @return UploadedFileInterface A new UploadedFile instance
     *
     * @throws \InvalidArgumentException If the error code is invalid
     */
    public function createUploadedFile(
        StreamInterface $stream,
        ?int $size = null,
        int $error = \UPLOAD_ERR_OK,
        ?string $clientFilename = null,
        ?string $clientMediaType = null
    ): UploadedFileInterface {
        return new UploadedFile($stream, $size ?? 0, $error, $clientFilename, $clientMediaType);
    }

    /**
     * Create multiple uploaded files from a $_FILES-style array.
     *
     * Processes nested arrays of file data (as provided by $_FILES) and creates
     * UploadedFile instances for each file. Supports both single and multiple file uploads.
     *
     * @param  array<mixed>  $files  $_FILES-style array structure
     * @return array<UploadedFileInterface> Array of UploadedFile instances
     */
    public function createUploadedFiles(array $files): array
    {
        $factory = new StreamFactory;
        $list = [];
        foreach ($files as $key => $data) {
            if (is_array($data)) {
                if (isset($data['tmp_name'])) {
                    $list[$key] = $this->getUploadedFile($factory, $data);
                } else {
                    $list[$key] = $this->createUploadedFiles($data);
                }
            }
        }

        return $list;
    }

    /**
     * Get an uploaded file or array of uploaded files from file data.
     *
     * Handles both single file uploads and multiple file uploads (when tmp_name is an array).
     *
     * @param  StreamFactoryInterface  $factory  Stream factory for creating streams
     * @param  array<string, mixed>  $file  File data array (tmp_name, size, error, name, type)
     * @return UploadedFileInterface|array<UploadedFileInterface> Single file or array of files
     */
    protected function getUploadedFile(StreamFactoryInterface $factory, array $file): UploadedFileInterface|array
    {
        if (is_array($file['tmp_name'])) {
            $files = [];
            foreach (array_keys($file['tmp_name']) as $key) {
                $files[$key] = new UploadedFile(
                    $factory->createStreamFromFile($file['tmp_name'][$key]),
                    (int) ($file['size'][$key] ?? 0),
                    (int) ($file['error'][$key] ?? \UPLOAD_ERR_OK),
                    $file['name'][$key] ?? null,
                    $file['type'][$key] ?? null
                );
            }

            return $files;
        }

        return $this->createUploadedFile(
            $factory->createStreamFromFile($file['tmp_name']),
            (int) ($file['size'] ?? 0),
            (int) ($file['error'] ?? \UPLOAD_ERR_OK),
            $file['name'] ?? null,
            $file['type'] ?? null
        );
    }
}
