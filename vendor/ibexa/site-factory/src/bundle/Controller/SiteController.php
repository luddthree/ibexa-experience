<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\SiteFactory\Controller;

use Ibexa\AdminUi\Form\SubmitHandler;
use Ibexa\Bundle\SiteFactory\Form\Data\SiteCreateData;
use Ibexa\Bundle\SiteFactory\Form\Data\SiteDeleteData;
use Ibexa\Bundle\SiteFactory\Form\Data\SiteListData;
use Ibexa\Bundle\SiteFactory\Form\Data\SitesDeleteData;
use Ibexa\Bundle\SiteFactory\Form\Data\SiteUpdateData;
use Ibexa\Bundle\SiteFactory\Form\DataMapper\SiteCreateMapper;
use Ibexa\Bundle\SiteFactory\Form\DataMapper\SiteUpdateMapper;
use Ibexa\Bundle\SiteFactory\Form\Type\SiteCreateType;
use Ibexa\Bundle\SiteFactory\Form\Type\SiteDeleteType;
use Ibexa\Bundle\SiteFactory\Form\Type\SiteListType;
use Ibexa\Bundle\SiteFactory\Form\Type\SitesDeleteType;
use Ibexa\Bundle\SiteFactory\Form\Type\SiteUpdateType;
use Ibexa\Contracts\AdminUi\Controller\Controller;
use Ibexa\Contracts\AdminUi\Notification\TranslatableNotificationHandlerInterface;
use Ibexa\Contracts\Core\Repository\Exceptions\NotFoundException;
use Ibexa\Contracts\Core\Repository\Exceptions\UnauthorizedException;
use Ibexa\Contracts\Core\Repository\LocationService;
use Ibexa\Contracts\Core\Repository\PermissionResolver;
use Ibexa\Contracts\Core\Repository\Values\Content\Location;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Contracts\SiteFactory\Service\SiteServiceInterface;
use Ibexa\Contracts\SiteFactory\Values\Query\Criterion\MatchName;
use Ibexa\Contracts\SiteFactory\Values\Site\Site;
use Ibexa\Contracts\SiteFactory\Values\Site\SiteQuery;
use Ibexa\Core\MVC\Symfony\Security\Authorization\Attribute;
use Ibexa\SiteFactory\DesignRegistry;
use Ibexa\SiteFactory\Pagination\Pagerfanta\SiteAdapter;
use JMS\TranslationBundle\Annotation\Desc;
use Pagerfanta\Pagerfanta;
use Symfony\Component\Form\Button;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Contracts\Translation\TranslatorInterface;

final class SiteController extends Controller
{
    /** @var \Ibexa\Contracts\SiteFactory\Service\SiteServiceInterface */
    private $siteService;

    /** @var \Symfony\Component\Form\FormFactoryInterface */
    private $formFactory;

    /** @var \Symfony\Contracts\Translation\TranslatorInterface */
    private $translator;

    /** @var \Ibexa\Contracts\AdminUi\Notification\TranslatableNotificationHandlerInterface */
    private $notificationHandler;

    /** @var \Ibexa\AdminUi\Form\SubmitHandler */
    private $submitHandler;

    /** @var \Ibexa\Bundle\SiteFactory\Form\DataMapper\SiteCreateMapper */
    private $siteCreateMapper;

    /** @var \Ibexa\Contracts\Core\Repository\PermissionResolver */
    private $permissionResolver;

    /** @var \Ibexa\SiteFactory\DesignRegistry */
    private $designRegistry;

    /** @var \Ibexa\Bundle\SiteFactory\Form\DataMapper\SiteUpdateMapper */
    private $siteUpdateMapper;

    /** @var \Ibexa\Contracts\Core\Repository\LocationService */
    private $locationService;

    /** @var bool */
    private $siteFactoryEnabled;

    /** @var \Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface */
    private $configResolver;

    /**
     * @param \Ibexa\Contracts\SiteFactory\Service\SiteServiceInterface $siteService
     * @param \Symfony\Component\Form\FormFactoryInterface $formFactory
     * @param \Symfony\Contracts\Translation\TranslatorInterface $translator
     * @param \Ibexa\Contracts\AdminUi\Notification\TranslatableNotificationHandlerInterface $notificationHandler
     * @param \Ibexa\AdminUi\Form\SubmitHandler $submitHandler
     * @param \Ibexa\Bundle\SiteFactory\Form\DataMapper\SiteCreateMapper $siteCreateMapper
     * @param \Ibexa\Bundle\SiteFactory\Form\DataMapper\SiteUpdateMapper $siteUpdateMapper
     * @param \Ibexa\Contracts\Core\Repository\PermissionResolver $permissionResolver
     * @param \Ibexa\SiteFactory\DesignRegistry $designRegistry
     * @param \Ibexa\Contracts\Core\Repository\LocationService $locationService
     * @param \Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface $configResolver
     * @param bool $siteFactoryEnabled
     */
    public function __construct(
        SiteServiceInterface $siteService,
        FormFactoryInterface $formFactory,
        TranslatorInterface $translator,
        TranslatableNotificationHandlerInterface $notificationHandler,
        SubmitHandler $submitHandler,
        SiteCreateMapper $siteCreateMapper,
        SiteUpdateMapper $siteUpdateMapper,
        PermissionResolver $permissionResolver,
        DesignRegistry $designRegistry,
        LocationService $locationService,
        ConfigResolverInterface $configResolver,
        bool $siteFactoryEnabled
    ) {
        $this->siteService = $siteService;
        $this->formFactory = $formFactory;
        $this->translator = $translator;
        $this->notificationHandler = $notificationHandler;
        $this->submitHandler = $submitHandler;
        $this->permissionResolver = $permissionResolver;
        $this->siteCreateMapper = $siteCreateMapper;
        $this->designRegistry = $designRegistry;
        $this->siteUpdateMapper = $siteUpdateMapper;
        $this->locationService = $locationService;
        $this->siteFactoryEnabled = $siteFactoryEnabled;
        $this->configResolver = $configResolver;
    }

