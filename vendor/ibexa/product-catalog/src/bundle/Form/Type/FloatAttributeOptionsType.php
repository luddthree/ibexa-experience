<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\Type;

use JMS\TranslationBundle\Annotation\Desc;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class FloatAttributeOptionsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('min', NumberType::class, [
            'disabled' => $options['translation_mode'],
            'html5' => true,
            'label' => /** @Desc("Minimum Value") */ 'ibexa_product_catalog.attribute.float.option.min_value',
            'required' => false,
        ]);

        $builder->add('max', NumberType::class, [
            'disabled' => $options['translation_mode'],
            'html5' => true,
            'label' => /** @Desc("Maximum Value") */ 'ibexa_product_catalog.attribute.float.option.max_value',
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
