<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Personalization\Event\Subscriber;

use Ibexa\Contracts\Core\Limitation\Target;
use Ibexa\Contracts\Core\Repository\Events\Content\BeforeDeleteContentEvent;
use Ibexa\Contracts\Core\Repository\Events\Content\BeforeDeleteTranslationEvent;
use Ibexa\Contracts\Core\Repository\Events\Content\CopyContentEvent;
use Ibexa\Contracts\Core\Repository\Events\Content\HideContentEvent;
use Ibexa\Contracts\Core\Repository\Events\Content\PublishVersionEvent;
use Ibexa\Contracts\Core\Repository\Events\Content\RevealContentEvent;
use Ibexa\Contracts\Core\Repository\Events\Content\UpdateContentMetadataEvent;
use Ibexa\Contracts\Core\Repository\PermissionResolver;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentMetadataUpdateStruct;
use Ibexa\Contracts\Core\Repository\Values\Content\Language;
use Ibexa\Contracts\Core\Repository\Values\Content\LocationCreateStruct;
use Ibexa\Contracts\Core\Repository\Values\Content\RelationList;
use Ibexa\Contracts\Core\Repository\Values\Content\Search\SearchResult;
use Ibexa\Contracts\Core\Repository\Values\Content\VersionInfo;
use Ibexa\Core\Base\Exceptions\UnauthorizedException;
use Ibexa\Personalization\Event\Subscriber\ContentEventSubscriber;
use Ibexa\Personalization\Service\Setting\SettingServiceInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @covers \Ibexa\Personalization\Event\Subscriber\ContentEventSubscriber
 *
 * @phpstan-type T array<object|int|array<object>>
 * @phpstan-extends \Ibexa\Tests\Personalization\Event\Subscriber\BaseRepositoryEventSubscriberTestCase<T>
 *
 * @phpstan-import-type TIsContentIncludedValueMap from \Ibexa\Tests\Personalization\Event\Subscriber\BaseRepositoryEventSubscriberTestCase
 */
final class ContentEventSubscriberTest extends BaseRepositoryEventSubscriberTestCase
{
    private const TRANSLATIONS = ['eng-GB'];

    /** @var \Symfony\Component\EventDispatcher\EventSubscriberInterface&\Ibexa\Personalization\Event\Subscriber\ContentEventSubscriber */
    private EventSubscriberInterface $eventSubscriber;

    /** @var \Ibexa\Contracts\Core\Repository\PermissionResolver&\PHPUnit\Framework\MockObject\MockObject */
    private PermissionResolver $permissionResolver;

    protected function setUp(): void
    {
        parent::setUp();

        $this->permissionResolver = $this->createMock(PermissionResolver::class);
        $settingService = $this->createMock(SettingServiceInterface::class);
        $settingService->method('isInstallationKeyFound')->willReturn(true);
        $this->eventSubscriber = new ContentEventSubscriber(
            $this->coreContentService,
            $this->contentService,
            $this->includedItemTypeResolver,
            $this->locationService,
            $this->repository,
            $this->permissionResolver,
            $settingService,
            $this->queryFactory,
            $this->searchService
        );
    }

    public function testOnPublishVersion(): void
    {
        $content = $this->createMock(Content::class);

        $this->mockIncludedItemTypeResolverIsContentIncluded(
            [
                [$content, true],
            ]
        );
        $this->mockContentServiceUpdateContent($content);

        $this->eventSubscriber->onPublishVersion(
            new PublishVersionEvent(
                $content,
                $this->createMock(VersionInfo::class),
                ['eng-GB']
            )
        );
    }

    public function testOnBeforeDeleteContentThrowUnauthorizedException(): void
    {
        $contentInfo = $this->createMock(ContentInfo::class);
        $versionInfo = $this->createVersionInfoMock($this->getLanguages());

        $this->mockCoreContentServiceLoadVersionInfo($contentInfo, $versionInfo);
        $this->mockPermissionResolverCanUser(
            $contentInfo,
            (new Target\Version())->deleteTranslations(self::TRANSLATIONS),
            false
        );

        $this->expectException(UnauthorizedException::class);

        $this->eventSubscriber->onBeforeDeleteContent(new BeforeDeleteContentEvent($contentInfo));
    }

