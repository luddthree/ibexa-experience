<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Personalization\Form\Type\Model;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;

final class SubmodelNominalType extends AbstractType
{
    public function getParent(): string
    {
        return SubmodelType::class;
    }

    public function getBlockPrefix(): string
    {
        return 'personalization_attribute_nominal';
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('attributeValues', CollectionType::class, [
            'entry_type' => NominalValueType::class,
            'prototype' => true,
            'allow_add' => true,
            'entry_options' => [
                'label' => false,
            ],
        ]);
    }
}

class_alias(SubmodelNominalType::class, 'Ibexa\Platform\Personalization\Form\Type\Model\SubmodelNominalType');
