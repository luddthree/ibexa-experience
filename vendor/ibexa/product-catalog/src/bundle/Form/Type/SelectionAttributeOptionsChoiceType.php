<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class SelectionAttributeOptionsChoiceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('value', TextType::class, [
            'disabled' => $options['translation_mode'],
            'required' => true,
        ]);

        $builder->add('label', TextType::class, [
            'required' => true,
            'property_path' => sprintf('[label][%s]', $options['language_code']),
        ]);
    }

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
            'translation_domain' => 'ibexa_product_catalog',
            'translation_mode' => false,
        ]);
        $resolver->setAllowedTypes('translation_mode', 'bool');
    }
}
