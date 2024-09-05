<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FormBuilder\Form\Type;

use Ibexa\Contracts\FormBuilder\FieldType\Model\Validator;
use Ibexa\FormBuilder\Definition\FieldDefinition;
use Ibexa\FormBuilder\Definition\FieldValidatorDefinition;
use Ibexa\FormBuilder\Definition\Validator\ConstraintFactory;
use Ibexa\FormBuilder\Exception\ValidatorConfigurationMapperNotFoundException;
use Ibexa\FormBuilder\Form\Mapper\ValidatorConfigurationMapperRegistry;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FieldValidatorConfigurationType extends AbstractType
{
    /** @var \Ibexa\FormBuilder\Form\Mapper\ValidatorConfigurationMapperRegistry */
    private $validatorConfigurationMapperRegistry;

    /** @var \Ibexa\FormBuilder\Definition\Validator\ConstraintFactory */
    private $constraintFactory;

    /**
     * @param \Ibexa\FormBuilder\Form\Mapper\ValidatorConfigurationMapperRegistry $validatorConfigurationMapperRegistry
     * @param \Ibexa\FormBuilder\Definition\Validator\ConstraintFactory $constraintFactory
     */
    public function __construct(
        ValidatorConfigurationMapperRegistry $validatorConfigurationMapperRegistry,
        ConstraintFactory $constraintFactory
    ) {
        $this->validatorConfigurationMapperRegistry = $validatorConfigurationMapperRegistry;
        $this->constraintFactory = $constraintFactory;
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
            $validatorConfigurationMapper = $this->validatorConfigurationMapperRegistry->getMapperForValidator(
                $options['field_validator_definition']
            );
        } catch (ValidatorConfigurationMapperNotFoundException $e) {
            return;
        }

        $validatorDefinition = $options['field_validator_definition'];

        $constraints = [];
        foreach ($validatorDefinition->getConstraints() as $identifier => $constraint) {
            $constraints[] = $this->constraintFactory->getConstraint($identifier, $constraint);
        }

        $builder->add(
            $validatorConfigurationMapper->map(
                $builder,
                $options['field_definition'],
                $options['field_validator_definition'],
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
            ->setDefaults(['data_class' => Validator::class])
            ->setRequired(['field_definition', 'field_validator_definition'])
            ->setAllowedTypes('field_definition', FieldDefinition::class)
            ->setAllowedTypes('field_validator_definition', FieldValidatorDefinition::class);
    }
}

class_alias(FieldValidatorConfigurationType::class, 'EzSystems\EzPlatformFormBuilder\Form\Type\FieldValidatorConfigurationType');
