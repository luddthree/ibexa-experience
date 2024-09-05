<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Form\Data\Scenario;

final class ScenarioStrategyData
{
    /** @var \Ibexa\Personalization\Form\Data\Scenario\ScenarioStrategyModelsData */
    private $models;

    /** @var ScenarioCategoryPathData */
    private $categoryPath;

    public function getModels(): ?ScenarioStrategyModelsData
    {
        return $this->models;
    }

    public function setModels(ScenarioStrategyModelsData $models): self
    {
        $this->models = $models;

        return $this;
    }

    public function getCategoryPath(): ?ScenarioCategoryPathData
    {
        return $this->categoryPath;
    }

    public function setCategoryPath(ScenarioCategoryPathData $categoryPath): self
    {
        $this->categoryPath = $categoryPath;

        return $this;
    }
}

class_alias(ScenarioStrategyData::class, 'Ibexa\Platform\Personalization\Form\Data\Scenario\ScenarioStrategyData');
