<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FieldTypePage\Form\Type\BlockAttribute;

use Ibexa\Contracts\FieldTypePage\FieldType\Page\Block\Definition\BlockAttributeDefinition;
use Ibexa\Contracts\FieldTypePage\FieldType\Page\Block\Definition\BlockDefinition;
use Ibexa\FieldTypePage\FieldType\Page\Block\Definition\Attribute\Validator\ConstraintFactory;
use Ibexa\FieldTypePage\FieldType\Page\Registry\AttributeFormTypeMapperRegistry;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class NestedAttributeValueType extends AbstractType
{
    private AttributeFormTypeMapperRegistry $attributeFormTypeMapperRegistry;

    private ConstraintFactory $constraintFactory;

    public function __construct(
        AttributeFormTypeMapperRegistry $attributeFormTypeMapperRegistry,
        ConstraintFactory $constraintFactory
    ) {
        $this->attributeFormTypeMapperRegistry = $attributeFormTypeMapperRegistry;
        $this->constraintFactory = $constraintFactory;
    }

    /**
     * @throws \EzSystems\EzPlatformPageFieldType\Exception\Registry\AttributeFormMapperNotFoundException
     * @throws \EzSystems\EzPlatformPageFieldType\Exception\Registry\AttributeValidatorNotFoundException
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $attributeDefinition = $options['block_attribute_definition'];
        $mapper = $this->attributeFormTypeMapperRegistry->getMapperForAttribute($attributeDefinition);
        $constraints = $attributeDefinition->getConstraints();

        $isRequired = isset($constraints['not_blank'])
            || isset($constraints['not_blank_richtext']);

        $builder->setRequired($isRequired);

        $builder->add(
            $mapper->map(
                $builder,
                $options['block_definition'],
                $attributeDefinition,
                $this->getConstraints($constraints),
            )
        );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setRequired(
                [
                    'attribute',
                    'block_definition',
                    'block_attribute_definition',
                ]
            )
            ->setDefaults(['language_code' => ''])
            ->setAllowedTypes('attribute', 'array')
            ->setAllowedTypes('block_definition', BlockDefinition::class)
            ->setAllowedTypes('block_attribute_definition', BlockAttributeDefinition::class)
            ->setAllowedTypes('language_code', 'string')
        ;
    }

    /**
     * @param array<string, array<string, string|array<string>>> $validators
     *
     * @return array<\Symfony\Component\Validator\Constraint>
     *
     * @throws \EzSystems\EzPlatformPageFieldType\Exception\Registry\AttributeValidatorNotFoundException
     */
    private function getConstraints(array $validators): array
    {
        $constraints = [];
        foreach ($validators as $validatorIdentifier => $validatorConfiguration) {
            $constraints[] = $this->constraintFactory->getConstraint($validatorIdentifier, $validatorConfiguration);
        }

        return $constraints;
    }
}
