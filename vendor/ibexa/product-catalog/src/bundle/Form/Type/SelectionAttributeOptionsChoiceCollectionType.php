<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Definition of choices available in selection attribute.
 */
final class SelectionAttributeOptionsChoiceCollectionType extends AbstractType
{
    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars += [
            'translation_mode' => $options['translation_mode'],
        ];
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setRequired('language_code');
        $resolver->setAllowedTypes('language_code', 'string');
        $resolver->setDefaults([
            'allow_add' => true,
            'allow_delete' => true,
            'entry_type' => SelectionAttributeOptionsChoiceType::class,
            'entry_options' => static fn (Options $options): array => [
                'language_code' => $options['language_code'],
                'translation_mode' => $options['translation_mode'],
            ],
            'translation_mode' => false,
        ]);
        $resolver->setAllowedTypes('translation_mode', 'bool');
    }

    public function getParent(): string
    {
        return CollectionType::class;
    }
}
