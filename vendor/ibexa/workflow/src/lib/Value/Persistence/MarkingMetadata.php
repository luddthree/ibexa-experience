<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Workflow\Value\Persistence;

use Ibexa\Contracts\Core\Persistence\ValueObject;

class MarkingMetadata extends ValueObject
{
    /** @var int */
    public $id;

    /** @var int */
    public $workflowId;

    /** @var string */
    public $name;

    /** @var array */
    public $result;

    /** @var int */
    public $reviewerId;

    /** @var string */
    public $message;
}

class_alias(MarkingMetadata::class, 'EzSystems\EzPlatformWorkflow\Value\Persistence\MarkingMetadata');
