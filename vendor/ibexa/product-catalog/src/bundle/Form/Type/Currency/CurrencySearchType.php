<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\Type\Currency;

use Ibexa\Bundle\ProductCatalog\Form\Data\Currency\CurrencyListFormData;
use Ibexa\Bundle\ProductCatalog\Form\Type\SearchType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class CurrencySearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('status', CurrencyStatusChoiceType::class, [
            'required' => false,
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CurrencyListFormData::class,
        ]);
    }

    public function getParent(): string
    {
        return SearchType::class;
    }
}
