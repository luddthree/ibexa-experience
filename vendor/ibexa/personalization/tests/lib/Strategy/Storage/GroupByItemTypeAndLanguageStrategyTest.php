<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Personalization\Strategy\Storage;

use Ibexa\Contracts\Personalization\Criteria\CriteriaInterface;
use Ibexa\Contracts\Personalization\Storage\DataSourceInterface;
use Ibexa\Contracts\Personalization\Value\ItemGroupListInterface;
use Ibexa\Contracts\Personalization\Value\ItemListInterface;
use Ibexa\Personalization\Strategy\Storage\GroupByItemTypeAndLanguageStrategy;
use Ibexa\Personalization\Strategy\Storage\GroupItemStrategyInterface;
use Ibexa\Personalization\Value\Storage\ItemGroupList;
use Ibexa\Personalization\Value\Storage\ItemList;
use Ibexa\Tests\Personalization\Creator\DataSourceTestItemCreator;
use Ibexa\Tests\Personalization\Storage\AbstractDataSourceTestCase;

/**
 * @phpstan-type TCountItemsValueMap array<array{
 *      contentTypeIdentifiers: array<string>,
 *      languageCodes: array<string>,
 *      numberOfItems: int,
 *  }>
 *
 * @phpstan-type TFetchItemsValueMap array<array{
 *      contentTypeIdentifiers: array<string>,
 *      languageCodes: array<string>,
 *      itemList: \Ibexa\Contracts\Personalization\Value\ItemListInterface,
 *  }>
 *
 * @covers \Ibexa\Personalization\Strategy\Storage\GroupByItemTypeAndLanguageStrategy
 */
final class GroupByItemTypeAndLanguageStrategyTest extends AbstractDataSourceTestCase
{
    private GroupItemStrategyInterface $strategy;

    /** @var \Ibexa\Contracts\Personalization\Storage\DataSourceInterface&\PHPUnit\Framework\MockObject\MockObject */
    private DataSourceInterface $dataSource;

    public function setUp(): void
    {
        $this->dataSource = $this->createMock(DataSourceInterface::class);
        $this->strategy = new GroupByItemTypeAndLanguageStrategy();
    }

    /**
     * @dataProvider provideDataForTestGetGroupList
     *
     * @phpstan-param TCountItemsValueMap $countItemsValueMap
     * @phpstan-param TFetchItemsValueMap $fetchItemsValueMap
     */
    public function testGetGroupList(
        ItemGroupListInterface $itemGroupList,
        CriteriaInterface $criteria,
        array $countItemsValueMap,
        array $fetchItemsValueMap
    ): void {
        $this->mockDataSourceCountItems($countItemsValueMap);
        $this->mockDataSourceFetchItems($fetchItemsValueMap);

        self::assertEquals(
            $itemGroupList,
            $this->strategy->getGroupList($this->dataSource, $criteria)
        );
    }

    /**
     * @return iterable<array{
     *     \Ibexa\Contracts\Personalization\Value\ItemGroupListInterface,
     *     \Ibexa\Contracts\Personalization\Criteria\CriteriaInterface,
     *     TCountItemsValueMap,
     *     TFetchItemsValueMap,
     * }>
     */
    public function provideDataForTestGetGroupList(): iterable
    {
        $itemsData = [
            DataSourceTestItemCreator::ARTICLE_TYPE_IDENTIFIER => [
                'en' => $this->itemCreator->createTestItemListForEnglishArticles(),
                'de' => $this->itemCreator->createTestItemListForGermanArticles(),
                'fr' => new ItemList([]),
            ],
            DataSourceTestItemCreator::BLOG_TYPE_IDENTIFIER => [
                'en' => $this->itemCreator->createTestItemListForEnglishBlogPosts(),
                'de' => new ItemList([]),
                'fr' => $this->itemCreator->createTestItemListForFrenchBlogPosts(),
            ],
        ];

        yield 'No results' => [
            new ItemGroupList([]),
            $this->itemCreator->createTestCriteria(
                ['foo'],
                ['pl']
            ),
            [],
            [],
        ];

        yield 'Results for articles and blog posts' => [
            $this->itemCreator->createTestItemGroupListForArticlesAndBlogPosts(),
            $this->itemCreator->createTestCriteria(
                [
                    DataSourceTestItemCreator::ARTICLE_TYPE_IDENTIFIER,
                    DataSourceTestItemCreator::BLOG_TYPE_IDENTIFIER,
                ],
                ['en', 'de', 'fr']
            ),
            $this->getCountItemsValueMap($itemsData),
            $this->getFetchItemsValueMap($itemsData),
        ];
    }

    /**
     * @param array<string, array<string, \Ibexa\Contracts\Personalization\Value\ItemListInterface>> $data
     *
     * @phpstan-return TCountItemsValueMap
     */
    private function getCountItemsValueMap(array $data): array
    {
        $countItemsValueMap = [];
        foreach ($data as $contentTypeIdentifier => $itemData) {
            /** @var \Ibexa\Contracts\Personalization\Value\ItemListInterface $itemList */
            foreach ($itemData as $languageCode => $itemList) {
                $countItemsValueMap[] = [
                    'contentTypeIdentifiers' => [$contentTypeIdentifier],
                    'languageCodes' => [$languageCode],
                    'numberOfItems' => $itemList->count(),
                ];
            }
        }

        return $countItemsValueMap;
    }

    /**
     * @param array<string, array<string, \Ibexa\Contracts\Personalization\Value\ItemListInterface>> $data
     *
     * @phpstan-return TFetchItemsValueMap
     */
    private function getFetchItemsValueMap(array $data): array
    {
        $fetchItemsValueMap = [];
        foreach ($data as $contentTypeIdentifier => $itemData) {
            /** @var \Ibexa\Contracts\Personalization\Value\ItemListInterface $itemList */
            foreach ($itemData as $languageCode => $itemList) {
                $fetchItemsValueMap[] = [
                    'contentTypeIdentifiers' => [$contentTypeIdentifier],
                    'languageCodes' => [$languageCode],
                    'itemList' => $itemList,
                ];
            }
        }

        return $fetchItemsValueMap;
    }

    /**
     * @phpstan-param TCountItemsValueMap $valueMap
     */
    private function mockDataSourceCountItems(array $valueMap): void
    {
        $this->dataSource
            ->method('countItems')
            ->willReturnCallback(
                static function (CriteriaInterface $criteria) use ($valueMap): int {
                    foreach ($valueMap as $value) {
                        if (
                            $criteria->getItemTypeIdentifiers() === $value['contentTypeIdentifiers']
                            && $criteria->getLanguages() === $value['languageCodes']
                        ) {
                            return $value['numberOfItems'];
                        }
                    }

                    return 0;
                }
            );
    }

    /**
     * @phpstan-param TFetchItemsValueMap $valueMap
     */
    private function mockDataSourceFetchItems(array $valueMap): void
    {
        $this->dataSource
            ->method('fetchItems')
            ->willReturnCallback(
                static function (CriteriaInterface $criteria) use ($valueMap): ItemListInterface {
                    foreach ($valueMap as $value) {
                        if (
                            $criteria->getItemTypeIdentifiers() === $value['contentTypeIdentifiers']
                            && $criteria->getLanguages() === $value['languageCodes']
                        ) {
                            return $value['itemList'];
                        }
                    }

                    return new ItemList([]);
                }
            );
    }
}
