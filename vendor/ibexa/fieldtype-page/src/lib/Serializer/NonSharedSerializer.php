<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FieldTypePage\Serializer;

use JMS\Serializer\ArrayTransformerInterface;
use JMS\Serializer\DeserializationContext;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;

/**
 * This Serializer creates new serializer instance on every call.
 *
 * It's a workaround to JMS Serializer issue which breaks on
 * consequent deserialize and serialize calls due to stateful
 * nature of the Visitor.
 *
 * @todo Validate if this is needed after upgrading to JMS Serializer 3.x.
 */
class NonSharedSerializer implements SerializerInterface, ArrayTransformerInterface
{
    /** @var \Ibexa\FieldTypePage\Serializer\SerializerFactory */
    protected $serializerFactory;

    /**
     * @param \Ibexa\FieldTypePage\Serializer\SerializerFactory $serializerFactory
     */
    public function __construct(SerializerFactory $serializerFactory)
    {
        $this->serializerFactory = $serializerFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function serialize($data, string $format, ?SerializationContext $context = null, ?string $type = null): string
    {
        return $this->serializerFactory->create()->serialize($data, $format, $context, $type);
    }

    /**
     * {@inheritdoc}
     */
    public function deserialize(string $data, string $type, string $format, DeserializationContext $context = null)
    {
        return $this->serializerFactory->create()->deserialize($data, $type, $format, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function toArray($data, ?SerializationContext $context = null, ?string $type = null): array
    {
        return $this->serializerFactory->create()->toArray($data, $context, $type);
    }

    /**
     * {@inheritdoc}
     */
    public function fromArray(array $data, string $type, ?DeserializationContext $context = null)
    {
        return $this->serializerFactory->create()->fromArray($data, $type, $context);
    }
}

class_alias(NonSharedSerializer::class, 'EzSystems\EzPlatformPageFieldType\Serializer\NonSharedSerializer');
