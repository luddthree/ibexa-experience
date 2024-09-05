<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Form\Data\Scenario;

final class ScenarioStrategyModelData
{
    private ?string $referenceCode;

    private ?string $dataType;

    private ?string $context;

    public function __construct(
        ?string $referenceCode = null,
        ?string $dataType = null,
        ?string $context = null
    ) {
        $this->referenceCode = $referenceCode;
        $this->dataType = $dataType;
        $this->context = $context;
    }

    public function getReferenceCode(): ?string
    {
        return $this->referenceCode;
    }

    public function setReferenceCode(?string $referenceCode): self
    {
        $this->referenceCode = $referenceCode;

        return $this;
    }

    public function setDataType(?string $dataType): self
    {
        $this->dataType = $dataType;

        return $this;
    }

    public function getDataType(): ?string
    {
        return $this->dataType;
    }

    public function getContext(): ?string
    {
        return $this->context;
    }

    public function setContext(?string $context): self
    {
        $this->context = $context;

        return $this;
    }
}

class_alias(ScenarioStrategyModelData::class, 'Ibexa\Platform\Personalization\Form\Data\Scenario\ScenarioStrategyModelData');
