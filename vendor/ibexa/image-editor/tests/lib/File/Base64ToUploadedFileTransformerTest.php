<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\ImageEditor\File;

use Ibexa\Core\IO\MimeTypeDetector\FileInfo;
use Ibexa\ImageEditor\File\Base64ToUploadedFileTransformer;
use PHPUnit\Framework\TestCase;

final class Base64ToUploadedFileTransformerTest extends TestCase
{
    /** @var \Ibexa\ImageEditor\File\Base64ToUploadedFileTransformer */
    private $transformer;

    protected function setUp(): void
    {
        $this->transformer = new Base64ToUploadedFileTransformer(
            new FileInfo()
        );
    }

    /**
     * @dataProvider base64ImageProvider
     */
    public function testTransform(string $base64, ?string $fileName = null): void
    {
        $uploadedFile = $this->transformer->transform($base64, $fileName);

        $this->assertFileExists($uploadedFile->getPath());

        if ($fileName) {
            $this->assertEquals(
                $fileName,
                $uploadedFile->getClientOriginalName()
            );
        }
    }

    public function base64ImageProvider(): array
    {
        return [
            [
                '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',
                'image.png',
            ],
            [
                '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',
            ],
        ];
    }
}

class_alias(Base64ToUploadedFileTransformerTest::class, 'Ibexa\Platform\Tests\ImageEditor\File\Base64ToUploadedFileTransformerTest');
