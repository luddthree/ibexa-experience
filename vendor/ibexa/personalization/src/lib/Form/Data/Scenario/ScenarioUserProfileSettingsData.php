<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Form\Data\Scenario;

use Ibexa\Personalization\Form\Data\OptionalIntegerData;
use Ibexa\Personalization\Form\Data\OptionalTextData;

final class ScenarioUserProfileSettingsData
{
    /** @var bool */
    private $excludeContextItems;

    /** @var bool */
    private $excludeAlreadyConsumed;

    /** @var \Ibexa\Personalization\Form\Data\OptionalIntegerData|null */
    private $excludeRepeatedRecommendations;

    /** @var \Ibexa\Personalization\Form\Data\Scenario\ScenarioBoostItemData|null */
    private $boostItem;

    /** @var \Ibexa\Personalization\Form\Data\OptionalTextData|null */
    private $userAttributeName;

    public function isExcludeContextItems(): bool
    {
        return $this->excludeContextItems;
    }

    public function setExcludeContextItems(bool $excludeContextItems): self
    {
        $this->excludeContextItems = $excludeContextItems;

        return $this;
    }

    public function isExcludeAlreadyConsumed(): bool
    {
        return $this->excludeAlreadyConsumed;
    }

    public function setExcludeAlreadyConsumed(bool $excludeAlreadyConsumed): self
    {
        $this->excludeAlreadyConsumed = $excludeAlreadyConsumed;

        return $this;
    }

    public function getExcludeRepeatedRecommendations(): ?OptionalIntegerData
    {
        return $this->excludeRepeatedRecommendations;
    }

    public function setExcludeRepeatedRecommendations(
        ?OptionalIntegerData $excludeRepeatedRecommendations = null
    ): self {
        $this->excludeRepeatedRecommendations = $excludeRepeatedRecommendations;

        return $this;
    }

    public function getBoostItem(): ?ScenarioBoostItemData
    {
        return $this->boostItem;
    }

    public function setBoostItem(
        ?ScenarioBoostItemData $boostItem = null
    ): self {
        $this->boostItem = $boostItem;

        return $this;
    }

    public function getUserAttributeName(): ?OptionalTextData
    {
        return $this->userAttributeName;
    }

    public function setUserAttributeName(
        ?OptionalTextData $userAttributeName = null
    ): self {
        $this->userAttributeName = $userAttributeName;

        return $this;
    }
}

class_alias(ScenarioUserProfileSettingsData::class, 'Ibexa\Platform\Personalization\Form\Data\Scenario\ScenarioUserProfileSettingsData');
