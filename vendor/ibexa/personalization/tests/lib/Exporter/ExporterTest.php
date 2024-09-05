<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Personalization\Exporter;

use Ibexa\Contracts\Personalization\Criteria\CriteriaInterface;
use Ibexa\Contracts\Personalization\Value\ItemGroupListInterface;
use Ibexa\Contracts\Personalization\Value\ItemListInterface;
use Ibexa\Personalization\Config\ItemType\IncludedItemTypeResolverInterface;
use Ibexa\Personalization\Criteria\Criteria;
use Ibexa\Personalization\Exporter\Exporter;
use Ibexa\Personalization\Exporter\ExporterInterface;
use Ibexa\Personalization\File\FileManagerInterface;
use Ibexa\Personalization\Generator\File\ExportFileGeneratorInterface;
use Ibexa\Personalization\Request\Item\PackageList;
use Ibexa\Personalization\Request\Item\UriPackage;
use Ibexa\Personalization\Service\Item\ItemServiceInterface;
use Ibexa\Personalization\Service\Storage\DataSourceServiceInterface;
use Ibexa\Personalization\Value\Export\Parameters;
use Ibexa\Tests\Personalization\Creator\DataSourceTestItemCreator;
use Ibexa\Tests\Personalization\Storage\AbstractDataSourceTestCase;

/**
 * @covers \Ibexa\Personalization\Exporter\Exporter
 */
final class ExporterTest extends AbstractDataSourceTestCase
{
    private const EXPORT_FILE_PATH_DIR = 'test/';

    private ExporterInterface $exporter;

    /** @var \Ibexa\Personalization\Service\Storage\DataSourceServiceInterface|\PHPUnit\Framework\MockObject\MockObject */
    private DataSourceServiceInterface $dataSourceService;

    /** @var \Ibexa\Personalization\Config\ItemType\IncludedItemTypeResolverInterface|\PHPUnit\Framework\MockObject\MockObject */
    private IncludedItemTypeResolverInterface $itemTypeResolver;

    /** @var \Ibexa\Personalization\Service\Item\ItemServiceInterface&\PHPUnit\Framework\MockObject\MockObject */
    private ItemServiceInterface $itemService;

    /** @var \Ibexa\Personalization\Generator\File\ExportFileGeneratorInterface|\PHPUnit\Framework\MockObject\MockObject */
    private ExportFileGeneratorInterface $exportFileGenerator;

    /** @var \Ibexa\Personalization\File\FileManagerInterface|\PHPUnit\Framework\MockObject\MockObject */
    private FileManagerInterface $fileManager;

    protected function setUp(): void
    {
        $this->dataSourceService = $this->createMock(DataSourceServiceInterface::class);
        $this->itemTypeResolver = $this->createMock(IncludedItemTypeResolverInterface::class);
        $this->itemService = $this->createMock(ItemServiceInterface::class);
        $this->exportFileGenerator = $this->createMock(ExportFileGeneratorInterface::class);
        $this->fileManager = $this->createMock(FileManagerInterface::class);

        $this->exporter = new Exporter(
            $this->dataSourceService,
            $this->itemTypeResolver,
            $this->itemService,
            $this->exportFileGenerator,
            $this->fileManager
        );
    }

    public function testReturnTrueIfHasExportItems(): void
    {
        $this->configureDataSourceServiceToReturnItemList(
            $this->createCriteria(),
            $this->getItemList()
        );
        $this->configureIncludedItemTypeResolverToReturnIncludedItemTypes(
            $this->getItemTypeIdentifierList(),
            false,
            'foo'
        );

        self::assertTrue($this->exporter->hasExportItems($this->createExportParameters()));
    }

    public function testReturnFalseIfNoItemsToExport(): void
    {
        $this->configureDataSourceServiceToReturnItemList(
            $this->createCriteria(),
            $this->itemCreator->createTestItemList()
        );
        $this->configureIncludedItemTypeResolverToReturnIncludedItemTypes(
            $this->getItemTypeIdentifierList(),
            false,
            'foo'
        );

        self::assertFalse($this->exporter->hasExportItems($this->createExportParameters()));
    }

