<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Workflow\MarkingStore;

use Ibexa\Contracts\Core\Repository\Values\Content\ContentCreateStruct;
use Ibexa\Contracts\Workflow\Persistence\Handler\HandlerInterface;
use Ibexa\Core\Base\Exceptions\InvalidArgumentException;
use Ibexa\Workflow\Exception\NotFoundException;
use Ibexa\Workflow\Value\Persistence\MarkingMetadataSetStruct;
use Ibexa\Workflow\Value\WorkflowActionResult;
use Ibexa\Workflow\Value\WorkflowMarkingContext;
use Symfony\Component\Workflow\Marking;
use Symfony\Component\Workflow\MarkingStore\MarkingStoreInterface;

class WorkflowMarkingStore implements MarkingStoreInterface
{
    /** @var string */
    private $workflowName;

    /** @var \Ibexa\Contracts\Workflow\Persistence\Handler\HandlerInterface */
    private $workflowHandler;

    /**
     * @param string $workflowName
     * @param \Ibexa\Contracts\Workflow\Persistence\Handler\HandlerInterface $workflowHandler
     */
    public function __construct(string $workflowName, HandlerInterface $workflowHandler)
    {
        $this->workflowName = $workflowName;
        $this->workflowHandler = $workflowHandler;
    }

    /**
     * @param \Ibexa\Contracts\Core\Repository\Values\Content\Content|\Ibexa\Contracts\Core\Repository\Values\Content\ContentCreateStruct $subject
     *
     * @return \Symfony\Component\Workflow\Marking
     */
    public function getMarking($subject): Marking
    {
        if ($subject instanceof ContentCreateStruct) {
            return new Marking();
        }

        try {
            $workflowMetadata = $this->workflowHandler->load(
                (int) $subject->id,
                (int) $subject->getVersionInfo()->versionNo,
                $this->workflowName
            );
            $markingsMetadata = $this->workflowHandler->loadMarkingMetadataByWorkflowId($workflowMetadata->id);
        } catch (NotFoundException $e) {
            return new Marking();
        }

        $markingMetadata = reset($markingsMetadata);

        $marking = new Marking(
            array_combine(
                array_column($markingsMetadata, 'name'),
                array_fill(0, \count($markingsMetadata), 1)
            )
        );

        $marking->setContext([
            'workflowId' => $markingMetadata->workflowId,
            'message' => $markingMetadata->message,
            'reviewerId' => $markingMetadata->reviewerId,
            'result' => new WorkflowActionResult($markingMetadata->result ?? []),
        ]);

        return $marking;
    }

    public function setMarking($subject, Marking $marking, array $context = []): void
    {
        if ($subject instanceof ContentCreateStruct) {
            return;
        }

        try {
            $spiWorkflowMetadata = $this->workflowHandler->load(
                (int) $subject->contentInfo->id,
                (int) $subject->getVersionInfo()->versionNo,
                $this->workflowName
            );
        } catch (NotFoundException $e) {
            return;
        }

        $context['result'] = $context['result'] ?? new WorkflowActionResult();

        $markingContext = $this->mapWorkflowMarkingContext($context);

        $setStruct = new MarkingMetadataSetStruct();
        $setStruct->places = array_keys($marking->getPlaces());
        $setStruct->contentId = (int) $subject->id;
        $setStruct->versionNo = (int) $subject->getVersionInfo()->versionNo;
        $setStruct->message = $markingContext->message;
        $setStruct->reviewerId = $markingContext->reviewerId;
        $setStruct->result = $markingContext->result;

        $this->workflowHandler->setMarkingMetadata($setStruct, $spiWorkflowMetadata->id);
    }

    protected function mapWorkflowMarkingContext(array $context): WorkflowMarkingContext
    {
        $message = $context['message'] ?? '';

        if (!is_string($message)) {
            throw new InvalidArgumentException('context["message"]', 'Must be a string');
        }

        $result = $context['result'] ?? null;

        if (!$result instanceof WorkflowActionResult) {
            throw new InvalidArgumentException('context["result"]', 'Must be an instance of ' . WorkflowActionResult::class);
        }

        $reviewerId = $context['reviewerId'] ?? null;

        if (!is_int($reviewerId) && null !== $reviewerId) {
            throw new InvalidArgumentException('context["reviewerId"]', 'Must be an integer (or null)');
        }

        $markingContext = new WorkflowMarkingContext();
        $markingContext->message = $message;
        $markingContext->reviewerId = $reviewerId;
        $markingContext->result = $result;

        return $markingContext;
    }
}

class_alias(WorkflowMarkingStore::class, 'EzSystems\EzPlatformWorkflow\MarkingStore\WorkflowMarkingStore');
