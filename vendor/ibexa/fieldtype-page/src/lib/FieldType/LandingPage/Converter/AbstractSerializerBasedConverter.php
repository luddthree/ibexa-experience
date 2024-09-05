<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FieldTypePage\FieldType\LandingPage\Converter;

use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;

abstract class AbstractSerializerBasedConverter
{
    /** @var \JMS\Serializer\SerializerInterface|\JMS\Serializer\ArrayTransformerInterface */
    protected $serializer;

    /**
     * @param \JMS\Serializer\SerializerInterface $serializer
     */
    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * @param mixed $object
     *
     * @return string
     */
    public function encode($object): string
    {
        return $this->serializer->serialize($object, 'json');
    }

    /**
     * @param string $json
     *
     * @return mixed
     */
    public function decode(string $json)
    {
        return $this->serializer->deserialize($json, $this->getObjectClass(), 'json');
    }

    /**
     * @param mixed $object
     *
     * @return array
     */
    public function toArray($object, ?SerializationContext $context = null): array
    {
        return $this->serializer->toArray($object, $context);
    }

    /**
     * @param array $array
     *
     * @return mixed
     */
    public function fromArray(array $array)
    {
        return $this->serializer->fromArray($array, $this->getObjectClass());
    }

    /**
     * @return string
     */
    abstract public function getObjectClass(): string;
}

class_alias(AbstractSerializerBasedConverter::class, 'EzSystems\EzPlatformPageFieldType\FieldType\LandingPage\Converter\AbstractSerializerBasedConverter');
