<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\EventSubscriber;

use Ibexa\Bundle\ProductCatalog\Form\Type\AttributeDefinitionBulkDeleteType;
use Ibexa\Bundle\ProductCatalog\Form\Type\AttributeDefinitionPreCreateType;
use Ibexa\Bundle\ProductCatalog\View\AttributeDefinitionListView;
use Ibexa\Contracts\ProductCatalog\AttributeDefinitionServiceInterface;
use Ibexa\Contracts\ProductCatalog\AttributeGroupServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\AttributeDefinition\AttributeDefinitionQuery;
use Ibexa\Contracts\ProductCatalog\Values\AttributeGroup\AttributeGroupQuery;
use Ibexa\Core\MVC\Symfony\SiteAccess\SiteAccessServiceInterface;
use Ibexa\Core\MVC\Symfony\View\View;
use Ibexa\ProductCatalog\Config\ConfigProviderInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class LocalAttributeDefinitionListViewSubscriber extends AbstractLocalViewSubscriber
{
    private AttributeDefinitionServiceInterface $attributeDefinitionService;

    private AttributeGroupServiceInterface $attributeGroupService;

    private FormFactoryInterface $formFactory;

    private UrlGeneratorInterface $urlGenerator;

    public function __construct(
        SiteAccessServiceInterface $siteAccessService,
        ConfigProviderInterface $configProvider,
        AttributeGroupServiceInterface $attributeGroupService,
        AttributeDefinitionServiceInterface $attributeDefinitionService,
        FormFactoryInterface $formFactory,
        UrlGeneratorInterface $urlGenerator
    ) {
        parent::__construct($siteAccessService, $configProvider);

        $this->attributeGroupService = $attributeGroupService;
        $this->attributeDefinitionService = $attributeDefinitionService;
        $this->formFactory = $formFactory;
        $this->urlGenerator = $urlGenerator;
    }

    protected function supports(View $view): bool
    {
        return $view instanceof AttributeDefinitionListView;
    }

    /**
     * @param \Ibexa\Bundle\ProductCatalog\View\AttributeDefinitionListView $view
     */
    protected function configureView(View $view): void
    {
        $view->setEditable(true);
        $view->addParameters([
            'bulk_delete_form' => $this->createBulkDeleteForm()->createView(),
            'pre_create_form' => $this->createPreCreateForm()->createView(),
            'no_attributes' => $this->isMissingAtLeastOneAttribute(),
            'no_attributes_groups' => $this->isMissingAtLeastOneAttributeGroup(),
        ]);
    }

    private function createPreCreateForm(): FormInterface
    {
        return $this->formFactory->create(AttributeDefinitionPreCreateType::class, null, [
            'action' => $this->urlGenerator->generate('ibexa.product_catalog.attribute_definition.pre_create'),
            'method' => Request::METHOD_POST,
        ]);
    }

    private function createBulkDeleteForm(): FormInterface
    {
        return $this->formFactory->create(AttributeDefinitionBulkDeleteType::class, null, [
            'action' => $this->urlGenerator->generate('ibexa.product_catalog.attribute_definition.bulk_delete'),
            'method' => Request::METHOD_POST,
        ]);
    }

    private function isMissingAtLeastOneAttribute(): bool
    {
        $query = new AttributeDefinitionQuery();
        $query->setLimit(0);

        $attributeDefinitionList = $this->attributeDefinitionService->findAttributesDefinitions($query);

        return $attributeDefinitionList->getTotalCount() === 0;
    }

    private function isMissingAtLeastOneAttributeGroup(): bool
    {
        $query = new AttributeGroupQuery();
        $query->setLimit(0);

        $attributeGroupList = $this->attributeGroupService->findAttributeGroups($query);

        return $attributeGroupList->getTotalCount() === 0;
    }
}
