<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Personalization\Event\Subscriber;

use Ibexa\Contracts\Core\Limitation\Target;
use Ibexa\Contracts\Core\Repository\ContentService as CoreContentService;
use Ibexa\Contracts\Core\Repository\Events\Trash\BeforeTrashEvent;
use Ibexa\Contracts\Core\Repository\Events\Trash\RecoverEvent;
use Ibexa\Contracts\Core\Repository\PermissionCriterionResolver;
use Ibexa\Contracts\Core\Repository\PermissionResolver;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use Ibexa\Contracts\Core\Repository\Values\Content\ContentInfo;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Contracts\Core\Repository\Values\Content\Query;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\LogicalAnd as CriterionLogicalAnd;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\LogicalNot as CriterionLogicalNot;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\Subtree as CriterionSubtree;
use Ibexa\Contracts\Core\Repository\Values\Content\RelationList;
use Ibexa\Contracts\Core\Repository\Values\Content\Search\SearchResult;
use Ibexa\Contracts\Core\Repository\Values\Content\TrashItem;
use Ibexa\Contracts\Core\Repository\Values\Content\VersionInfo;
use Ibexa\Core\Base\Exceptions\UnauthorizedException;
use Ibexa\Personalization\Event\Subscriber\TrashEventSubscriber;
use Ibexa\Personalization\Service\Setting\SettingServiceInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @covers \Ibexa\Personalization\Event\Subscriber\TrashEventSubscriber
 *
 * @phpstan-type T array<object|int>
 * @phpstan-extends \Ibexa\Tests\Personalization\Event\Subscriber\BaseRepositoryEventSubscriberTestCase<T>
 *
 * @phpstan-import-type TIsContentIncludedValueMap from \Ibexa\Tests\Personalization\Event\Subscriber\BaseRepositoryEventSubscriberTestCase
 */
final class TrashEventSubscriberTest extends BaseRepositoryEventSubscriberTestCase
{
    private const TRANSLATIONS = ['eng-GB'];

    /** @var \Symfony\Component\EventDispatcher\EventSubscriberInterface&\Ibexa\Personalization\Event\Subscriber\TrashEventSubscriber */
    private EventSubscriberInterface $eventSubscriber;

    /** @var \Ibexa\Contracts\Core\Repository\PermissionResolver&\PHPUnit\Framework\MockObject\MockObject */
    private PermissionResolver $permissionResolver;

    /** @var \Ibexa\Contracts\Core\Repository\PermissionCriterionResolver&\PHPUnit\Framework\MockObject\MockObject */
    private PermissionCriterionResolver $permissionCriterionResolver;

    protected function setUp(): void
    {
        parent::setUp();

        $this->permissionResolver = $this->createMock(PermissionResolver::class);
        $this->permissionCriterionResolver = $this->createMock(PermissionCriterionResolver::class);
        $settingService = $this->createMock(SettingServiceInterface::class);
        $settingService->method('isInstallationKeyFound')->willReturn(true);

        $this->eventSubscriber = new TrashEventSubscriber(
            $this->createMock(CoreContentService::class),
            $this->contentService,
            $this->includedItemTypeResolver,
            $this->locationService,
            $this->repository,
            $this->permissionResolver,
            $this->permissionCriterionResolver,
            $this->searchService,
            $settingService,
            $this->queryFactory
        );
    }

    /**
     * @dataProvider provideDataForTestOnRecover
     *
     * @phpstan-param TIsContentIncludedValueMap $includedItemTypeResolverMockValueMap
     *
     * @param array<\Ibexa\Contracts\Core\Repository\Values\Content\Content> $contentItemsToUpdate
     * @phpstan-param T $repositorySudoMockValues
     */
    public function testRecover(
        Location $location,
        array $includedItemTypeResolverMockValueMap,
        array $repositorySudoMockValues,
        array $contentItemsToUpdate
    ): void {
        $this->mockIncludedItemTypeResolverIsContentIncluded($includedItemTypeResolverMockValueMap);
        $this->mockRepositorySudo($repositorySudoMockValues);
        $this->mockContentServiceUpdateContentItems($contentItemsToUpdate);

        $this->eventSubscriber->onRecover(
            new RecoverEvent(
                $location,
                $this->createMock(TrashItem::class)
            )
        );
    }

