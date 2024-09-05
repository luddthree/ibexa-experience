<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\PageBuilder\Form\Type\Attribute;

use Ibexa\Contracts\FieldTypePage\FieldType\Page\Block\Definition\BlockAttributeDefinition;
use Ibexa\Contracts\FieldTypePage\FieldType\Page\Block\Definition\BlockDefinition;
use Ibexa\FieldTypePage\Exception\Registry\AttributeFormMapperNotFoundException;
use Ibexa\FieldTypePage\FieldType\Page\Block\Definition\Attribute\Validator\ConstraintFactory;
use Ibexa\FieldTypePage\FieldType\Page\Registry\AttributeFormTypeMapperRegistry;
use Ibexa\PageBuilder\Data\Attribute\Attribute;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AttributeType extends AbstractType implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    /** @var \Ibexa\FieldTypePage\FieldType\Page\Registry\AttributeFormTypeMapperRegistry */
    private $attributeFormTypeMapperRegistry;

    /** @var \Ibexa\FieldTypePage\FieldType\Page\Block\Definition\Attribute\Validator\ConstraintFactory */
    private $constraintFactory;

    /**
     * @param \Ibexa\FieldTypePage\FieldType\Page\Registry\AttributeFormTypeMapperRegistry $attributeFormTypeMapperRegistry
     * @param \Ibexa\FieldTypePage\FieldType\Page\Block\Definition\Attribute\Validator\ConstraintFactory $constraintFactory
     */
    public function __construct(
        AttributeFormTypeMapperRegistry $attributeFormTypeMapperRegistry,
        ConstraintFactory $constraintFactory,
        ?LoggerInterface $logger = null
    ) {
        $this->attributeFormTypeMapperRegistry = $attributeFormTypeMapperRegistry;
        $this->constraintFactory = $constraintFactory;
        $this->logger = $logger ?? new NullLogger();
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     *
     * @throws \Ibexa\FieldTypePage\Exception\Registry\AttributeValidatorNotFoundException
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('id', TextType::class, ['attr' => ['readonly' => true]])
            ->add('name', TextType::class, ['attr' => ['readonly' => true]]);

        try {
            $attributeFormTypeMapper = $this->attributeFormTypeMapperRegistry->getMapperForAttribute(
                $options['block_attribute_definition']
            );
        } catch (AttributeFormMapperNotFoundException $e) {
            $this->logger->error($e->getMessage(), [
                'exception' => $e,
            ]);

            return;
        }

        $attributeDefinition = $options['block_attribute_definition'];
        $isRequired = isset($attributeDefinition->getConstraints()['not_blank'])
            || isset($attributeDefinition->getConstraints()['not_blank_richtext']);

        $builder->setRequired($isRequired);

        $constraints = [];
        foreach ($attributeDefinition->getConstraints() as $identifier => $constraint) {
            $constraints[] = $this->constraintFactory->getConstraint($identifier, $constraint);
        }

        $builder->add(
            $attributeFormTypeMapper->map(
                $builder,
                $options['block_definition'],
                $options['block_attribute_definition'],
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
            ->setDefaults([
                'data_class' => Attribute::class,
                'language_code' => '',
            ])
            ->setRequired(['block_definition', 'block_attribute_definition'])
            ->setAllowedTypes('block_definition', BlockDefinition::class)
            ->setAllowedTypes('block_attribute_definition', BlockAttributeDefinition::class)
            ->setAllowedTypes('language_code', 'string')
        ;
    }
}

class_alias(AttributeType::class, 'EzSystems\EzPlatformPageBuilder\Form\Type\Attribute\AttributeType');
