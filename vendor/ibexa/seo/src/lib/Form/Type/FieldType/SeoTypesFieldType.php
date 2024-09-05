<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Seo\Form\Type\FieldType;

use Ibexa\Seo\Value\SeoTypesValue;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class SeoTypesFieldType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /**
         * @var array<string, array{
         *     label: string,
         *     fields: array<string, string>,
         * }> $seoTypes
         */
        $seoTypes = $options['seo_types'] ?? [];

        $builder->add('types', SeoTypesCollectionType::class, [
            'seo_types' => $seoTypes,
            'seo_types_placeholders' => $options['seo_types_placeholders'],
            'label' => false,
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'translation_domain' => 'ibexa_seo_fieldtype',
            'data_class' => SeoTypesValue::class,
            'seo_types' => [],
            'seo_types_placeholders' => null,
        ]);

        $resolver->addAllowedTypes('seo_types_placeholders', ['null', SeoTypesValue::class]);
    }
}
