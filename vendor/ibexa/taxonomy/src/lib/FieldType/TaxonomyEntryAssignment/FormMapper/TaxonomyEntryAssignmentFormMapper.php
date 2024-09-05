<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Taxonomy\FieldType\TaxonomyEntryAssignment\FormMapper;

use Ibexa\AdminUi\FieldType\FieldDefinitionFormMapperInterface;
use Ibexa\AdminUi\Form\Data\FieldDefinitionData;
use Ibexa\Contracts\ContentForms\Data\Content\FieldData;
use Ibexa\Contracts\ContentForms\FieldType\FieldValueFormMapperInterface;
use Ibexa\Contracts\Core\Repository\PermissionResolver;
use Ibexa\Taxonomy\Form\Type\TaxonomyEntryAssignmentFieldType;
use Ibexa\Taxonomy\Form\Type\TaxonomyType;
use JMS\TranslationBundle\Annotation\Desc;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class TaxonomyEntryAssignmentFormMapper implements FieldValueFormMapperInterface, FieldDefinitionFormMapperInterface
{
    private PermissionResolver $permissionResolver;

    public function __construct(PermissionResolver $permissionResolver)
    {
        $this->permissionResolver = $permissionResolver;
    }

    public function mapFieldValueForm(FormInterface $fieldForm, FieldData $data): void
    {
        $fieldDefinition = $data->fieldDefinition;
        $fieldSettings = $fieldDefinition->getFieldSettings();
        $formConfig = $fieldForm->getConfig();
        $isDisabled = false === $this->permissionResolver->hasAccess('taxonomy', 'assign');

        $fieldForm
            ->add(
                $formConfig->getFormFactory()->createBuilder()
                    ->create('value', TaxonomyEntryAssignmentFieldType::class, [
                        'required' => $fieldDefinition->isRequired,
                        'disabled' => $isDisabled,
                        'label' => $fieldDefinition->getName(),
                        'taxonomy' => $fieldSettings['taxonomy'],
                        'languageCode' => $data->field->languageCode,
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
