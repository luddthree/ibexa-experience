<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Service\Scenario;

use Ibexa\Personalization\Client\Consumer\Scenario\ScenarioDataFetcher;
use Ibexa\Personalization\Client\Consumer\Scenario\ScenarioDataFetcherInterface;
use Ibexa\Personalization\Client\Consumer\Scenario\ScenarioDataSenderInterface;
use Ibexa\Personalization\Exception\BadResponseException;
use Ibexa\Personalization\Exception\MalformedDataException;
use Ibexa\Personalization\Exception\ScenarioNotFoundException;
use Ibexa\Personalization\Service\Customer\CustomerInformationServiceInterface;
use Ibexa\Personalization\Service\Setting\SettingServiceInterface;
use Ibexa\Personalization\Value\Content\CrossContentType;
use Ibexa\Personalization\Value\Content\ItemType;
use Ibexa\Personalization\Value\Content\ItemTypeList;
use Ibexa\Personalization\Value\GranularityDateTimeRange;
use Ibexa\Personalization\Value\Scenario\ProfileFilterSet;
use Ibexa\Personalization\Value\Scenario\Scenario;
use Ibexa\Personalization\Value\Scenario\ScenarioList;
use Ibexa\Personalization\Value\Scenario\StandardFilterSet;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @internal
 */
final class ScenarioService implements ScenarioServiceInterface
{
    private ScenarioDataFetcherInterface $scenarioDataFetcher;

    private ScenarioDataSenderInterface $scenarioDataSender;

    private CustomerInformationServiceInterface $customerInformationService;

    private TranslatorInterface $translator;

    private SettingServiceInterface $settingService;

    public function __construct(
        SettingServiceInterface $settingService,
        ScenarioDataFetcherInterface $scenarioDataFetcher,
        ScenarioDataSenderInterface $scenarioDataSender,
        CustomerInformationServiceInterface $customerInformationService,
        TranslatorInterface $translator
    ) {
        $this->scenarioDataFetcher = $scenarioDataFetcher;
        $this->scenarioDataSender = $scenarioDataSender;
        $this->customerInformationService = $customerInformationService;
        $this->translator = $translator;
        $this->settingService = $settingService;
    }

    public function getScenarioList(
        int $customerId,
        ?GranularityDateTimeRange $granularityDateTimeRange = null
    ): ScenarioList {
        $response = $this->scenarioDataFetcher->fetchScenarioList(
            $customerId,
            $this->settingService->getLicenceKeyByCustomerId($customerId),
            $granularityDateTimeRange
        );

        $responseContents = json_decode($response->getBody()->getContents(), true);
        $types = $this->customerInformationService->getItemTypes($customerId);
        $scenarioItems = [];

        foreach ($responseContents['scenarioInfoList'] as $scenario) {
            $scenarioItems[] = Scenario::fromArray(
                $this->setItemTypesForScenario($scenario, $types)
            );
        }

        return new ScenarioList($scenarioItems);
    }

    public function getCalledScenarios(
        int $customerId,
        GranularityDateTimeRange $granularityDateTimeRange
    ): ScenarioList {
        $scenarioList = $this->getScenarioList(
            $customerId,
            $granularityDateTimeRange
        );

        $calledScenarios = [];

        foreach ($scenarioList as $scenario) {
            if ($scenario->getCalls() > 0) {
                $calledScenarios[] = $scenario;
            }
        }

        return new ScenarioList($calledScenarios);
    }

    public function getScenarioListByScenarioType(int $customerId, string $scenarioType): ScenarioList
    {
        $response = $this->scenarioDataFetcher->fetchScenarioListByScenarioType(
            $customerId,
            $this->settingService->getLicenceKeyByCustomerId($customerId),
            $scenarioType
        );

        $responseContents = json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);
        if (!isset($responseContents[ScenarioDataFetcher::KEY_SCENARIO_INFO_LIST])) {
            throw new MalformedDataException(
                sprintf('The "%s" key is missing', ScenarioDataFetcher::KEY_SCENARIO_INFO_LIST)
            );
        }