    public function getListData(Request $request, ?Location $location = null): array
    {
        $this->denyAccessUnlessGranted(new Attribute('site', 'view'));

        $data = new SiteListData();

        $form = $this->formFactory->create(SiteListType::class, $data, [
            'method' => Request::METHOD_GET,
            'csrf_protection' => false,
        ]);
        $sitesLimit = $this->configResolver->getParameter('pagination_site_factory.sites_limit');
        $form->handleRequest($request);
        if ($form->isSubmitted() && !$form->isValid()) {
            throw new BadRequestHttpException();
        }
        $currentPage = $request->query->get('site_list')['page'] ?? 1;
        $query = $this->buildListQuery($data);
        $pagination = new Pagerfanta(
            new SiteAdapter($this->siteService, $query)
        );

        $pagination->setMaxPerPage($sitesLimit);
        $pagination->setCurrentPage(min($currentPage, $pagination->getNbPages()));

        $siteList = $pagination->getCurrentPageResults();
        $locations = [];
        $sites = iterator_to_array($siteList);
        foreach ($sites as $site) {
            if ($site->id ?? false) {
                $rootLocation = null;
                try {
                    $rootLocation = $this->locationService->loadLocation($site->getTreeRootLocationId());
                } catch (NotFoundException | UnauthorizedException $e) {
                }

                $locations[$site->id] = $rootLocation;
            }
        }

        $canCreate = $this->permissionResolver->hasAccess('site', 'create');
        $canEdit = $this->permissionResolver->hasAccess('site', 'edit');
        $canDelete = $this->permissionResolver->hasAccess('site', 'delete');

        $siteDeleteForm = $this->formFactory->create(
            SiteDeleteType::class,
            new SiteDeleteData($siteList->sites[0] ?? null) // TODO: change to general form, it's a little workaround for now
        );

        return [
            'sites' => $siteList,
            'locations' => $locations,
            'can_edit' => $canEdit,
            'can_create' => $canCreate,
            'can_delete' => $canDelete,
            'has_templates' => !empty($this->designRegistry->getTemplates()),
            'form' => $form->createView(),
            'form_site_delete' => $siteDeleteForm->createView(),
            'site_factory_enabled' => $this->siteFactoryEnabled,
            'location' => $location,
            'pager' => $pagination,
        ];
    }

    public function gridAction(
        Request $request,
        ?Location $location = null
    ): Response {
        $data = $this->getListData($request, $location);

        return $this->render('@ibexadesign/site/grid.html.twig', $data);
    }

    public function listAction(
        Request $request,
        ?Location $location = null
    ): Response {
        $data = $this->getListData($request, $location);

        $deleteSitesForm = $this->formFactory->create(
            SitesDeleteType::class,
            new SitesDeleteData($this->getSitesIds($data['sites']))
        );

        $data['form_sites_delete'] = $deleteSitesForm->createView();

        return $this->render('@ibexadesign/site/list.html.twig', $data);
    }

    public function viewAction(Request $request, Site $site): Response
    {
        $this->denyAccessUnlessGranted(new Attribute('site', 'view'));

        $siteDeleteForm = $this->formFactory->create(
            SiteDeleteType::class,
            new SiteDeleteData($site)
        );

        return $this->render('@ibexadesign/site/view.html.twig', [
            'form_site_delete' => $siteDeleteForm->createView(),
            'deletable' => $site->status === Site::STATUS_OFFLINE,
            'site' => $site,
            'can_edit' => true,
        ]);
    }

    /**
     * Handles removing sites based on submitted form.
     */
    public function bulkDeleteAction(Request $request): Response
    {
        $this->denyAccessUnlessGranted(new Attribute('site', 'delete'));
        $form = $this->formFactory->create(
            SitesDeleteType::class,
            new SitesDeleteData()
        );
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $result = $this->submitHandler->handle($form, function (SitesDeleteData $data) {
                foreach ($data->getSites() as $siteId => $selected) {
                    $site = $this->siteService->loadSite($siteId);
                    $this->siteService->deleteSite($site);

                    $this->notificationHandler->success(
                        /** @Desc("Site '%name%' removed.") */
                        'site.delete.success',
                        ['%name%' => $site->name],
                        'ibexa_site_factory'
                    );
                }
            });

            if ($result instanceof Response) {
                return $result;
            }
        }

