<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Form\Data\Scenario;

final class ScenarioRecommendMainCategoryAndSubcategoriesData
{
    /** @var bool|null */
    private $checked;

    /** @var int|null */
    private $subcategoryLevel;

    public function isChecked(): ?bool
    {
        return $this->checked;
    }

    public function setChecked(?bool $checked = null): self
    {
        $this->checked = $checked;

        return $this;
    }

    public function getSubcategoryLevel(): ?int
    {
        return $this->subcategoryLevel;
    }

    public function setSubcategoryLevel(?int $subcategoryLevel = null): self
    {
        $this->subcategoryLevel = $subcategoryLevel;

        return $this;
    }
}

class_alias(ScenarioRecommendMainCategoryAndSubcategoriesData::class, 'Ibexa\Platform\Personalization\Form\Data\Scenario\ScenarioRecommendMainCategoryAndSubcategoriesData');
