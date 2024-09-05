<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Taxonomy\Controller\REST;

use Exception;
use Ibexa\Contracts\Taxonomy\Service\TaxonomyEntryAssignmentServiceInterface;
use Ibexa\Contracts\Taxonomy\Value\TaxonomyEntryAssignment;
use Ibexa\Contracts\Taxonomy\Value\TaxonomyEntryAssignmentCollection;
use Ibexa\Rest\Message;
use Ibexa\Rest\Server\Controller as BaseController;
use Ibexa\Rest\Server\Values\NoContent;
use Ibexa\Rest\Value;
use Symfony\Component\HttpFoundation\Request;

final class TaxonomyEntryAssignmentController extends BaseController
{
    private TaxonomyEntryAssignmentServiceInterface $taxonomyEntryAssignmentService;

    public function __construct(TaxonomyEntryAssignmentServiceInterface $taxonomyEntryAssignmentService)
    {
        $this->taxonomyEntryAssignmentService = $taxonomyEntryAssignmentService;
    }

    public function assignToContentAction(Request $request): Value
    {
        /** @var \Ibexa\Taxonomy\REST\Input\Value\TaxonomyEntryAssignToContent $assignToContentInput */
        $assignToContentInput = $this->inputDispatcher->parse(
            new Message(
                ['Content-Type' => $request->headers->get('Content-Type')],
                $request->getContent()
            )
        );

        $this->repository->beginTransaction();

        try {
            $this->taxonomyEntryAssignmentService->assignMultipleToContent(
                $assignToContentInput->getContent(),
                $assignToContentInput->getEntries(),
                $assignToContentInput->getContent()->versionInfo->versionNo,
            );

            $this->repository->commit();
        } catch (Exception $exception) {
            $this->repository->rollback();

            throw $exception;
        }

        return new NoContent();
    }

    public function unassignFromContentAction(Request $request): Value
    {
        /** @var \Ibexa\Taxonomy\REST\Input\Value\TaxonomyEntryUnassignFromContent $unassignFromContentInput */
        $unassignFromContentInput = $this->inputDispatcher->parse(
            new Message(
                ['Content-Type' => $request->headers->get('Content-Type')],
                $request->getContent()
            )
        );

        $this->repository->beginTransaction();

        try {
            $this->taxonomyEntryAssignmentService->unassignMultipleFromContent(
                $unassignFromContentInput->getContent(),
                $unassignFromContentInput->getEntries(),
                $unassignFromContentInput->getContent()->versionInfo->versionNo
            );

            $this->repository->commit();
        } catch (Exception $exception) {
            $this->repository->rollback();

            throw $exception;
        }

        return new NoContent();
    }

    public function loadAssignmentByIdAction(int $id): TaxonomyEntryAssignment
    {
        return $this->taxonomyEntryAssignmentService->loadAssignmentById($id);
    }

    public function loadAssignmentsAction(int $contentId): TaxonomyEntryAssignmentCollection
    {
        $content = $this->repository->getContentService()->loadContent($contentId);

        return $this->taxonomyEntryAssignmentService->loadAssignments($content, $content->versionInfo->versionNo);
    }
}
