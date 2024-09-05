<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\Values;

use DateTimeImmutable;
use Ibexa\Contracts\CorporateAccount\Values\CompanyHistory as CompanyHistoryInterface;

final class CompanyHistory implements CompanyHistoryInterface
{
    public int $id;

    public int $applicationId;

    public ?int $companyId;

    private ?int $userId;

    public string $eventName;

    /** @var array<string, mixed>|null */
    public ?array $eventData;

    public DateTimeImmutable $created;

    /** @param array<string, mixed>|null $eventData */
    public function __construct(
        int $id,
        int $applicationId,
        ?int $companyId,
        ?int $userId,
        string $eventName,
        DateTimeImmutable $created,
        ?array $eventData = null
    ) {
        $this->id = $id;
        $this->applicationId = $applicationId;
        $this->companyId = $companyId;
        $this->userId = $userId;
        $this->eventName = $eventName;
        $this->eventData = $eventData;
        $this->created = $created;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getApplicationId(): int
    {
        return $this->applicationId;
    }

    public function getCompanyId(): ?int
    {
        return $this->companyId;
    }

    public function getEventName(): string
    {
        return $this->eventName;
    }

    /** @return array<string, mixed>|null */
    public function getEventData(): ?array
    {
        return $this->eventData;
    }

    public function getCreated(): DateTimeImmutable
    {
        return $this->created;
    }

    public function getUserId(): ?int
    {
        return $this->userId;
    }
}
