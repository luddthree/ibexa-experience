<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Form\Data\Scenario;

final class ScenarioExclusionsData
{
    private bool $excludeContextItemsCategories;

    private ?ScenarioExcludedCategoriesData $excludeCategories;

    public function __construct(
        bool $excludeContextItemsCategories,
        ?ScenarioExcludedCategoriesData $excludeCategories = null
    ) {
        $this->excludeContextItemsCategories = $excludeContextItemsCategories;
        $this->excludeCategories = $excludeCategories;
    }

    public function isExcludeContextItemsCategories(): bool
    {
        return $this->excludeContextItemsCategories;
    }

    public function setExcludeContextItemsCategories(bool $excludeContextItemsCategories): self
    {
        $this->excludeContextItemsCategories = $excludeContextItemsCategories;

        return $this;
    }

    public function getExcludeCategories(): ?ScenarioExcludedCategoriesData
    {
        return $this->excludeCategories;
    }

    public function setExcludeCategories(?ScenarioExcludedCategoriesData $excludeCategories): self
    {
        $this->excludeCategories = $excludeCategories;

        return $this;
    }
}