    public function testGetExportEvents(): void
    {
        $this->configureFileManagerToCreateChunkDir();
        $this->configureIncludedItemTypeResolverToReturnIncludedItemTypes(
            $this->getItemTypeIdentifierList(),
            true,
            'foo'
        );
        $this->configureDataSourceServiceToReturnGroupedItems($this->createCriteria(), $this->getGroupedItems());

        self::assertEquals(
            $this->createPackageList(),
            $this->exporter->getPackageList($this->createExportParameters())
        );
    }

    public function testExport(): void
    {
        $this->configureFileManagerToCreateChunkDir();
        $this->configureIncludedItemTypeResolverToReturnIncludedItemTypes(
            $this->getItemTypeIdentifierList(),
            true,
            'foo'
        );
        $this->configureDataSourceServiceToReturnGroupedItems($this->createCriteria(), $this->getGroupedItems());

        $parameters = $this->createExportParameters();

        $this->itemService
            ->expects(self::once())
            ->method('export')
            ->with(
                $parameters,
                $this->createPackageList()
            );

        $this->exporter->export($parameters);
    }

    private function configureDataSourceServiceToReturnItemList(
        CriteriaInterface $criteria,
        ItemListInterface $itemList
    ): void {
        $this->dataSourceService
            ->expects(self::once())
            ->method('getItems')
            ->with($criteria)
            ->willReturn($itemList);
    }

    private function configureDataSourceServiceToReturnGroupedItems(
        CriteriaInterface $criteria,
        ItemGroupListInterface $groupedItems
    ): void {
        $this->dataSourceService
            ->expects(self::once())
            ->method('getGroupedItems')
            ->with($criteria, 'item_type_and_language')
            ->willReturn($groupedItems);
    }

    /**
     * @param array<string> $includedItemTypes
     */
    private function configureIncludedItemTypeResolverToReturnIncludedItemTypes(
        array $includedItemTypes,
        bool $uselogger,
        ?string $siteAccess = null
    ): void {
        $this->itemTypeResolver
            ->expects(self::once())
            ->method('resolve')
            ->with($includedItemTypes, $uselogger, $siteAccess)
            ->willReturn($includedItemTypes);
    }

    private function configureFileManagerToCreateChunkDir(): void
    {
        $this->fileManager
            ->expects(self::once())
            ->method('createChunkDir')
            ->willReturn(self::EXPORT_FILE_PATH_DIR);
    }

    private function createCriteria(): CriteriaInterface
    {
        return new Criteria(
            $this->getItemTypeIdentifierList(),
            $this->getLanguageList(),
            500
        );
    }

    private function createExportParameters(): Parameters
    {
        return Parameters::fromArray(
            [
                'customer_id' => '12345',
                'license_key' => '12345-12345-12345-12345',
                'item_type_identifier_list' => implode(',', $this->getItemTypeIdentifierList()),
                'languages' => implode(',', $this->getLanguageList()),
                'siteaccess' => 'foo',
                'web_hook' => 'https://reco.engine.com',
                'host' => 'https://localhost',
                'page_size' => '500',
            ]
        );
    }

    private function getItemList(): ItemListInterface
    {
        return $this->itemCreator->createTestItemList(
            $this->itemCreator->createTestItem(
                1,
                '1',
                DataSourceTestItemCreator::ARTICLE_TYPE_ID,
                DataSourceTestItemCreator::ARTICLE_TYPE_IDENTIFIER,
                DataSourceTestItemCreator::ARTICLE_TYPE_NAME,
                DataSourceTestItemCreator::LANGUAGE_EN
            ),
            $this->itemCreator->createTestItem(
                1,
                '2',
                DataSourceTestItemCreator::PRODUCT_TYPE_ID,
                DataSourceTestItemCreator::PRODUCT_TYPE_IDENTIFIER,
                DataSourceTestItemCreator::PRODUCT_TYPE_NAME,
                DataSourceTestItemCreator::LANGUAGE_EN
            ),
            $this->itemCreator->createTestItem(
                1,
                '3',
                DataSourceTestItemCreator::BLOG_TYPE_ID,
                DataSourceTestItemCreator::BLOG_TYPE_IDENTIFIER,
                DataSourceTestItemCreator::BLOG_TYPE_NAME,
                DataSourceTestItemCreator::LANGUAGE_EN
            ),
        );
    }

