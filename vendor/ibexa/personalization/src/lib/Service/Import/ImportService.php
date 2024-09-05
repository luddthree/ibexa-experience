<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Service\Import;

use Ibexa\Personalization\Client\Consumer\Import\ImportDataFetcherInterface;
use Ibexa\Personalization\Exception\BadResponseException;
use Ibexa\Personalization\Service\Setting\SettingServiceInterface;
use Ibexa\Personalization\Value\Import\ImportedItem;
use Ibexa\Personalization\Value\Import\ImportedItemList;
use Symfony\Component\HttpFoundation\Response;

/**
 * @internal
 */
final class ImportService implements ImportServiceInterface
{
    /** @var \Ibexa\Personalization\Client\Consumer\Import\ImportDataFetcherInterface */
    private $importDataFetcher;

    /** @var \Ibexa\Personalization\Service\Setting\SettingServiceInterface */
    private $settingService;

    public function __construct(
        SettingServiceInterface $settingService,
        ImportDataFetcherInterface $importDataFetcher
    ) {
        $this->importDataFetcher = $importDataFetcher;
        $this->settingService = $settingService;
    }

    /**
     * @throws \Ibexa\Personalization\Exception\BadResponseException
     * @throws \Ibexa\Contracts\Core\Exception\InvalidArgumentException
     */
    public function getImportedItems(int $customerId): ImportedItemList
    {
        try {
            $response = $this->importDataFetcher->fetchImportedItems(
                $customerId,
                $this->settingService->getLicenceKeyByCustomerId($customerId)
            );

            $responseContents = json_decode($response->getBody()->getContents(), true)['importedItems'];
            $importedItems = [];

            foreach ($responseContents as $importedItem) {
                $importedItems[] = ImportedItem::fromArray($importedItem);
            }

            return new ImportedItemList($importedItems);
        } catch (BadResponseException $exception) {
            if (Response::HTTP_NOT_FOUND === $exception->getCode()) {
                return new ImportedItemList([]);
            }

            throw $exception;
        }
    }
}

class_alias(ImportService::class, 'Ibexa\Platform\Personalization\Service\Import\ImportService');
