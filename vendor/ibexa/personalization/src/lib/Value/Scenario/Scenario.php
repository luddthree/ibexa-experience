<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Value\Scenario;

use Ibexa\Personalization\Form\Data\Scenario\ScenarioData;
use Ibexa\Personalization\Form\Data\Scenario\ScenarioStrategyCollectionData;
use Ibexa\Personalization\Value\Content\ItemType;
use Ibexa\Personalization\Value\Content\ItemTypeList;
use JsonSerializable;

final class Scenario implements JsonSerializable
{
    public const DEFAULT_TYPE = 'standard';
    private const AVAILABLE = 'AVAILABLE';
    private const NOT_AVAILABLE = 'NOT_AVAILABLE';
    private const ENABLED = 'ENABLED';
    private const STAGE_PRIMARY = 'primary_models';
    private const STAGE_FALL_BACK = 'fall_back_models';
    private const STAGE_FAIL_SAFE = 'fail_safe_models';
    private const STAGE_FAIL_ULTIMATELY = 'ultimately_safe_models';

    private string $referenceCode;

    private string $type;

    private string $title;

    private ?string $description;

    /** @var array<string>|null */
    private ?array $models;

    /** @var bool */
    private bool $isAvailable;

    /** @var bool */
    private bool $isEnabled;

    private ItemType $inputItemType;

    private ItemTypeList $outputItemTypes;

    private ?string $websiteContext;

    private ?string $profileContext;

    /** @var array<\Ibexa\Personalization\Value\Scenario\Event>|null */
    private ?array $events;

    private ?int $calls;

    private ?Stages $stages;

    /**
     * @param array<string>|null $models
     * @param array<\Ibexa\Personalization\Value\Scenario\Event>|null $events
     */
    private function __construct(
        string $referenceCode,
        string $title,
        bool $isAvailable,
        bool $isEnabled,
        ItemType $inputItemType,
        ItemTypeList $outputItemTypes,
        string $type = self::DEFAULT_TYPE,
        ?int $calls = null,
        ?Stages $stages = null,
        ?string $websiteContext = null,
        ?string $profileContext = null,
        ?array $models = null,
        ?string $description = null,
        ?array $events = null
    ) {
        $this->referenceCode = $referenceCode;
        $this->type = $type;
        $this->title = $title;
        $this->isAvailable = $isAvailable;
        $this->isEnabled = $isEnabled;
        $this->inputItemType = $inputItemType;
        $this->outputItemTypes = $outputItemTypes;
        $this->calls = $calls;
        $this->stages = $stages;
        $this->models = $models;
        $this->websiteContext = $websiteContext;
        $this->profileContext = $profileContext;
        $this->description = $description;
        $this->events = $events;
    }

