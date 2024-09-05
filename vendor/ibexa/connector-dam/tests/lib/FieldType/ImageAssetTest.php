<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Connector\Dam\FieldType;

use Ibexa\Connector\Dam\FieldType\ImageAsset\Type;
use Ibexa\Connector\Dam\FieldType\ImageAsset\Value;
use Ibexa\Contracts\Core\FieldType\Value as SPIValue;
use Ibexa\Contracts\Core\Persistence\Content\Handler as SPIContentHandler;
use Ibexa\Contracts\Core\Persistence\Content\VersionInfo;
use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo;
use Ibexa\Contracts\Core\Repository\Values\Content\Relation;
use Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType;
use Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition;
use Ibexa\Core\Base\Exceptions\InvalidArgumentException;
use Ibexa\Core\FieldType\Image\Value as ImageValue;
use Ibexa\Core\FieldType\ImageAsset;
use Ibexa\Core\FieldType\ImageAsset\Value as ImageAssetValue;
use Ibexa\Core\FieldType\ValidationError;
use Ibexa\Tests\Core\FieldType\BaseFieldTypeTest;

class ImageAssetTest extends BaseFieldTypeTest
{
    private const DESTINATION_CONTENT_ID = 14;
    private const REMOTE_UUID = '935070ff-6d5b-4c4d-a94f-24fb9f22eb74';
    private const REMOTE_SOURCE_ID = 'remote_source';

    /** @var \Ibexa\Contracts\Core\Repository\ContentService|\PHPUnit\Framework\MockObject\MockObject */
    private $contentServiceMock;

    /** @var \Ibexa\Core\FieldType\ImageAsset\AssetMapper|\PHPUnit\Framework\MockObject\MockObject */
    private $assetMapperMock;

    /** @var \Ibexa\Contracts\Core\Persistence\Content\Handler|\PHPUnit\Framework\MockObject\MockObject */
    private $contentHandlerMock;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->contentServiceMock = $this->createMock(ContentService::class);
        $this->assetMapperMock = $this->createMock(ImageAsset\AssetMapper::class);
        $this->contentHandlerMock = $this->createMock(SPIContentHandler::class);
        $versionInfo = new VersionInfo([
            'versionNo' => 24,
            'names' => [
                'en_GB' => 'name_en_GB',
                'de_DE' => 'Name_de_DE',
            ],
        ]);
        $currentVersionNo = 28;
        $destinationContentInfo = $this->createMock(ContentInfo::class);
        $destinationContentInfo
            ->method('__get')
            ->willReturnMap([
                ['currentVersionNo', $currentVersionNo],
                ['mainLanguageCode', 'en_GB'],
            ]);

        $this->contentHandlerMock
            ->method('loadContentInfo')
            ->with(self::DESTINATION_CONTENT_ID)
            ->willReturn($destinationContentInfo);

