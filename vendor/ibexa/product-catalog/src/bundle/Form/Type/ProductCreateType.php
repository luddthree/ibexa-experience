<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\ProductCatalog\Form\Type;

use Ibexa\Bundle\ProductCatalog\Form\Data\ProductCreateData;
use Ibexa\ContentForms\Form\Type\Content\BaseContentType;
use Ibexa\Contracts\ProductCatalog\Values\AttributeDefinitionAssignmentInterface;
use Ibexa\ProductCatalog\FieldType\ProductSpecification\Type;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ProductCreateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('create', SubmitType::class);

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            [$this, 'onFormPreSetData']
        );

        $builder->addEventListener(
            FormEvents::SUBMIT,
            [$this, 'onFormSubmit']
        );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ProductCreateData::class,
            'translation_domain' => 'ibexa_product_catalog',
        ]);
    }

    public function getParent(): string
    {
        return BaseContentType::class;
    }

    public function onFormPreSetData(FormEvent $event): void
    {
        $form = $event->getForm();

        /** @var \Ibexa\Bundle\ProductCatalog\Form\Data\ProductCreateData $data */
        $data = $event->getData();

        $form->add('attributes', AttributeCollectionType::class, [
            'product_type' => $data->getProductType(),
            'attribute_filter' => $this->getAttributeFilter(),
        ]);
    }

    public function onFormSubmit(FormEvent $event): void
    {
        /** @var \Ibexa\Bundle\ProductCatalog\Form\Data\ProductCreateData $data */
        $data = $event->getData();
        foreach ($data->getFieldsData() as $fieldData) {
            if ($fieldData->getFieldTypeIdentifier() === Type::FIELD_TYPE_IDENTIFIER) {
                // Elevate product specification data
                $data->setCode($fieldData->value->getCode());
                break;
            }
        }
    }

    /**
     * @return callable(AttributeDefinitionAssignmentInterface): bool
     */
    private function getAttributeFilter(): callable
    {
        return static function (AttributeDefinitionAssignmentInterface $attribute): bool {
            return !$attribute->isDiscriminator();
        };
    }
}
