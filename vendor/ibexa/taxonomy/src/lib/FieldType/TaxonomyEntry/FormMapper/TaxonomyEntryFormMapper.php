<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Taxonomy\FieldType\TaxonomyEntry\FormMapper;

use Ibexa\AdminUi\FieldType\FieldDefinitionFormMapperInterface;
use Ibexa\AdminUi\Form\Data\FieldDefinitionData;
use Ibexa\Contracts\ContentForms\Data\Content\FieldData;
use Ibexa\Contracts\ContentForms\FieldType\FieldValueFormMapperInterface;
use Ibexa\Taxonomy\Form\Type\TaxonomyEntryFieldType;
use Ibexa\Taxonomy\Form\Type\TaxonomyType;
use JMS\TranslationBundle\Annotation\Desc;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class TaxonomyEntryFormMapper implements FieldValueFormMapperInterface, FieldDefinitionFormMapperInterface
{
    public function mapFieldValueForm(FormInterface $fieldForm, FieldData $data): void
    {
        $fieldDefinition = $data->fieldDefinition;
        $fieldSettings = $fieldDefinition->getFieldSettings();
        $formConfig = $fieldForm->getConfig();

        $fieldForm
            ->add(
                $formConfig->getFormFactory()->createBuilder()
                    ->create('value', TaxonomyEntryFieldType::class, [
                        'required' => $fieldDefinition->isRequired,
                        'label' => $fieldDefinition->getName(),
                        'taxonomy' => $fieldSettings['taxonomy'] ?? null,
                        'field_definition' => $data->fieldDefinition,
                    ])
                    ->setAutoInitialize(false)
                    ->getForm()
            );
    }

    public function mapFieldDefinitionForm(FormInterface $fieldDefinitionForm, FieldDefinitionData $data): void
    {
        $isTranslation = $data->contentTypeData->languageCode !== $data->contentTypeData->mainLanguageCode;

        $fieldDefinitionForm
            ->add(
                'selectionTaxonomy',
                TaxonomyType::class,
                [
                    'required' => true,
                    'property_path' => 'fieldSettings[taxonomy]',
                    'label' => /** @Desc("Taxonomy") */ 'field_definition.ibexa_taxonomy_entry.taxonomy',
                    'translation_domain' => 'ibexa_content_type',
                    'disabled' => $isTranslation,
                ]
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'translation_domain' => 'ibexa_taxonomy_fieldtypes',
            ])
        ;
    }
}
