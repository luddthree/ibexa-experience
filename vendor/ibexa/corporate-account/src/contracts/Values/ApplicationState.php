<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\CorporateAccount\Values;

interface ApplicationState
{
    public function getId(): int;

    public function getApplicationId(): int;

    public function getState(): string;

    public function getCompanyId(): ?int;
}
