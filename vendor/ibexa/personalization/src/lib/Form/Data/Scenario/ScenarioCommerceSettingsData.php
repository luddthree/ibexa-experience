<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Form\Data\Scenario;

use Ibexa\Personalization\Form\Data\OptionalIntegerData;

final class ScenarioCommerceSettingsData
{
    /** @var bool */
    private $excludeTopSellingResults;

    /** @var bool */
    private $excludeCheaperItems;

    /** @var \Ibexa\Personalization\Form\Data\OptionalIntegerData */
    private $excludeMinimalItemPrice;

    /** @var bool */
    private $excludeItemsWithoutPrice;

    /** @var bool */
    private $excludeAlreadyPurchased;

    private ?bool $excludeProductVariants;

    public function isExcludeTopSellingResults(): bool
    {
        return $this->excludeTopSellingResults;
    }

    public function setExcludeTopSellingResults(bool $excludeTopSellingResults): ScenarioCommerceSettingsData
    {
        $this->excludeTopSellingResults = $excludeTopSellingResults;

        return $this;
    }

    public function isExcludeCheaperItems(): bool
    {
        return $this->excludeCheaperItems;
    }

    public function setExcludeCheaperItems(bool $excludeCheaperItems): ScenarioCommerceSettingsData
    {
        $this->excludeCheaperItems = $excludeCheaperItems;

        return $this;
    }

    public function getExcludeMinimalItemPrice(): ?OptionalIntegerData
    {
        return $this->excludeMinimalItemPrice;
    }

    public function setExcludeMinimalItemPrice(
        ?OptionalIntegerData $excludeMinimalItemPrice = null
    ): ScenarioCommerceSettingsData {
        $this->excludeMinimalItemPrice = $excludeMinimalItemPrice;

        return $this;
    }

    public function isExcludeItemsWithoutPrice(): bool
    {
        return $this->excludeItemsWithoutPrice;
    }

    public function setExcludeItemsWithoutPrice(bool $excludeItemsWithoutPrice): ?ScenarioCommerceSettingsData
    {
        $this->excludeItemsWithoutPrice = $excludeItemsWithoutPrice;

        return $this;
    }

    public function isExcludeAlreadyPurchased(): bool
    {
        return $this->excludeAlreadyPurchased;
    }

    public function setExcludeAlreadyPurchased(bool $excludeAlreadyPurchased): ScenarioCommerceSettingsData
    {
        $this->excludeAlreadyPurchased = $excludeAlreadyPurchased;

        return $this;
    }

    public function isExcludeProductVariants(): ?bool
    {
        return $this->excludeProductVariants;
    }

    public function setExcludeProductVariants(?bool $excludeProductVariants = null): ScenarioCommerceSettingsData
    {
        $this->excludeProductVariants = $excludeProductVariants;

        return $this;
    }
}

class_alias(ScenarioCommerceSettingsData::class, 'Ibexa\Platform\Personalization\Form\Data\Scenario\ScenarioCommerceSettingsData');
