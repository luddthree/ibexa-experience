<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\Type;

use Ibexa\Contracts\Core\Repository\Iterator\BatchIterator;
use Ibexa\Contracts\ProductCatalog\Iterator\BatchIteratorAdapter\ProductTypeListAdapter;
use Ibexa\Contracts\ProductCatalog\ProductTypeServiceInterface;
use Iterator;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ChoiceList\ChoiceList;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ProductTypeChoiceType extends AbstractType
{
    private ProductTypeServiceInterface $productTypeService;

    public function __construct(ProductTypeServiceInterface $productTypeService)
    {
        $this->productTypeService = $productTypeService;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'choice_loader' => ChoiceList::lazy($this, function (): Iterator {
                return new BatchIterator(new ProductTypeListAdapter($this->productTypeService));
            }),
            'choice_label' => 'name',
            'choice_value' => 'identifier',
        ]);
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }
}
