<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ActivityLog\REST\ValueObjectVisitor;

use Ibexa\ActivityLog\REST\Value\ActivityLog;
use Ibexa\Contracts\Rest\Output\Generator;
use Ibexa\Contracts\Rest\Output\ValueObjectVisitor;
use Ibexa\Contracts\Rest\Output\Visitor;
use Webmozart\Assert\Assert;

final class ActivityLogVisitor extends ValueObjectVisitor
{
    private const OBJECT_IDENTIFIER = 'ActivityLog';

    public function visit(Visitor $visitor, Generator $generator, $data): void
    {
        Assert::isInstanceOf($data, ActivityLog::class);

        $generator->startObjectElement(self::OBJECT_IDENTIFIER);

        $generator->valueElement('object_id', $data->activityLog->getObjectId());
        $generator->valueElement('object_class', $data->activityLog->getObjectClass());
        $generator->valueElement('action', $data->activityLog->getAction());
        $generator->generateFieldTypeHash('data', $data->getData());

        $generator->endObjectElement(self::OBJECT_IDENTIFIER);
    }
}
