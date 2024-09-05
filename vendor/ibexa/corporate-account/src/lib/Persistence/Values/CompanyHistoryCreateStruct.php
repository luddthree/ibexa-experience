<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\Persistence\Values;

use Ibexa\Contracts\Core\Persistence\ValueObject;

final class CompanyHistoryCreateStruct extends ValueObject
{
    public int $applicationId;

    public ?int $companyId;

    public string $eventName;

    /** @var array<string, mixed>|null */
    public ?array $eventData;

    public int $userId;

    /** @param array<string, mixed>|null $eventData */
    public function __construct(
        int $applicationId,
        ?int $companyId,
        int $userId,
        string $eventName,
        ?array $eventData = null
    ) {
        parent::__construct();

        $this->applicationId = $applicationId;
        $this->companyId = $companyId;
        $this->eventName = $eventName;
        $this->eventData = $eventData;
        $this->userId = $userId;
    }
}
