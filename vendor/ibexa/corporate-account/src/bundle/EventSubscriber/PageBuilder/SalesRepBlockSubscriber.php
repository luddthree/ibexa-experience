<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\CorporateAccount\EventSubscriber\PageBuilder;

use Ibexa\Contracts\Core\Exception\InvalidArgumentException;
use Ibexa\Contracts\Core\Repository\LocationService;
use Ibexa\Contracts\Core\Repository\PermissionResolver;
use Ibexa\Contracts\Core\Repository\SearchService;
use Ibexa\Contracts\Core\Repository\UserService;
use Ibexa\Contracts\Core\Repository\Values\Content\Query;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\ContentTypeIdentifier;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\LogicalAnd;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\ParentLocationId;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\SortClause\Random;
use Ibexa\Contracts\Core\Repository\Values\User\User;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Contracts\CorporateAccount\Permission\MemberResolver;
use Ibexa\Contracts\CorporateAccount\Service\CompanyService;
use Ibexa\CorporateAccount\Configuration\CorporateAccountConfiguration;
use Ibexa\FieldTypePage\FieldType\Page\Block\Renderer\BlockRenderEvents;
use Ibexa\FieldTypePage\FieldType\Page\Block\Renderer\Event\PreRenderEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class SalesRepBlockSubscriber implements EventSubscriberInterface
{
    private const BLOCK_IDENTIFIER = 'sales_rep';

    private MemberResolver $memberResolver;

    private CompanyService $companyService;

    private PermissionResolver $permissionResolver;

    private UserService $userService;

    private SearchService $searchService;

    private LocationService $locationService;

    private CorporateAccountConfiguration $corporateAccountConfiguration;

    private ConfigResolverInterface $configResolver;

    public function __construct(
        MemberResolver $memberResolver,
        PermissionResolver $permissionResolver,
        CompanyService $companyService,
        UserService $userService,
        SearchService $searchService,
        LocationService $locationService,
        CorporateAccountConfiguration $corporateAccountConfiguration,
        ConfigResolverInterface $configResolver
    ) {
        $this->memberResolver = $memberResolver;
        $this->companyService = $companyService;
        $this->permissionResolver = $permissionResolver;
        $this->userService = $userService;
        $this->searchService = $searchService;
        $this->locationService = $locationService;
        $this->corporateAccountConfiguration = $corporateAccountConfiguration;
        $this->configResolver = $configResolver;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            BlockRenderEvents::getBlockPreRenderEventName(self::BLOCK_IDENTIFIER) => 'onBlockPreRender',
        ];
    }

    public function onBlockPreRender(PreRenderEvent $event): void
    {
        /** @var \Ibexa\FieldTypePage\FieldType\Page\Block\Renderer\Twig\TwigRenderRequest $renderRequest */
        $renderRequest = $event->getRenderRequest();
        $parameters = $renderRequest->getParameters();

        try {
            $currentMember = $this->memberResolver->getCurrentMember();
            $salesRep = $this->companyService->getCompanySalesRepresentative(
                $currentMember->getCompany()
            );
        } catch (InvalidArgumentException $exception) {
            $salesRep = $this->getPreviewSalesRep();
            $renderRequest->setTemplate(
                '@ibexadesign/customer_portal/page_builder/blocks/no_sales_rep_context.html.twig'
            );
        }

        $parameters['sales_rep'] = $salesRep;

        $renderRequest->setParameters($parameters);
    }

    private function getPreviewSalesRep(): User
    {
        $salesRepGroup = $this->locationService->loadLocationByRemoteId(
            $this->corporateAccountConfiguration->getSalesRepUserGroupRemoteId()
        );

        $userContentTypes = $this->configResolver->getParameter('user_content_type_identifier');

        $filter = new LogicalAnd(
            [new ContentTypeIdentifier($userContentTypes), new ParentLocationId($salesRepGroup->id)]
        );

        $query = new Query([
            'filter' => $filter,
            'limit' => 1,
            'sortClauses' => [new Random()],
        ]);

        $randomSalesRep = $this->searchService->findContent(
            $query
        );
        $salesRepContent = null;
        if ($randomSalesRep->totalCount > 0) {
            /** @var \Ibexa\Contracts\Core\Repository\Values\Content\Content $salesRepContent */
            $salesRepContent = $randomSalesRep->searchHits[0]->valueObject;
        }

        return $this->userService->loadUser(
            $salesRepContent !== null
                ? $salesRepContent->id
                : $this->permissionResolver->getCurrentUserReference()->getUserId()
        );
    }
}