    /**
     * @dataProvider provideDataWithDeleteContentItems
     *
     * @param array<\Ibexa\Contracts\Core\Repository\Values\Content\Content> $mockContentServiceDeleteContentItems
     * @param array<\Ibexa\Contracts\Core\Repository\Values\Content\Content> $mockContentServiceUpdateContentItems
     * @phpstan-param TIsContentIncludedValueMap $includedItemTypeResolverMockValueMap
     * @phpstan-param T $mockRepositorySudoValues
     */
    public function testOnBeforeDeleteContent(
        array $mockContentServiceDeleteContentItems,
        array $mockContentServiceUpdateContentItems,
        array $includedItemTypeResolverMockValueMap,
        SearchResult $locationSearchResult,
        array $mockRepositorySudoValues
    ): void {
        $contentInfo = $this->createMock(ContentInfo::class);
        $versionInfo = $this->createVersionInfoMock($this->getLanguages());

        $this->mockLocationSearch($locationSearchResult);
        $this->mockCoreContentServiceLoadVersionInfo($contentInfo, $versionInfo);
        $this->mockPermissionResolverCanUser(
            $contentInfo,
            (new Target\Version())->deleteTranslations(self::TRANSLATIONS),
            true
        );
        $this->mockIncludedItemTypeResolverIsContentIncluded($includedItemTypeResolverMockValueMap);
        $this->mockRepositorySudo($mockRepositorySudoValues);
        $this->mockContentServiceDeleteContentItems($mockContentServiceDeleteContentItems);
        $this->mockContentServiceUpdateContentItems($mockContentServiceUpdateContentItems);

        $this->eventSubscriber->onBeforeDeleteContent(new BeforeDeleteContentEvent($contentInfo));
    }

    public function testOnBeforeDeleteTranslation(): void
    {
        $contentInfo = $this->createMock(ContentInfo::class);
        $content = $this->createMock(Content::class);

        $this->mockCoreContentServiceLoadContentByContentInfo($contentInfo, $content);
        $this->mockPermissionResolverCanUser(
            $contentInfo,
            (new Target\Version())->deleteTranslations(self::TRANSLATIONS),
            true
        );

        $this->mockIncludedItemTypeResolverIsContentIncluded(
            [
                [$content, true],
            ]
        );

        $this->eventSubscriber->onBeforeDeleteTranslation(
            new BeforeDeleteTranslationEvent($contentInfo, 'eng-GB')
        );
    }

    /**
     * @dataProvider provideDataWithDeleteContentItems
     *
     * @param array<\Ibexa\Contracts\Core\Repository\Values\Content\Content> $mockContentServiceDeleteContentItems
     * @param array<\Ibexa\Contracts\Core\Repository\Values\Content\Content> $mockContentServiceUpdateContentItems
     * @phpstan-param TIsContentIncludedValueMap $includedItemTypeResolverMockValueMap
     * @phpstan-param T $mockRepositorySudoValues
     */
    public function testOnHideContent(
        array $mockContentServiceDeleteContentItems,
        array $mockContentServiceUpdateContentItems,
        array $includedItemTypeResolverMockValueMap,
        SearchResult $locationSearchResult,
        array $mockRepositorySudoValues
    ): void {
        $this->mockLocationSearch($locationSearchResult);
        $this->mockIncludedItemTypeResolverIsContentIncluded($includedItemTypeResolverMockValueMap);
        $this->mockRepositorySudo($mockRepositorySudoValues);
        $this->mockContentServiceDeleteContentItems($mockContentServiceDeleteContentItems);
        $this->mockContentServiceUpdateContentItems($mockContentServiceUpdateContentItems);

        $this->eventSubscriber->onHideContent(
            new HideContentEvent(
                $this->createMock(ContentInfo::class)
            )
        );
    }

