<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Form\Data\Scenario;

final class ScenarioRemoveData
{
    private ?int $customerId;

    private ?string $referenceCode;

    public function __construct(
        ?int $customerId = null,
        ?string $referenceCode = null
    ) {
        $this->customerId = $customerId;
        $this->referenceCode = $referenceCode;
    }

    public function getCustomerId(): ?int
    {
        return $this->customerId;
    }

    public function setCustomerId(int $customerId): self
    {
        $this->customerId = $customerId;

        return $this;
    }

    public function getReferenceCode(): ?string
    {
        return $this->referenceCode;
    }

    public function setReferenceCode(?string $referenceCode = null): self
    {
        $this->referenceCode = $referenceCode;

        return $this;
    }
}

class_alias(ScenarioRemoveData::class, 'Ibexa\Platform\Personalization\Form\Data\Scenario\ScenarioRemoveData');
