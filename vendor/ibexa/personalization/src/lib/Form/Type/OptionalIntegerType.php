<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Form\Type;

use Ibexa\Personalization\Event\Subscriber\Form\OptionalFieldSubscriber;
use Ibexa\Personalization\Form\Data\OptionalIntegerData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class OptionalIntegerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('enabled', CheckboxType::class, [
                'label' => false,
                'attr' => [
                    'data-related-id' => $builder->getOption('data-related-id'),
                ],
            ])
            ->add('value', IntegerType::class, [
                'label' => $builder->getOption('value_label'),
            ])
            ->addEventSubscriber(new OptionalFieldSubscriber())
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
                'data_class' => OptionalIntegerData::class,
                'data-related-id' => null,
                'value_label' => null,
        ])
        ->setAllowedTypes('data-related-id', ['string', 'null'])
        ->setAllowedTypes('value_label', ['string', 'null']);
    }
}

class_alias(OptionalIntegerType::class, 'Ibexa\Platform\Personalization\Form\Type\OptionalIntegerType');