    /**
     * @dataProvider provideDataForTestRevealContent
     *
     * @param array<\Ibexa\Contracts\Core\Repository\Values\Content\Content> $mockContentServiceUpdateContentItems
     * @phpstan-param TIsContentIncludedValueMap $includedItemTypeResolverMockValueMap
     * @phpstan-param T $mockRepositorySudoValues
     */
    public function testOnRevealContent(
        array $mockContentServiceUpdateContentItems,
        array $includedItemTypeResolverMockValueMap,
        SearchResult $locationSearchResult,
        array $mockRepositorySudoValues
    ): void {
        $this->mockIncludedItemTypeResolverIsContentIncluded($includedItemTypeResolverMockValueMap);
        $this->mockRepositorySudo($mockRepositorySudoValues);
        $this->mockContentServiceUpdateContentItems($mockContentServiceUpdateContentItems);
        $this->mockLocationSearch($locationSearchResult);

        $this->eventSubscriber->onRevealContent(
            new RevealContentEvent(
                $this->createMock(ContentInfo::class)
            )
        );
    }

    public function testOnUpdateContentMetadata(): void
    {
        $content = $this->createMock(Content::class);

        $this->mockIncludedItemTypeResolverIsContentIncluded(
            [
                [$content, true],
            ]
        );
        $this->mockContentServiceUpdateContent($content);

        $this->eventSubscriber->onUpdateContentMetadata(
            new UpdateContentMetadataEvent(
                $content,
                $this->createMock(ContentInfo::class),
                $this->createMock(ContentMetadataUpdateStruct::class)
            )
        );
    }

    public function testOnCopyContent(): void
    {
        $content = $this->createMock(Content::class);

        $this->mockIncludedItemTypeResolverIsContentIncluded(
            [
                [$content, true],
            ]
        );
        $this->mockContentServiceUpdateContent($content);

        $this->eventSubscriber->onCopyContent(
            new CopyContentEvent(
                $content,
                $this->createMock(ContentInfo::class),
                $this->createMock(LocationCreateStruct::class)
            )
        );
    }

    /**
     * @return iterable<array{
     *     array<\Ibexa\Contracts\Core\Repository\Values\Content\Content>,
     *     array<\Ibexa\Contracts\Core\Repository\Values\Content\Content>,
     *     TIsContentIncludedValueMap,
     *     \Ibexa\Contracts\Core\Repository\Values\Content\Search\SearchResult,
     *     T
     * }>
     */
    public function provideDataWithDeleteContentItems(): iterable
    {
        $content = $this->createMock(Content::class);
        $location = $this->createLocation($content);

        yield 'Content without children and relations' => [
            [$content],
            [],
            [
                [$content, true],
            ],
            new SearchResult(['searchHits' => []]),
            [
                [$location],
                new RelationList(),
            ],
        ];

        $relatedContentItems = $this->getRelatedContentItems();
        $includedRelatedContentItems = $this->getIncludedRelatedContentItems($relatedContentItems);
        $relationList = $this->getRelationList();

        yield 'Content with relations and without children' => [
            [$content],
            $relatedContentItems,
            [
                [$content, true],
                ...$includedRelatedContentItems,
            ],
            new SearchResult(['searchHits' => []]),
            [
                [$location],
                $relationList,
                ...$relatedContentItems,
            ],
        ];

        $childContentItems = $this->getChildContentItems();
        $locationSearchResult = $this->getLocationSearchResult(...$childContentItems);
        $isContentIncludedValueMap = $this->getBaseIsContentIncludedValueMap($content, $childContentItems);
        $includedChildContentItems = $this->getIncludedChildContentItems($childContentItems);

        yield 'Content with children and without relations' => [
            [
                $content,
                ...$includedChildContentItems,
            ],
            [],
            $isContentIncludedValueMap,
            $locationSearchResult,
            [
                [$location],
                new RelationList(),
            ],
        ];

        array_push($isContentIncludedValueMap, ...$includedRelatedContentItems);

        yield 'Content with relations and children' => [
            [
                $content,
                ...$includedChildContentItems,
            ],
            $relatedContentItems,
            $isContentIncludedValueMap,
            $locationSearchResult,
            [
                [$location],
                $relationList,
                ...$relatedContentItems,
            ],
        ];
    }

