<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\Type;

use JMS\TranslationBundle\Annotation\Desc;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class SelectionAttributeOptionsType extends AbstractType
{
    public function getParent(): string
    {
        return AttributeDefinitionOptions::class;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('choices', SelectionAttributeOptionsChoiceCollectionType::class, [
            'required' => false,
            'label' => /** @Desc("Options") */ 'ibexa_product_catalog.attribute.selection.option.choices',
            'translation_mode' => $options['translation_mode'],
            'language_code' => $options['language_code'],
        ]);
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