        return $this->redirect($this->generateUrl('ibexa.site_factory.list'));
    }

    public function createAction(Request $request): Response
    {
        $this->denyAccessUnlessGranted(new Attribute('site', 'create'));

        $form = $this->formFactory->create(
            SiteCreateType::class,
            new SiteCreateData(
                null,
                true,
                $this->getParentLocationId()
            ),
            [
                'method' => Request::METHOD_POST,
            ]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $result = $this->submitHandler->handle($form, function (SiteCreateData $data) use ($form): Response {
                $siteCreateStruct = $this->siteCreateMapper->reverseMap($data);

                $site = $this->siteService->createSite($siteCreateStruct);

                $this->notificationHandler->success(
                    $this->translator->trans(
                        /** @Desc("Site '%name%' has been created.") */
                        'site.create.success',
                        [
                            '%name%' => $site->name,
                        ],
                        'ibexa_site_factory'
                    )
                );

                if ($form->getClickedButton() instanceof Button
                    && $form->getClickedButton()->getName() === SiteCreateType::BTN_SAVE
                ) {
                    return $this->redirectToRoute('ibexa.site_factory.edit', [
                        'siteId' => $site->id,
                    ]);
                }

                return $this->redirectToRoute('ibexa.site_factory.view', [
                    'siteId' => $site->id,
                ]);
            });

            if ($result instanceof Response) {
                return $result;
            }
        }

        return $this->render('@ibexadesign/site/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    public function editAction(Request $request, Site $site): Response
    {
        $this->denyAccessUnlessGranted(new Attribute('site', 'edit'));

        $siteUpdateData = SiteUpdateData::fromSite($site);
        $form = $this->formFactory->create(
            SiteUpdateType::class,
            $siteUpdateData
        );
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $result = $this->submitHandler->handle($form, function (SiteUpdateData $data) use ($site, $form): Response {
                $data->setTreeRootLocationId($site->getTreeRootLocationId());
                $siteUpdateStruct = $this->siteUpdateMapper->reverseMap($data);
                $site = $this->siteService->updateSite($site, $siteUpdateStruct);

                $this->notificationHandler->success(
                    /** @Desc("Site '%name%' has been updated.") */
                    'site.update.success',
                    [
                        '%name%' => $site->name,
                    ],
                    'ibexa_site_factory'
                );

                if ($form->getClickedButton() instanceof Button
                    && $form->getClickedButton()->getName() === SiteUpdateType::BTN_SAVE
                ) {
                    return $this->redirectToRoute('ibexa.site_factory.edit', [
                        'siteId' => $site->id,
                    ]);
                }

                return $this->redirectToRoute('ibexa.site_factory.view', [
                    'siteId' => $site->id,
                ]);
            });

            if ($result instanceof Response) {
                return $result;
            }
        }

        return $this->render('@ibexadesign/site/edit.html.twig', [
            'form' => $form->createView(),
            'site' => $site,
        ]);
    }

    private function buildListQuery(SiteListData $data): SiteQuery
    {
        $query = new SiteQuery();
        $searchQuery = $data->getSearchQuery();
        if ($searchQuery !== null) {
            $query->criteria = new MatchName($searchQuery);
        }

        return $query;
    }

    public function deleteAction(Request $request): Response
    {
        $this->denyAccessUnlessGranted(new Attribute('site', 'create'));

        $form = $this->formFactory->create(
            SiteDeleteType::class
        );

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $result = $this->submitHandler->handle($form, function (SiteDeleteData $data) {
                $site = $data->getSite();

                $this->siteService->deleteSite($site);

                $this->notificationHandler->success(
                    /** @Desc("Site '%name%' removed.") */
                    'site.delete.success',
                    ['%name%' => $site->name],
                    'ibexa_site_factory'
                );

                return new RedirectResponse($this->generateUrl('ibexa.site_factory.list'));
            });

            if ($result instanceof Response) {
                return $result;
            }
        }

        return $this->redirect($this->generateUrl('ibexa.site_factory.list'));
    }

    private function getParentLocationId(): int
    {
        $templates = $this->designRegistry->getTemplates();
        $parentLocation = reset($templates)->getParentLocation();
        if ($parentLocation !== null) {
            return $parentLocation->id;
        } else {
            return $this->configResolver->getParameter('site_factory.sites_location_id');
        }
    }

    private function getSitesIds(iterable $siteList): array
    {
        $sitesId = array_column(iterator_to_array($siteList), 'id');

        return array_fill_keys($sitesId, false);
    }
}

class_alias(SiteController::class, 'EzSystems\EzPlatformSiteFactoryBundle\Controller\SiteController');
