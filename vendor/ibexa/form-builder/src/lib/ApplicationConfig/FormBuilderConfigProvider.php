<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FormBuilder\ApplicationConfig;

use Ibexa\Contracts\AdminUi\UI\Config\ProviderInterface;
use Ibexa\FormBuilder\Definition\FieldDefinitionFactory;
use JMS\Serializer\Naming\IdenticalPropertyNamingStrategy;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerBuilder;

class FormBuilderConfigProvider implements ProviderInterface
{
    /** @var \Ibexa\FormBuilder\Definition\FieldDefinitionFactory */
    private $fieldDefinitionFactory;

    /**
     * @param \Ibexa\FormBuilder\Definition\FieldDefinitionFactory $fieldDefinitionFactory
     */
    public function __construct(FieldDefinitionFactory $fieldDefinitionFactory)
    {
        $this->fieldDefinitionFactory = $fieldDefinitionFactory;
    }

    /**
     * @return array
     */
    public function getConfig(): array
    {
        $serializer = SerializerBuilder::create()
            ->setPropertyNamingStrategy(new IdenticalPropertyNamingStrategy())
            ->build();

        $context = new SerializationContext();
        $context->setSerializeNull(true);

        return [
            'fieldsConfig' => $serializer->toArray(
                $this->fieldDefinitionFactory->getFieldDefinitions(),
                $context
            ),
        ];
    }
}

class_alias(FormBuilderConfigProvider::class, 'EzSystems\EzPlatformFormBuilder\ApplicationConfig\FormBuilderConfigProvider');