    private function getGroupedItems(): ItemGroupListInterface
    {
        return $this->itemCreator->createTestItemGroupList(
            $this->itemCreator->createTestItemGroup(
                'article_en',
                $this->itemCreator->createTestItemList(
                    $this->itemCreator->createTestItem(
                        1,
                        '1',
                        DataSourceTestItemCreator::ARTICLE_TYPE_ID,
                        DataSourceTestItemCreator::ARTICLE_TYPE_IDENTIFIER,
                        DataSourceTestItemCreator::ARTICLE_TYPE_NAME,
                        DataSourceTestItemCreator::LANGUAGE_EN
                    )
                )
            ),
            $this->itemCreator->createTestItemGroup(
                'product_en',
                $this->itemCreator->createTestItemList(
                    $this->itemCreator->createTestItem(
                        1,
                        '2',
                        DataSourceTestItemCreator::PRODUCT_TYPE_ID,
                        DataSourceTestItemCreator::PRODUCT_TYPE_IDENTIFIER,
                        DataSourceTestItemCreator::PRODUCT_TYPE_NAME,
                        DataSourceTestItemCreator::LANGUAGE_EN
                    )
                )
            ),
            $this->itemCreator->createTestItemGroup(
                'blog_en',
                $this->itemCreator->createTestItemList(
                    $this->itemCreator->createTestItem(
                        1,
                        '3',
                        DataSourceTestItemCreator::BLOG_TYPE_ID,
                        DataSourceTestItemCreator::BLOG_TYPE_IDENTIFIER,
                        DataSourceTestItemCreator::BLOG_TYPE_NAME,
                        DataSourceTestItemCreator::LANGUAGE_EN
                    )
                )
            ),
        );
    }

    private function createPackageList(): PackageList
    {
        return new PackageList(
            [
                new UriPackage(
                    DataSourceTestItemCreator::ARTICLE_TYPE_ID,
                    DataSourceTestItemCreator::ARTICLE_TYPE_NAME,
                    DataSourceTestItemCreator::LANGUAGE_EN,
                    'https://localhost/api/ibexa/v2/personalization/v1/export/download/test/article_en_1'
                ),
                new UriPackage(
                    DataSourceTestItemCreator::PRODUCT_TYPE_ID,
                    DataSourceTestItemCreator::PRODUCT_TYPE_NAME,
                    DataSourceTestItemCreator::LANGUAGE_EN,
                    'https://localhost/api/ibexa/v2/personalization/v1/export/download/test/product_en_1'
                ),
                new UriPackage(
                    DataSourceTestItemCreator::BLOG_TYPE_ID,
                    DataSourceTestItemCreator::BLOG_TYPE_NAME,
                    DataSourceTestItemCreator::LANGUAGE_EN,
                    'https://localhost/api/ibexa/v2/personalization/v1/export/download/test/blog_en_1'
                ),
            ]
        );
    }

    /**
     * @return array<string>
     */
    private function getItemTypeIdentifierList(): array
    {
        return [
            DataSourceTestItemCreator::ARTICLE_TYPE_IDENTIFIER,
            DataSourceTestItemCreator::PRODUCT_TYPE_IDENTIFIER,
            DataSourceTestItemCreator::BLOG_TYPE_IDENTIFIER,
        ];
    }

    /**
     * @return array<string>
     */
    private function getLanguageList(): array
    {
        return [
            DataSourceTestItemCreator::LANGUAGE_EN,
            DataSourceTestItemCreator::LANGUAGE_DE,
        ];
    }
}
