<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\Type\BlockAttribute;

use Ibexa\Bundle\ProductCatalog\Form\DataTransformer\ProductListAttributeTransformer;
use Ibexa\Contracts\ProductCatalog\ProductServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\Product\ProductQuery;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\LogicalAnd;
use Ibexa\Contracts\ProductCatalog\Values\Product\Query\Criterion\ProductCode;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ProductListAttributeType extends AbstractType
{
    private ProductServiceInterface $productService;

    public function __construct(ProductServiceInterface $productService)
    {
        $this->productService = $productService;
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        if (isset($view->vars['value']['codes'])) {
            $view->vars['products'] = $this->getProductList($view->vars['value']['codes']);
        } else {
            $view->vars['products'] = [];
        }
    }

    /**
     * @param array<string> $codes
     *
     * @return array<string, \Ibexa\Contracts\ProductCatalog\Values\ProductInterface>
     */
    private function getProductList(array $codes): array
    {
        $products = [];

        $productList = $this->productService->findProducts(
            $this->getProductQuery($codes)
        )->getProducts();

        foreach ($productList as $product) {
            $products[$product->getCode()] = $product;
        }

        return $products;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'codes',
                CollectionType::class,
                [
                    'entry_type' => TextType::class,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'delete_empty' => true,
                    'prototype' => true,
                    'entry_options' => [
                        'block_prefix' => 'product_list_attribute_entry',
                    ],
                ]
            )
            ->addModelTransformer(new ProductListAttributeTransformer());
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'error_bubbling' => false,
        ]);
    }

    public function getBlockPrefix(): string
    {
        return 'product_list_attribute';
    }

    /**
     * @param array<string> $codes
     */
    private function getProductQuery(array $codes): ProductQuery
    {
        return new ProductQuery(
            new LogicalAnd(
                [
                    new ProductCode($codes),
                ]
            ),
            null,
            [],
            0,
            count($codes)
        );
    }
}
