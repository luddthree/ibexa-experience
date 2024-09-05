<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\CorporateAccount\Values;

use DateTimeImmutable;

interface CompanyHistory
{
    public function getId(): int;

    public function getApplicationId(): int;

    public function getCompanyId(): ?int;

    public function getUserId(): ?int;

    public function getEventName(): string;

    /** @return array<string, mixed>|null */
    public function getEventData(): ?array;

    public function getCreated(): DateTimeImmutable;
}
