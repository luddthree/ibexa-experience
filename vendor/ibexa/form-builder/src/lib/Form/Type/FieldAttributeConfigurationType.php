<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FormBuilder\Form\Type;

use Ibexa\Contracts\FormBuilder\FieldType\Model\Attribute;
use Ibexa\FormBuilder\Definition\FieldAttributeDefinition;
use Ibexa\FormBuilder\Definition\FieldDefinition;
use Ibexa\FormBuilder\Definition\Validator\ConstraintFactory;
use Ibexa\FormBuilder\Exception\AttributeConfigurationMapperNotFoundException;
use Ibexa\FormBuilder\Form\Mapper\AttributeConfigurationMapperRegistry;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FieldAttributeConfigurationType extends AbstractType
{
    /** @var \Ibexa\FormBuilder\Definition\Validator\ConstraintFactory */
    private $constraintFactory;

    /** @var \Ibexa\FormBuilder\Form\Mapper\AttributeConfigurationMapperRegistry */
    private $attributeConfigurationMapperRegistry;

    /**
     * @param \Ibexa\FormBuilder\Form\Mapper\AttributeConfigurationMapperRegistry $attributeConfigurationMapperRegistry
     * @param \Ibexa\FormBuilder\Definition\Validator\ConstraintFactory $constraintFactory
     */
    public function __construct(
        AttributeConfigurationMapperRegistry $attributeConfigurationMapperRegistry,
        ConstraintFactory $constraintFactory
    ) {
        $this->constraintFactory = $constraintFactory;
        $this->attributeConfigurationMapperRegistry = $attributeConfigurationMapperRegistry;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     *
     * @throws \Ibexa\FormBuilder\Exception\ValidatorNotFoundException
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('identifier', HiddenType::class, ['attr' => ['readonly' => true]]);

        try {
            $attributeConfigurationMapper = $this->attributeConfigurationMapperRegistry->getMapperForAttribute(
                $options['field_attribute_definition']
            );
        } catch (AttributeConfigurationMapperNotFoundException $e) {
            return;
        }

        $attributeDefinition = $options['field_attribute_definition'];

        $builder->setRequired(
            isset($attributeDefinition->getConstraints()['not_blank'])
        );

        $constraints = [];
        foreach ($attributeDefinition->getConstraints() as $identifier => $constraint) {
            $constraints[] = $this->constraintFactory->getConstraint($identifier, $constraint);
        }

        $builder->add(
            $attributeConfigurationMapper->map(
                $builder,
                $options['field_definition'],
                $options['field_attribute_definition'],
                $constraints
            )
        );
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults(['data_class' => Attribute::class])
            ->setRequired(['field_definition', 'field_attribute_definition'])
            ->setAllowedTypes('field_definition', FieldDefinition::class)
            ->setAllowedTypes('field_attribute_definition', FieldAttributeDefinition::class);
    }
}

class_alias(FieldAttributeConfigurationType::class, 'EzSystems\EzPlatformFormBuilder\Form\Type\FieldAttributeConfigurationType');
