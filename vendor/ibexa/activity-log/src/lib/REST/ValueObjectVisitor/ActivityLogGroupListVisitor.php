<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ActivityLog\REST\ValueObjectVisitor;

use Ibexa\ActivityLog\REST\Value\ActivityLogGroupList;
use Ibexa\Contracts\Rest\Output\Generator;
use Ibexa\Contracts\Rest\Output\ValueObjectVisitor;
use Ibexa\Contracts\Rest\Output\Visitor;
use Webmozart\Assert\Assert;

/**
 * @internal
 */
final class ActivityLogGroupListVisitor extends ValueObjectVisitor
{
    private const LIST_OBJECT_IDENTIFIER = 'ActivityLogGroupList';

    private const OBJECT_IDENTIFIER = 'ActivityLogGroups';

    public function visit(Visitor $visitor, Generator $generator, $data): void
    {
        Assert::isInstanceOf($data, ActivityLogGroupList::class);

        $visitor->setHeader('Content-Type', $generator->getMediaType(ActivityLogGroupList::MEDIA_TYPE));

        $generator->startObjectElement(self::LIST_OBJECT_IDENTIFIER);
        $generator->attribute('href', $this->router->generate('ibexa.activity_log.rest.activity_log_group.list'));
        $this->visitLogList($visitor, $generator, $data);
        $generator->endObjectElement(self::LIST_OBJECT_IDENTIFIER);
    }

    private function visitLogList(Visitor $visitor, Generator $generator, ActivityLogGroupList $data): void
    {
        $generator->startList(self::OBJECT_IDENTIFIER);
        foreach ($data as $activityLog) {
            $visitor->visitValueObject($activityLog);
        }
        $generator->endList(self::OBJECT_IDENTIFIER);
    }
}
