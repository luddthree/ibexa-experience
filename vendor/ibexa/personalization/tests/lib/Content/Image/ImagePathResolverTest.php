<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Personalization\Content\Image;

use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Contracts\Core\Repository\Values\Content\Content as APIContent;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo;
use Ibexa\Contracts\Core\Repository\Values\Content\Field;
use Ibexa\Core\FieldType\Image\Value as ImageValue;
use Ibexa\Core\FieldType\ImageAsset\Value as ImageAssetValue;
use Ibexa\Core\FieldType\Relation\Value as RelationValue;
use Ibexa\Core\FieldType\RelationList\Value as RelationListValue;
use Ibexa\Core\FieldType\Value;
use Ibexa\Core\Repository\Values\Content\Content;
use Ibexa\Core\Repository\Values\Content\VersionInfo;
use Ibexa\Core\Repository\Values\ContentType\ContentType;
use Ibexa\Personalization\Content\Image\ImagePathResolver;
use Ibexa\Personalization\Content\Image\ImagePathResolverInterface;
use Ibexa\Personalization\Mapper\OutputTypeAttributesMapperInterface;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Ibexa\Personalization\Content\Image\ImagePathResolver
 */
final class ImagePathResolverTest extends TestCase
{
    private const WEB_ROOT_DIR = __DIR__ . '/../../../fixtures/public';
    private const TEST_IMAGE_PATH = '/image/test-image.png';

    /** @var \Ibexa\Contracts\Core\Repository\ContentService|mixed|\PHPUnit\Framework\MockObject\MockObject */
    private ContentService $contentService;

    /** @var \Ibexa\Personalization\Mapper\OutputTypeAttributesMapperInterface|\PHPUnit\Framework\MockObject\MockObject */
    private OutputTypeAttributesMapperInterface $outputTypeAttributesMapper;

    private ImagePathResolverInterface $imagePathResolver;

    protected function setUp(): void
    {
        $this->contentService = $this->createMock(ContentService::class);
        $this->outputTypeAttributesMapper = $this->createMock(OutputTypeAttributesMapperInterface::class);
        $this->imagePathResolver = new ImagePathResolver(
            $this->contentService,
            $this->outputTypeAttributesMapper,
            self::WEB_ROOT_DIR
        );
    }

    public function testReturnTrueWhenImageExists(): void
    {
        self::assertTrue($this->imagePathResolver->imageExists(self::TEST_IMAGE_PATH));
    }

    public function testReturnFalseWhenImageDoesNotExists(): void
    {
        self::assertFalse($this->imagePathResolver->imageExists('invalid.jpg'));
    }

    /**
     * @dataProvider provideDataForTestResolveImagePathByContentId
     * @dataProvider provideDataForTestResolveImagePathByContentIdWithDestinationContent
     *
     * @param array{array<int|bool|null|\Ibexa\Contracts\Core\Repository\Values\Content\Content>} $contentMap
     */
    public function testResolveImagePathByContentId(
        ?string $expectedImageUrl,
        string $recommendedImage,
        int $customerId,
        string $fieldIdentifier,
        APIContent $content,
        array $contentMap
    ): void {
        $this->mockContentServiceLoadContent($contentMap);
        $this->mockOutputTypeAttributesMapperReverseMapAttribute(
            $customerId,
            $content->getContentType()->id,
            $fieldIdentifier
        );

        self::assertEquals(
            $expectedImageUrl,
            $this->imagePathResolver->resolveImagePathByContentId($customerId, $recommendedImage, $content->id)
        );
    }

    /**
     * @dataProvider provideDataForTestResolveImagePathByContentRemoteId
     * @dataProvider provideDataForTestResolveImagePathByContentRemoteIdWithDestinationContent
     *
     * @param array{array<int|bool|null|\Ibexa\Contracts\Core\Repository\Values\Content\Content>} $destinationContentMap
     */
    public function testResolveImagePathByContentRemoteId(
        ?string $expectedImageUrl,
        string $recommendedImage,
        int $customerId,
        string $fieldIdentifier,
        string $remoteId,
        APIContent $content,
        array $destinationContentMap
    ): void {
        $this->mockContentServiceLoadContentByRemoteId($remoteId, $content);
        $this->mockOutputTypeAttributesMapperReverseMapAttribute(
            $customerId,
            $content->getContentType()->id,
            $fieldIdentifier
        );

        if (!empty($destinationContentMap)) {
            $this->mockContentServiceLoadContent($destinationContentMap);
        }

        self::assertEquals(
            $expectedImageUrl,
            $this->imagePathResolver->resolveImagePathByContentRemoteId(12345, $recommendedImage, $remoteId)
        );
    }

