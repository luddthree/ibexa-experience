<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Form\Data\Scenario;

final class ScenarioStrategyCollectionData
{
    /** @var \Ibexa\Personalization\Form\Data\Scenario\ScenarioStrategyData */
    private $primaryModels;

    /** @var \Ibexa\Personalization\Form\Data\Scenario\ScenarioStrategyData */
    private $fallback;

    /** @var \Ibexa\Personalization\Form\Data\Scenario\ScenarioStrategyData */
    private $failSafe;

    /** @var \Ibexa\Personalization\Form\Data\Scenario\ScenarioStrategyData */
    private $ultimatelyFailSafe;

    /**
     * @return \Ibexa\Personalization\Form\Data\Scenario\ScenarioStrategyData
     */
    public function getPrimaryModels(): ?ScenarioStrategyData
    {
        return $this->primaryModels;
    }

    public function setPrimaryModels(?ScenarioStrategyData $primaryModels = null): self
    {
        $this->primaryModels = $primaryModels;

        return $this;
    }

    public function getFallback(): ?ScenarioStrategyData
    {
        return $this->fallback;
    }

    public function setFallback(?ScenarioStrategyData $fallback): self
    {
        $this->fallback = $fallback;

        return $this;
    }

    public function getFailSafe(): ?ScenarioStrategyData
    {
        return $this->failSafe;
    }

    public function setFailSafe(?ScenarioStrategyData $failSafe): self
    {
        $this->failSafe = $failSafe;

        return $this;
    }

    public function getUltimatelyFailSafe(): ?ScenarioStrategyData
    {
        return $this->ultimatelyFailSafe;
    }

    public function setUltimatelyFailSafe(?ScenarioStrategyData $ultimatelyFailSafe): self
    {
        $this->ultimatelyFailSafe = $ultimatelyFailSafe;

        return $this;
    }
}

class_alias(ScenarioStrategyCollectionData::class, 'Ibexa\Platform\Personalization\Form\Data\Scenario\ScenarioStrategyCollectionData');
