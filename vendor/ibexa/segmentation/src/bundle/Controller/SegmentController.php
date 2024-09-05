<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\Segmentation\Controller;

use Ibexa\AdminUi\Form\SubmitHandler;
use Ibexa\Contracts\AdminUi\Controller\Controller;
use Ibexa\Contracts\AdminUi\Notification\TranslatableNotificationHandlerInterface;
use Ibexa\Contracts\Core\Repository\PermissionResolver;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Contracts\Segmentation\Configuration\ProtectedSegmentGroupsConfigurationInterface;
use Ibexa\Contracts\Segmentation\SegmentationServiceInterface;
use Ibexa\Core\Base\Exceptions\InvalidArgumentException;
use Ibexa\Segmentation\Data\SegmentBulkDeleteData;
use Ibexa\Segmentation\Data\SegmentGroupBulkDeleteData;
use Ibexa\Segmentation\Data\SegmentGroupDeleteData;
use Ibexa\Segmentation\Form\Type\SegmentBulkDeleteType;
use Ibexa\Segmentation\Form\Type\SegmentCreateType;
use Ibexa\Segmentation\Form\Type\SegmentGroupBulkDeleteType;
use Ibexa\Segmentation\Form\Type\SegmentGroupCreateType;
use Ibexa\Segmentation\Form\Type\SegmentGroupDeleteType;
use Ibexa\Segmentation\Form\Type\SegmentGroupUpdateType;
use Ibexa\Segmentation\Form\Type\SegmentUpdateType;
use Ibexa\Segmentation\Value\SegmentCreateStruct;
use Ibexa\Segmentation\Value\SegmentGroupCreateStruct;
use Ibexa\Segmentation\Value\SegmentGroupUpdateStruct;
use Ibexa\Segmentation\Value\SegmentUpdateStruct;
use JMS\TranslationBundle\Annotation\Desc;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class SegmentController extends Controller
{
    /** @var \Ibexa\Contracts\Segmentation\SegmentationServiceInterface */
    private $segmentationService;

    /** @var \Symfony\Component\Form\FormFactoryInterface */
    private $formFactory;

    /** @var \Ibexa\AdminUi\Form\SubmitHandler */
    private $submitHandler;

    /** @var \Ibexa\Contracts\AdminUi\Notification\TranslatableNotificationHandlerInterface */
    private $notificationHandler;

    /** @var \Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface */
    private $configResolver;

    /** @var \Ibexa\Contracts\Core\Repository\PermissionResolver */
    private $permissionResolver;

    /** @var \Ibexa\Contracts\Segmentation\Configuration\ProtectedSegmentGroupsConfigurationInterface */
    private $protectedSegmentGroupsConfiguration;

    public function __construct(
        SegmentationServiceInterface $segmentationService,
        FormFactoryInterface $formFactory,
        SubmitHandler $submitHandler,
        TranslatableNotificationHandlerInterface $notificationHandler,
        ConfigResolverInterface $configResolver,
        PermissionResolver $permissionResolver,
        ProtectedSegmentGroupsConfigurationInterface $protectedSegmentGroupsConfiguration
    ) {
        $this->segmentationService = $segmentationService;
        $this->formFactory = $formFactory;
        $this->submitHandler = $submitHandler;
        $this->notificationHandler = $notificationHandler;
        $this->configResolver = $configResolver;
        $this->permissionResolver = $permissionResolver;
        $this->protectedSegmentGroupsConfiguration = $protectedSegmentGroupsConfiguration;
    }

    public function groupListAction(Request $request): Response
    {
        $page = $request->query->getInt('page', 1);

        $segmentGroups = $this->segmentationService->loadSegmentGroups();

        $pagerfanta = new Pagerfanta(new ArrayAdapter($segmentGroups));

        $pagerfanta->setMaxPerPage(
            (int)$this->configResolver->getParameter('segmentation.pagination.segment_groups_limit')
        );
        $pagerfanta->setCurrentPage(min($page, $pagerfanta->getNbPages()));

        $segmentGroupIds = array_column($pagerfanta->getCurrentPageResults(), 'id');
        $segmentsPerGroup = array_combine(
            $segmentGroupIds,
            array_map([$this->segmentationService, 'loadSegmentsAssignedToGroup'], $pagerfanta->getCurrentPageResults())
        );

        $segmentGroupCreateForm = $this->formFactory->create(SegmentGroupCreateType::class);

        $segmentGroupsDeleteData = new SegmentGroupBulkDeleteData(
            array_combine($segmentGroupIds, array_fill_keys($segmentGroupIds, false))
        );
        $segmentGroupBulkDeleteForm = $this->formFactory->create(
            SegmentGroupBulkDeleteType::class,
            $segmentGroupsDeleteData
        );

        return $this->render('@ibexadesign/segmentation/admin/groups/list.html.twig', [
            'pager' => $pagerfanta,
            'segments_per_group' => $segmentsPerGroup,
            'form_segment_group_create' => $segmentGroupCreateForm->createView(),
            'form_segment_group_bulk_delete' => $segmentGroupBulkDeleteForm->createView(),
            'can_create' => $this->permissionResolver->hasAccess('segment_group', 'create'),
            'can_delete' => $this->permissionResolver->hasAccess('segment_group', 'remove'),
            'protected_segment_groups' => $this->protectedSegmentGroupsConfiguration->getProtectedGroupIdentifiers(),
        ]);
    }

    public function groupViewAction(int $segmentGroupId): Response
    {
        $segmentGroup = $this->segmentationService->loadSegmentGroup($segmentGroupId);
        $segments = $this->segmentationService->loadSegmentsAssignedToGroup($segmentGroup);

        $deleteForm = $this->formFactory->create(
            SegmentGroupDeleteType::class,
            new SegmentGroupDeleteData($segmentGroup)
        );

        $segmentCreateForm = $this->formFactory->create(
            SegmentCreateType::class,
            new SegmentCreateStruct(['group' => $segmentGroup])
        );

        $segmentIds = array_column($segments, 'id');
        $segmentBulkDelete = new SegmentBulkDeleteData(
            array_combine($segmentIds, array_fill_keys($segmentIds, false))
        );

        $segmentBulkDeleteForm = $this->formFactory->create(
            SegmentBulkDeleteType::class,
            $segmentBulkDelete
        );

        $updateForm = $this->formFactory->create(
            SegmentGroupUpdateType::class,
            new SegmentGroupUpdateStruct([
                'identifier' => $segmentGroup->identifier,
                'name' => $segmentGroup->name,
            ])
        );

        $segmentUpdateForms = [];
        foreach ($segments as $segment) {
            $segmentUpdateForms[$segment->id] = $this->formFactory->create(
                SegmentUpdateType::class,
                new SegmentUpdateStruct([
                    'identifier' => $segment->identifier,
                    'name' => $segment->name,
                    'group' => $segmentGroup,
                ])
            )->createView();
        }

        $protectedSegmentGroups = $this->protectedSegmentGroupsConfiguration->getProtectedGroupIdentifiers();
        $isSegmentGroupProtected = in_array($segmentGroup->identifier, $protectedSegmentGroups, true);

        return $this->render('@ibexadesign/segmentation/admin/groups/view.html.twig', [
            'segment_group' => $segmentGroup,
            'form_segment_group_delete' => $deleteForm->createView(),
            'form_segment_group_update' => $updateForm->createView(),
            'form_segment_create' => $segmentCreateForm->createView(),
            'form_segment_bulk_delete' => $segmentBulkDeleteForm->createView(),
            'segments' => $segments,
            'segment_update_forms' => $segmentUpdateForms,
            'can_update' => $this->permissionResolver->hasAccess('segment_group', 'update'),
            'can_delete' => $this->permissionResolver->hasAccess('segment_group', 'remove'),
            'can_create_segment' => $this->permissionResolver->hasAccess('segment', 'create'),
            'can_update_segment' => $this->permissionResolver->hasAccess('segment', 'update'),
            'can_delete_segment' => $this->permissionResolver->hasAccess('segment', 'remove'),
            'is_segment_group_protected' => $isSegmentGroupProtected,
        ]);
    }

    public function groupCreateAction(Request $request): Response
    {
        $form = $this->formFactory->create(SegmentGroupCreateType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $result = $this->submitHandler->handle($form, function (SegmentGroupCreateStruct $data): Response {
                $segmentGroup = $this->segmentationService->createSegmentGroup($data);

                $this->notificationHandler->success(
                    /** @Desc("Segment Group '%segment_group%' created.") */
                    'segment_group.create.success',
                    ['%segment_group%' => $segmentGroup->name],
                    'ibexa_segmentation'
                );

                return $this->redirectToRoute(
                    'ibexa.segmentation.group.view',
                    ['segmentGroupId' => $segmentGroup->id]
                );
            });

            if ($result instanceof Response) {
                return $result;
            }
        }

        return $this->redirectToRoute('ibexa.segmentation.group.list');
    }

    public function groupUpdateAction(Request $request, int $segmentGroupId): Response
    {
        $form = $this->formFactory->create(SegmentGroupUpdateType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $result = $this->submitHandler->handle(
                $form,
                function (SegmentGroupUpdateStruct $data) use ($segmentGroupId): Response {
                    $segmentGroup = $this->segmentationService->loadSegmentGroup($segmentGroupId);
                    $protectedSegmentGroups = $this->protectedSegmentGroupsConfiguration->getProtectedGroupIdentifiers();

                    $this->assertSegmentGroupNotProtected($segmentGroup->identifier, $protectedSegmentGroups);

                    $updatedSegmentGroup = $this->segmentationService->updateSegmentGroup($segmentGroup, $data);

                    $this->notificationHandler->success(
                        /** @Desc("Segment Group '%segment_group%' updated.") */
                        'segment_group.update.success',
                        ['%segment_group%' => $updatedSegmentGroup->name],
                        'ibexa_segmentation'
                    );

                    return $this->redirectToRoute(
                        'ibexa.segmentation.group.view',
                        ['segmentGroupId' => $updatedSegmentGroup->id]
                    );
                }
            );

            if ($result instanceof Response) {
                return $result;
            }
        }

        return $this->redirectToRoute('ibexa.segmentation.group.list');
    }

    public function groupDeleteAction(Request $request): Response
    {
        $form = $this->formFactory->create(SegmentGroupDeleteType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $result = $this->submitHandler->handle($form, function (SegmentGroupDeleteData $data): Response {
                $segmentGroup = $data->getSegmentGroup();
                $protectedSegmentGroups = $this->protectedSegmentGroupsConfiguration->getProtectedGroupIdentifiers();

                if (null !== $segmentGroup) {
                    $this->assertSegmentGroupNotProtected($segmentGroup->identifier, $protectedSegmentGroups);
                }

                $this->segmentationService->removeSegmentGroup($segmentGroup);

                $this->notificationHandler->success(
                    /** @Desc("Segment Group '%segment_group%' removed.") */
                    'segment_group.delete.success',
                    ['%segment_group%' => $segmentGroup->name],
                    'ibexa_segmentation'
                );

                return $this->redirectToRoute('ibexa.segmentation.group.list');
            });

            if ($result instanceof Response) {
                return $result;
            }
        }

        return $this->redirectToRoute('ibexa.segmentation.group.list');
    }

    public function segmentGroupBulkDeleteAction(Request $request): Response
    {
        $form = $this->formFactory->create(SegmentGroupBulkDeleteType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $result = $this->submitHandler->handle($form, function (SegmentGroupBulkDeleteData $data): Response {
                $protectedSegmentGroups = $this->protectedSegmentGroupsConfiguration->getProtectedGroupIdentifiers();

                foreach (array_keys($data->getSegmentGroups()) as $segmentGroupId) {
                    $segmentGroup = $this->segmentationService->loadSegmentGroup($segmentGroupId);

                    $this->assertSegmentGroupNotProtected($segmentGroup->identifier, $protectedSegmentGroups);

                    $this->segmentationService->removeSegmentGroup($segmentGroup);

                    $this->notificationHandler->success(
                        /** @Desc("Segment Group '%segment_group%' removed.") */
                        'segment_group.delete.success',
                        ['%segment_group%' => $segmentGroup->name],
                        'ibexa_segmentation'
                    );
                }

                return $this->redirectToRoute('ibexa.segmentation.group.list');
            });

            if ($result instanceof Response) {
                return $result;
            }
        }

        return $this->redirectToRoute('ibexa.segmentation.group.list');
    }

    public function segmentCreateAction(Request $request): Response
    {
        $form = $this->formFactory->create(SegmentCreateType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $result = $this->submitHandler->handle($form, function (SegmentCreateStruct $data): Response {
                $protectedSegmentGroups = $this->protectedSegmentGroupsConfiguration->getProtectedGroupIdentifiers();

                $this->assertSegmentGroupNotProtected($data->group->identifier, $protectedSegmentGroups);

                $segment = $this->segmentationService->createSegment($data);

                $this->notificationHandler->success(
                    /** @Desc("Segment '%name%' added.") */
                    'segment.create.success',
                    ['%name%' => $segment->name],
                    'ibexa_segmentation'
                );

                return $this->redirectToRoute(
                    'ibexa.segmentation.group.view',
                    ['segmentGroupId' => $segment->group->id]
                );
            });

            if ($result instanceof Response) {
                return $result;
            }
        }

        return $this->redirectToRoute('ibexa.segmentation.group.list');
    }

    public function segmentUpdateAction(Request $request, int $segmentId): Response
    {
        $form = $this->formFactory->create(SegmentUpdateType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $result = $this->submitHandler->handle(
                $form,
                function (SegmentUpdateStruct $data) use ($segmentId): Response {
                    $segment = $this->segmentationService->loadSegment($segmentId);

                    $protectedSegmentGroups = $this->protectedSegmentGroupsConfiguration->getProtectedGroupIdentifiers();

                    $this->assertSegmentGroupNotProtected($data->group->identifier, $protectedSegmentGroups);

                    $updatedSegment = $this->segmentationService->updateSegment($segment, $data);

                    $this->notificationHandler->success(
                        /** @Desc("Segment '%name%' updated.") */
                        'segment.update.success',
                        ['%name%' => $updatedSegment->name],
                        'ibexa_segmentation'
                    );

                    return $this->redirectToRoute(
                        'ibexa.segmentation.group.view',
                        ['segmentGroupId' => $updatedSegment->group->id]
                    );
                }
            );

            if ($result instanceof Response) {
                return $result;
            }
        }

        return $this->redirectToRoute('ibexa.segmentation.group.list');
    }

    public function segmentBulkDeleteAction(Request $request): Response
    {
        $form = $this->formFactory->create(SegmentBulkDeleteType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $result = $this->submitHandler->handle($form, function (SegmentBulkDeleteData $data): Response {
                $groupId = null;
                $protectedSegmentGroups = $this->protectedSegmentGroupsConfiguration->getProtectedGroupIdentifiers();

                foreach (array_keys($data->getSegments()) as $segmentId) {
                    $segment = $this->segmentationService->loadSegment($segmentId);

                    $this->assertSegmentGroupNotProtected($segment->group->identifier, $protectedSegmentGroups);

                    $this->segmentationService->removeSegment($segment);

                    $this->notificationHandler->success(
                        /** @Desc("Segment '%segment%' removed.") */
                        'segment.delete.success',
                        ['%segment%' => $segment->name],
                        'ibexa_segmentation'
                    );

                    $groupId = $segment->group->id;
                }

                return $this->redirectToRoute(
                    'ibexa.segmentation.group.view',
                    ['segmentGroupId' => $groupId]
                );
            });

            if ($result instanceof Response) {
                return $result;
            }
        }

        return $this->redirectToRoute('ibexa.segmentation.group.list');
    }

    /**
     * @param array<string> $protectedSegmentGroups
     */
    private function assertSegmentGroupNotProtected(
        string $segmentGroupIdentifier,
        array $protectedSegmentGroups
    ): void {
        if (in_array($segmentGroupIdentifier, $protectedSegmentGroups, true)) {
            throw new InvalidArgumentException(
                'SegmentGroup',
                'Cannot modify protected Segment Group'
            );
        }
    }
}

class_alias(SegmentController::class, 'Ibexa\Platform\Bundle\Segmentation\Controller\SegmentController');
