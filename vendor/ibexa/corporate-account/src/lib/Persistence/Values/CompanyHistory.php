<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\Persistence\Values;

use DateTimeImmutable;
use Ibexa\Contracts\Core\Persistence\ValueObject;

final class CompanyHistory extends ValueObject
{
    public int $id;

    public int $applicationId;

    public ?int $companyId;

    public ?int $userId;

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
        parent::__construct();

        $this->id = $id;
        $this->applicationId = $applicationId;
        $this->companyId = $companyId;
        $this->userId = $userId;
        $this->eventName = $eventName;
        $this->created = $created;
        $this->eventData = $eventData;
    }
}
