<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\Persistence\Values;

use Ibexa\Contracts\Core\Persistence\ValueObject;

final class CompanyHistoryUpdateStruct extends ValueObject
{
    public int $id;

    public ?int $applicationId = null;

    public ?int $companyId = null;

    public ?string $eventName = null;

    /** @var array<string, mixed> */
    public ?array $eventData = null;

    public ?int $userId = null;

    public function __construct(
        int $id
    ) {
        parent::__construct();

        $this->id = $id;
    }
}
