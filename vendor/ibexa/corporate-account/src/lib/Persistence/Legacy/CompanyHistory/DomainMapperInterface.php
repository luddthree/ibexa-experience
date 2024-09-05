<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\Persistence\Legacy\CompanyHistory;

use Ibexa\Contracts\CorporateAccount\Values\CompanyHistory;
use Ibexa\CorporateAccount\Persistence\Values\CompanyHistory as PersistenceCompanyHistory;

interface DomainMapperInterface
{
    public function createFromPersistence(
        PersistenceCompanyHistory $persistenceCompanyHistory
    ): CompanyHistory;
}
