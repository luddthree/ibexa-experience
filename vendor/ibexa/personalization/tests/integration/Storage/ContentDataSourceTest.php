<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Integration\Personalization\Storage;

use Ibexa\Contracts\Personalization\Criteria\CriteriaInterface;
use Ibexa\Contracts\Personalization\Storage\DataSourceInterface;
use Ibexa\Personalization\Criteria\Criteria;
use Ibexa\Personalization\Exception\ItemNotFoundException;
use Ibexa\Personalization\Storage\ContentDataSource;
use Ibexa\Tests\Integration\Personalization\BaseIntegrationTestCase;

/**
 * @covers \Ibexa\Personalization\Storage\ContentDataSource
 */
final class ContentDataSourceTest extends BaseIntegrationTestCase
{
    private DataSourceInterface $contentDataSource;

    protected function setUp(): void
    {
        parent::setUp();

        $this->contentDataSource = self::getServiceByClassName(ContentDataSource::class);
    }

    /**
     * @dataProvider provideDataForTestCountItems
     */
    public function testCountItems(
        int $expectedCount,
        CriteriaInterface $criteria
    ): void {
        self::assertSame(
            $expectedCount,
            $this->contentDataSource->countItems($criteria)
        );
    }

    public function testFetchItemThrowItemNotFoundException(): void
    {
        $this->expectException(ItemNotFoundException::class);
        $this->expectExceptionMessage('Item not found with id: foo1111111111 and language: eng-GB');

        $this->contentDataSource->fetchItem('foo1111111111', self::LANGUAGE_CODE_ENG);
    }

    /**
     * @dataProvider provideDataForTestFetchItem
     */
    public function testFetchItem(
        string $remoteId,
        string $languageCode,
        string $expectedName
    ): void {
        $content = $this->getContentService()->loadContentByRemoteId($remoteId);
        $id = (string)$content->getVersionInfo()->getContentInfo()->getId();
        $fetchedItem = $this->contentDataSource->fetchItem(
            $id,
            $languageCode
        );

        self::assertSame($id, $fetchedItem->getId());
        self::assertSame($content->getContentType()->identifier, $fetchedItem->getType()->getIdentifier());
        self::assertSame($languageCode, $fetchedItem->getLanguage());
        self::assertSame($expectedName, $fetchedItem->getAttributes()['name']);
    }

    /**
     * @dataProvider provideDataForTestFetchItems
     */
    public function testFetchItems(
        int $expectedCount,
        CriteriaInterface $criteria
    ): void {
        self::assertCount(
            $expectedCount,
            $this->contentDataSource->fetchItems($criteria)
        );
    }

    /**
     * @return iterable<array{
     *     int,
     *     \Ibexa\Contracts\Personalization\Criteria\CriteriaInterface
     * }>
     */
    public function provideDataForTestCountItems(): iterable
    {
        yield '6 console content items in English' => [
            6,
            new Criteria(
                [self::CONTENT_TYPE_CONSOLE],
                [self::LANGUAGE_CODE_ENG]
            ),
        ];

        yield '4 console content items in Polish' => [
            4,
            new Criteria(
                [self::CONTENT_TYPE_CONSOLE],
                [self::LANGUAGE_CODE_POL]
            ),
        ];

        yield '30 content items in English' => [
            30,
            new Criteria(
                [self::CONTENT_TYPE_CONSOLE, self::CONTENT_TYPE_LAPTOP, self::CONTENT_TYPE_SMARTPHONE],
                [self::LANGUAGE_CODE_ENG]
            ),
        ];
    }

    /**
     * @return iterable<array{
     *     int,
     *     \Ibexa\Contracts\Personalization\Criteria\CriteriaInterface
     * }>
     */
    public function provideDataForTestFetchItems(): iterable
    {
        yield 'No results' => [
            0,
            new Criteria(
                ['not_existing_content_type'],
                [self::LANGUAGE_CODE_ENG]
            ),
        ];

        yield 'Return content items (30) in English language' => [
            30,
            new Criteria(
                [self::CONTENT_TYPE_CONSOLE, self::CONTENT_TYPE_LAPTOP, self::CONTENT_TYPE_SMARTPHONE],
                [self::LANGUAGE_CODE_ENG]
            ),
        ];

        yield 'Return 6 console content items in English' => [
            6,
            new Criteria(
                [self::CONTENT_TYPE_CONSOLE],
                [self::LANGUAGE_CODE_ENG]
            ),
        ];

        yield 'Return 4 console content in Polish' => [
            4,
            new Criteria(
                [self::CONTENT_TYPE_CONSOLE],
                [self::LANGUAGE_CODE_POL]
            ),
        ];

        yield 'Return content items (58) in English and Polish languages' => [
            58,
            new Criteria(
                [self::CONTENT_TYPE_CONSOLE, self::CONTENT_TYPE_LAPTOP, self::CONTENT_TYPE_SMARTPHONE],
                [self::LANGUAGE_CODE_ENG, self::LANGUAGE_CODE_POL]
            ),
        ];
    }

    /**
     * @return iterable<array{
     *     string,
     *     string,
     * }>
     */
    public function provideDataForTestFetchItem(): iterable
    {
        yield 'Fetch item for English version' => [
            self::REMOTE_ID_CONSOLE_XBOX_SERIES_X,
            self::LANGUAGE_CODE_ENG,
            'XBOX Series X console',
        ];

        yield 'Fetch item for Polish version' => [
            self::REMOTE_ID_CONSOLE_XBOX_SERIES_X,
            self::LANGUAGE_CODE_POL,
            'Konsola XBOX Series X',
        ];
    }
}
