<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FieldTypePage\Serializer;

use JMS\Serializer\ContextFactory\DefaultSerializationContextFactory;
use JMS\Serializer\ContextFactory\SerializationContextFactoryInterface;
use JMS\Serializer\SerializationContext;

class SerializationContextFactory implements SerializationContextFactoryInterface
{
    /** @var \JMS\Serializer\ContextFactory\SerializationContextFactoryInterface */
    private $innerSerializationContextFactory;

    /**
     * @param \JMS\Serializer\ContextFactory\SerializationContextFactoryInterface $innerSerializationContextFactory
     */
    public function __construct(
        SerializationContextFactoryInterface $innerSerializationContextFactory = null
    ) {
        if (null === $innerSerializationContextFactory) {
            $innerSerializationContextFactory = new DefaultSerializationContextFactory();
        }

        $this->innerSerializationContextFactory = $innerSerializationContextFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function createSerializationContext(): SerializationContext
    {
        $serializationContext = $this->innerSerializationContextFactory->createSerializationContext();
        $serializationContext->setSerializeNull(true);

        return $serializationContext;
    }
}

class_alias(SerializationContextFactory::class, 'EzSystems\EzPlatformPageFieldType\Serializer\SerializationContextFactory');
