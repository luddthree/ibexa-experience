<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\Type;

use Ibexa\AdminUi\Form\Type\Language\LanguageChoiceType;
use Ibexa\Bundle\ProductCatalog\Form\Data\ProductCreateRedirectData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ProductCreateRedirectType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('language', LanguageChoiceType::class);
        $builder->add('productType', ProductTypeChoiceType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ProductCreateRedirectData::class,
            'translation_domain' => 'ibexa_product_catalog',
        ]);
    }
}