        $types = $this->customerInformationService->getItemTypes($customerId);
        $scenarioItems = [];

        foreach ($responseContents['scenarioInfoList'] as $scenario) {
            $scenarioItems[] = Scenario::fromArray(
                $this->setItemTypesForScenario($scenario, $types)
            );
        }

        return new ScenarioList($scenarioItems);
    }

    /**
     * @throws \Ibexa\Personalization\Exception\BadResponseException
     */
    public function getScenario(
        int $customerId,
        string $scenarioName
    ): Scenario {
        try {
            $response = $this->scenarioDataFetcher->fetchScenario(
                $customerId,
                $this->settingService->getLicenceKeyByCustomerId($customerId),
                $scenarioName
            );

            return $this->processScenario(
                $customerId,
                json_decode($response->getBody()->getContents(), true)['scenario']
            );
        } catch (BadResponseException $exception) {
            if (Response::HTTP_NOT_FOUND === $exception->getCode()) {
                throw new ScenarioNotFoundException($scenarioName);
            }

            throw $exception;
        }
    }

    /**
     * @throws \Ibexa\Personalization\Exception\BadResponseException
     */
    public function createScenario(int $customerId, Scenario $scenario): Scenario
    {
        try {
            $response = $this->scenarioDataSender->createScenario(
                $customerId,
                $this->settingService->getLicenceKeyByCustomerId($customerId),
                $scenario
            );

            return $this->processScenario(
                $customerId,
                json_decode($response->getBody()->getContents(), true)['scenario']
            );
        } catch (BadResponseException $exception) {
            throw $exception;
        }
    }

    public function updateScenario(
        int $customerId,
        Scenario $scenario
    ): Scenario {
        $response = $this->scenarioDataSender->updateScenario(
            $customerId,
            $this->settingService->getLicenceKeyByCustomerId($customerId),
            $scenario
        );

        return $this->processScenario(
            $customerId,
            json_decode($response->getBody()->getContents(), true)['scenario']
        );
    }

    /**
     * @phpstan-param array{
     *  'referenceCode': string,
     *  'type': string,
     *  'title': string,
     *  'available': string,
     *  'enabled': string,
     *  'inputItemType': int,
     *  'outputItemTypes': array<int>,
     *  'statisticItems': ?array,
     *  'stages': ?array,
     *  'websiteContext': string,
     *  'profileContext': string,
     *  'models': ?array,
     *  'description': string,
     * } $scenarioResponseContent
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    private function processScenario(int $customerId, array $scenarioResponseContent): Scenario
    {
        $types = $this->customerInformationService->getItemTypes($customerId);
        $scenario = $this->setItemTypesForScenario(
            $scenarioResponseContent,
            $types
        );

        return Scenario::fromArray($scenario);
    }

    public function deleteScenario(int $customerId, string $scenarioName): int
    {
        $response = $this->scenarioDataSender->deleteScenario(
            $customerId,
            $this->settingService->getLicenceKeyByCustomerId($customerId),
            $scenarioName
        );

        return $response->getStatusCode();
    }

    public function updateScenarioProfileFilterSet(
        int $customerId,
        string $scenarioName,
        ProfileFilterSet $profileFilterSet
    ): ProfileFilterSet {
        $response = $this->scenarioDataSender->updateScenarioFilterSet(
            $customerId,
            $this->settingService->getLicenceKeyByCustomerId($customerId),
            ScenarioDataFetcher::SCENARIO_FILTER_PROFILE,
            $scenarioName,
            ['json' => $profileFilterSet]
        );

        $profileFilterSet = json_decode($response->getBody()->getContents(), true)['profileFilterSet'];

        return ProfileFilterSet::fromArray($profileFilterSet);
    }

    public function updateScenarioStandardFilterSet(
        int $customerId,
        string $scenarioName,
        StandardFilterSet $standardFilterSet
    ): StandardFilterSet {
        $response = $this->scenarioDataSender->updateScenarioFilterSet(
            $customerId,
            $this->settingService->getLicenceKeyByCustomerId($customerId),
            ScenarioDataFetcher::SCENARIO_FILTER_STANDARD,
            $scenarioName,
            ['json' => $standardFilterSet]
        );

        $standardFilterSet = json_decode($response->getBody()->getContents(), true)['standardFilterSet'];

        return StandardFilterSet::fromArray($standardFilterSet);
    }

    public function getScenarioProfileFilterSet(
        int $customerId,
        string $scenarioName
    ): ProfileFilterSet {
        $response = $this->scenarioDataFetcher->fetchScenarioFilterSet(
            $customerId,
            $this->settingService->getLicenceKeyByCustomerId($customerId),
            ScenarioDataFetcher::SCENARIO_FILTER_PROFILE,
            $scenarioName
        );

        $profileFilterSet = json_decode($response->getBody()->getContents(), true)['profileFilterSet'];

        return ProfileFilterSet::fromArray($profileFilterSet);
    }

    public function getScenarioStandardFilterSet(
        int $customerId,
        string $scenarioName
    ): StandardFilterSet {
        $response = $this->scenarioDataFetcher->fetchScenarioFilterSet(
            $customerId,
            $this->settingService->getLicenceKeyByCustomerId($customerId),
            ScenarioDataFetcher::SCENARIO_FILTER_STANDARD,
            $scenarioName
        );

        $standardFilterSet = json_decode($response->getBody()->getContents(), true)['standardFilterSet'];

        return StandardFilterSet::fromArray($standardFilterSet);
    }

    /**
     * @phpstan-param array{
     *  'referenceCode': string,
     *  'type': string,
     *  'title': string,
     *  'available': string,
     *  'enabled': string,
     *  'inputItemType': int,
     *  'outputItemTypes': array<int>,
     *  'statisticItems': ?array,
     *  'stages': ?array,
     *  'websiteContext': string,
     *  'profileContext': string,
     *  'models': ?array,
     *  'description': string,
     * } $scenario
     *
     * @phpstan-return array{
     *  'referenceCode': string,
     *  'type': string,
     *  'title': string,
     *  'available': string,
     *  'enabled': string,
     *  'inputItemType': ItemType,
     *  'outputItemTypes': ItemTypeList,
     *  'statisticItems': ?array,
     *  'stages': ?array,
     *  'websiteContext': string,
     *  'profileContext': string,
     *  'models': ?array,
     *  'description': string,
     * } $properties
     *
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     */
    private function setItemTypesForScenario(array $scenario, ItemTypeList $types): array
    {
        $scenario['inputItemType'] =
            $types->findItemType($scenario['inputItemType'])
            ?? $this->getUndefinedItemType($scenario['inputItemType']);

        $outputItemTypes = [];
        foreach ($scenario['outputItemTypes'] as $outputItemTypeId) {
            $outputItemTypes[] =
                $types->findItemType($outputItemTypeId)
                ?? $this->getUndefinedItemType($outputItemTypeId);
        }

        if (count($outputItemTypes) > 1) {
            array_unshift(
                $outputItemTypes,
                new CrossContentType(
                    $this->translator->trans(
                        /** @Desc("All") */
                        'ibexa_personalization.scenario.output_type_all',
                        [],
                        'ibexa_personalization'
                    )
                )
            );
        }

        $scenario['outputItemTypes'] = new ItemTypeList($outputItemTypes);

        return $scenario;
    }

    private function getUndefinedItemType(int $typeId): ItemType
    {
        return ItemType::fromArray([
            'id' => $typeId,
            'description' => $this->translator->trans(
                /** @Desc("Undefined") */
                'ibexa_personalization.scenario.undefined',
                [],
                'ibexa_personalization'
            ),
            'contentTypes' => [],
        ]);
    }
}

class_alias(ScenarioService::class, 'Ibexa\Platform\Personalization\Service\Scenario\ScenarioService');
