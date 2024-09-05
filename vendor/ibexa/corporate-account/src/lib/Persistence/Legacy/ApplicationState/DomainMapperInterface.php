<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\CorporateAccount\Persistence\Legacy\ApplicationState;

use Ibexa\Contracts\CorporateAccount\Values\ApplicationState;
use Ibexa\CorporateAccount\Persistence\Values\ApplicationState as PersistenceApplicationState;

interface DomainMapperInterface
{
    public function createFromPersistence(
        PersistenceApplicationState $persistenceApplicationState
    ): ApplicationState;
}
