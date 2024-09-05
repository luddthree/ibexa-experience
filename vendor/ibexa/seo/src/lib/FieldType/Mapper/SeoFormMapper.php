<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Seo\FieldType\Mapper;

use Ibexa\AdminUi\FieldType\FieldDefinitionFormMapperInterface;
use Ibexa\AdminUi\Form\Data\FieldDefinitionData;
use Ibexa\Contracts\ContentForms\Data\Content\FieldData;
use Ibexa\Contracts\ContentForms\FieldType\FieldValueFormMapperInterface;
use Ibexa\Contracts\Seo\Resolver\SeoTypesResolverInterface;
use Ibexa\Seo\Form\Type\FieldType\SeoTypesFieldType;
use Ibexa\Seo\Form\Type\FieldType\SeoTypesValueFieldType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class SeoFormMapper implements FieldDefinitionFormMapperInterface, FieldValueFormMapperInterface
{
    private SeoTypesResolverInterface $seoTypesResolver;

    public function __construct(
        SeoTypesResolverInterface $seoTypesResolver
    ) {
        $this->seoTypesResolver = $seoTypesResolver;
    }

    public function mapFieldDefinitionForm(FormInterface $fieldDefinitionForm, FieldDefinitionData $data): void
    {
        $fieldDefinitionForm->add(
            'types',
            SeoTypesFieldType::class,
            [
                'label' => false,
                'property_path' => 'fieldSettings[types]',
                'seo_types' => $this->seoTypesResolver->getFieldsByTypes(),
                'required' => false,
            ]
        );
    }

    public function mapFieldValueForm(FormInterface $fieldForm, FieldData $data): void
    {
        $definition = $data->fieldDefinition;

        /** @var \Ibexa\Seo\Value\SeoTypesValue|null $fieldTypesPlaceholders */
        $fieldTypesPlaceholders = $definition->getFieldSettings()['types'] ?? null;

        $fieldForm->add(
            'value',
            SeoTypesValueFieldType::class,
            [
                'label' => false,
                'property_path' => 'value',
                'seo_types' => $this->seoTypesResolver->getFieldsByTypes(),
                'seo_types_placeholders' => $fieldTypesPlaceholders,
                'required' => false,
            ]
        );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'translation_domain' => 'ibexa_content_type',
            ]);
    }
}
