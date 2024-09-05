<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Exporter;

use Exception;
use Ibexa\Contracts\Personalization\Criteria\CriteriaInterface;
use Ibexa\Contracts\Personalization\Value\ItemGroupInterface;
use Ibexa\Personalization\Config\ItemType\IncludedItemTypeResolverInterface;
use Ibexa\Personalization\Criteria\Criteria;
use Ibexa\Personalization\File\FileManagerInterface;
use Ibexa\Personalization\Generator\File\ExportFileGeneratorInterface;
use Ibexa\Personalization\Request\Item\PackageList;
use Ibexa\Personalization\Request\Item\UriPackage;
use Ibexa\Personalization\Service\Item\ItemServiceInterface;
use Ibexa\Personalization\Service\Storage\DataSourceServiceInterface;
use Ibexa\Personalization\Strategy\Storage\SupportedGroupItemStrategy;
use Ibexa\Personalization\Value\Export\FileSettings;
use Ibexa\Personalization\Value\Export\Parameters;
use LogicException;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\NullLogger;

/**
 * Generates and export content to Recommendation Server.
 */
final class Exporter implements ExporterInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    private const EXPORT_DOWNLOAD_URL = '%s/api/ibexa/v2/personalization/v1/export/download/%s';
    private const FILE_FORMAT_NAME = '%s_%s_%d';

    private DataSourceServiceInterface $dataSourceService;

    private IncludedItemTypeResolverInterface $itemTypeResolver;

    private ItemServiceInterface $itemService;

    private ExportFileGeneratorInterface $exportFileGenerator;

    private FileManagerInterface $fileManager;

    public function __construct(
        DataSourceServiceInterface $dataSourceService,
        IncludedItemTypeResolverInterface $itemTypeResolver,
        ItemServiceInterface $itemService,
        ExportFileGeneratorInterface $exportFileGenerator,
        FileManagerInterface $fileManager
    ) {
        $this->dataSourceService = $dataSourceService;
        $this->itemTypeResolver = $itemTypeResolver;
        $this->itemService = $itemService;
        $this->exportFileGenerator = $exportFileGenerator;
        $this->fileManager = $fileManager;
        $this->logger = new NullLogger();
    }

    /**
     * @throws \Exception
     */
    public function export(Parameters $parameters): void
    {
        try {
            $packageList = $this->getPackageList($parameters);

            $this->logger->info(sprintf('Exporting %s item types', $packageList->countItemTypes()));

            $this->itemService->export($parameters, $packageList);
        } catch (Exception $e) {
            $this->logger->error(sprintf('Error during export: %s', $e->getMessage()));

            throw $e;
        }
    }

    public function hasExportItems(Parameters $parameters): bool
    {
        return $this->dataSourceService->getItems($this->createCriteria($parameters, false))->count() > 0;
    }

    public function getPackageList(Parameters $parameters): PackageList
    {
        $chunkDir = $this->fileManager->createChunkDir();
        $packages = [];

        $this->fileManager->lock();

        $this->logger->info('Fetching items from data sources');

        $groupedItems = $this->dataSourceService->getGroupedItems(
            $this->createCriteria($parameters, true),
            SupportedGroupItemStrategy::GROUP_BY_ITEM_TYPE_AND_LANGUAGE
        );

        foreach ($groupedItems->getGroups() as $group) {
            $packages[] = $this->createPackages($group, $chunkDir, $parameters);
        }

        $this->fileManager->unlock();

        $packages = array_merge([], ...$packages);

        return new PackageList($packages);
    }

    private function createCriteria(Parameters $parameters, bool $useLogger): CriteriaInterface
    {
        return new Criteria(
            $this->itemTypeResolver->resolve(
                $parameters->getItemTypeIdentifierList(),
                $useLogger,
                $parameters->getSiteaccess()
            ),
            $parameters->getLanguages(),
            $parameters->getPageSize()
        );
    }

    /**
     * @return array<\Ibexa\Personalization\Request\Item\AbstractPackage>
     */
    private function createPackages(
        ItemGroupInterface $itemGroup,
        string $chunkDir,
        Parameters $parameters
    ): array {
        $uriPackages = [];
        $items = $itemGroup->getItems();
        $itemType = $items->first()->getType();
        $itemGroupIdentifier = $itemGroup->getIdentifier();
        $pos = strrpos($itemGroupIdentifier, '_');
        if ($pos === false) {
            throw new LogicException('Group identifier string should be suffixed with language code');
        }

        $language = substr($itemGroupIdentifier, $pos + 1);
        $identifier = substr($itemGroupIdentifier, 0, $pos);

        $count = $items->count();
        $pageSize = $parameters->getPageSize();
        $length = ceil($count / $pageSize);
        $this->logger->info(
            sprintf(
                'Fetching %s items of item type: identifier %s (language: %s)',
                $count,
                $identifier,
                $language
            )
        );

        for ($i = 1; $i <= $length; ++$i) {
            $page = $i;
            $offset = $page * $pageSize - $pageSize;
            $filename = sprintf(self::FILE_FORMAT_NAME, $identifier, $language, $page);
            $chunkPath = $chunkDir . $filename;
            $exportFileSettings = new FileSettings(
                $items->slice($offset, $pageSize),
                $identifier,
                $language,
                $page,
                $chunkPath
            );

            $this->generateExportFile($exportFileSettings);

            $uriPackages[] = new UriPackage(
                $itemType->getId(),
                $itemType->getName(),
                $language,
                $this->generateFileUrl($parameters->getHost(), $chunkPath)
            );
        }

        $this->checkItemTypeIdentifiers($identifier, $items);

        return $uriPackages;
    }

    private function generateExportFile(FileSettings $exportFileSettings): void
    {
        $this->logger->info(sprintf(
            'Generating file for item type identifier: %s, language: %s, chunk: #%s',
            $exportFileSettings->getIdentifier(),
            $exportFileSettings->getLanguage(),
            $exportFileSettings->getChunkPath()
        ));

        $this->exportFileGenerator->generate($exportFileSettings);
    }

    private function generateFileUrl(string $host, string $chunkPath): string
    {
        $url = sprintf(self::EXPORT_DOWNLOAD_URL, $host, $chunkPath);
        $this->logger->info(sprintf('Generating url: %s', $url));

        return $url;
    }

    /**
     * @param iterable<\Ibexa\Contracts\Personalization\Value\ItemInterface> $items
     */
    private function checkItemTypeIdentifiers(string $groupItemTypeIdentifier, iterable $items): void
    {
        foreach ($items as $item) {
            $itemTypeIdentifier = $item->getType()->getIdentifier();

            if ($groupItemTypeIdentifier !== $itemTypeIdentifier) {
                $this->logger->info(sprintf(
                    'Item: %s has different item type identifier: %s than group item type %s',
                    $item->getId(),
                    $itemTypeIdentifier,
                    $groupItemTypeIdentifier
                ));
            }
        }
    }
}
