<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Controller\AttributeGroup;

use Ibexa\Bundle\ProductCatalog\Form\Type\AttributeGroupSearchType;
use Ibexa\Bundle\ProductCatalog\View\AttributeGroupListView;
use Ibexa\Contracts\AdminUi\Controller\Controller;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Contracts\ProductCatalog\AttributeGroupServiceInterface;
use Ibexa\Contracts\ProductCatalog\Permission\Policy\AttributeGroup\View;
use Ibexa\Contracts\ProductCatalog\Values\AttributeGroup\AttributeGroupQuery;
use Ibexa\ProductCatalog\Pagerfanta\Adapter\AttributeGroupListAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

final class ListController extends Controller
{
    private AttributeGroupServiceInterface $attributeGroupService;

    private ConfigResolverInterface $configResolver;

    public function __construct(
        AttributeGroupServiceInterface $attributeGroupService,
        ConfigResolverInterface $configResolver
    ) {
        $this->attributeGroupService = $attributeGroupService;
        $this->configResolver = $configResolver;
    }

    public function renderAction(Request $request): AttributeGroupListView
    {
        $this->denyAccessUnlessGranted(new View());

        $query = new AttributeGroupQuery();

        $searchForm = $this->createSearchForm();
        $searchForm->handleRequest($request);
        if ($searchForm->isSubmitted() && $searchForm->isValid()) {
            $query->setNamePrefix($searchForm->getData()->getQuery());
        }

        $attributeGroups = new Pagerfanta(new AttributeGroupListAdapter($this->attributeGroupService, $query));
        $attributeGroups->setMaxPerPage($this->configResolver->getParameter('product_catalog.pagination.attribute_groups_limit'));
        $attributeGroups->setCurrentPage($request->query->getInt('page', 1));

        return new AttributeGroupListView(
            '@ibexadesign/product_catalog/attribute_group/list.html.twig',
            $attributeGroups,
            $searchForm
        );
    }

    private function createSearchForm(): FormInterface
    {
        return $this->createForm(AttributeGroupSearchType::class, null, [
            'method' => Request::METHOD_GET,
            'csrf_protection' => false,
        ]);
    }
}