    /**
     * @dataProvider provideDataForTestOnTrashThrowUnauthorizedException
     *
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion|bool $criterion
     */
    public function testOnTrashThrowUnauthorizedException(
        Location $location,
        bool $canUser,
        $criterion,
        ?int $contentWithNoAccess = null
    ): void {
        $this->canDelete(
            $location->getContentInfo(),
            $location,
            $canUser,
            $criterion,
            $contentWithNoAccess
        );

        $this->expectException(UnauthorizedException::class);

        $this->eventSubscriber->onBeforeTrash(new BeforeTrashEvent($location));
    }

    /**
     * @dataProvider provideDataForTestOnBeforeTrash
     *
     * @param array<\Ibexa\Contracts\Core\Repository\Values\Content\Content> $relatedContents
     * @phpstan-param TIsContentIncludedValueMap $includedItemTypeResolverMockValueMap
     *
     * @phpstan-param T $repositorySudoMockValues
     *
     * @param array<\Ibexa\Contracts\Core\Repository\Values\Content\Content> $contentsToDelete
     */
    public function testOnBeforeTrash(
        Location $location,
        array $relatedContents,
        array $includedItemTypeResolverMockValueMap,
        SearchResult $locationSearchResult,
        array $repositorySudoMockValues,
        array $contentsToDelete
    ): void {
        $this->canDelete(
            $location->getContentInfo(),
            $location,
            true,
            $this->createMock(Query\Criterion::class),
            0,
        );

        $this->mockContentServiceUpdateContentItems($relatedContents);
        $this->mockIncludedItemTypeResolverIsContentIncluded($includedItemTypeResolverMockValueMap);
        $this->mockRepositorySudo($repositorySudoMockValues);
        $this->mockContentServiceDeleteContentItems($contentsToDelete);
        $this->mockLocationSearch($locationSearchResult);

        $this->eventSubscriber->onBeforeTrash(new BeforeTrashEvent($location));
    }

    /**
     * @return iterable<array{
     *     \Ibexa\Contracts\Core\Repository\Values\Content\Location,
     *     bool,
     *     \Ibexa\Contracts\Core\Repository\Values\Content\Query\CriterionInterface|bool,
     *     3?: int
     * }>
     */
    public function provideDataForTestOnTrashThrowUnauthorizedException(): iterable
    {
        $content = $this->createMock(Content::class);
        $content
            ->expects(self::atLeastOnce())
            ->method('getVersionInfo')
            ->willReturn($this->createVersionInfoMock(self::TRANSLATIONS));

        $location = $this->createLocation($content);

        yield 'Throw UnauthorizedException on PermissionResolver::canUser' => [
            $location,
            false,
            false,
        ];

        yield 'Throw UnauthorizedException on PermissionCriterionResolver::getPermissionsCriterion' => [
            $location,
            true,
            false,
        ];

        yield 'Throw UnauthorizedException on find content with limited access' => [
            $location,
            true,
            $this->createMock(Query\Criterion::class),
            1,
        ];
    }

    /**
     * @return iterable<array{
     *      \Ibexa\Contracts\Core\Repository\Values\Content\Location,
     *      array<\Ibexa\Contracts\Core\Repository\Values\Content\Content>,
     *      TIsContentIncludedValueMap,
     *      \Ibexa\Contracts\Core\Repository\Values\Content\Search\SearchResult,
     *      T,
     *      array<\Ibexa\Contracts\Core\Repository\Values\Content\Content>,
     * }>
     */
    public function provideDataForTestOnBeforeTrash(): iterable
    {
        $content = $this->createMock(Content::class);
        $content
            ->expects(self::atLeastOnce())
            ->method('getVersionInfo')
            ->willReturn($this->createVersionInfoMock(self::TRANSLATIONS));

        $location = $this->createLocation($content);

        yield 'Content without relations and children' => [
            $location,
            [],
            [
                [$content, true],
            ],
            $this->getLocationSearchResult(),
            [
                new RelationList(),
            ],
            [$content],
        ];

        $relatedContentItems = $this->getRelatedContentItems();

        yield 'Content with relations and without children' => [
            $location,
            $relatedContentItems,
            [
                [$content, true],
                [$relatedContentItems[0], true],
                [$relatedContentItems[1], true],
            ],
            new SearchResult(['searchHits' => []]),
            [
                $this->getRelationList(),
                ...$relatedContentItems,
            ],
            [$content],
        ];

        $childContentItems = $this->getChildContentItems();
        $locationSearchResult = $this->getLocationSearchResult(...$childContentItems);

        $isContentIncludedValueMap = [
            [$content, true],
            [$childContentItems[0], true],
            [$childContentItems[1], true],
            [$childContentItems[2], false],
        ];

        $contentItemsToDelete = [$content, $childContentItems[0], $childContentItems[1]];

        yield 'Content with children and without relations' => [
            $location,
            [],
            $isContentIncludedValueMap,
            $locationSearchResult,
            [
                new RelationList(),
                ...$childContentItems,
            ],
            $contentItemsToDelete,
        ];

        yield 'Content with relations and with children' => [
            $location,
            [
                $relatedContentItems[0],
                $relatedContentItems[1],
            ],
            $isContentIncludedValueMap,
            $locationSearchResult,
            [
                $this->getRelationList(),
                ...$childContentItems,
            ],
            $contentItemsToDelete,
        ];
    }

