<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Form\Data\Scenario;

final class ScenarioStrategyModelsData
{
    /** @var \Ibexa\Personalization\Form\Data\Scenario\ScenarioStrategyModelData|null */
    private $firstModelStrategy;

    /** @var \Ibexa\Personalization\Form\Data\Scenario\ScenarioStrategyModelData|null */
    private $secondModelStrategy;

    public function getFirstModelStrategy(): ?ScenarioStrategyModelData
    {
        return $this->firstModelStrategy;
    }

    public function setFirstModelStrategy(?ScenarioStrategyModelData $firstModelStrategy = null): self
    {
        $this->firstModelStrategy = $firstModelStrategy;

        return $this;
    }

    public function getSecondModelStrategy(): ?ScenarioStrategyModelData
    {
        return $this->secondModelStrategy;
    }

    public function setSecondModelStrategy(?ScenarioStrategyModelData $secondModelStrategy = null): self
    {
        $this->secondModelStrategy = $secondModelStrategy;

        return $this;
    }
}

class_alias(ScenarioStrategyModelsData::class, 'Ibexa\Platform\Personalization\Form\Data\Scenario\ScenarioStrategyModelsData');
