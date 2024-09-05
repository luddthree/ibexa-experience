<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Workflow\Value\Persistence;

use Ibexa\Contracts\Core\Persistence\ValueObject;

class WorkflowMetadata extends ValueObject
{
    /** @var int */
    public $id;

    /** @var string */
    public $name;

    /** @var int */
    public $contentId;

    /** @var int */
    public $versionNo;

    /** @var int */
    public $initialOwnerId;

    /** @var int */
    public $startDate;
}

class_alias(WorkflowMetadata::class, 'EzSystems\EzPlatformWorkflow\Value\Persistence\WorkflowMetadata');
