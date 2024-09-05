<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Form\Data\Scenario;

final class ScenarioCategoryPathData
{
    /** @var bool|null */
    private $wholeSite;

    /** @var \Ibexa\Personalization\Form\Data\Scenario\ScenarioRecommendSameCategoryPathData|null */
    private $sameCategory;

    /** @var \Ibexa\Personalization\Form\Data\Scenario\ScenarioRecommendMainCategoryAndSubcategoriesData|null */
    private $mainCategoryAndSubcategories;

    public function getWholeSite(): ?bool
    {
        return $this->wholeSite;
    }

    public function setWholeSite(?bool $wholeSite): self
    {
        $this->wholeSite = $wholeSite;

        return $this;
    }

    public function getSameCategory(): ?ScenarioRecommendSameCategoryPathData
    {
        return $this->sameCategory;
    }

    public function setSameCategory(?ScenarioRecommendSameCategoryPathData $sameCategory = null): self
    {
        $this->sameCategory = $sameCategory;

        return $this;
    }

    public function getMainCategoryAndSubcategories(): ?ScenarioRecommendMainCategoryAndSubcategoriesData
    {
        return $this->mainCategoryAndSubcategories;
    }

    public function setMainCategoryAndSubcategories(
        ?ScenarioRecommendMainCategoryAndSubcategoriesData $mainCategoryAndSubcategories = null
    ): self {
        $this->mainCategoryAndSubcategories = $mainCategoryAndSubcategories;

        return $this;
    }
}

class_alias(ScenarioCategoryPathData::class, 'Ibexa\Platform\Personalization\Form\Data\Scenario\ScenarioCategoryPathData');
