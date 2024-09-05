<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\Persistence\Legacy\ApplicationState;

use Ibexa\CorporateAccount\Persistence\Values\ApplicationState as PersistenceApplicationState;
use Ibexa\CorporateAccount\Values\ApplicationState;

final class DomainMapper implements DomainMapperInterface
{
    public function createFromPersistence(
        PersistenceApplicationState $persistenceApplicationState
    ): ApplicationState {
        return new ApplicationState(
            $persistenceApplicationState->id,
            $persistenceApplicationState->applicationId,
            $persistenceApplicationState->state,
            $persistenceApplicationState->companyId
        );
    }
}
