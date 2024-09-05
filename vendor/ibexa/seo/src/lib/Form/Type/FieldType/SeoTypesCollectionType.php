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
use Symfony\Contracts\Translation\TranslatorInterface;

final class SeoTypesCollectionType extends AbstractType
{
    private TranslatorInterface $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var \Ibexa\Seo\Value\SeoTypesValue|null $placeholdersOption */
        $placeholdersOption = $options['seo_types_placeholders'];

        /** @var \Ibexa\Seo\Value\SeoTypeValue[] $seoTypesPlaceholders */
        $seoTypesPlaceholders = $placeholdersOption ? $placeholdersOption->getTypes() : [];

        /**
         * @var array<string, array{
         *     label: string,
         *     fields: array<string, string>,
         * }> $seoTypes
         */
        $seoTypes = $options['seo_types'] ?? [];

        foreach ($seoTypes as $type => $typeConfig) {
            /** @Ignore */
            $labelTranslation = $this->translator->trans(
                sprintf('type.%s', $type),
                [],
                'ibexa_seo_types'
            );

            $builder
                ->add($type, SeoFieldType::class, [
                    'required' => false,
                    'label' => $labelTranslation,
                    'seo_type' => $type,
                    'seo_fields' => $typeConfig['fields'],
                    'seo_fields_placeholders' => $seoTypesPlaceholders[$type] ?? null,
                ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'translation_domain' => 'ibexa_seo_fieldtype',
            'seo_types' => [],
            'seo_types_placeholders' => null,
        ]);

        $resolver->addAllowedTypes('seo_types_placeholders', ['null', SeoTypesValue::class]);
    }
}
