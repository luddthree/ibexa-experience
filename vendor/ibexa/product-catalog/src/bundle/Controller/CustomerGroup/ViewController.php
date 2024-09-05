<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Controller\CustomerGroup;

use Ibexa\Bundle\ProductCatalog\Form\Data\CustomerGroupDeleteData;
use Ibexa\Bundle\ProductCatalog\Form\Type\CustomerGroupDeleteType;
use Ibexa\Bundle\ProductCatalog\View\CustomerGroupDetailedView;
use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\IsUserBased;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\LogicalAnd;
use Ibexa\Contracts\Core\Repository\Values\Filter\Filter;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Contracts\ProductCatalog\Permission\Policy\CustomerGroup\View;
use Ibexa\Contracts\ProductCatalog\Values\Content\Query\Criterion\CustomerGroupId;
use Ibexa\Contracts\ProductCatalog\Values\CustomerGroupInterface;
use Ibexa\Core\Pagination\Pagerfanta\ContentFilteringAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

final class ViewController extends AbstractController
{
    private ContentService $contentService;

    private ConfigResolverInterface $configResolver;

    public function __construct(ContentService $contentService, ConfigResolverInterface $configResolver)
    {
        $this->contentService = $contentService;
        $this->configResolver = $configResolver;
    }

    public function renderAction(Request $request, CustomerGroupInterface $customerGroup): CustomerGroupDetailedView
    {
        $this->denyAccessUnlessGranted(new View());

        $view = new CustomerGroupDetailedView(
            '@ibexadesign/product_catalog/customer_group/view.html.twig',
            $customerGroup,
            $this->getCustomerGroupUsers($request, $customerGroup)
        );

        $view->addParameters([
            'delete_form' => $this->createDeleteForm($customerGroup)->createView(),
        ]);

        return $view;
    }

    /**
     * @return iterable<\Ibexa\Contracts\Core\Repository\Values\User\User>
     */
    private function getCustomerGroupUsers(Request $request, CustomerGroupInterface $customerGroup): iterable
    {
        $limit = $this->configResolver->getParameter('product_catalog.pagination.customer_group_users_limit');

        $filter = new Filter();
        $filter->andWithCriterion(new LogicalAnd([
            new IsUserBased(),
            new CustomerGroupId($customerGroup->getId()),
        ]));

        $users = new Pagerfanta(new ContentFilteringAdapter($this->contentService, $filter));
        $users->setMaxPerPage($limit);
        $users->setCurrentPage($request->query->getInt('page', 1));

        return $users;
    }

    private function createDeleteForm(CustomerGroupInterface $customerGroup): FormInterface
    {
        $actionUrl = $this->generateUrl(
            'ibexa.product_catalog.customer_group.delete',
            [
                'customerGroupId' => $customerGroup->getId(),
            ]
        );

        return $this->createForm(
            CustomerGroupDeleteType::class,
            new CustomerGroupDeleteData($customerGroup),
            [
                'method' => Request::METHOD_POST,
                'action' => $actionUrl,
            ]
        );
    }
}