    /**
     * @return iterable<array{
     *      \Ibexa\Contracts\Core\Repository\Values\Content\Location,
     *      TIsContentIncludedValueMap,
     *      T,
     *      array<\Ibexa\Contracts\Core\Repository\Values\Content\Content>,
     * }>
     */
    public function provideDataForTestOnRecover(): iterable
    {
        $content = $this->createMock(Content::class);
        $mainContentLocation = $this->createLocation($content);

        yield 'Content without relations' => [
            $mainContentLocation,
            [
                [$content, true],
            ],
            [new RelationList()],
            [$content],
        ];

        $relatedContentItems = $this->getRelatedContentItems();
        yield 'Content with relations' => [
            $mainContentLocation,
            [
                [$content, true],
                [$relatedContentItems[0], true],
                [$relatedContentItems[1], true],
            ],
            [
                $this->getRelationList(),
                ...$relatedContentItems,
            ],
            [
                $content,
                ...$relatedContentItems,
            ],
        ];
    }

    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion|bool $criterion
     */
    private function canDelete(
        ContentInfo $contentInfo,
        Location $location,
        bool $canUser,
        $criterion,
        ?int $contentWithLimitedAccess = null
    ): void {
        $target = (new Target\Version())->deleteTranslations(self::TRANSLATIONS);
        $this->mockPermissionResolverCanUser(
            $contentInfo,
            [$location, $target],
            $canUser
        );

        if (!$canUser) {
            return;
        }

        $this->mockPermissionCriterionResolverGetPermissionsCriterion($criterion);

        if (
            !$criterion instanceof Query\Criterion
            || null === $contentWithLimitedAccess
        ) {
            return;
        }

        $this->mockSearchServiceFindContent(
            $location,
            $criterion,
            new SearchResult(['totalCount' => $contentWithLimitedAccess])
        );
    }

    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Query\CriterionInterface|bool $criterion
     */
    private function mockPermissionCriterionResolverGetPermissionsCriterion($criterion): void
    {
        $this->permissionCriterionResolver
            ->expects(self::once())
            ->method('getPermissionsCriterion')
            ->with('content', 'remove')
            ->willReturn($criterion);
    }

    /**
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidCriterionArgumentException
     */
    private function mockSearchServiceFindContent(
        Location $location,
        Query\Criterion $criterion,
        SearchResult $result
    ): void {
        $query = new Query(
            [
                'limit' => 0,
                'filter' => new CriterionLogicalAnd(
                    [
                        new CriterionSubtree($location->getPathString()),
                        new CriterionLogicalNot($criterion),
                    ]
                ),
            ]
        );

        $this->searchService
            ->expects(self::once())
            ->method('findContent')
            ->with($query)
            ->willReturn($result);
    }

    /**
     * @phpstan-param array{
     *     \Ibexa\Contracts\Core\Repository\Values\Content\Location,
     *     \Ibexa\Contracts\Core\Limitation\Target\Version
     * } $targets
     */
    private function mockPermissionResolverCanUser(
        ContentInfo $contentInfo,
        array $targets,
        bool $canUser
    ): void {
        $this->permissionResolver
            ->expects(self::once())
            ->method('canUser')
            ->with(
                'content',
                'remove',
                $contentInfo,
                $targets
            )
            ->willReturn($canUser);
    }

    /**
     * @param array<string> $translations
     */
    private function createVersionInfoMock(array $translations): VersionInfo
    {
        $versionInfo = $this->createMock(VersionInfo::class);
        $versionInfo
            ->expects(self::atLeastOnce())
            ->method('__get')
            ->with('languageCodes')
            ->willReturn($translations);

        return $versionInfo;
    }
}
