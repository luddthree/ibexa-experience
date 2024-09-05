<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Segmentation\Tab;

use Ibexa\AdminUi\Specification\ContentType\ContentTypeIsUser;
use Ibexa\Contracts\AdminUi\Tab\AbstractEventDispatchingTab;
use Ibexa\Contracts\AdminUi\Tab\ConditionalTabInterface;
use Ibexa\Contracts\AdminUi\Tab\OrderedTabInterface;
use Ibexa\Contracts\Core\Repository\PermissionResolver;
use Ibexa\Contracts\Core\Repository\UserService;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Contracts\Segmentation\SegmentationServiceInterface;
use JMS\TranslationBundle\Annotation\Desc;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

final class UserViewTab extends AbstractEventDispatchingTab implements OrderedTabInterface, ConditionalTabInterface
{
    public const URI_FRAGMENT = 'ibexa-tab-location-view-segments';

    /** @var \Ibexa\Contracts\Core\Repository\PermissionResolver */
    private $permissionResolver;

    /** @var \Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface */
    private $configResolver;

    /** @var \Ibexa\Contracts\Core\Repository\UserService */
    private $userService;

    /** @var \Ibexa\Contracts\Segmentation\SegmentationServiceInterface */
    private $segmentationService;

    /** @var \Symfony\Component\HttpFoundation\RequestStack */
    private $requestStack;

    public function __construct(
        Environment $twig,
        TranslatorInterface $translator,
        EventDispatcherInterface $eventDispatcher,
        PermissionResolver $permissionResolver,
        ConfigResolverInterface $configResolver,
        UserService $userService,
        SegmentationServiceInterface $segmentationService,
        RequestStack $requestStack
    ) {
        parent::__construct($twig, $translator, $eventDispatcher);

        $this->permissionResolver = $permissionResolver;
        $this->configResolver = $configResolver;
        $this->userService = $userService;
        $this->segmentationService = $segmentationService;
        $this->requestStack = $requestStack;
    }

    public function getIdentifier(): string
    {
        return 'segments';
    }

    public function getName(): string
    {
        /** @Desc("Segments") */
        return $this->translator->trans('tab.segments.name', [], 'ibexa_segmentation');
    }

    public function getOrder(): int
    {
        return 900;
    }

    public function evaluate(array $parameters): bool
    {
        /** @var \Ibexa\Contracts\Core\Repository\Values\ContentType\ContentType $contentType */
        $contentType = $parameters['contentType'];
        $contentTypeIsUserSpecification = new ContentTypeIsUser($this->configResolver->getParameter('user_content_type_identifier'));
        $isUser = $contentTypeIsUserSpecification->isSatisfiedBy($contentType);
        $hasAccess = $this->permissionResolver->hasAccess('segment', 'view_user_segment_list');

        return $isUser && $hasAccess;
    }

    public function getTemplate(): string
    {
        return '@ibexadesign/segmentation/admin/tab/segments/tab.html.twig';
    }

    public function getTemplateParameters(array $contextParameters = []): array
    {
        /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Content $content */
        $content = $contextParameters['content'];
        $user = $this->userService->loadUser($content->id);

        $segments = $this->segmentationService->loadSegmentsAssignedToUser($user);
        $request = $this->requestStack->getCurrentRequest();
        /** @var array{segments?: int} $pageParameter */
        $pageParameter = $request->query->get('page');

        $pager = new Pagerfanta(new ArrayAdapter($segments));

        $limit = $this->configResolver->getParameter('segmentation.pagination.user_view_segments_limit');
        $page = $pageParameter['segments'] ?? 1;

        $pager->setMaxPerPage($limit);
        $pager->setCurrentPage(min($page, $pager->getNbPages()));

        $routeParams = [
            'name' => $request->get('_route'),
            'params' => $request->get('_route_params'),
            'page' => $page,
            'limit' => $limit,
        ];

        $viewParameters = [
            'pager' => $pager,
            'route_params' => $routeParams,
        ];

        return array_replace($contextParameters, $viewParameters);
    }
}

class_alias(UserViewTab::class, 'Ibexa\Platform\Segmentation\Tab\UserViewTab');
