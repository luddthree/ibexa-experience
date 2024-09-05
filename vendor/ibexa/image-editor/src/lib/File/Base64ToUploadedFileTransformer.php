<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ImageEditor\File;

use Ibexa\Core\IO\MimeTypeDetector\FileInfo;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

final class Base64ToUploadedFileTransformer implements Base64FileTransformer
{
    /** @var string */
    private $tempDir;

    /** @var \Ibexa\Core\IO\MimeTypeDetector\FileInfo */
    private $fileInfo;

    public function __construct(
        FileInfo $fileInfo,
        ?string $tempDir = null
    ) {
        $this->tempDir = $tempDir ?? sys_get_temp_dir();
        $this->fileInfo = $fileInfo;
    }

    public function transform(string $base64, ?string $fileName): UploadedFile
    {
        $decodedBase64 = base64_decode($base64);

        if (!$decodedBase64) {
            throw new TransformationFailedException('Invalid Base64');
        }

        $extension = $this->guessExtension($decodedBase64);

        $inputUri = tempnam($this->tempDir, 'Ibexa_ImageEditor_BinaryFile');
        rename($inputUri, $inputUri .= '.' . $extension);

        file_put_contents(
            $inputUri,
            $decodedBase64
        );

        register_shutdown_function('unlink', $inputUri);

        return new UploadedFile(
            $inputUri,
            $fileName ?? basename($inputUri),
        );
    }

    private function guessExtension(string $decodedBase64): string
    {
        $mimeType = $this->fileInfo->getFromBuffer($decodedBase64);

        return explode('/', $mimeType)[1];
    }
}

class_alias(Base64ToUploadedFileTransformer::class, 'Ibexa\Platform\ImageEditor\File\Base64ToUploadedFileTransformer');
