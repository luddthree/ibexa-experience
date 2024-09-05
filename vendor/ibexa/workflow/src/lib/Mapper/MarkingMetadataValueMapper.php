<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Workflow\Mapper;

use Ibexa\Contracts\Core\Persistence\ValueObject as PersistenceValueObject;
use Ibexa\Contracts\Core\Repository\Values\ValueObject;
use Ibexa\Workflow\Value\MarkingMetadata;
use Ibexa\Workflow\Value\WorkflowActionResult;
use Ibexa\Workflow\Value\WorkflowMarkingContext;

class MarkingMetadataValueMapper implements ValueMapper
{
    /**
     * {@inheritdoc}
     *
     * @param \Ibexa\Workflow\Value\Persistence\MarkingMetadata
     *
     * @return \Ibexa\Workflow\Value\MarkingMetadata
     */
    public function fromPersistenceValue(PersistenceValueObject $object): ValueObject
    {
        $apiMarkingContext = new WorkflowMarkingContext();
        $apiMarkingContext->message = $object->message;
        $apiMarkingContext->reviewerId = $object->reviewerId;
        $apiMarkingContext->result = new WorkflowActionResult($object->result ?? []);
        $apiMarkingContext->workflowId = $object->workflowId;

        $apiMarkingMetadata = new MarkingMetadata();
        $apiMarkingMetadata->id = $object->id;
        $apiMarkingMetadata->name = $object->name;
        $apiMarkingMetadata->context = $apiMarkingContext;

        return $apiMarkingMetadata;
    }
}

class_alias(MarkingMetadataValueMapper::class, 'EzSystems\EzPlatformWorkflow\Mapper\MarkingMetadataValueMapper');
