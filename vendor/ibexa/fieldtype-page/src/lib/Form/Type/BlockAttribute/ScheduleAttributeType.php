<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FieldTypePage\Form\Type\BlockAttribute;

use Ibexa\FieldTypePage\Form\DataTransformer\ScheduleAttributeDataTransformer;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ScheduleAttributeType extends AbstractType
{
    /** @var \JMS\Serializer\SerializerInterface */
    private $serializer;

    /**
     * @param \JMS\Serializer\SerializerInterface $serializer
     */
    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'block_configuration_attribute_schedule_attribute';
    }

    /**
     * {@inheritdoc}
     */
    public function getParent(): string
    {
        return TextareaType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): FormBuilderInterface
    {
        $builder->addViewTransformer(new ScheduleAttributeDataTransformer($this->serializer, $options['attribute_identifier']));

        return $builder;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setRequired('attribute_identifier')
            ->setAllowedTypes('attribute_identifier', 'string')
            ->setDefaults([
                'multiple' => true,
            ])
        ;
    }
}

class_alias(ScheduleAttributeType::class, 'EzSystems\EzPlatformPageFieldType\Form\Type\BlockAttribute\ScheduleAttributeType');
