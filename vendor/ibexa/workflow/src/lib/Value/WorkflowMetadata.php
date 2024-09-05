<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Workflow\Value;

use Ibexa\Contracts\Core\Repository\Values\ValueObject;

class WorkflowMetadata extends ValueObject
{
    /** @var int */
    public $id;

    /** @var string */
    public $name;

    /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Content */
    public $content;

    /** @var \Ibexa\Contracts\Core\Repository\Values\Content\VersionInfo */
    public $versionInfo;

    /** @var \Symfony\Component\Workflow\Workflow */
    public $workflow;

    /** @var \Ibexa\Workflow\Value\TransitionMetadata[] */
    public $transitions;

    /** @var \Ibexa\Workflow\Value\MarkingMetadata[] */
    public $markings;

    /** @var \Ibexa\Contracts\Core\Repository\Values\User\User */
    public $initialOwner;

    /** @var \DateTimeInterface */
    public $startDate;
}

class_alias(WorkflowMetadata::class, 'EzSystems\EzPlatformWorkflow\Value\WorkflowMetadata');
