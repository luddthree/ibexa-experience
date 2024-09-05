<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FormBuilder\FieldType\Serializer;

use JMS\Serializer\ContextFactory\DefaultDeserializationContextFactory;
use JMS\Serializer\ContextFactory\DeserializationContextFactoryInterface;
use JMS\Serializer\DeserializationContext;

/**
 * @todo It's probably not needed anymore
 */
class DeserializationContextFactory implements DeserializationContextFactoryInterface
{
    /** @var \JMS\Serializer\ContextFactory\DeserializationContextFactoryInterface */
    private $innerDeserializationContextFactory;

    /**
     * @param \JMS\Serializer\ContextFactory\DeserializationContextFactoryInterface $innerDeserializationContextFactory
     */
    public function __construct(DeserializationContextFactoryInterface $innerDeserializationContextFactory = null)
    {
        $this->innerDeserializationContextFactory = $innerDeserializationContextFactory ?? new DefaultDeserializationContextFactory();
    }

    /**
     * {@inheritdoc}
     */
    public function createDeserializationContext(): DeserializationContext
    {
        $deserializationContext = $this->innerDeserializationContextFactory->createDeserializationContext();

        return $deserializationContext;
    }
}

class_alias(DeserializationContextFactory::class, 'EzSystems\EzPlatformFormBuilder\FieldType\Serializer\DeserializationContextFactory');
