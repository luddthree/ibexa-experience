<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Controller\AttributeDefinition;

use Ibexa\Bundle\ProductCatalog\Form\Type\AttributeDefinitionSearchType;
use Ibexa\Bundle\ProductCatalog\View\AttributeDefinitionListView;
use Ibexa\Contracts\AdminUi\Controller\Controller;
use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\Contracts\ProductCatalog\AttributeDefinitionServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\AttributeDefinition\AttributeDefinitionQuery;
use Ibexa\Contracts\ProductCatalog\Values\AttributeDefinition\Query\NameCriterion;
use Ibexa\Contracts\ProductCatalog\Values\Common\Query\Criterion\FieldValueCriterion;
use Ibexa\ProductCatalog\Pagerfanta\Adapter\AttributeDefinitionListAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

final class ListController extends Controller
{
    private AttributeDefinitionServiceInterface $attributeDefinitionService;

    private ConfigResolverInterface $configResolver;

    public function __construct(
        AttributeDefinitionServiceInterface $attributeDefinitionService,
        ConfigResolverInterface $configResolver
    ) {
        $this->attributeDefinitionService = $attributeDefinitionService;
        $this->configResolver = $configResolver;
    }

    public function renderAction(Request $request): AttributeDefinitionListView
    {
        $query = new AttributeDefinitionQuery();

        $searchForm = $this->createSearchForm();
        $searchForm->handleRequest($request);
        if ($searchForm->isSubmitted() && $searchForm->isValid()) {
            $nameQuery = $searchForm->getData()->getQuery();
            if (!empty($nameQuery)) {
                $query->and(new NameCriterion(
                    $searchForm->getData()->getQuery(),
                    FieldValueCriterion::COMPARISON_STARTS_WITH,
                ));
            }
        }

        $adapter = new AttributeDefinitionListAdapter($this->attributeDefinitionService, $query);

        $attributesDefinitions = new Pagerfanta($adapter);
        $attributesDefinitions->setMaxPerPage($this->configResolver->getParameter(
            'product_catalog.pagination.attribute_definitions_limit'
        ));
        $attributesDefinitions->setCurrentPage($request->query->getInt('page', 1));

        return new AttributeDefinitionListView(
            '@ibexadesign/product_catalog/attribute_definition/list.html.twig',
            $attributesDefinitions,
            $searchForm
        );
    }

    private function createSearchForm(): FormInterface
    {
        return $this->createForm(AttributeDefinitionSearchType::class, null, [
            'action' => $this->generateUrl('ibexa.product_catalog.attribute_definition.list'),
            'csrf_protection' => false,
            'method' => Request::METHOD_GET,
        ]);
    }
}
