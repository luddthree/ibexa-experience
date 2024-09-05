<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\Type\Catalog;

use Ibexa\Bundle\ProductCatalog\Form\Type\CatalogFilterCollectionType;
use Ibexa\Bundle\ProductCatalog\Form\Type\FilterChoiceType;
use JMS\TranslationBundle\Annotation\Desc;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class CatalogType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('name', TextType::class);
        $builder->add('identifier', TextType::class, [
            'disabled' => $options['translation_mode'],
        ]);
        $builder->add('description', TextareaType::class, [
            'required' => false,
        ]);
        $builder->add('criteria', CatalogFilterCollectionType::class, [
            'filter_definitions' => $options['filter_definitions'],
            'required' => false,
            'label' => /** @Desc("Filters") */ 'catalog.filters',
        ]);
        $builder->add('filters', FilterChoiceType::class, [
            'mapped' => false,
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'translation_mode' => false,
            'filter_definitions' => [],
            'translation_domain' => 'ibexa_product_catalog',
        ]);
    }
}
