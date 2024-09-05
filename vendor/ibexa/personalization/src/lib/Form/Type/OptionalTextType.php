<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Form\Type;

use Ibexa\Personalization\Event\Subscriber\Form\OptionalFieldSubscriber;
use Ibexa\Personalization\Form\Data\OptionalTextData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class OptionalTextType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('enabled', CheckboxType::class, [
                'label' => false,
                'attr' => [
                    'data-related' => $builder->getOption('data-related'),
                    'class' => $builder->getOption('class'),
                ],
            ])
            ->add('value', TextType::class, [
                'required' => true,
                'label' => $builder->getOption('value_label'),
            ])
            ->addEventSubscriber(new OptionalFieldSubscriber())
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => OptionalTextData::class,
            'data-related' => null,
            'class' => null,
            'value_label' => null,
        ])
        ->setAllowedTypes('data-related', ['null', 'string'])
        ->setAllowedTypes('class', ['null', 'string'])
        ->setAllowedTypes('value_label', ['null', 'string']);
    }
}

class_alias(OptionalTextType::class, 'Ibexa\Platform\Personalization\Form\Type\OptionalTextType');
