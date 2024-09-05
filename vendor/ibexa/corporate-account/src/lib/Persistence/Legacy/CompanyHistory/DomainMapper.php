<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\Persistence\Legacy\CompanyHistory;

use Ibexa\CorporateAccount\Persistence\Values\CompanyHistory as PersistenceCompanyHistory;
use Ibexa\CorporateAccount\Values\CompanyHistory;

final class DomainMapper implements DomainMapperInterface
{
    public function createFromPersistence(
        PersistenceCompanyHistory $persistenceCompanyHistory
    ): CompanyHistory {
        return new CompanyHistory(
            $persistenceCompanyHistory->id,
            $persistenceCompanyHistory->applicationId,
            $persistenceCompanyHistory->companyId,
            $persistenceCompanyHistory->userId,
            $persistenceCompanyHistory->eventName,
            $persistenceCompanyHistory->created,
            $persistenceCompanyHistory->eventData
        );
    }
}
