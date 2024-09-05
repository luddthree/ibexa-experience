<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\ProductCatalog\Tab\Product;

use Ibexa\Bundle\ProductCatalog\Form\Type\ProductBulkDeleteType;
use Ibexa\Bundle\ProductCatalog\Form\Type\ProductVariantGeneratorType;
use Ibexa\Contracts\AdminUi\Tab\AbstractEventDispatchingTab;
use Ibexa\Contracts\AdminUi\Tab\ConditionalTabInterface;
use Ibexa\Contracts\AdminUi\Tab\OrderedTabInterface;
use Ibexa\Contracts\ProductCatalog\ProductServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\ProductTypeInterface;
use Ibexa\ProductCatalog\Pagerfanta\Adapter\ProductVariantListAdapter;
use JMS\TranslationBundle\Annotation\Desc;
use Pagerfanta\Pagerfanta;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

class VariantsTab extends AbstractEventDispatchingTab implements OrderedTabInterface, ConditionalTabInterface
{
    public const URI_FRAGMENT = 'ibexa-tab-product-variants';

    private ProductServiceInterface $productService;

    private RequestStack $requestStack;

    private FormFactoryInterface $formFactory;

    private UrlGeneratorInterface $urlGenerator;

    public function __construct(
        Environment $twig,
        TranslatorInterface $translator,
        EventDispatcherInterface $eventDispatcher,
        ProductServiceInterface $productService,
        RequestStack $requestStack,
        FormFactoryInterface $formFactory,
        UrlGeneratorInterface $urlGenerator
    ) {
        parent::__construct($twig, $translator, $eventDispatcher);

        $this->productService = $productService;
        $this->requestStack = $requestStack;
        $this->formFactory = $formFactory;
        $this->urlGenerator = $urlGenerator;
    }

    public function getTemplate(): string
    {
        return '@ibexadesign/product_catalog/product/tab/variants.html.twig';
    }

    public function getTemplateParameters(array $parameters = []): array
    {
        /** @var \Ibexa\Contracts\ProductCatalog\Values\ProductInterface $product */
        $product = $parameters['product'];

        $createVariantForm = $this->formFactory->create(
            ProductVariantGeneratorType::class,
            null,
            [
                'action' => $this->urlGenerator->generate(
                    'ibexa.product_catalog.product.variant_generator',
                    [
                        'productCode' => $product->getCode(),
                    ]
                ),
                'product_type' => $product->getProductType(),
            ]
        );

        $deleteVariantForm = $this->formFactory->create(
            ProductBulkDeleteType::class,
            null,
            [
                'method' => Request::METHOD_POST,
                'action' => $this->urlGenerator->generate('ibexa.product_catalog.product.bulk_delete'),
            ]
        );

        $pagerfanta = new Pagerfanta(new ProductVariantListAdapter($this->productService, $product));
        $pagerfanta->setMaxPerPage(10);
        $pagerfanta->setCurrentPage($this->resolveCurrentPage());

        return [
            'create_variant_form' => $createVariantForm->createView(),
            'delete_variant_form' => $deleteVariantForm->createView(),
            'discriminators' => $this->getDiscriminators($product->getProductType()),
            'product' => $product,
            'variants' => $pagerfanta,
        ];
    }

    /**
     * @return array<\Ibexa\Contracts\ProductCatalog\Values\AttributeDefinitionInterface>
     */
    private function getDiscriminators(ProductTypeInterface $productType): array
    {
        $discriminators = [];
        foreach ($productType->getAttributesDefinitions() as $assigment) {
            if ($assigment->isDiscriminator()) {
                $discriminators[] = $assigment->getAttributeDefinition();
            }
        }

        return $discriminators;
    }

    /**
     * @param array<mixed> $parameters
     */
    public function evaluate(array $parameters): bool
    {
        return $parameters['product']->isBaseProduct();
    }

    public function getOrder(): int
    {
        return 275;
    }

    public function getIdentifier(): string
    {
        return 'variants';
    }

    public function getName(): string
    {
        return $this->translator->trans(/** @Desc("Variants") */ 'tab.name.variants', [], 'ibexa_product_catalog');
    }

    private function resolveCurrentPage(): int
    {
        $request = $this->requestStack->getCurrentRequest();
        if ($request !== null) {
            $page = $request->query->all('page');

            return (int)($page['variants'] ?? 1);
        }

        return 1;
    }
}
