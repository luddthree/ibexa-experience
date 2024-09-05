<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Taxonomy\Controller;

use Ibexa\AdminUi\Form\SubmitHandler;
use Ibexa\Contracts\AdminUi\Controller\Controller;
use Ibexa\Contracts\AdminUi\Notification\TranslatableNotificationHandlerInterface;
use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Contracts\Taxonomy\Service\TaxonomyEntryAssignmentServiceInterface;
use Ibexa\Contracts\Taxonomy\Service\TaxonomyServiceInterface;
use Ibexa\Contracts\Taxonomy\Value\TaxonomyEntryAssignment;
use Ibexa\Taxonomy\Form\Data\TaxonomyContentAssignData;
use Ibexa\Taxonomy\Form\Data\TaxonomyContentUnassignData;
use Ibexa\Taxonomy\Form\Data\TaxonomyEntryDeleteData;
use Ibexa\Taxonomy\Form\Data\TaxonomyEntryMoveData;
use Ibexa\Taxonomy\Form\Type\TaxonomyContentAssignType;
use Ibexa\Taxonomy\Form\Type\TaxonomyContentUnassignType;
use Ibexa\Taxonomy\Form\Type\TaxonomyEntryDeleteType;
use Ibexa\Taxonomy\Form\Type\TaxonomyEntryMoveType;
use JMS\TranslationBundle\Annotation\Desc;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class ContentController extends Controller
{
    private FormFactoryInterface $formFactory;

    private SubmitHandler $submitHandler;

    private TranslatableNotificationHandlerInterface $notificationHandler;

    private ContentService $contentService;

    private TaxonomyServiceInterface $taxonomyService;

    private TaxonomyEntryAssignmentServiceInterface $taxonomyEntryAssignmentService;

    public function __construct(
        FormFactoryInterface $formFactory,
        SubmitHandler $submitHandler,
        TranslatableNotificationHandlerInterface $notificationHandler,
        ContentService $contentService,
        TaxonomyServiceInterface $taxonomyService,
        TaxonomyEntryAssignmentServiceInterface $taxonomyEntryAssignmentService
    ) {
        $this->formFactory = $formFactory;
        $this->submitHandler = $submitHandler;
        $this->notificationHandler = $notificationHandler;
        $this->contentService = $contentService;
        $this->taxonomyService = $taxonomyService;
        $this->taxonomyEntryAssignmentService = $taxonomyEntryAssignmentService;
    }

    public function deleteAction(Request $request, string $taxonomyName): Response
    {
        $form = $this->formFactory->create(
            TaxonomyEntryDeleteType::class,
            new TaxonomyEntryDeleteData(),
            ['taxonomy' => $taxonomyName],
        );

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $result = $this->submitHandler->handle($form, function (TaxonomyEntryDeleteData $data): ?Response {
                $entry = $data->getEntry();
                if (null === $entry) {
                    return null;
                }

                $parentEntry = $entry->parent;
                if (null === $parentEntry) {
                    return null;
                }

                $this->contentService->deleteContent($entry->content->contentInfo);

                $this->notificationHandler->success(
                    /** @Desc("Entry '%entryName%' deleted.") */
                    'taxonomy.entry.delete.success',
                    ['%entryName%' => $entry->name],
                    'ibexa_taxonomy_entry'
                );

                return new RedirectResponse($this->generateUrl('ibexa.content.view', [
                    'contentId' => $parentEntry->content->id,
                    'locationId' => $parentEntry->content->contentInfo->mainLocationId,
                ]));
            });

            if ($result instanceof Response) {
                return $result;
            }
        }

        return $this->redirect($this->generateUrl('ibexa.dashboard'));
    }

    public function moveAction(Request $request, string $taxonomyName): Response
    {
        $form = $this->formFactory->create(
            TaxonomyEntryMoveType::class,
            new TaxonomyEntryMoveData(),
            ['taxonomy' => $taxonomyName],
        );

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $result = $this->submitHandler->handle($form, function (TaxonomyEntryMoveData $data): ?Response {
                $entry = $data->getEntry();
                if (null === $entry) {
                    return null;
                }

                $parentEntry = $entry->parent;
                if (null === $parentEntry) {
                    return null;
                }

                $newParentEntry = $data->getNewParent();
                if (null === $newParentEntry) {
                    return null;
                }

                $this->taxonomyService->moveEntry($entry, $newParentEntry);

                $this->notificationHandler->success(
                    /** @Desc("Entry '%entryName%' moved to '%newParentEntryName%'.") */
                    'taxonomy.entry.move.success',
                    [
                        '%entryName%' => $entry->name,
                        '%newParentEntryName%' => $newParentEntry->name,
                    ],
                    'ibexa_taxonomy_entry'
                );

                return new RedirectResponse($this->generateUrl('ibexa.content.view', [
                    'contentId' => $entry->content->id,
                    'locationId' => $entry->content->contentInfo->mainLocationId,
                ]));
            });

            if ($result instanceof Response) {
                return $result;
            }
        }

        return $this->redirect($this->generateUrl('ibexa.dashboard'));
    }

    public function assignTaxonomyEntryAction(Request $request, string $taxonomyName): Response
    {
        $form = $this->formFactory->create(
            TaxonomyContentAssignType::class,
            new TaxonomyContentAssignData(),
            ['taxonomy' => $taxonomyName],
        );

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $result = $this->submitHandler->handle($form, function (TaxonomyContentAssignData $data): ?Response {
                $entry = $data->getEntry();
                if (null === $entry) {
                    return null;
                }

                foreach ($data->getLocations() as $location) {
                    $content = $location->getContent();
                    $assignments = $this->taxonomyEntryAssignmentService->loadAssignments(
                        $content,
                        $content->versionInfo->versionNo,
                    );

                    $alreadyAssigned = array_filter(
                        $assignments->assignments,
                        static fn (TaxonomyEntryAssignment $assignment): bool => $entry->id === $assignment->entry->id
                    );

                    if (count($alreadyAssigned) > 0) {
                        continue;
                    }

                    $this->taxonomyEntryAssignmentService->assignToContent(
                        $content,
                        $entry,
                        $content->versionInfo->versionNo,
                    );
                }

                $this->notificationHandler->success(
                    /** @Desc("Assigned content to entry '%entryName%'.") */
                    'taxonomy.entry.assign.success',
                    [
                        '%entryName%' => $entry->name,
                    ],
                    'ibexa_taxonomy_entry'
                );

                return new RedirectResponse($this->generateUrl('ibexa.content.view', [
                    'contentId' => $entry->content->id,
                    'locationId' => $entry->content->contentInfo->mainLocationId,
                    '_fragment' => 'ibexa-tab-location-view-assigned-content',
                ]));
            });

            if ($result instanceof Response) {
                return $result;
            }
        }

        return $this->redirect($this->generateUrl('ibexa.dashboard'));
    }

    public function unassignTaxonomyEntryAction(Request $request, string $taxonomyName): Response
    {
        $form = $this->formFactory->create(
            TaxonomyContentUnassignType::class,
            new TaxonomyContentUnassignData(),
            ['taxonomy' => $taxonomyName],
        );

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $result = $this->submitHandler->handle($form, function (TaxonomyContentUnassignData $data): ?Response {
                $entry = $data->getEntry();
                if (null === $entry) {
                    return null;
                }

                foreach (array_keys($data->getAssignedContentItems()) as $contentId) {
                    $content = $this->contentService->loadContent($contentId);
                    $this->taxonomyEntryAssignmentService->unassignFromContent(
                        $content,
                        $entry,
                        $content->versionInfo->versionNo,
                    );
                }

                $this->notificationHandler->success(
                    /** @Desc("Unassigned content from entry '%entryName%'.") */
                    'taxonomy.entry.unassign.success',
                    [
                        '%entryName%' => $entry->name,
                    ],
                    'ibexa_taxonomy_entry'
                );

                return new RedirectResponse($this->generateUrl('ibexa.content.view', [
                    'contentId' => $entry->content->id,
                    'locationId' => $entry->content->contentInfo->mainLocationId,
                    '_fragment' => 'ibexa-tab-location-view-assigned-content',
                ]));
            });

            if ($result instanceof Response) {
                return $result;
            }
        }

        return $this->redirect($this->generateUrl('ibexa.dashboard'));
    }
}
