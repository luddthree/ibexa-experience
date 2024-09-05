<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\FormBuilder\FieldType\Converter;

use JMS\Serializer\Serializer;

abstract class AbstractSerializerBasedConverter
{
    /** @var \JMS\Serializer\Serializer */
    protected $serializer;

    /**
     * @param \JMS\Serializer\Serializer $serializer
     */
    public function __construct(Serializer $serializer)
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
    public function toArray($object): array
    {
        return $this->serializer->toArray($object);
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

class_alias(AbstractSerializerBasedConverter::class, 'EzSystems\EzPlatformFormBuilder\FieldType\Converter\AbstractSerializerBasedConverter');
