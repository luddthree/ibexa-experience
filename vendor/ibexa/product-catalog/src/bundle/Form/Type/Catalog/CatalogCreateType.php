<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\Type\Catalog;

use Ibexa\AdminUi\Form\Type\Language\LanguageChoiceType;
use Ibexa\Bundle\ProductCatalog\Form\Data\Catalog\CatalogCreateData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class CatalogCreateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('language', LanguageChoiceType::class, [
            'disabled' => $options['translation_mode'],
        ]);
    }

    public function getParent(): string
    {
        return CatalogType::class;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CatalogCreateData::class,
            'translation_domain' => 'ibexa_product_catalog',
        ]);
    }
}
