<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\Type;

use Ibexa\AdminUi\Form\Type\Language\LanguageChoiceType;
use Ibexa\Bundle\ProductCatalog\Form\Data\AttributeGroupUpdateData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class AttributeGroupUpdateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $isTranslation = $options['main_language_code'] !== $options['language_code'];

        $builder->add('language', LanguageChoiceType::class, ['disabled' => $isTranslation]);
        $builder->add('name', TextType::class);
        $builder->add('identifier', TextType::class, ['disabled' => $isTranslation]);
        $builder->add('position', IntegerType::class, ['disabled' => $isTranslation]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => AttributeGroupUpdateData::class,
            'main_language_code' => null,
        ])
        ->setDefined(['main_language_code'])
        ->setAllowedTypes('main_language_code', ['null', 'string'])
        ->setRequired(['language_code']);
    }
}
