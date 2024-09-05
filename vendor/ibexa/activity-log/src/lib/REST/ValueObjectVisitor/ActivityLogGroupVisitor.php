<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ActivityLog\REST\ValueObjectVisitor;

use Ibexa\ActivityLog\REST\Value\ActivityLogGroup;
use Ibexa\Contracts\Rest\Output\Generator;
use Ibexa\Contracts\Rest\Output\ValueObjectVisitor;
use Ibexa\Contracts\Rest\Output\Visitor;
use Webmozart\Assert\Assert;

final class ActivityLogGroupVisitor extends ValueObjectVisitor
{
    private const OBJECT_IDENTIFIER = 'ActivityLogGroup';

    private const LIST_IDENTIFIER = 'ActivityLogEntries';

    public function visit(Visitor $visitor, Generator $generator, $data): void
    {
        Assert::isInstanceOf($data, ActivityLogGroup::class);

        $generator->startObjectElement(self::OBJECT_IDENTIFIER);

        $generator->valueElement('user_id', $data->group->getUserId());
        $generator->valueElement('logged_at', $data->group->getLoggedAt()->getTimestamp());

        $this->visitLogList($visitor, $generator, $data->getLogEntries());

        $generator->endObjectElement(self::OBJECT_IDENTIFIER);
    }

    /**
     * @param iterable<\Ibexa\ActivityLog\REST\Value\ActivityLog> $data
     */
    private function visitLogList(Visitor $visitor, Generator $generator, iterable $data): void
    {
        $generator->startList(self::LIST_IDENTIFIER);
        foreach ($data as $activityLog) {
            $visitor->visitValueObject($activityLog);
        }
        $generator->endList(self::LIST_IDENTIFIER);
    }
}
