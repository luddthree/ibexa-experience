<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\EventSubscriber;

use Ibexa\Bundle\ProductCatalog\Form\Data\AttributeDefinitionDeleteData;
use Ibexa\Bundle\ProductCatalog\Form\Type\AttributeDefinitionDeleteType;
use Ibexa\Bundle\ProductCatalog\View\AttributeDefinitionView;
use Ibexa\Contracts\ProductCatalog\Values\AttributeDefinitionInterface;
use Ibexa\Core\MVC\Symfony\SiteAccess\SiteAccessServiceInterface;
use Ibexa\Core\MVC\Symfony\View\View;
use Ibexa\ProductCatalog\Config\ConfigProviderInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class LocalAttributeDefinitionViewSubscriber extends AbstractLocalViewSubscriber
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
        return $view instanceof AttributeDefinitionView;
    }

    /**
     * @param \Ibexa\Bundle\ProductCatalog\View\AttributeDefinitionView $view
     */
    protected function configureView(View $view): void
    {
        $view->setEditable(true);
        $view->addParameters([
            'delete_form' => $this->createDeleteForm($view->getAttributeDefinition())->createView(),
        ]);
    }

    private function createDeleteForm(AttributeDefinitionInterface $attributeDefinition): FormInterface
    {
        $actionUrl = $this->urlGenerator->generate(
            'ibexa.product_catalog.attribute_definition.delete',
            [
                'attributeDefinitionIdentifier' => $attributeDefinition->getIdentifier(),
            ]
        );

        return $this->formFactory->create(
            AttributeDefinitionDeleteType::class,
            new AttributeDefinitionDeleteData($attributeDefinition),
            [
                'method' => Request::METHOD_POST,
                'action' => $actionUrl,
            ]
        );
    }
}
