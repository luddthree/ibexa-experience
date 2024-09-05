<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\Extension;

use Ibexa\AdminUi\Form\Data\ContentTypeData;
use Ibexa\AdminUi\Form\Type\ContentType\ContentTypeUpdateType;
use Ibexa\Bundle\ProductCatalog\Form\Type\AttributeDefinitionAssignmentCollectionType;
use Ibexa\Bundle\ProductCatalog\Form\Type\RegionSettingsDataType;
use Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition;
use Ibexa\Contracts\ProductCatalog\RegionServiceInterface;
use Ibexa\Contracts\ProductCatalog\Values\Region\RegionQuery;
use Ibexa\Core\Helper\FieldsGroups\FieldsGroupsList;
use Ibexa\ProductCatalog\FieldType\ProductSpecification\Type as ProductSpecificationType;
use JMS\TranslationBundle\Annotation\Desc;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Event\PreSetDataEvent;
use Symfony\Component\Form\Event\SubmitEvent;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;

final class ContentTypeEditTypeExtension extends AbstractTypeExtension
{
    private FieldsGroupsList $fieldsGroupsList;

    private RegionServiceInterface $regionService;

    public function __construct(
        FieldsGroupsList $fieldsGroupsList,
        RegionServiceInterface $regionService
    ) {
        $this->fieldsGroupsList = $fieldsGroupsList;
        $this->regionService = $regionService;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (PreSetDataEvent $event): void {
                $this->updateForm($event->getForm(), $event->getData());
            }
        );

        $builder->addEventListener(
            FormEvents::SUBMIT,
            function (SubmitEvent $event): void {
                $this->updateForm($event->getForm(), $event->getData());
            }
        );
    }

    public static function getExtendedTypes(): iterable
    {
        return [ContentTypeUpdateType::class];
    }

    public function updateForm(FormInterface $form, ContentTypeData $data): void
    {
        $specification = $this->findProductSpecification($data);

        if ($specification !== null) {
            $form->remove('isContainer');
            $form->remove('defaultSortField');
            $form->remove('defaultAlwaysAvailable');

            $this->addAttributeDefinitionAssignmentForm($form, $specification);
            $this->addVirtualSettingForm($form, $specification);

            if ($this->isAtLeastOneRegionDefined()) {
                $this->addVatRatesAssignmentForm($form, $specification);
            }
        }
    }

    private function addAttributeDefinitionAssignmentForm(FormInterface $form, FieldDefinition $specification): void
    {
        $propertyPath = sprintf(
            'fieldDefinitionsData[%s][%s].fieldSettings[attributes_definitions]',
            $specification->fieldGroup ?: $this->fieldsGroupsList->getDefaultGroup(),
            $specification->identifier
        );

        $form->add(
            'attributesDefinitions',
            CollectionType::class,
            [
                'entry_type' => AttributeDefinitionAssignmentCollectionType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'disabled' => false,
                'label' => /** @Desc("Attributes") */ 'field_definition.ibexa_product_specification.attributes_definitions',
                'property_path' => $propertyPath,
                'translation_domain' => 'ibexa_product_catalog',
            ]
        );
    }

    private function addVirtualSettingForm(FormInterface $form, FieldDefinition $specification): void
    {
        $propertyPath = sprintf(
            'fieldDefinitionsData[%s][%s].fieldSettings[is_virtual]',
            $specification->fieldGroup ?: $this->fieldsGroupsList->getDefaultGroup(),
            $specification->identifier
        );

        $form->add(
            'virtual',
            CheckboxType::class,
            [
                'attr' => [
                    'hidden' => true,
                ],
                'required' => true,
                'label' => false,
                'property_path' => $propertyPath,
                'translation_domain' => 'ibexa_product_catalog',
            ]
        );
    }

    private function addVatRatesAssignmentForm(FormInterface $form, FieldDefinition $specification): void
    {
        $propertyPath = sprintf(
            'fieldDefinitionsData[%s][%s].fieldSettings[regions]',
            $specification->fieldGroup ?: $this->fieldsGroupsList->getDefaultGroup(),
            $specification->identifier
        );

        $form->add(
            'regions',
            CollectionType::class,
            [
                'entry_type' => RegionSettingsDataType::class,
                'property_path' => $propertyPath,
            ]
        );
    }

    private function findProductSpecification(ContentTypeData $contentTypeData): ?FieldDefinition
    {
        return $contentTypeData->contentTypeDraft->getFirstFieldDefinitionOfType(ProductSpecificationType::FIELD_TYPE_IDENTIFIER);
    }

    private function isAtLeastOneRegionDefined(): bool
    {
        $query = new RegionQuery();
        $query->setLimit(0);

        return $this->regionService->findRegions($query)->getTotalCount() > 0;
    }
}