    /**
     * @phpstan-return iterable<array{
     *  ?string,
     *  string,
     *  int,
     *  string,
     *  \Ibexa\Contracts\Core\Repository\Values\Content\Content,
     *  array{array<int|bool|null|\Ibexa\Contracts\Core\Repository\Values\Content\Content>}
     * }>
     */
    public function provideDataForTestResolveImagePathByContentIdWithDestinationContent(): iterable
    {
        $contentWithImageAsset = $this->createTestDestinationContent($this->getImageAssetFieldTypeValue());
        $contentWithContentRelation = $this->createTestDestinationContent($this->getContentRelationFieldTypeValue());
        $contentWithContentRelationList = $this->createTestDestinationContent($this->getContentRelationListFieldTypeValue());
        $destinationContent = $this->createTestDestinationImageContent();

        yield from $this->getDataForResolveByIdWithDestinationContent($contentWithImageAsset, $destinationContent);
        yield from $this->getDataForResolveByIdWithDestinationContent($contentWithContentRelation, $destinationContent);
        yield from $this->getDataForResolveByIdWithDestinationContent($contentWithContentRelationList, $destinationContent);
    }

    /**
     * @phpstan-return iterable<array{
     *  ?string,
     *  string,
     *  int,
     *  string,
     *  \Ibexa\Contracts\Core\Repository\Values\Content\Content,
     *  array{array<int|bool|null|\Ibexa\Contracts\Core\Repository\Values\Content\Content>}
     * }>
     */
    public function provideDataForTestResolveImagePathByContentId(): iterable
    {
        $content = $this->createTestContent(
            1,
            '637d58bfddf164627bdfd265733280a0',
            1,
            [
                new Field(
                    [
                        'id' => 123,
                        'fieldDefIdentifier' => 'foo',
                        'value' => $this->getImageFieldTypeValue(),
                        'languageCode' => 'eng-GB',
                        'fieldTypeIdentifier' => 'bar',
                    ]
                ),
            ]
        );

        yield [
            self::TEST_IMAGE_PATH,
            'invalid.url',
            12345,
            'foo',
            $content,
            [
                [1, null, null, true, $content],
            ],
        ];

        yield [
            null,
            'invalid.url',
            12345,
            'baz',
            $content,
            [
                [1, null, null, true, $content],
            ],
        ];
    }

    /**
     * @phpstan-return iterable<array{
     *  string,
     *  string,
     *  int,
     *  string,
     *  \Ibexa\Contracts\Core\Repository\Values\Content\Content,
     *  array{array<int|bool|null|\Ibexa\Contracts\Core\Repository\Values\Content\Content>}
     * }>
     */
    private function getDataForResolveByIdWithDestinationContent(
        APIContent $content,
        APIContent $destinationContent
    ): iterable {
        yield [
            self::TEST_IMAGE_PATH,
            'invalid.url',
            12345,
            'foo',
            $content,
            [
                [1, null, null, true, $content],
                [2, null, null, true, $destinationContent],
            ],
        ];
    }

    /**
     * @phpstan-return iterable<array{
     *  ?string,
     *  string,
     *  int,
     *  string,
     *  string,
     *  \Ibexa\Contracts\Core\Repository\Values\Content\Content,
     * }>
     */
    public function provideDataForTestResolveImagePathByContentRemoteId(): iterable
    {
        $content = $this->createTestContent(
            1,
            '637d58bfddf164627bdfd265733280a0',
            1,
            [
                new Field(
                    [
                        'id' => 123,
                        'fieldDefIdentifier' => 'foo',
                        'value' => $this->getImageFieldTypeValue(),
                        'languageCode' => 'eng-GB',
                        'fieldTypeIdentifier' => 'bar',
                    ]
                ),
            ],
        );

        yield [
            self::TEST_IMAGE_PATH,
            'invalid.url',
            12345,
            'foo',
            '637d58bfddf164627bdfd265733280a0',
            $content,
            [],
        ];

        yield [
            null,
            'invalid.url',
            12345,
            'baz',
            '637d58bfddf164627bdfd265733280a0',
            $content,
            [],
        ];
    }

    /**
     * @phpstan-return iterable<array{
     *  string,
     *  string,
     *  int,
     *  string,
     *  string,
     *  \Ibexa\Contracts\Core\Repository\Values\Content\Content,
     *  array{array<int|bool|null|\Ibexa\Contracts\Core\Repository\Values\Content\Content>}
     * }>
     */
    public function provideDataForTestResolveImagePathByContentRemoteIdWithDestinationContent(): iterable
    {
        $contentWithImageAsset = $this->createTestDestinationContent($this->getImageAssetFieldTypeValue());
        $contentWithContentRelation = $this->createTestDestinationContent($this->getContentRelationFieldTypeValue());
        $contentWithContentRelationList = $this->createTestDestinationContent($this->getContentRelationListFieldTypeValue());
        $destinationContent = $this->createTestDestinationImageContent();

        yield from $this->getDataForResolveByRemoteIdWithDestinationContent($contentWithImageAsset, $destinationContent);
        yield from $this->getDataForResolveByRemoteIdWithDestinationContent($contentWithContentRelation, $destinationContent);
        yield from $this->getDataForResolveByRemoteIdWithDestinationContent($contentWithContentRelationList, $destinationContent);
    }