    public function getReferenceCode(): string
    {
        return $this->referenceCode;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function isAvailable(): bool
    {
        return $this->isAvailable;
    }

    public function isEnabled(): bool
    {
        return $this->isEnabled;
    }

    public function getInputItemType(): ItemType
    {
        return $this->inputItemType;
    }

    public function getOutputItemTypes(): ItemTypeList
    {
        return $this->outputItemTypes;
    }

    public function getCalls(): ?int
    {
        return $this->calls;
    }

    /**
     * @return array<string>|null
     */
    public function getModels(): ?array
    {
        return $this->models;
    }

    public function getWebsiteContext(): ?string
    {
        return $this->websiteContext;
    }

    public function getProfileContext(): ?string
    {
        return $this->profileContext;
    }

    /**
     * @return array<\Ibexa\Personalization\Value\Scenario\Event>|null
     */
    public function getEvents(): ?array
    {
        return $this->events;
    }

    /**
     * @return \Ibexa\Personalization\Value\Scenario\Stages|null
     */
    public function getStages(): ?Stages
    {
        return $this->stages;
    }

    /**
     * @phpstan-param array{
     *  referenceCode: string,
     *  type: string,
     *  title: string,
     *  available: string,
     *  enabled: string,
     *  inputItemType: \Ibexa\Personalization\Value\Content\ItemType,
     *  outputItemTypes: \Ibexa\Personalization\Value\Content\ItemTypeList,
     *  statisticItems?: ?array,
     *  stages?: ?array,
     *  websiteContext?: string,
     *  profileContext?: string,
     *  models?: ?array,
     *  description?: ?string
     * } $properties
     *
     * @return self
     */
    public static function fromArray(array $properties): self
    {
        $calls = null;
        $events = null;

        if (isset($properties['statisticItems'])) {
            $calls = self::countCalls($properties['statisticItems']);
            $events = self::extractEvents($properties['statisticItems']);
        }

        return new self(
            $properties['referenceCode'],
            $properties['title'],
            $properties['available'] === self::AVAILABLE,
            $properties['enabled'] === self::ENABLED,
            $properties['inputItemType'],
            $properties['outputItemTypes'],
            $properties['type'] ?? self::DEFAULT_TYPE,
            $calls,
            !empty($properties['stages']) ? self::encodeStages($properties['stages']) : null,
            $properties['websiteContext'] ?? null,
            $properties['profileContext'] ?? null,
            $properties['models'] ?? null,
            $properties['description'] ?? null,
            $events
        );
    }

    public static function fromScenarioData(ScenarioData $scenarioData): self
    {
        /** @var \Ibexa\Personalization\Form\Data\Scenario\ScenarioStrategyCollectionData $strategies */
        $strategies = $scenarioData->getStrategy();
        $stages = self::getStagesFromScenarioStrategy($strategies);

        return new self(
            $scenarioData->getId(),
            $scenarioData->getName(),
            self::hasStages($stages),
            true,
            $scenarioData->getInputType(),
            $scenarioData->getOutputType(),
            $scenarioData->getType() ?? self::DEFAULT_TYPE,
            null,
            $stages,
            null,
            null,
            null,
            $scenarioData->getDescription(),
        );
    }

    /**
     * @phpstan-return array{
     *  'referenceCode': string,
     *  'type': string,
     *  'title': string,
     *  'available': string,
     *  'enabled': string,
     *  'inputItemType': int,
     *  'outputItemTypes': ItemTypeList,
     *  'stages': ?Stages,
     *  'description': ?string,
     * }
     */
    public function jsonSerialize(): array
    {
        return [
            'referenceCode' => $this->getReferenceCode(),
            'type' => $this->getType(),
            'title' => $this->getTitle(),
            'available' => $this->isAvailable() ? self::AVAILABLE : self::NOT_AVAILABLE,
            'enabled' => self::ENABLED,
            'inputItemType' => $this->getInputItemType()->getId(),
            'outputItemTypes' => $this->getOutputItemTypes(),
            'stages' => $this->getStages(),
            'description' => $this->getDescription(),
        ];
    }

    /**
     * @phpstan-param array<array-key, array{
     *  'timespanBegin': string,
     *  'timespanDuration': string,
     *  'scenarioCalls': int,
     *  'deliveredRecommendations': int,
     *  'conversionRatePercent': ?float,
     * }> $statisticItems
     */
    private static function countCalls(array $statisticItems): int
    {
        return (int)array_sum(
            array_column($statisticItems, 'scenarioCalls')
        );
    }

    /**
     * @phpstan-param array<array-key, array{
     *  'timespanBegin': string,
     *  'timespanDuration': string,
     *  'scenarioCalls': int,
     *  'deliveredRecommendations': int,
     *  'conversionRatePercent': ?float,
     * }> $statisticItems
     *
     * @return array<\Ibexa\Personalization\Value\Scenario\Event>
     */
    private static function extractEvents(array $statisticItems): array
    {
        $events = [];
        foreach ($statisticItems as $item) {
            $events[] = Event::fromArray($item);
        }

        return $events;
    }

    private static function getStagesFromScenarioStrategy(ScenarioStrategyCollectionData $strategies): Stages
    {
        /** @var \Ibexa\Personalization\Form\Data\Scenario\ScenarioStrategyData $primaryModels */
        $primaryModels = $strategies->getPrimaryModels();
        /** @var \Ibexa\Personalization\Form\Data\Scenario\ScenarioStrategyData $fallback */
        $fallback = $strategies->getFallback();
        /** @var \Ibexa\Personalization\Form\Data\Scenario\ScenarioStrategyData $failSafe */
        $failSafe = $strategies->getFailSafe();
        /** @var \Ibexa\Personalization\Form\Data\Scenario\ScenarioStrategyData $ultimatelyFailSafe */
        $ultimatelyFailSafe = $strategies->getUltimatelyFailSafe();

        return new Stages(
            Stage::fromScenarioStrategyData($primaryModels),
            Stage::fromScenarioStrategyData($fallback),
            Stage::fromScenarioStrategyData($failSafe),
            Stage::fromScenarioStrategyData($ultimatelyFailSafe),
        );
    }

    /**
     * @phpstan-param array<int, array{
     *  'xingModels': array,
     *  'useCategoryPath': ?int
     * }> $stages
     */
    private static function encodeStages(array $stages): Stages
    {
        $encodedStages = [];

        if (array_key_exists(0, $stages)) {
            $encodedStages[self::STAGE_PRIMARY] = Stage::fromArray($stages[0]);
        }

        if (array_key_exists(1, $stages)) {
            $encodedStages[self::STAGE_FALL_BACK] = Stage::fromArray($stages[1]);
        }

        if (array_key_exists(2, $stages)) {
            $encodedStages[self::STAGE_FAIL_SAFE] = Stage::fromArray($stages[2]);
        }

        if (array_key_exists(3, $stages)) {
            $encodedStages[self::STAGE_FAIL_ULTIMATELY] = Stage::fromArray($stages[3]);
        }

        return new Stages(
            $encodedStages[self::STAGE_PRIMARY] ?? null,
            $encodedStages[self::STAGE_FALL_BACK] ?? null,
            $encodedStages[self::STAGE_FAIL_SAFE] ?? null,
            $encodedStages[self::STAGE_FAIL_ULTIMATELY] ?? null,
        );
    }

    private static function hasStages(Stages $stages): bool
    {
        foreach ($stages as $stage) {
            if (null !== $stage->getXingModels()) {
                return true;
            }
        }

        return false;
    }
}

class_alias(Scenario::class, 'Ibexa\Platform\Personalization\Value\Scenario\Scenario');
