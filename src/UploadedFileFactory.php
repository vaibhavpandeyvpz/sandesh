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

use Interop\Http\Factory\UploadedFileFactoryInterface;

/**
 * Class UploadedFileFactory
 * @package Sandesh
 */
class UploadedFileFactory implements UploadedFileFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function createUploadedFile($file, $size = null, $error = UPLOAD_ERR_OK, $clientFilename = null, $clientMediaType = null)
    {
        return new UploadedFile($file, (int)$size, (int)$error, $clientFilename, $clientMediaType);
    }
}