    /**
     * @phpstan-return iterable<array{
     *  string,
     *  string,
     *  int,
     *  string,
     *  string,
     *  \Ibexa\Contracts\Core\Repository\Values\Content\Content,
     *  array{array<int|bool|null|\Ibexa\Contracts\Core\Repository\Values\Content\Content>}
     * }>
     */
    private function getDataForResolveByRemoteIdWithDestinationContent(
        APIContent $content,
        APIContent $destinationContent
    ): iterable {
        yield [
            self::TEST_IMAGE_PATH,
            'invalid.url',
            12345,
            'foo',
            '637d58bfddf164627bdfd265733280a0',
            $content,
            [
                [2, null, null, true, $destinationContent],
            ],
        ];
    }

    private function createTestDestinationImageContent(): APIContent
    {
        return $this->createTestContent(
            2,
            'fddf16466573358b280a0227bdfd637d',
            2,
            [
                new Field(
                    [
                        'id' => 1234,
                        'fieldDefIdentifier' => 'image',
                        'value' => $this->getImageFieldTypeValue(),
                        'languageCode' => 'eng-GB',
                        'fieldTypeIdentifier' => 'Image',
                    ]
                ),
            ]
        );
    }

    private function createTestDestinationContent(Value $value): APIContent
    {
        return $this->createTestContent(
            1,
            '637d58bfddf164627bdfd265733280a0',
            1,
            [
                new Field(
                    [
                        'id' => 123,
                        'fieldDefIdentifier' => 'foo',
                        'value' => $value,
                        'languageCode' => 'eng-GB',
                        'fieldTypeIdentifier' => 'bar',
                    ]
                ),
            ]
        );
    }

    /**
     * @param array<\Ibexa\Contracts\Core\Repository\Values\Content\Field> $fields
     */
    private function createTestContent(
        int $contentId,
        string $remoteId,
        int $contentTypeId,
        array $fields
    ): APIContent {
        return $this->createContent(
            $this->createVersionInfo(
                $this->createContentInfo($contentId, $remoteId)
            ),
            $this->createContentType($contentTypeId),
            $fields
        );
    }

    private function createContentInfo(
        int $contentId,
        string $remoteId,
        string $mainLaguageCode = 'eng-GB'
    ): ContentInfo {
        return new ContentInfo(
            [
                'id' => $contentId,
                'mainLanguageCode' => $mainLaguageCode,
                'remoteId' => $remoteId,
            ]
        );
    }

    private function createVersionInfo(ContentInfo $contentInfo): VersionInfo
    {
        return new VersionInfo(
            [
                'contentInfo' => $contentInfo,
            ]
        );
    }

    private function createContentType(int $contentTypeId): ContentType
    {
        return new ContentType(
            [
                'id' => $contentTypeId,
            ]
        );
    }

    /**
     * @param array<\Ibexa\Contracts\Core\Repository\Values\Content\Field> $fields
     */
    private function createContent(
        VersionInfo $versionInfo,
        ContentType $contentType,
        array $fields
    ): APIContent {
        return new Content(
            [
                'contentType' => $contentType,
                'internalFields' => $fields,
                'versionInfo' => $versionInfo,
            ]
        );
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    private function getImageFieldTypeValue(): ImageValue
    {
        return new ImageValue(
            [
                'uri' => self::TEST_IMAGE_PATH,
            ]
        );
    }

    private function getImageAssetFieldTypeValue(): ImageAssetValue
    {
        return new ImageAssetValue(2);
    }

    private function getContentRelationFieldTypeValue(): RelationValue
    {
        return new RelationValue(2);
    }

    private function getContentRelationListFieldTypeValue(): RelationListValue
    {
        return new RelationListValue([2]);
    }

    /**
     * @param array{array<int|bool|null|\Ibexa\Contracts\Core\Repository\Values\Content\Content>} $contentMap
     */
    private function mockContentServiceLoadContent(array $contentMap): void
    {
        $this->contentService
            ->expects(self::atLeastOnce())
            ->method('loadContent')
            ->willReturnMap($contentMap);
    }

    private function mockContentServiceLoadContentByRemoteId(string $remoteId, APIContent $content): void
    {
        $this->contentService
            ->expects(self::once())
            ->method('loadContentByRemoteId')
            ->with($remoteId)
            ->willReturn($content);
    }

    private function mockOutputTypeAttributesMapperReverseMapAttribute(
        int $customerId,
        int $contentTypeId,
        string $fieldIdentifier
    ): void {
        $this->outputTypeAttributesMapper
            ->expects(self::once())
            ->method('reverseMapAttribute')
            ->with($customerId, $contentTypeId, 'image')
            ->willReturn($fieldIdentifier);
    }
}
