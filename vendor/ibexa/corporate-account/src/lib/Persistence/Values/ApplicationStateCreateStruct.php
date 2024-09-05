<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\Persistence\Values;

use Ibexa\Contracts\Core\Persistence\ValueObject;

final class ApplicationStateCreateStruct extends ValueObject
{
    public int $applicationId;

    public string $state;

    public ?int $companyId;

    public function __construct(
        int $applicationId,
        string $state,
        ?int $companyId = null
    ) {
        parent::__construct();

        $this->applicationId = $applicationId;
        $this->state = $state;
        $this->companyId = $companyId;
    }
}
