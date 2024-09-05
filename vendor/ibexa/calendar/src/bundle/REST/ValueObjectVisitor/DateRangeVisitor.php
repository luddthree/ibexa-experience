<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Calendar\REST\ValueObjectVisitor;

use Ibexa\Contracts\Rest\Output\Generator;
use Ibexa\Contracts\Rest\Output\ValueObjectVisitor;
use Ibexa\Contracts\Rest\Output\Visitor;

final class DateRangeVisitor extends ValueObjectVisitor
{
    /**
     * @param \Ibexa\Contracts\Rest\Output\Visitor $visitor
     * @param \Ibexa\Contracts\Rest\Output\Generator $generator
     * @param \Ibexa\Contracts\Calendar\DateRange $data
     */
    public function visit(Visitor $visitor, Generator $generator, $data): void
    {
        $generator->startObjectElement('DateRange');

        $generator->startValueElement('startDate', $data->getStartDate()->format('U'));
        $generator->endValueElement('startDate');

        $generator->startValueElement('endDate', $data->getEndDate()->format('U'));
        $generator->endValueElement('endDate');

        $generator->endObjectElement('DateRange');
    }
}

class_alias(DateRangeVisitor::class, 'EzSystems\EzPlatformCalendarBundle\REST\ValueObjectVisitor\DateRangeVisitor');
