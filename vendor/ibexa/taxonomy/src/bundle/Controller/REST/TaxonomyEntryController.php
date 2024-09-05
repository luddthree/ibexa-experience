<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Taxonomy\Controller\REST;

use Exception;
use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Contracts\Taxonomy\Service\TaxonomyServiceInterface;
use Ibexa\Rest\Message;
use Ibexa\Rest\Server\Controller as BaseController;
use Ibexa\Rest\Server\Values\NoContent;
use Ibexa\Rest\Value;
use Ibexa\Taxonomy\REST\Values\RestTaxonomyEntry;
use Symfony\Component\HttpFoundation\Request;

final class TaxonomyEntryController extends BaseController
{
    private ContentService $contentService;

    private TaxonomyServiceInterface $taxonomyService;

    public function __construct(
        ContentService $contentService,
        TaxonomyServiceInterface $taxonomyService
    ) {
        $this->contentService = $contentService;
        $this->taxonomyService = $taxonomyService;
    }

    public function loadByIdAction(int $id): RestTaxonomyEntry
    {
        return new RestTaxonomyEntry(
            $this->taxonomyService->loadEntryById($id)
        );
    }

    public function loadByIdentifierAction(string $identifier): RestTaxonomyEntry
    {
        return new RestTaxonomyEntry(
            $this->taxonomyService->loadEntryByIdentifier($identifier)
        );
    }

    public function loadByContentIdAction(int $contentId): RestTaxonomyEntry
    {
        return new RestTaxonomyEntry(
            $this->taxonomyService->loadEntryByContentId($contentId)
        );
    }

    public function bulkRemoveAction(Request $request): Value
    {
        /** @var \Ibexa\Taxonomy\REST\Input\Value\TaxonomyEntryBulkRemove $removeEntriesInput */
        $removeEntriesInput = $this->inputDispatcher->parse(
            new Message(
                ['Content-Type' => $request->headers->get('Content-Type')],
                $request->getContent()
            )
        );

        $this->repository->beginTransaction();
        try {
            foreach ($removeEntriesInput->entries as $entry) {
                $this->contentService->deleteContent($entry->content->contentInfo);
            }

            $this->repository->commit();
        } catch (Exception $e) {
            $this->repository->rollback();

            throw $e;
        }

        return new NoContent();
    }

    public function bulkMoveAction(Request $request): Value
    {
        /** @var \Ibexa\Taxonomy\REST\Input\Value\TaxonomyEntryBulkMove $moveEntriesInput */
        $moveEntriesInput = $this->inputDispatcher->parse(
            new Message(
                ['Content-Type' => $request->headers->get('Content-Type')],
                $request->getContent()
            )
        );

        $this->repository->beginTransaction();
        try {
            foreach ($moveEntriesInput->entries as $moveEntryInput) {
                $this->taxonomyService->moveEntryRelativeToSibling(
                    $moveEntryInput->getEntry(),
                    $moveEntryInput->getSibling(),
                    $moveEntryInput->getPosition()
                );
            }

            $this->repository->commit();
        } catch (Exception $e) {
            $this->repository->rollback();

            throw $e;
        }

        return new NoContent();
    }
}
