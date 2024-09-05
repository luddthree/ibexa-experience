<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Workflow\Value\Persistence;

use Ibexa\Contracts\Core\Persistence\ValueObject;

class MarkingMetadataSetStruct extends ValueObject
{
    /** @var string[] */
    public $places;

    /** @var int */
    public $contentId;

    /** @var int */
    public $versionNo;

    /** @var \Ibexa\Workflow\Value\WorkflowActionResult */
    public $result;

    /** @var int */
    public $reviewerId;

    /** @var string */
    public $message;
}

class_alias(MarkingMetadataSetStruct::class, 'EzSystems\EzPlatformWorkflow\Value\Persistence\MarkingMetadataSetStruct');
