<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\EventSubscriber;

use Ibexa\Bundle\ProductCatalog\Form\Type\AttributeGroupBulkDeleteType;
use Ibexa\Bundle\ProductCatalog\View\AttributeGroupListView;
use Ibexa\Contracts\ProductCatalog\AttributeGroupServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\AttributeGroup\AttributeGroupQuery;
use Ibexa\Core\MVC\Symfony\SiteAccess\SiteAccessServiceInterface;
use Ibexa\Core\MVC\Symfony\View\View;
use Ibexa\ProductCatalog\Config\ConfigProviderInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class LocalAttributeGroupListViewSubscriber extends AbstractLocalViewSubscriber
{
    private AttributeGroupServiceInterface $attributeGroupService;

    private FormFactoryInterface $formFactory;

    private UrlGeneratorInterface $urlGenerator;

    public function __construct(
        SiteAccessServiceInterface $siteAccessService,
        ConfigProviderInterface $configProvider,
        AttributeGroupServiceInterface $attributeGroupService,
        FormFactoryInterface $formFactory,
        UrlGeneratorInterface $urlGenerator
    ) {
        parent::__construct($siteAccessService, $configProvider);

        $this->attributeGroupService = $attributeGroupService;
        $this->formFactory = $formFactory;
        $this->urlGenerator = $urlGenerator;
    }

    protected function supports(View $view): bool
    {
        return $view instanceof AttributeGroupListView;
    }

    /**
     * @param \Ibexa\Bundle\ProductCatalog\View\AttributeGroupListView $view
     */
    protected function configureView(View $view): void
    {
        $view->setEditable(true);
        $view->addParameters([
            'bulk_delete_form' => $this->createBulkDeleteForm()->createView(),
            'no_attributes_groups' => $this->isMissingAtLeastOneAttributeGroup(),
        ]);
    }

    private function createBulkDeleteForm(): FormInterface
    {
        return $this->formFactory->create(AttributeGroupBulkDeleteType::class, null, [
            'method' => Request::METHOD_POST,
            'action' => $this->urlGenerator->generate('ibexa.product_catalog.attribute_group.bulk_delete'),
        ]);
    }

    private function isMissingAtLeastOneAttributeGroup(): bool
    {
        $query = new AttributeGroupQuery();
        $query->setLimit(0);

        $attributeGroupList = $this->attributeGroupService->findAttributeGroups($query);

        return $attributeGroupList->getTotalCount() === 0;
    }
}
