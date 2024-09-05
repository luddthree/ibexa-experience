<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Seo\Form\Type\FieldType;

use Ibexa\Seo\FieldType\SeoValue;
use Ibexa\Seo\Value\SeoTypesValue;
use JMS\TranslationBundle\Annotation\Desc;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class SeoTypesValueFieldType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add(
            'seoTypesValue',
            SeoTypesFieldType::class,
            [
                'required' => false,
                'label' => /** @Desc("SEO types") */ 'field_definition.ibexa_seo.types',
                'seo_types' => $options['seo_types'],
                'seo_types_placeholders' => $options['seo_types_placeholders'],
            ]
        );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'translation_domain' => 'ibexa_seo_fieldtype',
            'data_class' => SeoValue::class,
            'seo_types' => [],
            'seo_types_placeholders' => null,
        ]);

        $resolver->addAllowedTypes('seo_types_placeholders', ['null', SeoTypesValue::class]);
    }
}
