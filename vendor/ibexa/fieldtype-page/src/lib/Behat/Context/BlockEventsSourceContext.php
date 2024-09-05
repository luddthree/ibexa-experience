<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FieldTypePage\Behat\Context;

use Behat\Behat\Context\Context;
use DateTime;
use Ibexa\Contracts\Calendar\DateRange;
use Ibexa\Contracts\Calendar\EventQuery;
use Ibexa\Contracts\Calendar\EventQueryBuilder;

abstract class BlockEventsSourceContext implements Context
{
    protected function buildEventQuery(string $eventTypeIdentifier): EventQuery
    {
        $queryBuilder = new EventQueryBuilder();
        $queryBuilder->withTypes([
            $eventTypeIdentifier,
        ]);
        $queryBuilder->withDateRange(
            new DateRange(
                new DateTime('+3Week'),
                new DateTime('+5Week')
            )
        );

        return $queryBuilder->getQuery();
    }
}
