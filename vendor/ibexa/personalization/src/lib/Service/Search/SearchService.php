<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Service\Search;

use Ibexa\Personalization\Client\Consumer\Search\SearchAttributeDataFetcher;
use Ibexa\Personalization\Client\Consumer\Search\SearchAttributeDataFetcherInterface;
use Ibexa\Personalization\Factory\Search\AttributeCriteriaFactoryInterface;
use Ibexa\Personalization\Mapper\Search\SearchAttributesResultMapperInterface;
use Ibexa\Personalization\Service\Setting\SettingServiceInterface;
use Ibexa\Personalization\Value\Search\ResultList;

final class SearchService implements SearchServiceInterface
{
    private SearchAttributeDataFetcherInterface $searchAttributeDataFetcher;

    private SettingServiceInterface $settingService;

    private SearchAttributesResultMapperInterface $searchAttributesResultMapper;

    private AttributeCriteriaFactoryInterface $attributeCriteriaFactory;

    public function __construct(
        SearchAttributeDataFetcherInterface $searchAttributeDataFetcher,
        SettingServiceInterface $settingService,
        SearchAttributesResultMapperInterface $searchAttributesResultMapper,
        AttributeCriteriaFactoryInterface $attributeCriteriaFactory
    ) {
        $this->searchAttributeDataFetcher = $searchAttributeDataFetcher;
        $this->settingService = $settingService;
        $this->searchAttributesResultMapper = $searchAttributesResultMapper;
        $this->attributeCriteriaFactory = $attributeCriteriaFactory;
    }

    /**
     * @throws \JsonException
     */
    public function searchAttributes(int $customerId, string $phrase): ResultList
    {
        $response = $this->searchAttributeDataFetcher->search(
            $customerId,
            $this->settingService->getLicenceKeyByCustomerId($customerId),
            $this->attributeCriteriaFactory->getAttributesCriteria($customerId, $phrase)
        );

        $searchResult = json_decode(
            $response->getBody()->getContents(),
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        return new ResultList(
            $this->searchAttributesResultMapper->map($customerId, $searchResult[SearchAttributeDataFetcher::ITEMS_KEY])
        );
    }
}
