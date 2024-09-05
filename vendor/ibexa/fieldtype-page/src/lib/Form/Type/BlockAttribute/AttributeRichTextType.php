<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FieldTypePage\Form\Type\BlockAttribute;

use Ibexa\FieldTypeRichText\Form\Type\RichTextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AttributeRichTextType extends AbstractType
{
    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars['language_code'] = $options['language_code'];
        $view->vars['has_distraction_free_mode'] = $options['has_distraction_free_mode'];
    }

    /**
     * {@inheritdoc}
     */
    public function getParent(): string
    {
        return RichTextType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'block_configuration_attribute_richtext';
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'language_code' => '',
            'has_distraction_free_mode' => true,
        ]);

        $resolver->setAllowedTypes('has_distraction_free_mode', ['bool']);
    }
}

class_alias(AttributeRichTextType::class, 'EzSystems\EzPlatformPageFieldType\Form\Type\BlockAttribute\AttributeRichTextType');
