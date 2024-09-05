<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\EventSubscriber;

use Ibexa\Bundle\ProductCatalog\Form\Data\AttributeDefinitionPreCreateData;
use Ibexa\Bundle\ProductCatalog\Form\Data\AttributeGroupBulkDeleteData;
use Ibexa\Bundle\ProductCatalog\Form\Type\AttributeDefinitionPreCreateType;
use Ibexa\Bundle\ProductCatalog\Form\Type\AttributeGroupBulkDeleteType;
use Ibexa\Bundle\ProductCatalog\View\AttributeGroupView;
use Ibexa\Contracts\ProductCatalog\Values\AttributeGroupInterface;
use Ibexa\Core\MVC\Symfony\SiteAccess\SiteAccessServiceInterface;
use Ibexa\Core\MVC\Symfony\View\View;
use Ibexa\ProductCatalog\Config\ConfigProviderInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class LocalAttributeGroupViewSubscriber extends AbstractLocalViewSubscriber
{
    private FormFactoryInterface $formFactory;

    private UrlGeneratorInterface $urlGenerator;

    public function __construct(
        SiteAccessServiceInterface $siteAccessService,
        ConfigProviderInterface $configProvider,
        FormFactoryInterface $formFactory,
        UrlGeneratorInterface $urlGenerator
    ) {
        parent::__construct($siteAccessService, $configProvider);

        $this->formFactory = $formFactory;
        $this->urlGenerator = $urlGenerator;
    }

    protected function supports(View $view): bool
    {
        return $view instanceof AttributeGroupView;
    }

    /**
     * @param \Ibexa\Bundle\ProductCatalog\View\AttributeGroupView $view
     */
    protected function configureView(View $view): void
    {
        $view->setEditable(true);
        $attributeGroup = $view->getAttributeGroup();

        $view->addParameters([
            'pre_create_form' => $this->createPreCreateForm($attributeGroup)->createView(),
            'group_delete_form' => $this->createGroupDeleteForm($attributeGroup)->createView(),
        ]);
    }

    private function createPreCreateForm(AttributeGroupInterface $attributeGroup): FormInterface
    {
        $attributeDefinitionPreCreateData = new AttributeDefinitionPreCreateData();
        $attributeDefinitionPreCreateData->setAttributeGroup($attributeGroup);

        return $this->formFactory->create(
            AttributeDefinitionPreCreateType::class,
            $attributeDefinitionPreCreateData,
            [
                'action' => $this->urlGenerator->generate(
                    'ibexa.product_catalog.attribute_definition.pre_create',
                    [
                        'attributeGroupIdentifier' => $attributeGroup->getIdentifier(),
                    ]
                ),
                'method' => Request::METHOD_POST,
            ]
        );
    }

    private function createGroupDeleteForm(AttributeGroupInterface $attributeGroup): FormInterface
    {
        $attributeGroupDeleteData = new AttributeGroupBulkDeleteData();
        $attributeGroupDeleteData->setAttributeGroups([$attributeGroup]);

        return $this->formFactory->create(
            AttributeGroupBulkDeleteType::class,
            $attributeGroupDeleteData,
            [
                'method' => Request::METHOD_POST,
                'action' => $this->urlGenerator->generate('ibexa.product_catalog.attribute_group.bulk_delete'),
                'allow_add' => false,
            ]
        );
    }
}
