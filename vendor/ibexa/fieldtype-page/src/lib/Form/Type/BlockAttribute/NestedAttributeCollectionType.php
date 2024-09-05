<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FieldTypePage\Form\Type\BlockAttribute;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class NestedAttributeCollectionType extends AbstractType
{
    public function getParent(): string
    {
        return CollectionType::class;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setRequired(
            [
                'label',
                'translation_domain',
                'label_translation_parameters',
            ]
        );
        $resolver->setAllowedTypes('label', 'string');
        $resolver->setAllowedTypes('translation_domain', 'string');
        $resolver->setAllowedTypes('label_translation_parameters', 'array');
        $resolver->setDefaults([
            'entry_type' => NestedAttributeEntryType::class,
        ]);
    }
}