    /**
     * @return iterable<array{
     *     array<\Ibexa\Contracts\Core\Repository\Values\Content\Content>,
     *     TIsContentIncludedValueMap,
     *     \Ibexa\Contracts\Core\Repository\Values\Content\Search\SearchResult,
     *     T
     * }>
     */
    public function provideDataForTestRevealContent(): iterable
    {
        $content = $this->createMock(Content::class);
        $location = $this->createLocation($content);

        yield 'Content without children and relations' => [
            [$content],
            [
                [$content, true],
            ],
            new SearchResult(['searchHits' => []]),
            [
                [$location],
                new RelationList(),
            ],
        ];

        $relatedContentItems = $this->getRelatedContentItems();
        $includedRelatedContentItems = $this->getIncludedRelatedContentItems($relatedContentItems);
        $relationList = $this->getRelationList();

        yield 'Content with relations and without children' => [
            [
                $content,
                ...$relatedContentItems,
            ],
            [
                [$content, true],
                ...$includedRelatedContentItems,
            ],
            new SearchResult(['searchHits' => []]),
            [
                [$location],
                $relationList,
                ...$relatedContentItems,
            ],
        ];

        $childContentItems = $this->getChildContentItems();
        $locationSearchResult = $this->getLocationSearchResult(...$childContentItems);
        $isContentIncludedValueMap = $this->getBaseIsContentIncludedValueMap($content, $childContentItems);
        $includedChildContentItems = $this->getIncludedChildContentItems($childContentItems);

        yield 'Content with children and without relations' => [
            [
                $content,
                ...$includedChildContentItems,
            ],
            $isContentIncludedValueMap,
            $locationSearchResult,
            [
                [$location],
                new RelationList(),
                ...$childContentItems,
            ],
        ];

        array_push($isContentIncludedValueMap, ...$includedRelatedContentItems);

        yield 'Content with relations and children' => [
            [
                $content,
                ...$includedChildContentItems,
                ...$relatedContentItems,
            ],
            $isContentIncludedValueMap,
            $locationSearchResult,
            [
                [$location],
                $relationList,
                ...$relatedContentItems,
            ],
        ];
    }

    /**
     * @return array<\Ibexa\Contracts\Core\Repository\Values\Content\Language>
     */
    private function getLanguages(): array
    {
        $language = $this->createMock(Language::class);
        $language
            ->expects(self::once())
            ->method('getLanguageCode')
            ->willReturn('eng-GB');

        $languages[] = $language;

        return $languages;
    }

    /**
     * @param array<\Ibexa\Contracts\Core\Repository\Values\Content\Language> $languages
     */
    private function createVersionInfoMock(array $languages): VersionInfo
    {
        $versionInfo = $this->createMock(VersionInfo::class);
        $versionInfo
            ->expects(self::once())
            ->method('getLanguages')
            ->willReturn($languages);

        return $versionInfo;
    }

    private function mockCoreContentServiceLoadVersionInfo(
        ContentInfo $contentInfo,
        VersionInfo $versionInfo
    ): void {
        $this->coreContentService
            ->expects(self::once())
            ->method('loadVersionInfo')
            ->with($contentInfo)
            ->willReturn($versionInfo);
    }

    private function mockCoreContentServiceLoadContentByContentInfo(
        ContentInfo $contentInfo,
        Content $content
    ): void {
        $this->coreContentService
            ->method('loadContentByContentInfo')
            ->with($contentInfo)
            ->willReturn($content);
    }

    private function mockPermissionResolverCanUser(
        ContentInfo $contentInfo,
        Target $target,
        bool $canUser
    ): void {
        $this->permissionResolver
            ->expects(self::once())
            ->method('canUser')
            ->with(
                'content',
                'remove',
                $contentInfo,
                [$target]
            )
            ->willReturn($canUser);
    }
}