        $this->contentHandlerMock
            ->method('loadVersionInfo')
            ->with(self::DESTINATION_CONTENT_ID, $currentVersionNo)
            ->willReturn($versionInfo);
    }

    /**
     * {@inheritdoc}
     */
    protected function provideFieldTypeIdentifier(): string
    {
        return ImageAsset\Type::FIELD_TYPE_IDENTIFIER;
    }

    /**
     * {@inheritdoc}
     */
    protected function createFieldTypeUnderTest()
    {
        return new Type(new ImageAsset\Type(
            $this->contentServiceMock,
            $this->assetMapperMock,
            $this->contentHandlerMock
        ));
    }

    /**
     * {@inheritdoc}
     */
    protected function getValidatorConfigurationSchemaExpectation(): array
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    protected function getSettingsSchemaExpectation(): array
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    protected function getEmptyValueExpectation()
    {
        return new Value();
    }

    /**
     * {@inheritdoc}
     */
    public function provideInvalidInputForAcceptValue(): array
    {
        return [
            [
                true,
                InvalidArgumentException::class,
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function provideValidInputForAcceptValue(): array
    {
        $destinationContentId = 7;

        return [
            [
                null,
                $this->getEmptyValueExpectation(),
            ],
            [
                $destinationContentId,
                new Value($destinationContentId),
            ],
            [
                new ContentInfo([
                    'id' => $destinationContentId,
                ]),
                new Value($destinationContentId),
            ],
            [
                new ImageAssetValue($destinationContentId),
                new Value($destinationContentId),
            ],
            [
                [
                    'destinationContentId' => self::REMOTE_UUID,
                    'source' => self::REMOTE_SOURCE_ID,
                ],
                new Value(self::REMOTE_UUID, null, self::REMOTE_SOURCE_ID),
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function provideInputForToHash(): array
    {
        $destinationContentId = 7;
        $alternativeText = 'The alternative text for image';

        return [
            [
                new Value(),
                [
                    'destinationContentId' => null,
                    'alternativeText' => null,
                    'source' => null,
                ],
            ],
            [
                new Value($destinationContentId),
                [
                    'destinationContentId' => $destinationContentId,
                    'alternativeText' => null,
                    'source' => null,
                ],
            ],
            [
                new Value($destinationContentId, $alternativeText),
                [
                    'destinationContentId' => $destinationContentId,
                    'alternativeText' => $alternativeText,
                    'source' => null,
                ],
            ],
            [
                new Value(self::REMOTE_UUID, $alternativeText, self::REMOTE_SOURCE_ID),
                [
                    'destinationContentId' => self::REMOTE_UUID,
                    'alternativeText' => $alternativeText,
                    'source' => self::REMOTE_SOURCE_ID,
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function provideInputForFromHash(): array
    {
        $destinationContentId = 7;
        $alternativeText = 'The alternative text for image';

        return [
            [
                null,
                new Value(),
            ],
            [
                [
                    'destinationContentId' => $destinationContentId,
                    'alternativeText' => null,
                ],
                new Value($destinationContentId),
            ],
            [
                [
                    'destinationContentId' => $destinationContentId,
                    'alternativeText' => $alternativeText,
                ],
                new Value($destinationContentId, $alternativeText),
            ],
            [
                [
                    'destinationContentId' => self::REMOTE_UUID,
                    'alternativeText' => $alternativeText,
                    'source' => self::REMOTE_SOURCE_ID,
                ],
                new Value(self::REMOTE_UUID, $alternativeText, self::REMOTE_SOURCE_ID),
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function provideInvalidDataForValidate(): array
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function testValidateNonAsset()
    {
        $destinationContentId = 7;
        $destinationContent = $this->createMock(Content::class);
        $invalidContentTypeIdentifier = 'article';
        $invalidContentType = $this->createMock(ContentType::class);
        $invalidContentType
            ->expects($this->once())
            ->method('__get')
            ->with('identifier')
            ->willReturn($invalidContentTypeIdentifier);

        $destinationContent
            ->method('getContentType')
            ->willReturn($invalidContentType);

        $this->contentServiceMock
            ->expects($this->once())
            ->method('loadContent')
            ->with($destinationContentId)
            ->willReturn($destinationContent);

        $this->assetMapperMock
            ->expects($this->once())
            ->method('isAsset')
            ->with($destinationContent)
            ->willReturn(false);

        $invalidContentType
            ->expects($this->once())
            ->method('__get')
            ->with('identifier')
            ->willReturn($invalidContentTypeIdentifier);

        $validationErrors = $this->doValidate([], new Value($destinationContentId));

        $this->assertIsArray($validationErrors);
        $this->assertEquals([
            new ValidationError(
                'Content %type% is not a valid asset target',
                null,
                [
                    '%type%' => $invalidContentTypeIdentifier,
                ],
                'destinationContentId'
            ),
        ], $validationErrors);
    }

    /**
     * {@inheritdoc}
     */
    public function provideValidDataForValidate(): array
    {
        return [
            [
                [],
                $this->getEmptyValueExpectation(),
            ],
        ];
    }

    /**
     * @dataProvider provideDataForTestValidateValidNonEmptyAssetValue
     *
     * @param array<\Ibexa\Core\FieldType\ValidationError> $expectedValidationErrors
     */
    public function testValidateValidNonEmptyAssetValue(
        int $fileSize,
        array $expectedValidationErrors
    ) {
        $destinationContentId = 7;
        $destinationContent = $this->createMock(Content::class);

        $this->contentServiceMock
            ->expects($this->once())
            ->method('loadContent')
            ->with($destinationContentId)
            ->willReturn($destinationContent);

        $this->assetMapperMock
            ->expects($this->once())
            ->method('isAsset')
            ->with($destinationContent)
            ->willReturn(true);

        $assetValueMock = $this->createMock(ImageValue::class);
        $assetValueMock
            ->method('getFileSize')
            ->willReturn($fileSize);

        $this->assetMapperMock
            ->expects($this->once())
            ->method('getAssetValue')
            ->with($destinationContent)
            ->willReturn($assetValueMock);

        $fieldDefinitionMock = $this->createMock(FieldDefinition::class);
        $fieldDefinitionMock
            ->method('getValidatorConfiguration')
            ->willReturn(
                [
                    'FileSizeValidator' => [
                        'maxFileSize' => 1.4,
                    ],
                ]
            );

        $this->assetMapperMock
            ->method('getAssetFieldDefinition')
            ->willReturn($fieldDefinitionMock);

        $validationErrors = $this->doValidate([], new ImageAsset\Value($destinationContentId));
        $this->assertEquals(
            $expectedValidationErrors,
            $validationErrors
        );
    }

    /**
     * @return iterable<array{
     *     int,
     *     array<\Ibexa\Core\FieldType\ValidationError>,
     * }>
     */
    public function provideDataForTestValidateValidNonEmptyAssetValue(): iterable
    {
        yield 'No validation errors' => [
            123456,
            [],
        ];

        yield 'Maximum file size exceeded' => [
            12345678912356,
            [
                new ValidationError(
                    'The file size cannot exceed %size% megabyte.',
                    'The file size cannot exceed %size% megabytes.',
                    [
                        '%size%' => 1.4,
                    ],
                    'fileSize'
                ),
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function provideDataForGetName(): array
    {
        return [
            'empty_destination_content_id' => [
                $this->getEmptyValueExpectation(),
                '',
                [],
                'en_GB',
            ],
            'destination_content_id' => [
                new Value(self::DESTINATION_CONTENT_ID), 'name_en_GB', [], 'en_GB',
            ],
            'destination_content_id_de_DE' => [
                new Value(self::DESTINATION_CONTENT_ID), 'Name_de_DE', [], 'de_DE',
            ],
        ];
    }

    /**
     * @dataProvider provideDataForGetName
     */
    public function testGetName(SPIValue $value, string $expected, array $fieldSettings = [], string $languageCode = 'en_GB'): void
    {
        /** @var \Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition|\PHPUnit\Framework\MockObject\MockObject $fieldDefinitionMock */
        $fieldDefinitionMock = $this->createMock(FieldDefinition::class);
        $fieldDefinitionMock->method('getFieldSettings')->willReturn($fieldSettings);

        $name = $this->getFieldTypeUnderTest()->getName($value, $fieldDefinitionMock, $languageCode);

        self::assertSame($expected, $name);
    }

    public function testIsSearchable()
    {
        $this->assertTrue($this->getFieldTypeUnderTest()->isSearchable());
    }

    /**
     * @covers \Ibexa\Core\FieldType\Relation\Type::getRelations
     */
    public function testGetRelations()
    {
        $destinationContentId = 7;
        $fieldType = $this->createFieldTypeUnderTest();

        $this->assertEquals(
            [
                Relation::ASSET => [$destinationContentId],
            ],
            $fieldType->getRelations($fieldType->acceptValue($destinationContentId))
        );
    }
}

class_alias(ImageAssetTest::class, 'Ibexa\Platform\Tests\Connector\Dam\FieldType\ImageAssetTest');
