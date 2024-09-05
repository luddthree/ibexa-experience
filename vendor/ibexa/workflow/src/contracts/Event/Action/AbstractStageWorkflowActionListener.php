<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Contracts\Workflow\Event\Action;

use Symfony\Component\Workflow\Event\EnteredEvent;
use Symfony\Component\Workflow\WorkflowInterface;

abstract class AbstractStageWorkflowActionListener extends AbstractConditionalWorkflowActionListener
{
    public function getActionMetadata(WorkflowInterface $workflow, array $markingPlaces): ?array
    {
        foreach ($markingPlaces as $place => $mark) {
            $actionMetadata = $workflow->getMetadataStore()->getPlaceMetadata($place)['actions'][$this->getIdentifier()] ?? null;

            if ($actionMetadata !== null) {
                return $actionMetadata;
            }
        }

        return null;
    }

    abstract public function onWorkflowEvent(EnteredEvent $event): void;
}

class_alias(AbstractStageWorkflowActionListener::class, 'EzSystems\EzPlatformWorkflow\Event\Action\AbstractStageWorkflowActionListener');
