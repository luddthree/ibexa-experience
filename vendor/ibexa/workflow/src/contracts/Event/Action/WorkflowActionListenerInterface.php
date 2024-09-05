<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Workflow\Event\Action;

use Symfony\Component\Workflow\Event\Event;

interface WorkflowActionListenerInterface
{
    public function getIdentifier(): string;

    public function setResult(Event $event, $value): void;
}

class_alias(WorkflowActionListenerInterface::class, 'EzSystems\EzPlatformWorkflow\Event\Action\WorkflowActionListenerInterface');
