<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\Type\Catalog;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

final class CatalogFilterRangeAttributeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $commonOptions = $options['common_options'] ?? [];

        $min = $options['min'];
        $max = $options['max'];

        if ($min !== null || $max !== null) {
            $commonOptions += [
                'constraints' => new Assert\Range([
                    'min' => $min,
                    'max' => $max,
                ]),
                'attr' => [
                    'min' => $min,
                    'max' => $max,
                ],
            ];
        }

        $builder
            ->add('min', $options['type_widget'], $commonOptions)
            ->add('max', $options['type_widget'], $commonOptions);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'type_widget' => NumberType::class,
                'common_options' => [],
                'min' => null,
                'max' => null,
            ])
            ->setAllowedTypes('type_widget', ['string'])
            ->setAllowedTypes('common_options', ['array'])
            ->setAllowedTypes('min', ['null', 'int', 'float'])
            ->setAllowedTypes('min', ['null', 'int', 'float'])
        ;
    }
}
