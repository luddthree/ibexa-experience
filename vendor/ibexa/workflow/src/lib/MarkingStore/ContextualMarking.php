<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Workflow\MarkingStore;

use Ibexa\Workflow\Value\WorkflowMarkingContext;

interface ContextualMarking
{
    public function getContext(): WorkflowMarkingContext;
}

class_alias(ContextualMarking::class, 'EzSystems\EzPlatformWorkflow\MarkingStore\ContextualMarking');
