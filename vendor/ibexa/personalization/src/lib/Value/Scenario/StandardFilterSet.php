<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Value\Scenario;

use Ibexa\Personalization\Form\Data\Scenario\ScenarioCommerceSettingsData;
use Ibexa\Personalization\Form\Data\Scenario\ScenarioExclusionsData;
use Ibexa\Personalization\Form\Data\Scenario\ScenarioUserProfileSettingsData;
use JsonSerializable;

final class StandardFilterSet implements JsonSerializable
{
    private const YES = 'YES';
    private const NO = 'NO';

    private bool $excludeContextItems;

    private bool $excludeContextItemsCategories;

    /** @var array<string> */
    private array $excludedCategories;

    private bool $excludeCheaperItems;

    private bool $excludeItemsWithoutPrice;

    private bool $excludeTopSellingResults;

    private ?int $minimalItemPrice;

    private ?bool $excludeVariants;

    /**
     * @param array<string> $excludedCategories
     */
    public function __construct(
        bool $excludeContextItems = false,
        bool $excludeContextItemsCategories = false,
        array $excludedCategories = [],
        bool $excludeCheaperItems = false,
        bool $excludeItemsWithoutPrice = false,
        bool $excludeTopSellingResults = false,
        ?int $minimalItemPrice = null,
        ?bool $excludeVariants = null
    ) {
        $this->excludeContextItems = $excludeContextItems;
        $this->excludeContextItemsCategories = $excludeContextItemsCategories;
        $this->excludedCategories = $excludedCategories;
        $this->excludeCheaperItems = $excludeCheaperItems;
        $this->excludeItemsWithoutPrice = $excludeItemsWithoutPrice;
        $this->excludeTopSellingResults = $excludeTopSellingResults;
        $this->minimalItemPrice = $minimalItemPrice;
        $this->excludeVariants = $excludeVariants;
    }

    public function isExcludeContextItems(): bool
    {
        return $this->excludeContextItems;
    }

    public function isExcludeContextItemsCategories(): bool
    {
        return $this->excludeContextItemsCategories;
    }

    /**
     * @return array<string>
     */
    public function getExcludedCategories(): array
    {
        return $this->excludedCategories;
    }

    public function getExcludeCheaperItems(): bool
    {
        return $this->excludeCheaperItems;
    }

    public function isExcludeItemsWithoutPrice(): bool
    {
        return $this->excludeItemsWithoutPrice;
    }

    public function isExcludeTopSellingResults(): bool
    {
        return $this->excludeTopSellingResults;
    }

    public function getMinimalItemPrice(): ?int
    {
        return $this->minimalItemPrice;
    }

    public function doesExcludeVariants(): ?bool
    {
        return $this->excludeVariants;
    }

    /**
     * @phpstan-param array{
     *  'excludeContextItems': bool,
     *  'excludeContextItemsCategories': bool,
     *  'excludedCategories': array<string>,
     *  'excludeCheaperItems': string,
     *  'excludeItemsWithoutPrice': bool,
     *  'excludeTopSellingResults': bool,
     *  'minimalItemPrice': ?int,
     *  'excludeVariants': ?bool,
     * } $properties
     */
    public static function fromArray(array $properties): self
    {
        return new self(
            $properties['excludeContextItems'] ?? false,
            $properties['excludeContextItemsCategories'] ?? false,
            $properties['excludedCategories'] ?? [],
            $properties['excludeCheaperItems'] === self::YES,
            $properties['excludeItemsWithoutPrice'] ?? false,
            $properties['excludeTopSellingResults'] ?? false,
            $properties['minimalItemPrice'] ?? null,
            $properties['excludeVariants'] ?? null
        );
    }

    public static function fromScenarioSettingsData(
        ?ScenarioUserProfileSettingsData $profileSettingsData = null,
        ?ScenarioCommerceSettingsData $commerceSettingsData = null,
        ?ScenarioExclusionsData $exclusionsData = null
    ): self {
        $excludeContextItems = false;
        $excludeContextItemsCategories = false;
        $excludedCategories = [];
        $excludeCheaperItems = false;
        $excludeItemsWithoutPrice = false;
        $excludeTopSellingResults = false;
        $minimalItemPrice = null;
        $excludeVariants = null;

        if (null !== $profileSettingsData) {
            $excludeContextItems = $profileSettingsData->isExcludeContextItems();
        }

        if (null !== $commerceSettingsData) {
            $excludeCheaperItems = $commerceSettingsData->isExcludeCheaperItems();
            $excludeItemsWithoutPrice = $commerceSettingsData->isExcludeItemsWithoutPrice();
            $excludeTopSellingResults = $commerceSettingsData->isExcludeTopSellingResults();
            $minimalItemPrice = null !== $commerceSettingsData->getExcludeMinimalItemPrice()
                ? $commerceSettingsData->getExcludeMinimalItemPrice()->getValue()
                : null;
            $excludeVariants = $commerceSettingsData->isExcludeProductVariants();
        }

        if (null !== $exclusionsData) {
            $excludeContextItemsCategories = $exclusionsData->isExcludeContextItemsCategories();
            $excludedCategories = self::getCategoryPaths($exclusionsData);
        }

        return new StandardFilterSet(
            $excludeContextItems,
            $excludeContextItemsCategories,
            $excludedCategories,
            $excludeCheaperItems,
            $excludeItemsWithoutPrice,
            $excludeTopSellingResults,
            $minimalItemPrice,
            $excludeVariants
        );
    }

    /**
     * @phpstan-return array{
     *  'excludeContextItems': bool,
     *  'excludeContextItemsCategories': bool,
     *  'excludedCategories': array<string>,
     *  'excludeCheaperItems': string,
     *  'excludeItemsWithoutPrice': bool,
     *  'excludeTopSellingResults': bool,
     *  'minimalItemPrice': ?int,
     *  'excludeVariants': ?bool,
     * } $properties
     */
    public function jsonSerialize(): array
    {
        return [
              'excludeContextItems' => $this->isExcludeContextItems(),
              'excludeContextItemsCategories' => $this->isExcludeContextItemsCategories(),
              'excludedCategories' => $this->getExcludedCategories(),
              'excludeCheaperItems' => $this->getExcludeCheaperItems() ? self::YES : self::NO,
              'excludeItemsWithoutPrice' => $this->isExcludeItemsWithoutPrice(),
              'excludeTopSellingResults' => $this->isExcludeTopSellingResults(),
              'minimalItemPrice' => $this->getMinimalItemPrice(),
              'excludeVariants' => $this->doesExcludeVariants(),
        ];
    }

    /**
     * @return array<string>
     */
    private static function getCategoryPaths(ScenarioExclusionsData $exclusionsData): array
    {
        $categories = $exclusionsData->getExcludeCategories();

        if (null === $categories) {
            return [];
        }

        return $categories->getPaths();
    }
}

class_alias(StandardFilterSet::class, 'Ibexa\Platform\Personalization\Value\Scenario\StandardFilterSet');
