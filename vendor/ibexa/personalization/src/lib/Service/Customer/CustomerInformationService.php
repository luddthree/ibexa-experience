<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Service\Customer;

use Ibexa\Personalization\Client\Consumer\Customer\FeaturesDataFetcherInterface;
use Ibexa\Personalization\Client\Consumer\Customer\InformationDataFetcher;
use Ibexa\Personalization\Client\Consumer\Customer\InformationDataFetcherInterface;
use Ibexa\Personalization\Exception\BadResponseException;
use Ibexa\Personalization\Exception\CredentialsNotFoundException;
use Ibexa\Personalization\Service\Setting\SettingServiceInterface;
use Ibexa\Personalization\Value\Content\ItemType;
use Ibexa\Personalization\Value\Content\ItemTypeList;
use Ibexa\Personalization\Value\Customer\BaseInformation;
use Ibexa\Personalization\Value\Customer\Features;
use Symfony\Component\HttpFoundation\Response;

/**
 * @internal
 */
final class CustomerInformationService implements CustomerInformationServiceInterface
{
    /** @var \Ibexa\Personalization\Client\Consumer\Customer\InformationDataFetcherInterface */
    private $informationDataFetcher;

    /** @var \Ibexa\Personalization\Service\Setting\SettingServiceInterface */
    private $settingService;

    private FeaturesDataFetcherInterface $featuresDataFetcher;

    public function __construct(
        SettingServiceInterface $settingService,
        InformationDataFetcherInterface $informationDataFetcher,
        FeaturesDataFetcherInterface $featuresDataFetcher
    ) {
        $this->settingService = $settingService;
        $this->informationDataFetcher = $informationDataFetcher;
        $this->featuresDataFetcher = $featuresDataFetcher;
    }

    public function getBaseInformation(int $customerId): BaseInformation
    {
        try {
            $response = $this->informationDataFetcher->fetchInformation(
                $customerId,
                $this->settingService->getLicenceKeyByCustomerId($customerId)
            );

            $responseContents = $response->getBody()->getContents();

            return BaseInformation::fromArray(
                json_decode($responseContents, true)[InformationDataFetcher::PARAM_BASE_INFORMATION]
            );
        } catch (BadResponseException $exception) {
            if (Response::HTTP_SERVICE_UNAVAILABLE === $exception->getCode()) {
                return BaseInformation::fromArray([]);
            }

            throw $exception;
        }
    }

    public function getItemTypes(int $customerId): ItemTypeList
    {
        try {
            $response = $this->informationDataFetcher->fetchInformation(
                $customerId,
                $this->settingService->getLicenceKeyByCustomerId($customerId),
                [
                    InformationDataFetcher::PARAM_ITEM_TYPE_CONFIGURATION,
                ]
            );
            $responseContents = $response->getBody()->getContents();
            $itemConfiguration = json_decode($responseContents, true);
            $itemTypeList = [];

            foreach ($itemConfiguration[InformationDataFetcher::PARAM_ITEM_TYPE_CONFIGURATION]['types'] as $itemType) {
                $itemTypeList[] = ItemType::fromArray($itemType);
            }

            return new ItemTypeList($itemTypeList);
        } catch (BadResponseException $exception) {
            if (Response::HTTP_SERVICE_UNAVAILABLE === $exception->getCode()) {
                return new ItemTypeList([]);
            }

            throw $exception;
        }
    }

    public function getFeatures(int $customerId): Features
    {
        $licenceKey = $this->settingService->getLicenceKeyByCustomerId($customerId);
        if ($licenceKey === null) {
            throw new CredentialsNotFoundException();
        }

        try {
            $response = $this->featuresDataFetcher->fetchFeatures($customerId, $licenceKey);

            $responseContents = $response->getBody()->getContents();

            return Features::fromArray(
                json_decode($responseContents, true, 512, JSON_THROW_ON_ERROR)
            );
        } catch (BadResponseException $exception) {
            if (Response::HTTP_SERVICE_UNAVAILABLE === $exception->getCode()) {
                return Features::fromArray([]);
            }

            throw $exception;
        }
    }
}

class_alias(CustomerInformationService::class, 'Ibexa\Platform\Personalization\Service\Customer\CustomerInformationService');
