<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\Type;

use JMS\TranslationBundle\Annotation\Desc;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class IntegerAttributeOptionsType extends AbstractType
{
    public function getParent(): string
    {
        return AttributeDefinitionOptions::class;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('min', IntegerType::class, [
            'disabled' => $options['translation_mode'],
            'label' => /** @Desc("Minimal Value") */ 'ibexa_product_catalog.attribute.integer.option.min_value',
            'required' => false,
        ]);

        $builder->add('max', IntegerType::class, [
            'disabled' => $options['translation_mode'],
            'label' => /** @Desc("Maximum Value") */ 'ibexa_product_catalog.attribute.integer.option.max_value',
            'required' => false,
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'translation_domain' => 'ibexa_product_catalog',
            'translation_mode' => false,
        ]);
        $resolver->setAllowedTypes('translation_mode', 'bool');
    }
}
