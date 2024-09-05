<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Form\Type\Model;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class NominalValueType extends AbstractType
{
    public function getBlockPrefix(): string
    {
        return 'personalization_attribute_nominal_value';
    }

    public function getParent(): string
    {
        return CollectionType::class;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'entry_type' => NominalValueEntryType::class,
            'prototype' => true,
            'allow_add' => true,
            'entry_options' => [
                'label' => false,
            ],
        ]);
    }
}

class_alias(NominalValueType::class, 'Ibexa\Platform\Personalization\Form\Type\Model\NominalValueType');
