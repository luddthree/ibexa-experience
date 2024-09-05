<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Tests\Personalization\Scenario;

use Ibexa\Personalization\Value\Content\CrossContentType;
use Ibexa\Personalization\Value\Content\ItemType;
use Ibexa\Personalization\Value\Content\ItemTypeList;
use Ibexa\Personalization\Value\Scenario\Scenario;
use Ibexa\Personalization\Value\Scenario\ScenarioList;
use Ibexa\Tests\Personalization\Fixture\Loader;

final class ScenarioListLoader
{
    /**
     * @throws \Ibexa\Contracts\Core\Exception\InvalidArgumentException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     * @throws \JsonException
     */
    public static function getScenarioList(): ScenarioList
    {
        $body = Loader::load(Loader::SCENARIO_LIST_FIXTURE);

        return self::processFetchedScenario($body);
    }

    /**
     * @throws \Ibexa\Contracts\Core\Exception\InvalidArgumentException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     * @throws \JsonException
     */
    public static function getCommerceScenarioList(): ScenarioList
    {
        $body = Loader::load(Loader::COMMERCE_SCENARIO_LIST_FIXTURE);

        return self::processFetchedScenario($body);
    }

    /**
     * @throws \Ibexa\Contracts\Core\Exception\InvalidArgumentException
     * @throws \Ibexa\Contracts\Core\Repository\Exceptions\InvalidArgumentException
     * @throws \JsonException
     */
    private static function processFetchedScenario(string $body): ScenarioList
    {
        $responseItems = json_decode($body, true, 512, JSON_THROW_ON_ERROR);

        $types = self::getItemTypeConfiguration();
        $scenarioList = [];

        foreach ($responseItems['scenarioInfoList'] as $scenario) {
            $scenario['inputItemType'] = $types->findItemType($scenario['inputItemType'])
                ?? self::getUndefinedItem($scenario['inputItemType']);

            $outputItems = [];
            foreach ($scenario['outputItemTypes'] as $outputItemTypeKey => $outputItemTypeId) {
                $outputItems[] = $types->findItemType($outputItemTypeId)
                    ?? self::getUndefinedItem($outputItemTypeId);
            }

            if (count($outputItems) > 1) {
                array_unshift($outputItems, self::getCrossContentTypeItem());
            }

            $scenario['outputItemTypes'] = new ItemTypeList($outputItems);

            $scenarioList[] = Scenario::fromArray($scenario);
        }

        return new ScenarioList($scenarioList);
    }

    /**
     * @throws \JsonException
     * @throws \Ibexa\Contracts\Core\Exception\InvalidArgumentException
     */
    private static function getItemTypeConfiguration(): ItemTypeList
    {
        $itemTypes = json_decode(
            Loader::load(Loader::CUSTOMER_ITEM_TYPE_CONFIGURATION_FIXTURE),
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        $itemTypeList = [];

        foreach ($itemTypes['itemTypeConfiguration']['types'] as $itemType) {
            $itemTypeList[] = ItemType::fromArray($itemType);
        }

        return new ItemTypeList($itemTypeList);
    }

    private static function getCrossContentTypeItem(): CrossContentType
    {
        return new CrossContentType('All');
    }

    private static function getUndefinedItem(int $typeId): ItemType
    {
        return ItemType::fromArray([
            'id' => $typeId,
            'description' => 'Undefined',
            'contentTypes' => [],
        ]);
    }
}

class_alias(ScenarioListLoader::class, 'Ibexa\Platform\Tests\Personalization\Scenario\ScenarioListLoader');
