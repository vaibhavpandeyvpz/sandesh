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

use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UploadedFileFactoryInterface;
use Psr\Http\Message\UploadedFileInterface;

/**
 * Class UploadedFileFactory
 * @package Sandesh
 */
class UploadedFileFactory implements UploadedFileFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function createUploadedFile(
        StreamInterface $stream,
        int $size = null,
        int $error = \UPLOAD_ERR_OK,
        string $clientFilename = null,
        string $clientMediaType = null
    ): UploadedFileInterface
    {
        return new UploadedFile($stream, (int)$size, (int)$error, $clientFilename, $clientMediaType);
    }

    /**
     * @param array $files
     * @return UploadedFileInterface[]
     */
    public function createUploadedFiles(array $files)
    {
        $factory = new StreamFactory();
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
     * @param StreamFactoryInterface $factory
     * @param array $file
     * @return UploadedFileInterface|UploadedFileInterface[]
     */
    protected function getUploadedFile(StreamFactoryInterface $factory, array $file)
    {
        if (is_array($file['tmp_name'])) {
            $files = [];
            foreach (array_keys($file['tmp_name']) as $key) {
                $files[$key] = new UploadedFile(
                    $factory->createStreamFromFile($file['tmp_name'][$key]),
                    (int)$file['size'][$key],
                    (int)$file['error'][$key],
                    $file['name'][$key],
                    $file['type'][$key]
                );
            }
            return $files;
        }
        return $this->createUploadedFile(
            $factory->createStreamFromFile($file['tmp_name']),
            (int)$file['size'],
            (int)$file['error'],
            $file['name'],
            $file['type']
        );
    }
}
