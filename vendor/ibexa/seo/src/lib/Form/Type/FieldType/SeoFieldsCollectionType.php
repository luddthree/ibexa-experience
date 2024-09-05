<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Seo\Form\Type\FieldType;

use Ibexa\Seo\Value\SeoTypeValue;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

final class SeoFieldsCollectionType extends AbstractType
{
    private TranslatorInterface $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var \Ibexa\Seo\Value\SeoTypeValue|null $placeholdersOption */
        $placeholdersOption = $options['seo_fields_placeholders'];
        $seoFieldsPlaceholders = null !== $placeholdersOption ? $placeholdersOption->getFields() : [];

        /**
         * @var array<string, array{
         *     type: string,
         *     label: string
         * }> $seoFields
         */
        $seoFields = $options['seo_fields'] ?? [];

        /** @var string $seoType */
        $seoType = $options['seo_type'];

        foreach ($seoFields as $seoFieldName => $seoFieldConfig) {
            /** @Ignore */
            $labelTranslation = $this->translator->trans(
                sprintf('type.%s.field.%s', $seoType, $seoFieldName),
                [],
                'ibexa_seo_types'
            );

            $builder->add($seoFieldName, TextType::class, [
                'required' => false,
                'label' => $labelTranslation,
                'attr' => [
                    'placeholder' => $seoFieldsPlaceholders[$seoFieldName] ?? null,
                ],
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setRequired([
            'seo_type',
            'seo_fields',
            'seo_fields_placeholders',
        ]);

        $resolver->setDefaults([
            'seo_fields' => [],
            'seo_fields_placeholders' => null,
        ]);

        $resolver->addAllowedTypes('seo_type', ['string']);
        $resolver->addAllowedTypes('seo_fields_placeholders', ['null', SeoTypeValue::class]);
    }
}
