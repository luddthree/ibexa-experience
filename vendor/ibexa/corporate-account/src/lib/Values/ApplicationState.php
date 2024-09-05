<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\Values;

use Ibexa\Contracts\CorporateAccount\Values\ApplicationState as ApplicationStateInterface;

final class ApplicationState implements ApplicationStateInterface
{
    public int $id;

    public int $applicationId;

    public string $state;

    public ?int $companyId;

    public function __construct(
        int $id,
        int $applicationId,
        string $state,
        ?int $companyId = null
    ) {
        $this->id = $id;
        $this->applicationId = $applicationId;
        $this->state = $state;
        $this->companyId = $companyId;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getApplicationId(): int
    {
        return $this->applicationId;
    }

    public function getState(): string
    {
        return $this->state;
    }

    public function getCompanyId(): ?int
    {
        return $this->companyId;
    }
}
